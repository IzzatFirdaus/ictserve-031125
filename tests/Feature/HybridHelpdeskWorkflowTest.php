<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\CrossModuleIntegration;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\CrossModuleIntegrationService;
use App\Services\HybridHelpdeskService;
use App\Services\NotificationService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Hybrid Helpdesk Workflow Feature Tests
 *
 * Validates guest and authenticated ticket flows, claiming, access rules,
 * and cross-module integration behaviour for the hybrid helpdesk architecture.
 *
 * @requirements 1.1, 1.2, 1.3, 2.2, 3.1
 */
class HybridHelpdeskWorkflowTest extends TestCase
{
    protected HybridHelpdeskService $hybridHelpdeskService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hybridHelpdeskService = app(HybridHelpdeskService::class);
    }

    #[Test]
    public function guest_ticket_creation_persists_enhanced_fields(): void
    {
        $category = TicketCategory::factory()->hardware()->create([
            'sla_response_hours' => 4,
            'sla_resolution_hours' => 24,
        ]);

        $ticket = $this->hybridHelpdeskService->createGuestTicket([
            'guest_name' => 'Guest User',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '+60123456789',
            'guest_staff_id' => 'MOTAC001',
            'guest_grade' => 'N41',
            'guest_division' => 'ICT Division',
            'category_id' => $category->id,
            'priority' => 'high',
            'subject' => 'Laptop power issue',
            'description' => 'Guest laptop is not powering on after system update.',
        ]);

        $this->assertTrue($ticket->isGuestSubmission());
        $this->assertStringStartsWith('HD', $ticket->ticket_number);
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'guest_name' => 'Guest User',
            'guest_email' => 'guest@example.com',
            'guest_grade' => 'N41',
            'guest_division' => 'ICT Division',
            'priority' => 'high',
            'status' => 'open',
        ]);
        $this->assertNotNull($ticket->sla_response_due_at);
        $this->assertNotNull($ticket->sla_resolution_due_at);
    }

    #[Test]
    public function authenticated_ticket_creation_stores_internal_notes(): void
    {
        $category = TicketCategory::factory()->create([
            'code' => 'NET',
            'name_en' => 'Network Issues',
            'name_ms' => 'Isu Rangkaian',
            'sla_response_hours' => 2,
            'sla_resolution_hours' => 12,
        ]);
        $user = User::factory()->create(['email' => 'staff@motac.gov.my']);

        $ticket = $this->hybridHelpdeskService->createAuthenticatedTicket([
            'category_id' => $category->id,
            'priority' => 'urgent',
            'subject' => 'VPN connection failure',
            'description' => 'Unable to establish VPN connection from remote office.',
            'internal_notes' => 'Escalate to network operations for review.',
        ], $user);

        $this->assertTrue($ticket->isAuthenticatedSubmission());
        $this->assertEquals($user->id, $ticket->user_id);
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'user_id' => $user->id,
            'internal_notes' => 'Escalate to network operations for review.',
            'guest_email' => null,
            'status' => 'open',
        ]);
    }

    #[Test]
    public function ticket_claiming_process_attaches_authenticated_user_and_logs_comment(): void
    {
        $user = User::factory()->create(['email' => 'owner@example.com']);
        $ticket = HelpdeskTicket::factory()
            ->guest()
            ->create([
                'guest_email' => 'owner@example.com',
                'status' => 'open',
            ]);

        $result = $this->hybridHelpdeskService->claimGuestTicket($ticket, $user);

        $this->assertTrue($result);
        $ticket->refresh();
        $this->assertEquals($user->id, $ticket->user_id);
        $this->assertTrue($ticket->comments()->where('comment', 'Ticket claimed by authenticated user.')->where('is_internal', true)->exists());
    }

    #[Test]
    public function get_user_accessible_tickets_returns_owned_and_email_matched_records(): void
    {
        $user = User::factory()->create(['email' => 'hybrid@motac.gov.my']);

        $ownedTicket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open',
        ]);
        $guestTicket = HelpdeskTicket::factory()
            ->guest()
            ->create([
                'guest_email' => 'hybrid@motac.gov.my',
                'status' => 'open',
            ]);
        HelpdeskTicket::factory()
            ->guest()
            ->create([
                'guest_email' => 'other@example.com',
            ]);

        $tickets = $this->hybridHelpdeskService
            ->getUserAccessibleTickets($user)
            ->get();

        $this->assertCount(2, $tickets);
        $this->assertTrue($tickets->contains(fn (HelpdeskTicket $ticket) => $ticket->id === $ownedTicket->id));
        $this->assertTrue($tickets->contains(fn (HelpdeskTicket $ticket) => $ticket->id === $guestTicket->id));
    }

    #[Test]
    public function cross_module_integration_is_created_when_ticket_links_to_asset_loan(): void
    {
        $division = Division::factory()->ict()->create();
        $category = TicketCategory::factory()->create([
            'code' => 'MAINTENANCE',
            'name_en' => 'Maintenance',
            'name_ms' => 'Penyelenggaraan',
            'sla_response_hours' => 3,
            'sla_resolution_hours' => 18,
        ]);
        $user = User::factory()->create([
            'email' => 'staff.maintenance@motac.gov.my',
            'role' => 'staff',
            'division_id' => $division->id,
        ]);
        $asset = Asset::factory()->create();
        $loanApplication = LoanApplication::factory()
            ->authenticated()
            ->create([
                'user_id' => $user->id,
                'division_id' => $division->id,
            ]);

        $ticket = $this->hybridHelpdeskService->createAuthenticatedTicket([
            'category_id' => $category->id,
            'priority' => 'high',
            'subject' => 'Asset requires maintenance',
            'description' => 'Projector lamp failure detected during inspection.',
            'asset_id' => $asset->id,
        ], $user);

        $notificationService = \Mockery::mock(NotificationService::class)->shouldIgnoreMissing();
        $this->app->instance(NotificationService::class, $notificationService);

        $integrationService = app(CrossModuleIntegrationService::class);
        $integration = $integrationService->linkTicketToLoan($ticket, $loanApplication);

        $this->assertInstanceOf(CrossModuleIntegration::class, $integration);
        $this->assertDatabaseHas('cross_module_integrations', [
            'id' => $integration->id,
            'helpdesk_ticket_id' => $ticket->id,
            'loan_application_id' => $loanApplication->id,
            'integration_type' => CrossModuleIntegration::TYPE_ASSET_TICKET_LINK,
            'trigger_event' => CrossModuleIntegration::EVENT_TICKET_ASSET_SELECTED,
        ]);
        $integrationData = $integration->integration_data;
        $this->assertIsArray($integrationData);
        $this->assertArrayHasKey('asset_id', $integrationData);
        $this->assertEquals($asset->id, $integrationData['asset_id']);
        $this->assertArrayHasKey('ticket_category', $integrationData);
        $this->assertNotNull($ticket->category);
        $this->assertEquals($ticket->category->name, $integrationData['ticket_category']);
        $this->assertArrayHasKey('linked_at', $integrationData);
        $this->assertNotNull($integrationData['linked_at']);
    }
}

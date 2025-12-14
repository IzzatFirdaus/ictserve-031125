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
    public function guest_ticket_creation_with_null_user_id_and_submitter_fields(): void
    {
        $division = Division::factory()->create();
        $category = TicketCategory::factory()->hardware()->create([
            'sla_response_hours' => 4,
            'sla_resolution_hours' => 24,
        ]);

        $ticket = $this->hybridHelpdeskService->createGuestTicket([
            'guest_name' => 'Ahmad Bin Ali',
            'guest_email' => 'ahmad.ali@motac.gov.my',
            'guest_phone' => '+60123456789',
            'guest_staff_id' => 'MOTAC001',
            'guest_grade' => 'N41',
            'guest_division' => 'Bahagian ICT',
            'division_id' => $division->id,
            'job_grade' => 'Gred 41',
            'declaration_accepted' => true,
            'category_id' => $category->id,
            'priority' => 'high',
            'subject' => 'Masalah kuasa laptop',
            'description' => 'Laptop tetamu tidak dapat dihidupkan selepas kemas kini sistem.',
        ]);

        // Verify hybrid data association: guest submission has user_id=NULL
        $this->assertNull($ticket->user_id, 'Guest submissions must have user_id=NULL for v3.6.0 hybrid architecture');
        $this->assertTrue($ticket->isGuestSubmission());
        $this->assertStringStartsWith('HD', $ticket->ticket_number);

        // Verify submitter_* fields are captured for guest submissions
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'user_id' => null, // Critical: NULL for guest submissions
            'guest_name' => 'Ahmad Bin Ali',
            'guest_email' => 'ahmad.ali@motac.gov.my',
            'guest_grade' => 'N41',
            'guest_division' => 'Bahagian ICT',
            'division_id' => $division->id,
            'job_grade' => 'Gred 41',
            'declaration_accepted' => true,
            'priority' => 'high',
            'status' => 'open',
        ]);
        $this->assertNotNull($ticket->sla_response_due_at);
        $this->assertNotNull($ticket->sla_resolution_due_at);
    }

    #[Test]
    public function authenticated_ticket_creation_with_user_id_linked_and_auto_fill(): void
    {
        $division = Division::factory()->create();
        $category = TicketCategory::factory()->create([
            'code' => 'NET',
            'name_en' => 'Network Issues',
            'name_ms' => 'Isu Rangkaian',
            'sla_response_hours' => 2,
            'sla_resolution_hours' => 12,
        ]);
        $user = User::factory()->create([
            'email' => 'siti.rahman@motac.gov.my',
            'name' => 'Siti Rahman',
            'division_id' => $division->id,
        ]);

        $ticket = $this->hybridHelpdeskService->createAuthenticatedTicket([
            'division_id' => $division->id,
            'job_grade' => 'Gred 44',
            'declaration_accepted' => true,
            'category_id' => $category->id,
            'priority' => 'urgent',
            'subject' => 'Kegagalan sambungan VPN',
            'description' => 'Tidak dapat mewujudkan sambungan VPN dari pejabat jauh.',
            'internal_notes' => 'Eskalasi kepada operasi rangkaian untuk semakan.',
        ], $user);

        // Verify hybrid data association: authenticated submission has user_id linked
        $this->assertNotNull($ticket->user_id, 'Authenticated submissions must have user_id linked for v3.6.0 hybrid architecture');
        $this->assertTrue($ticket->isAuthenticatedSubmission());
        $this->assertEquals($user->id, $ticket->user_id);

        // Verify guest_* fields are NULL for authenticated submissions
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'user_id' => $user->id, // Critical: Linked for authenticated submissions
            'division_id' => $division->id,
            'job_grade' => 'Gred 44',
            'declaration_accepted' => true,
            'internal_notes' => 'Eskalasi kepada operasi rangkaian untuk semakan.',
            'guest_email' => null, // NULL for authenticated submissions
            'guest_name' => null,  // NULL for authenticated submissions
            'status' => 'open',
        ]);
    }

    #[Test]
    public function hybrid_ticket_claiming_transitions_from_guest_to_authenticated(): void
    {
        $user = User::factory()->create(['email' => 'farid.hassan@motac.gov.my']);
        $ticket = HelpdeskTicket::factory()
            ->guest()
            ->create([
                'user_id' => null, // Initially NULL for guest submission
                'guest_email' => 'farid.hassan@motac.gov.my',
                'guest_name' => 'Farid Hassan',
                'status' => 'open',
            ]);

        // Verify initial guest state
        $this->assertNull($ticket->user_id, 'Ticket should start as guest submission with user_id=NULL');
        $this->assertTrue($ticket->isGuestSubmission());

        $result = $this->hybridHelpdeskService->claimGuestTicket($ticket, $user);

        $this->assertTrue($result);
        $ticket->refresh();

        // Verify hybrid data association after claiming
        $this->assertEquals($user->id, $ticket->user_id, 'After claiming, user_id should be linked');
        $this->assertTrue($ticket->isAuthenticatedSubmission(), 'After claiming, ticket becomes authenticated');
        $this->assertTrue($ticket->comments()->where('comment', 'Ticket claimed by authenticated user.')->where('is_internal', true)->exists());
    }

    #[Test]
    public function hybrid_data_association_returns_owned_and_email_matched_records(): void
    {
        $user = User::factory()->create(['email' => 'zainab.ibrahim@motac.gov.my']);

        // Authenticated ticket (user_id linked)
        $ownedTicket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'guest_email' => null,
            'status' => 'open',
        ]);

        // Guest ticket (user_id=NULL, email matched)
        $guestTicket = HelpdeskTicket::factory()
            ->guest()
            ->create([
                'user_id' => null, // Critical: NULL for guest submissions
                'guest_email' => 'zainab.ibrahim@motac.gov.my',
                'status' => 'open',
            ]);

        // Other user's guest ticket (should not be accessible)
        HelpdeskTicket::factory()
            ->guest()
            ->create([
                'user_id' => null,
                'guest_email' => 'other.user@motac.gov.my',
            ]);

        $tickets = $this->hybridHelpdeskService
            ->getUserAccessibleTickets($user)
            ->get();

        // Verify hybrid data association works correctly
        $this->assertCount(2, $tickets, 'User should access both authenticated (user_id linked) and guest (email matched) tickets');
        $this->assertTrue($tickets->contains(fn (HelpdeskTicket $ticket) => $ticket->id === $ownedTicket->id));
        $this->assertTrue($tickets->contains(fn (HelpdeskTicket $ticket) => $ticket->id === $guestTicket->id));

        // Verify data association types
        $authenticatedTickets = $tickets->filter(fn (HelpdeskTicket $ticket) => $ticket->user_id !== null);
        $guestTickets = $tickets->filter(fn (HelpdeskTicket $ticket) => $ticket->user_id === null);

        $this->assertCount(1, $authenticatedTickets, 'Should have 1 authenticated ticket (user_id linked)');
        $this->assertCount(1, $guestTickets, 'Should have 1 guest ticket (user_id=NULL, email matched)');
    }

    #[Test]
    public function cross_module_integration_created_when_authenticated_ticket_links_to_asset_loan(): void
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
            'email' => 'ahmad.maintenance@motac.gov.my',
            'role' => 'staff',
            'division_id' => $division->id,
        ]);
        $asset = Asset::factory()->create();
        $loanApplication = LoanApplication::factory()
            ->authenticated()
            ->create([
                'user_id' => $user->id, // Authenticated loan application
                'division_id' => $division->id,
            ]);

        // Create authenticated ticket (user_id linked)
        $ticket = $this->hybridHelpdeskService->createAuthenticatedTicket([
            'category_id' => $category->id,
            'priority' => 'high',
            'subject' => 'Aset memerlukan penyelenggaraan',
            'description' => 'Kegagalan lampu projektor dikesan semasa pemeriksaan.',
            'asset_id' => $asset->id,
        ], $user);

        // Verify this is an authenticated ticket (user_id linked)
        $this->assertNotNull($ticket->user_id, 'Cross-module integration should work with authenticated tickets (user_id linked)');
        $this->assertEquals($user->id, $ticket->user_id);

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

    #[Test]
    public function hybrid_helpdesk_interface_displays_bahasa_melayu_content(): void
    {
        $user = User::factory()->create(['email' => 'test.user@motac.gov.my']);

        // Test guest form page (BM content)
        $guestResponse = $this->get('/helpdesk/guest/create');
        $guestResponse->assertStatus(200);
        $guestResponse->assertSee('Borang Aduan ICT', false); // BM: ICT Complaint Form
        $guestResponse->assertSee('Nama Penuh', false); // BM: Full Name
        $guestResponse->assertSee('E-mel Rasmi', false); // BM: Official Email
        $guestResponse->assertSee('Hantar Aduan', false); // BM: Submit Complaint

        // Test authenticated dashboard (BM content)
        $dashboardResponse = $this->actingAs($user)->get('/helpdesk/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Papan Pemuka', false); // BM: Dashboard
        $dashboardResponse->assertSee('Aduan Saya', false); // BM: My Complaints
        $dashboardResponse->assertSee('Sejarah Aduan', false); // BM: Complaint History

        // Verify language switcher is disabled/hidden in v3.6.0
        $guestResponse->assertDontSee('English');
        $guestResponse->assertDontSee('language-switcher');
        $dashboardResponse->assertDontSee('English');
        $dashboardResponse->assertDontSee('language-switcher');
    }

    #[Test]
    public function hybrid_ticket_status_messages_use_bahasa_melayu(): void
    {
        $user = User::factory()->create(['email' => 'status.test@motac.gov.my']);

        // Create authenticated ticket
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open',
            'subject' => 'Ujian status BM',
        ]);

        // Test ticket detail page shows BM status
        $response = $this->actingAs($user)->get("/helpdesk/tickets/{$ticket->id}");
        $response->assertStatus(200);
        $response->assertSee('open', false); // Status value: open
        $response->assertSee('Status', false); // BM: Status
        $response->assertSee('Butiran', false); // BM: Details

        // Update ticket status and verify BM status messages
        $ticket->update(['status' => 'in_progress']);
        $response = $this->actingAs($user)->get("/helpdesk/tickets/{$ticket->id}");
        $response->assertSee('Dalam Proses', false); // BM: In Progress

        $ticket->update(['status' => 'resolved']);
        $response = $this->actingAs($user)->get("/helpdesk/tickets/{$ticket->id}");
        $response->assertSee('Diselesaikan', false); // BM: Resolved
    }
}

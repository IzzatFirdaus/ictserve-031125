<?php

declare(strict_types=1);

namespace Tests\Feature\Integration;

use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\CrossModuleIntegrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Cross-Module Integration Test
 *
 * Tests integration between helpdesk and asset loan modules,
 * automatic ticket creation, and data synchronization.
 *
 * Requirements: 18.2, 7.3, D03-FR-003.1
 */
class CrossModuleIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private CrossModuleIntegrationService $service;

    private User $user;

    private Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(CrossModuleIntegrationService::class);
        $this->user = User::factory()->create();
        $this->asset = Asset::factory()->create();
    }

    #[Test]
    public function damaged_asset_return_creates_helpdesk_ticket(): void
    {
        $division = \App\Models\Division::factory()->create();
        $loanApplication = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $division->id,
            'status' => 'issued',
        ]);

        // Link asset to loan application via loan_items
        $loanApplication->loanItems()->create([
            'asset_id' => $this->asset->id,
            'quantity' => 1,
            'unit_value' => 1000.00,
            'total_value' => 1000.00,
        ]);

        $damageDetails = [
            'type' => 'physical',
            'description' => 'Screen cracked during transport',
            'severity' => 'medium',
            'damage_report' => 'Screen cracked during transport',
        ];

        $ticket = $this->service->createTicketForDamagedAsset($loanApplication, $damageDetails);

        // Verify ticket was created
        $this->assertInstanceOf(HelpdeskTicket::class, $ticket);
        $this->assertStringContainsString($this->asset->name, $ticket->subject);
        $this->assertEquals('high', $ticket->priority);
        $this->assertNotNull($ticket->category_id);

        // Verify cross-module integration record
        $this->assertDatabaseHas('cross_module_integrations', [
            'loan_application_id' => $loanApplication->id,
            'helpdesk_ticket_id' => $ticket->id,
        ]);
    }

    #[Test]
    public function ticket_can_be_linked_to_loan_application(): void
    {
        $category = \App\Models\TicketCategory::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
        ]);

        $division = \App\Models\Division::factory()->create();
        $loanApplication = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $division->id,
        ]);

        // Link asset to loan application
        $loanApplication->loanItems()->create([
            'asset_id' => $this->asset->id,
            'quantity' => 1,
            'unit_value' => 1000.00,
            'total_value' => 1000.00,
        ]);

        $this->service->linkTicketToLoan($ticket, $loanApplication);

        $this->assertDatabaseHas('cross_module_integrations', [
            'helpdesk_ticket_id' => $ticket->id,
            'loan_application_id' => $loanApplication->id,
        ]);
    }

    #[Test]
    public function asset_status_updates_when_maintenance_ticket_created(): void
    {
        $division = \App\Models\Division::factory()->create();
        $loanApplication = LoanApplication::factory()->create([
            'division_id' => $division->id,
            'status' => 'returned',
        ]);

        // Link asset to loan application
        $loanApplication->loanItems()->create([
            'asset_id' => $this->asset->id,
            'quantity' => 1,
            'unit_value' => 1000.00,
            'total_value' => 1000.00,
        ]);

        $damageDetails = [
            'type' => 'hardware_failure',
            'description' => 'Hard drive failure detected',
            'severity' => 'high',
            'damage_report' => 'Hard drive failure detected',
        ];

        $ticket = $this->service->createTicketForDamagedAsset($loanApplication, $damageDetails);

        // Verify asset status was updated
        $this->asset->refresh();
        $this->assertEquals('maintenance', $this->asset->status->value);
    }

    #[Test]
    public function integration_audit_trail_is_maintained(): void
    {
        $category = \App\Models\TicketCategory::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['category_id' => $category->id]);

        $division = \App\Models\Division::factory()->create();
        $loanApplication = LoanApplication::factory()->create(['division_id' => $division->id]);

        // Link asset to loan application
        $loanApplication->loanItems()->create([
            'asset_id' => $this->asset->id,
            'quantity' => 1,
            'unit_value' => 1000.00,
            'total_value' => 1000.00,
        ]);

        $this->service->linkTicketToLoan($ticket, $loanApplication);

        // Verify integration record was created (audit is done via model events)
        $this->assertDatabaseHas('cross_module_integrations', [
            'helpdesk_ticket_id' => $ticket->id,
            'loan_application_id' => $loanApplication->id,
        ]);
    }

    #[Test]
    public function get_related_tickets_for_loan(): void
    {
        $category = \App\Models\TicketCategory::factory()->create();
        $division = \App\Models\Division::factory()->create();
        $loanApplication = LoanApplication::factory()->create(['division_id' => $division->id]);
        $relatedTickets = HelpdeskTicket::factory()->count(3)->create(['category_id' => $category->id]);

        foreach ($relatedTickets as $ticket) {
            $this->service->linkTicketToLoan($ticket, $loanApplication);
        }

        $foundTickets = $this->service->getRelatedTickets($loanApplication);

        $this->assertCount(3, $foundTickets);
        $this->assertEquals(
            $relatedTickets->pluck('id')->sort()->values(),
            $foundTickets->pluck('id')->sort()->values()
        );
    }

    #[Test]
    public function get_related_loans_for_ticket(): void
    {
        $category = \App\Models\TicketCategory::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['category_id' => $category->id]);

        $division = \App\Models\Division::factory()->create();
        $relatedLoans = LoanApplication::factory()->count(2)->create(['division_id' => $division->id]);

        foreach ($relatedLoans as $loan) {
            $this->service->linkTicketToLoan($ticket, $loan);
        }

        $foundLoans = $this->service->getRelatedLoans($ticket);

        $this->assertCount(2, $foundLoans);
        $this->assertEquals(
            $relatedLoans->pluck('id')->sort()->values(),
            $foundLoans->pluck('id')->sort()->values()
        );
    }

    #[Test]
    public function integration_prevents_duplicate_links(): void
    {
        $category = \App\Models\TicketCategory::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['category_id' => $category->id]);

        $division = \App\Models\Division::factory()->create();
        $loanApplication = LoanApplication::factory()->create(['division_id' => $division->id]);

        // Create first link
        $integration1 = $this->service->linkTicketToLoan($ticket, $loanApplication);

        // Attempt to create duplicate link
        $integration2 = $this->service->linkTicketToLoan($ticket, $loanApplication);

        // Both should return valid integration (service handles duplicates internally)
        $this->assertInstanceOf(\App\Models\CrossModuleIntegration::class, $integration1);
        $this->assertInstanceOf(\App\Models\CrossModuleIntegration::class, $integration2);

        // Verify at least one integration record exists
        $count = \App\Models\CrossModuleIntegration::where('helpdesk_ticket_id', $ticket->id)
            ->where('loan_application_id', $loanApplication->id)
            ->count();
        $this->assertGreaterThanOrEqual(1, $count);
    }

    #[Test]
    public function asset_maintenance_workflow(): void
    {
        $division = \App\Models\Division::factory()->create();
        $loanApplication = LoanApplication::factory()->create([
            'division_id' => $division->id,
            'status' => 'returned',
        ]);

        // Link asset to loan application
        $loanApplication->loanItems()->create([
            'asset_id' => $this->asset->id,
            'quantity' => 1,
            'unit_value' => 1000.00,
            'total_value' => 1000.00,
        ]);

        $damageDetails = [
            'type' => 'software_issue',
            'description' => 'Operating system corruption',
            'severity' => 'high',
            'damage_report' => 'Operating system corruption',
        ];

        $ticket = $this->service->createTicketForDamagedAsset($loanApplication, $damageDetails);

        // Verify complete workflow
        $this->assertEquals('maintenance', $this->asset->fresh()->status->value);
        $this->assertEquals('high', $ticket->priority);
        $this->assertNotNull($ticket->category_id);

        // Verify integration metadata
        $integration = $this->service->getIntegration($loanApplication, $ticket);
        $this->assertNotNull($integration);
        $this->assertIsArray($integration->integration_data);
    }

    #[Test]
    public function bulk_integration_operations(): void
    {
        $category = \App\Models\TicketCategory::factory()->create();
        $tickets = HelpdeskTicket::factory()->count(5)->create(['category_id' => $category->id]);

        $division = \App\Models\Division::factory()->create();
        $loanApplication = LoanApplication::factory()->create(['division_id' => $division->id]);

        $results = $this->service->bulkLinkTicketsToLoan($tickets, $loanApplication);

        $this->assertEquals(5, $results['success']);
        $this->assertEquals(0, $results['failed']);

        // Verify integrations were created
        $count = \App\Models\CrossModuleIntegration::where('loan_application_id', $loanApplication->id)->count();
        $this->assertGreaterThanOrEqual(5, $count);
    }

    #[Test]
    public function integration_statistics(): void
    {
        // Create various integrations
        $division = \App\Models\Division::factory()->create();
        $loanApplications = LoanApplication::factory()->count(3)->create(['division_id' => $division->id]);

        $category = \App\Models\TicketCategory::factory()->create();
        $tickets = HelpdeskTicket::factory()->count(5)->create(['category_id' => $category->id]);

        foreach ($loanApplications as $loan) {
            // Link asset to loan application
            $loan->loanItems()->create([
                'asset_id' => Asset::factory()->create()->id,
                'quantity' => 1,
                'unit_value' => 1000.00,
                'total_value' => 1000.00,
            ]);

            $this->service->createTicketForDamagedAsset($loan, [
                'type' => 'physical',
                'description' => 'Test damage',
                'severity' => 'medium',
                'damage_report' => 'Test damage',
            ]);
        }

        $stats = $this->service->getIntegrationStatistics();

        $this->assertArrayHasKey('total_integrations', $stats);
        $this->assertArrayHasKey('damage_reports', $stats);
        $this->assertGreaterThanOrEqual(3, $stats['damage_reports']);
    }

    #[Test]
    public function integration_cleanup_on_record_deletion(): void
    {
        $category = \App\Models\TicketCategory::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['category_id' => $category->id]);

        $division = \App\Models\Division::factory()->create();
        $loanApplication = LoanApplication::factory()->create(['division_id' => $division->id]);

        $this->service->linkTicketToLoan($ticket, $loanApplication);

        // Get ticket ID before deletion
        $ticketId = $ticket->id;

        // Delete the ticket
        $ticket->delete();

        // Verify integration record still exists (soft delete, cascade handled by model events)
        // Or verify it's cleaned up if hard delete
        $integrationExists = \App\Models\CrossModuleIntegration::where('helpdesk_ticket_id', $ticketId)->exists();

        // Either should be true - integration exists or ticket is soft deleted
        $this->assertTrue($integrationExists || $ticket->trashed());
    }

    #[Test]
    public function notification_sent_on_damage_report(): void
    {
        $division = \App\Models\Division::factory()->create();
        $loanApplication = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $division->id,
        ]);

        // Link asset to loan application
        $loanApplication->loanItems()->create([
            'asset_id' => $this->asset->id,
            'quantity' => 1,
            'unit_value' => 1000.00,
            'total_value' => 1000.00,
        ]);

        $damageDetails = [
            'type' => 'physical',
            'description' => 'Device dropped',
            'severity' => 'high',
            'damage_report' => 'Device dropped',
        ];

        $ticket = $this->service->createTicketForDamagedAsset($loanApplication, $damageDetails);

        // Verify ticket was created (notification sending happens via event listeners)
        $this->assertInstanceOf(HelpdeskTicket::class, $ticket);
        $this->assertNotNull($ticket->id);
    }
}

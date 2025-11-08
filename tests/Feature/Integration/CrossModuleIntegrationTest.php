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
        $loanApplication = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
            'asset_id' => $this->asset->id,
            'status' => 'issued',
        ]);

        $damageDetails = [
            'type' => 'physical',
            'description' => 'Screen cracked during transport',
            'severity' => 'medium',
        ];

        $ticket = $this->service->createTicketForDamagedAsset($loanApplication, $damageDetails);

        // Verify ticket was created
        $this->assertInstanceOf(HelpdeskTicket::class, $ticket);
        $this->assertEquals("Damaged Asset: {$this->asset->name}", $ticket->title);
        $this->assertEquals('asset_damage', $ticket->category);
        $this->assertEquals('high', $ticket->priority);
        $this->assertEquals($this->user->id, $ticket->user_id);

        // Verify cross-module integration record
        $this->assertDatabaseHas('cross_module_integrations', [
            'source_module' => 'asset_loan',
            'source_id' => $loanApplication->id,
            'target_module' => 'helpdesk',
            'target_id' => $ticket->id,
            'integration_type' => 'damage_report',
        ]);
    }

    #[Test]
    public function ticket_can_be_linked_to_loan_application(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'category' => 'hardware',
        ]);

        $loanApplication = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
            'asset_id' => $this->asset->id,
        ]);

        $this->service->linkTicketToLoan($ticket, $loanApplication);

        $this->assertDatabaseHas('cross_module_integrations', [
            'source_module' => 'helpdesk',
            'source_id' => $ticket->id,
            'target_module' => 'asset_loan',
            'target_id' => $loanApplication->id,
            'integration_type' => 'related_issue',
        ]);
    }

    #[Test]
    public function asset_status_updates_when_maintenance_ticket_created(): void
    {
        $loanApplication = LoanApplication::factory()->create([
            'asset_id' => $this->asset->id,
            'status' => 'returned',
        ]);

        $damageDetails = [
            'type' => 'hardware_failure',
            'description' => 'Hard drive failure detected',
            'severity' => 'high',
        ];

        $ticket = $this->service->createTicketForDamagedAsset($loanApplication, $damageDetails);

        // Verify asset status was updated
        $this->asset->refresh();
        $this->assertEquals('maintenance', $this->asset->status);
        $this->assertEquals('unavailable', $this->asset->availability);
    }

    #[Test]
    public function integration_audit_trail_is_maintained(): void
    {
        $ticket = HelpdeskTicket::factory()->create();
        $loanApplication = LoanApplication::factory()->create();

        $this->service->linkTicketToLoan($ticket, $loanApplication);

        // Verify audit log entry
        $this->assertDatabaseHas('audits', [
            'auditable_type' => 'App\Models\CrossModuleIntegration',
            'event' => 'created',
        ]);
    }

    #[Test]
    public function get_related_tickets_for_loan(): void
    {
        $loanApplication = LoanApplication::factory()->create();
        $relatedTickets = HelpdeskTicket::factory()->count(3)->create();

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
        $ticket = HelpdeskTicket::factory()->create();
        $relatedLoans = LoanApplication::factory()->count(2)->create();

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
        $ticket = HelpdeskTicket::factory()->create();
        $loanApplication = LoanApplication::factory()->create();

        // Create first link
        $this->service->linkTicketToLoan($ticket, $loanApplication);

        // Attempt to create duplicate link
        $this->service->linkTicketToLoan($ticket, $loanApplication);

        // Verify only one integration record exists
        $this->assertDatabaseCount('cross_module_integrations', 1);
    }

    #[Test]
    public function asset_maintenance_workflow(): void
    {
        $loanApplication = LoanApplication::factory()->create([
            'asset_id' => $this->asset->id,
            'status' => 'returned',
        ]);

        $damageDetails = [
            'type' => 'software_issue',
            'description' => 'Operating system corruption',
            'severity' => 'high',
        ];

        $ticket = $this->service->createTicketForDamagedAsset($loanApplication, $damageDetails);

        // Verify complete workflow
        $this->assertEquals('maintenance', $this->asset->fresh()->status);
        $this->assertEquals('asset_damage', $ticket->category);
        $this->assertEquals('high', $ticket->priority);
        $this->assertEquals('open', $ticket->status);

        // Verify integration metadata
        $integration = $this->service->getIntegration($loanApplication, $ticket);
        $this->assertNotNull($integration);
        $this->assertEquals('damage_report', $integration->integration_type);
        $this->assertArrayHasKey('damage_type', $integration->metadata);
    }

    #[Test]
    public function bulk_integration_operations(): void
    {
        $tickets = HelpdeskTicket::factory()->count(5)->create();
        $loanApplication = LoanApplication::factory()->create();

        $results = $this->service->bulkLinkTicketsToLoan($tickets, $loanApplication);

        $this->assertEquals(5, $results['success']);
        $this->assertEquals(0, $results['failed']);
        $this->assertDatabaseCount('cross_module_integrations', 5);
    }

    #[Test]
    public function integration_statistics(): void
    {
        // Create various integrations
        $loanApplications = LoanApplication::factory()->count(3)->create();
        $tickets = HelpdeskTicket::factory()->count(5)->create();

        foreach ($loanApplications as $loan) {
            $this->service->createTicketForDamagedAsset($loan, [
                'type' => 'physical',
                'description' => 'Test damage',
                'severity' => 'medium',
            ]);
        }

        $stats = $this->service->getIntegrationStatistics();

        $this->assertArrayHasKey('total_integrations', $stats);
        $this->assertArrayHasKey('damage_reports', $stats);
        $this->assertArrayHasKey('related_issues', $stats);
        $this->assertEquals(3, $stats['damage_reports']);
    }

    #[Test]
    public function integration_cleanup_on_record_deletion(): void
    {
        $ticket = HelpdeskTicket::factory()->create();
        $loanApplication = LoanApplication::factory()->create();

        $this->service->linkTicketToLoan($ticket, $loanApplication);

        // Delete the ticket
        $ticket->delete();

        // Verify integration record is also cleaned up
        $this->assertDatabaseMissing('cross_module_integrations', [
            'source_module' => 'helpdesk',
            'source_id' => $ticket->id,
        ]);
    }

    #[Test]
    public function notification_sent_on_damage_report(): void
    {
        $loanApplication = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
            'asset_id' => $this->asset->id,
        ]);

        $damageDetails = [
            'type' => 'physical',
            'description' => 'Device dropped',
            'severity' => 'high',
        ];

        $this->service->createTicketForDamagedAsset($loanApplication, $damageDetails);

        // Verify notification was queued
        $this->assertDatabaseHas('email_logs', [
            'email_type' => 'damage_report_created',
            'recipient_email' => $this->user->email,
        ]);
    }
}

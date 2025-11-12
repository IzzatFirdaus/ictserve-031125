<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use App\Models\TicketCategory;
use App\Services\CrossModuleIntegrationService;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

/**
 * Cross-Module Integration Service Test
 *
 * Comprehensive tests for helpdesk-loan integration and maintenance workflows.
 *
 * @see D03-FR-016.1 Cross-module integration
 * @see D03-FR-003.5 Automatic maintenance ticket creation
 * @see D04 §6.2 Cross-module integration service
 */
class CrossModuleIntegrationServiceTest extends TestCase
{
    use RefreshDatabase;

    private CrossModuleIntegrationService $service;

    private NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationService = \Mockery::mock(NotificationService::class); /** @phpstan-ignore-line */

        $this->service = new CrossModuleIntegrationService(
            $this->notificationService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_creates_maintenance_ticket_for_damaged_asset(): void
    {
        // Arrange
        $asset = Asset::factory()->create([
            'name' => 'Dell Laptop',
            'asset_tag' => 'MOTAC-LAP-001',
            'condition' => AssetCondition::GOOD,
        ]);

        $application = LoanApplication::factory()->create([
            'application_number' => 'LA2025010001',
            'applicant_name' => 'Ahmad bin Ali',
            'applicant_email' => 'ahmad@motac.gov.my',
            'applicant_phone' => '0123456789',
        ]);

        $damageData = [
            'damage_report' => 'Screen cracked during transport',
        ];

        $this->notificationService->shouldReceive('sendMaintenanceNotification');

        // Act
        $ticket = $this->service->createMaintenanceTicket($asset, $application, $damageData);

        // Assert
        $this->assertInstanceOf(HelpdeskTicket::class, $ticket);
        $this->assertStringContainsString('Asset Maintenance Required', $ticket->subject);
        $this->assertStringContainsString('MOTAC-LAP-001', $ticket->subject);
        $this->assertEquals('MAINTENANCE', $ticket->category->code);
        $this->assertEquals('high', $ticket->priority);
        $this->assertEquals($asset->id, $ticket->asset_id);
        $this->assertEquals($application->id, $ticket->related_loan_application_id);
        $this->assertEquals('Ahmad bin Ali', $ticket->guest_name);
        $this->assertEquals('ahmad@motac.gov.my', $ticket->guest_email);

        // Verify asset status updated
        $asset->refresh();
        $this->assertEquals(AssetStatus::MAINTENANCE, $asset->status);
        $this->assertEquals(1, $asset->maintenance_tickets_count);

        // Verify application updated
        $application->refresh();
        $this->assertTrue($application->maintenance_required);
        $this->assertContains($ticket->id, $application->related_helpdesk_tickets);
    }public function test_it_builds_comprehensive_maintenance_description(): void
    {
        // Arrange
        $asset = Asset::factory()->create([
            'name' => 'HP Projector',
            'asset_tag' => 'MOTAC-PROJ-005',
            'brand' => 'HP',
            'model' => 'ProBook 450',
            'condition' => AssetCondition::DAMAGED,
        ]);

        $application = LoanApplication::factory()->create([
            'application_number' => 'LA2025010002',
            'applicant_name' => 'Siti binti Hassan',
            'loan_start_date' => now()->subDays(5),
            'loan_end_date' => now(),
        ]);

        $damageData = [
            'damage_report' => 'Lamp not working, overheating issue',
        ];

        $this->notificationService->shouldReceive('sendMaintenanceNotification')->once();

        // Act
        $ticket = $this->service->createMaintenanceTicket($asset, $application, $damageData);

        // Assert
        $this->assertStringContainsString('LA2025010002', $ticket->description);
        $this->assertStringContainsString('MOTAC-PROJ-005', $ticket->description);
        $this->assertStringContainsString('HP ProBook 450', $ticket->description);
        $this->assertStringContainsString('Lamp not working, overheating issue', $ticket->description);
        $this->assertStringContainsString('Siti binti Hassan', $ticket->description);
    }public function test_it_gets_unified_asset_history_with_loans_and_tickets(): void
    {
        // Arrange
        $asset = Asset::factory()->create();

        // Create loan history
        $application1 = LoanApplication::factory()->create([
            'application_number' => 'LA2025010001',
            'applicant_name' => 'Ahmad bin Ali',
            'status' => LoanStatus::COMPLETED,
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application1->id,
            'asset_id' => $asset->id,
        ]);

        $application2 = LoanApplication::factory()->create([
            'application_number' => 'LA2025010002',
            'applicant_name' => 'Siti binti Hassan',
            'status' => LoanStatus::IN_USE,
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application2->id,
            'asset_id' => $asset->id,
        ]);

        // Create maintenance history
        $ticket1 = HelpdeskTicket::factory()->create([
            'ticket_number' => 'HD2025000001',
            'subject' => 'Screen replacement',
            'asset_id' => $asset->id,
            'status' => 'resolved',
        ]);

        $ticket2 = HelpdeskTicket::factory()->create([
            'ticket_number' => 'HD2025000002',
            'subject' => 'Battery replacement',
            'asset_id' => $asset->id,
            'status' => 'in_progress',
        ]);

        // Act
        $history = $this->service->getUnifiedAssetHistory($asset->id);

        // Assert
        $this->assertCount(4, $history); // 2 loans + 2 tickets

        // Verify loan entries
        $loanEntries = collect($history)->where('type', 'loan');
        $this->assertCount(2, $loanEntries);

        // Verify maintenance entries
        $maintenanceEntries = collect($history)->where('type', 'maintenance');
        $this->assertCount(2, $maintenanceEntries);

        // Verify sorted by date descending (most recent first)
        $this->assertGreaterThanOrEqual($history[1]['date'], $history[0]['date']);
    }public function test_it_checks_if_asset_has_pending_maintenance_tickets(): void
    {
        // Arrange
        $category = TicketCategory::factory()->create(['code' => 'MAINTENANCE']);

        $asset = Asset::factory()->create();

        HelpdeskTicket::factory()->create([
            'asset_id' => $asset->id,
            'category_id' => $category->id,
            'status' => 'in_progress',
        ]);

        // Act
        $hasPending = $this->service->hasPendingMaintenanceTickets($asset->id);

        // Assert
        $this->assertTrue($hasPending);
    }public function test_it_returns_false_when_no_pending_maintenance_tickets(): void
    {
        // Arrange
        $category = TicketCategory::factory()->create(['code' => 'MAINTENANCE']);

        $asset = Asset::factory()->create();

        HelpdeskTicket::factory()->create([
            'asset_id' => $asset->id,
            'category_id' => $category->id,
            'status' => 'resolved',
        ]);

        // Act
        $hasPending = $this->service->hasPendingMaintenanceTickets($asset->id);

        // Assert
        $this->assertFalse($hasPending);
    }public function test_it_gets_asset_maintenance_statistics(): void
    {
        // Arrange
        $category = TicketCategory::factory()->create(['code' => 'MAINTENANCE']);

        $asset = Asset::factory()->create();

        HelpdeskTicket::factory()->create([
            'asset_id' => $asset->id,
            'category_id' => $category->id,
            'status' => 'open',
        ]);

        HelpdeskTicket::factory()->create([
            'asset_id' => $asset->id,
            'category_id' => $category->id,
            'status' => 'in_progress',
        ]);

        HelpdeskTicket::factory()->create([
            'asset_id' => $asset->id,
            'category_id' => $category->id,
            'status' => 'resolved',
            'created_at' => now()->subHours(10),
            'resolved_at' => now()->subHours(2),
        ]);

        HelpdeskTicket::factory()->create([
            'asset_id' => $asset->id,
            'category_id' => $category->id,
            'status' => 'closed',
        ]);

        // Act
        $stats = $this->service->getAssetMaintenanceStats($asset->id);

        // Assert
        $this->assertEquals(4, $stats['total_tickets']);
        $this->assertEquals(2, $stats['open_tickets']); // new + in_progress
        $this->assertEquals(1, $stats['resolved_tickets']);
        $this->assertEquals(1, $stats['closed_tickets']);
        $this->assertNotNull($stats['average_resolution_time']);
        $this->assertNotNull($stats['last_maintenance_date']);
    }public function test_it_syncs_asset_status_to_maintenance_when_open_tickets_exist(): void
    {
        // Arrange
        $category = TicketCategory::factory()->create(['code' => 'MAINTENANCE']);

        $asset = Asset::factory()->create([
            'status' => AssetStatus::AVAILABLE,
        ]);

        HelpdeskTicket::factory()->create([
            'asset_id' => $asset->id,
            'category_id' => $category->id,
            'status' => 'in_progress',
        ]);

        // Act
        $this->service->syncAssetStatus($asset->id);

        // Assert
        $asset->refresh();
        $this->assertEquals(AssetStatus::MAINTENANCE, $asset->status);
    }public function test_it_syncs_asset_status_to_available_when_no_open_tickets(): void
    {
        // Arrange
        $category = TicketCategory::factory()->create(['code' => 'MAINTENANCE']);

        $asset = Asset::factory()->create([
            'status' => AssetStatus::MAINTENANCE,
        ]);

        HelpdeskTicket::factory()->create([
            'asset_id' => $asset->id,
            'category_id' => $category->id,
            'status' => 'resolved',
        ]);

        // Act
        $this->service->syncAssetStatus($asset->id);

        // Assert
        $asset->refresh();
        $this->assertEquals(AssetStatus::AVAILABLE, $asset->status);
    }public function test_it_syncs_asset_status_to_loaned_when_currently_loaned(): void
    {
        // Arrange
        $category = TicketCategory::factory()->create(['code' => 'MAINTENANCE']);

        $asset = Asset::factory()->create([
            'status' => AssetStatus::MAINTENANCE,
        ]);

        // Create active loan
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::IN_USE,
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
        ]);

        // No open maintenance tickets
        HelpdeskTicket::factory()->create([
            'asset_id' => $asset->id,
            'category_id' => $category->id,
            'status' => 'resolved',
        ]);

        // Act
        $this->service->syncAssetStatus($asset->id);

        // Assert
        $asset->refresh();
        $this->assertEquals(AssetStatus::LOANED, $asset->status);
    }public function test_it_schedules_maintenance_for_asset(): void
    {
        // Arrange
        $asset = Asset::factory()->create([
            'name' => 'Dell Laptop',
            'asset_tag' => 'MOTAC-LAP-001',
        ]);

        $maintenanceData = [
            'description' => 'Quarterly preventive maintenance',
            'priority' => 'medium',
            'scheduled_date' => now()->addDays(7),
        ];

        // Act
        $ticket = $this->service->scheduleMaintenance($asset->id, $maintenanceData);

        // Assert
        $this->assertInstanceOf(HelpdeskTicket::class, $ticket);
        $this->assertStringContainsString('Scheduled Maintenance', $ticket->subject);
        $this->assertEquals('MAINTENANCE', $ticket->category->code);
        $this->assertEquals('medium', $ticket->priority);
        $this->assertEquals('open', $ticket->status);
        $this->assertEquals($asset->id, $ticket->asset_id);

        // Verify asset next maintenance date updated
        $asset->refresh();
        $this->assertNotNull($asset->next_maintenance_date);
    }public function test_it_completes_maintenance_ticket_and_updates_asset(): void
    {
        // Arrange
        $asset = Asset::factory()->create([
            'status' => AssetStatus::MAINTENANCE,
            'condition' => AssetCondition::FAIR,
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'asset_id' => $asset->id,
            'status' => 'in_progress',
        ]);

        $completionData = [
            'resolution_notes' => 'Replaced battery and cleaned internals',
            'asset_condition' => AssetCondition::GOOD,
            'next_maintenance_date' => now()->addMonths(6),
        ];

        // Act
        $this->service->completeMaintenanceTicket($ticket->id, $completionData);

        // Assert
        $ticket->refresh();
        $this->assertEquals('resolved', $ticket->status);
        $this->assertNotNull($ticket->resolved_at);
        $this->assertEquals('Replaced battery and cleaned internals', $ticket->resolution_notes);

        $asset->refresh();
        $this->assertEquals(AssetCondition::GOOD, $asset->condition);
        $this->assertNotNull($asset->last_maintenance_date);
        $this->assertNotNull($asset->next_maintenance_date);
    }public function test_it_throws_exception_when_completing_ticket_without_asset(): void
    {
        // Arrange
        $ticket = HelpdeskTicket::factory()->create([
            'asset_id' => null,
            'status' => 'in_progress',
        ]);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('not associated with an asset');

        $this->service->completeMaintenanceTicket($ticket->id, []);
    }public function test_it_gets_comprehensive_asset_lifecycle_report(): void
    {
        // Arrange
        $asset = Asset::factory()->create([
            'asset_tag' => 'MOTAC-LAP-001',
            'name' => 'Dell Laptop',
            'brand' => 'Dell',
            'model' => 'Latitude 5420',
            'status' => AssetStatus::AVAILABLE,
            'condition' => AssetCondition::GOOD,
            'purchase_date' => now()->subYear(),
        ]);

        // Create loan history
        $application = LoanApplication::factory()->create([
            'application_number' => 'LA2025010001',
            'applicant_name' => 'Ahmad bin Ali',
            'status' => LoanStatus::COMPLETED,
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
        ]);

        // Create maintenance history
        HelpdeskTicket::factory()->create([
            'ticket_number' => 'HD2025000001',
            'subject' => 'Battery replacement',
            'asset_id' => $asset->id,
            'status' => 'resolved',
        ]);

        // Act
        $report = $this->service->getAssetLifecycleReport($asset->id);

        // Assert
        $this->assertArrayHasKey('asset', $report);
        $this->assertArrayHasKey('loan_history', $report);
        $this->assertArrayHasKey('maintenance_history', $report);
        $this->assertArrayHasKey('maintenance_statistics', $report);
        $this->assertArrayHasKey('total_loans', $report);
        $this->assertArrayHasKey('total_maintenance_tickets', $report);
        $this->assertArrayHasKey('utilization_rate', $report);

        $this->assertEquals('MOTAC-LAP-001', $report['asset']['asset_tag']);
        $this->assertEquals(1, $report['total_loans']);
        $this->assertEquals(1, $report['total_maintenance_tickets']);
    }public function test_it_triggers_preventive_maintenance_when_due(): void
    {
        // Arrange
        $asset = Asset::factory()->create([
            'next_maintenance_date' => now()->subDays(1), // Overdue
        ]);

        // Act
        $ticket = $this->service->triggerPreventiveMaintenance($asset->id);

        // Assert
        $this->assertInstanceOf(HelpdeskTicket::class, $ticket);
        $this->assertStringContainsString('Preventive maintenance triggered', $ticket->description);
    }public function test_it_triggers_preventive_maintenance_based_on_usage_threshold(): void
    {
        // Arrange
        $asset = Asset::factory()->create([
            'next_maintenance_date' => now()->addMonths(3), // Not due yet
        ]);

        // Create 10 loans to trigger usage threshold
        for ($i = 0; $i < 10; $i++) {
            $application = LoanApplication::factory()->create();
            LoanItem::factory()->create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
            ]);
        }

        // Act
        $ticket = $this->service->triggerPreventiveMaintenance($asset->id);

        // Assert
        $this->assertInstanceOf(HelpdeskTicket::class, $ticket);
        $this->assertStringContainsString('Loan count: 10', $ticket->description);
    }public function test_it_does_not_trigger_preventive_maintenance_when_not_needed(): void
    {
        // Arrange
        $asset = Asset::factory()->create([
            'next_maintenance_date' => now()->addMonths(3), // Not due
        ]);

        // Only 5 loans (below threshold of 10)
        for ($i = 0; $i < 5; $i++) {
            $application = LoanApplication::factory()->create();
            LoanItem::factory()->create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
            ]);
        }

        // Act
        $ticket = $this->service->triggerPreventiveMaintenance($asset->id);

        // Assert
        $this->assertNull($ticket);
    }public function test_it_logs_maintenance_ticket_creation_events(): void
    {
        // Arrange
        Log::spy();

        $asset = Asset::factory()->create([
            'asset_tag' => 'MOTAC-LAP-001',
        ]);

        $application = LoanApplication::factory()->create([
            'application_number' => 'LA2025010001',
        ]);

        $damageData = [
            'damage_report' => 'Screen cracked',
        ];

        $this->notificationService->shouldReceive('sendMaintenanceNotification')->once();

        // Act
        $ticket = $this->service->createMaintenanceTicket($asset, $application, $damageData);

        // Assert
        Log::shouldHaveReceived('info')
            ->with('Maintenance ticket created for damaged asset', Mockery::on(function ($context) use ($ticket, $asset, $application) {
                return $context['ticket_number'] === $ticket->ticket_number
                    && $context['asset_tag'] === $asset->asset_tag
                    && $context['application_number'] === $application->application_number;
            }));
        $this->assertTrue(true);
    }
}


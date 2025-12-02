<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Models\Asset;
use App\Models\CrossModuleIntegration;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\CrossModuleIntegrationService;
use App\Services\Notifications\TicketNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * Unit tests for CrossModuleIntegrationService.
 *
 * Tests cross-module integration between asset loan and helpdesk modules.
 */
#[CoversClass(CrossModuleIntegrationService::class)]
class CrossModuleIntegrationServiceTest extends TestCase
{
    use RefreshDatabase;

    private CrossModuleIntegrationService $service;

    /** @var TicketNotificationService&MockInterface */
    private MockInterface $notificationServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationServiceMock = Mockery::mock(TicketNotificationService::class);
        $this->notificationServiceMock->shouldReceive('sendMaintenanceNotification')->byDefault();

        $this->service = new CrossModuleIntegrationService($this->notificationServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test creating maintenance ticket for damaged asset.
     */
    public function test_create_maintenance_ticket_for_damaged_asset(): void
    {
        $user = User::factory()->create();
        $asset = Asset::factory()->create([
            'name' => 'Test Laptop',
            'asset_tag' => 'ASSET-001',
            'brand' => 'Dell',
            'model' => 'Latitude 5520',
            'status' => AssetStatus::LOANED,
            'condition' => AssetCondition::GOOD,
        ]);

        $application = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'applicant_name' => 'Test User',
            'applicant_email' => 'test@motac.gov.my',
            'status' => LoanStatus::ISSUED,
        ]);

        $damageData = [
            'damage_report' => 'Screen cracked during transport',
        ];

        $ticket = $this->service->createMaintenanceTicket($asset, $application, $damageData);

        $this->assertInstanceOf(HelpdeskTicket::class, $ticket);
        $this->assertStringContainsString('ASSET-001', $ticket->subject);
        $this->assertEquals('high', $ticket->priority);
        $this->assertEquals('open', $ticket->status);
        $this->assertEquals($asset->id, $ticket->asset_id);
        $this->assertEquals($application->id, $ticket->related_loan_application_id);

        // Verify asset status updated
        $asset->refresh();
        $this->assertEquals(AssetStatus::MAINTENANCE, $asset->status);
    }

    /**
     * Test get unified asset history returns combined loan and ticket history.
     */
    public function test_get_unified_asset_history_returns_combined_history(): void
    {
        $user = User::factory()->create();
        $asset = Asset::factory()->create();

        // Create loan application with loan item
        $application = LoanApplication::factory()->create([
            'user_id' => $user->id,
            'applicant_name' => 'Test Borrower',
            'status' => LoanStatus::COMPLETED,
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
        ]);

        // Create helpdesk ticket
        HelpdeskTicket::factory()->create([
            'asset_id' => $asset->id,
            'subject' => 'Maintenance Required',
            'status' => 'resolved',
        ]);

        $history = $this->service->getUnifiedAssetHistory($asset->id);

        $this->assertIsArray($history);
        $this->assertCount(2, $history);

        // Verify both types are present
        $types = array_column($history, 'type');
        $this->assertContains('loan', $types);
        $this->assertContains('maintenance', $types);
    }

    /**
     * Test link ticket to loan creates integration record.
     */
    public function test_link_ticket_to_loan_creates_integration_record(): void
    {
        $user = User::factory()->create();
        $asset = Asset::factory()->create();

        $ticket = HelpdeskTicket::factory()->create([
            'asset_id' => $asset->id,
        ]);

        $application = LoanApplication::factory()->create([
            'user_id' => $user->id,
        ]);

        $integration = $this->service->linkTicketToLoan($ticket, $application);

        $this->assertInstanceOf(CrossModuleIntegration::class, $integration);
        $this->assertEquals($ticket->id, $integration->helpdesk_ticket_id);
        $this->assertEquals($application->id, $integration->loan_application_id);
        $this->assertEquals(CrossModuleIntegration::TYPE_ASSET_TICKET_LINK, $integration->integration_type);
    }

    /**
     * Test has pending maintenance tickets returns true when tickets exist.
     */
    public function test_has_pending_maintenance_tickets_returns_true_when_tickets_exist(): void
    {
        $asset = Asset::factory()->create();

        // Create maintenance category
        $category = TicketCategory::factory()->create([
            'code' => 'MAINTENANCE',
        ]);

        // Create open maintenance ticket
        HelpdeskTicket::factory()->create([
            'asset_id' => $asset->id,
            'category_id' => $category->id,
            'status' => 'open',
        ]);

        $hasPending = $this->service->hasPendingMaintenanceTickets($asset->id);

        $this->assertTrue($hasPending);
    }

    /**
     * Test has pending maintenance tickets returns false when no tickets.
     */
    public function test_has_pending_maintenance_tickets_returns_false_when_no_tickets(): void
    {
        $asset = Asset::factory()->create();

        $hasPending = $this->service->hasPendingMaintenanceTickets($asset->id);

        $this->assertFalse($hasPending);
    }

    /**
     * Test get asset maintenance stats returns correct statistics.
     */
    public function test_get_asset_maintenance_stats_returns_correct_statistics(): void
    {
        $asset = Asset::factory()->create();

        // Create maintenance category
        $category = TicketCategory::factory()->create([
            'code' => 'MAINTENANCE',
        ]);

        // Create various tickets
        HelpdeskTicket::factory()->create([
            'asset_id' => $asset->id,
            'category_id' => $category->id,
            'status' => 'open',
        ]);

        HelpdeskTicket::factory()->create([
            'asset_id' => $asset->id,
            'category_id' => $category->id,
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        $stats = $this->service->getAssetMaintenanceStats($asset->id);

        $this->assertIsArray($stats);
        $this->assertEquals(2, $stats['total_tickets']);
        $this->assertEquals(1, $stats['open_tickets']);
        $this->assertEquals(1, $stats['resolved_tickets']);
    }

    /**
     * Test sync asset status updates to maintenance when tickets exist.
     */
    public function test_sync_asset_status_updates_to_maintenance_when_tickets_exist(): void
    {
        $asset = Asset::factory()->create([
            'status' => AssetStatus::AVAILABLE,
        ]);

        // Create maintenance category
        $category = TicketCategory::factory()->create([
            'code' => 'MAINTENANCE',
        ]);

        // Create open maintenance ticket
        HelpdeskTicket::factory()->create([
            'asset_id' => $asset->id,
            'category_id' => $category->id,
            'status' => 'open',
        ]);

        $this->service->syncAssetStatus($asset->id);

        $asset->refresh();
        $this->assertEquals(AssetStatus::MAINTENANCE, $asset->status);
    }

    /**
     * Test sync asset status updates to available when no tickets.
     */
    public function test_sync_asset_status_updates_to_available_when_no_tickets(): void
    {
        $asset = Asset::factory()->create([
            'status' => AssetStatus::MAINTENANCE,
        ]);

        $this->service->syncAssetStatus($asset->id);

        $asset->refresh();
        $this->assertEquals(AssetStatus::AVAILABLE, $asset->status);
    }

    /**
     * Test schedule maintenance creates ticket with scheduled date.
     */
    public function test_schedule_maintenance_creates_ticket_with_scheduled_date(): void
    {
        $asset = Asset::factory()->create([
            'name' => 'Test Projector',
            'asset_tag' => 'PROJ-001',
        ]);

        $scheduledDate = now()->addDays(7);
        $maintenanceData = [
            'description' => 'Quarterly preventive maintenance',
            'priority' => 'medium',
            'scheduled_date' => $scheduledDate,
        ];

        $ticket = $this->service->scheduleMaintenance($asset->id, $maintenanceData);

        $this->assertInstanceOf(HelpdeskTicket::class, $ticket);
        $this->assertStringContainsString('Scheduled Maintenance', $ticket->subject);
        $this->assertEquals('medium', $ticket->priority);

        // Verify asset next maintenance date updated
        $asset->refresh();
        $this->assertEquals($scheduledDate->toDateString(), $asset->next_maintenance_date->toDateString());
    }

    /**
     * Test get unified analytics returns all module metrics.
     */
    public function test_get_unified_analytics_returns_all_module_metrics(): void
    {
        // Create some test data
        $user = User::factory()->create();
        LoanApplication::factory()->count(3)->create(['user_id' => $user->id]);
        HelpdeskTicket::factory()->count(2)->create();
        Asset::factory()->count(5)->create();

        $analytics = $this->service->getUnifiedAnalytics();

        $this->assertIsArray($analytics);
        $this->assertArrayHasKey('loan_metrics', $analytics);
        $this->assertArrayHasKey('helpdesk_metrics', $analytics);
        $this->assertArrayHasKey('asset_metrics', $analytics);
        $this->assertArrayHasKey('integration_metrics', $analytics);

        $this->assertArrayHasKey('total_applications', $analytics['loan_metrics']);
        $this->assertArrayHasKey('total_tickets', $analytics['helpdesk_metrics']);
        $this->assertArrayHasKey('total_assets', $analytics['asset_metrics']);
    }

    /**
     * Test unified search returns results from all modules.
     */
    public function test_unified_search_returns_results_from_all_modules(): void
    {
        $user = User::factory()->create();

        // Create searchable data
        LoanApplication::factory()->create([
            'user_id' => $user->id,
            'applicant_name' => 'Ahmad Albab',
            'application_number' => 'LA2025001',
        ]);

        HelpdeskTicket::factory()->create([
            'guest_name' => 'Ahmad Albab',
            'ticket_number' => 'HD2025001',
        ]);

        Asset::factory()->create([
            'name' => 'Ahmad Laptop',
            'asset_tag' => 'AHMAD-001',
        ]);

        $results = $this->service->unifiedSearch('Ahmad');

        $this->assertIsArray($results);
        $this->assertArrayHasKey('loan_applications', $results);
        $this->assertArrayHasKey('helpdesk_tickets', $results);
        $this->assertArrayHasKey('assets', $results);

        $this->assertCount(1, $results['loan_applications']);
        $this->assertCount(1, $results['helpdesk_tickets']);
        $this->assertCount(1, $results['assets']);
    }

    /**
     * Test get ticket integrations returns related integrations.
     */
    public function test_get_ticket_integrations_returns_related_integrations(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create();
        $application = LoanApplication::factory()->create(['user_id' => $user->id]);

        CrossModuleIntegration::factory()->create([
            'helpdesk_ticket_id' => $ticket->id,
            'loan_application_id' => $application->id,
        ]);

        $integrations = $this->service->getTicketIntegrations($ticket->id);

        $this->assertCount(1, $integrations);
        $this->assertEquals($ticket->id, $integrations->first()->helpdesk_ticket_id);
    }

    /**
     * Test get loan integrations returns related integrations.
     */
    public function test_get_loan_integrations_returns_related_integrations(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create();
        $application = LoanApplication::factory()->create(['user_id' => $user->id]);

        CrossModuleIntegration::factory()->create([
            'helpdesk_ticket_id' => $ticket->id,
            'loan_application_id' => $application->id,
        ]);

        $integrations = $this->service->getLoanIntegrations($application->id);

        $this->assertCount(1, $integrations);
        $this->assertEquals($application->id, $integrations->first()->loan_application_id);
    }
}

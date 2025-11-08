<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\Asset;
use App\Models\LoanApplication;
use App\Services\AlertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Alert Service Tests
 *
 * @trace D03-FR-013.4 (Configurable Alert System)
 * @trace D03-FR-009.3 (Automated Notifications)
 */
class AlertServiceTest extends TestCase
{
    use RefreshDatabase;

    private AlertService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AlertService::class);
    }

    public function test_detects_overdue_returns(): void
    {
        LoanApplication::factory()->count(3)->create([
            'status' => 'approved',
            'return_by' => now()->subDays(5),
        ]);

        $overdue = $this->service->checkOverdueReturns();

        $this->assertCount(3, $overdue);
    }

    public function test_detects_upcoming_returns(): void
    {
        LoanApplication::factory()->count(2)->create([
            'status' => 'approved',
            'return_by' => now()->addDays(2),
        ]);

        $upcoming = $this->service->checkUpcomingReturns();

        $this->assertCount(2, $upcoming);
    }

    public function test_does_not_include_already_returned_loans(): void
    {
        LoanApplication::factory()->create([
            'status' => 'returned',
            'return_by' => now()->subDays(5),
        ]);

        $overdue = $this->service->checkOverdueReturns();

        $this->assertCount(0, $overdue);
    }

    public function test_detects_pending_approvals(): void
    {
        LoanApplication::factory()->count(4)->create([
            'status' => 'pending',
            'created_at' => now()->subHours(50),
        ]);

        $pending = $this->service->checkPendingApprovals();

        $this->assertCount(4, $pending);
    }

    public function test_detects_low_asset_availability(): void
    {
        Asset::factory()->count(1)->create(['status' => 'available']);
        Asset::factory()->count(5)->create(['status' => 'loaned']);

        $lowStock = $this->service->checkLowAssetAvailability();

        $this->assertNotEmpty($lowStock);
    }

    public function test_does_not_alert_when_sufficient_assets(): void
    {
        Asset::factory()->count(10)->create(['status' => 'available']);

        $lowStock = $this->service->checkLowAssetAvailability();

        $this->assertEmpty($lowStock);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\LoanApplication;
use App\Services\AssetAvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Asset Management Service Tests
 *
 * Tests asset availability checking, reservation, and status management.
 *
 * @see D03-FR-003.1 Asset availability checking
 * @see D03-FR-003.2 Asset reservation
 * Requirements: 3.1, 3.2, 7.2, 16.3
 */
class AssetManagementServiceTest extends TestCase
{
    use RefreshDatabase;

    private AssetAvailabilityService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AssetAvailabilityService;
    }

    #[Test]
    public function it_checks_asset_availability_correctly(): void
    {
        $asset = Asset::factory()->available()->create();

        $isAvailable = $this->service->isAvailable($asset->id);

        $this->assertTrue($isAvailable);
    }

    #[Test]
    public function it_detects_unavailable_assets(): void
    {
        $asset = Asset::factory()->create(['status' => AssetStatus::LOANED]);

        $isAvailable = $this->service->isAvailable($asset->id);

        $this->assertFalse($isAvailable);
    }

    #[Test]
    public function it_checks_availability_for_date_range(): void
    {
        $asset = Asset::factory()->available()->create();
        $startDate = now()->addDays(1);
        $endDate = now()->addDays(3);

        $isAvailable = $this->service->isAvailableForDateRange(
            $asset->id,
            $startDate,
            $endDate
        );

        $this->assertTrue($isAvailable);
    }

    #[Test]
    public function it_detects_conflicts_with_existing_loans(): void
    {
        $asset = Asset::factory()->available()->create();

        LoanApplication::factory()->create([
            'loan_start_date' => now()->addDays(2),
            'loan_end_date' => now()->addDays(5),
            'status' => 'approved',
        ])->loanItems()->create([
            'asset_id' => $asset->id,
            'quantity' => 1,
            'unit_value' => 1000.00,
            'total_value' => 1000.00,
        ]);

        $isAvailable = $this->service->isAvailableForDateRange(
            $asset->id,
            now()->addDays(3),
            now()->addDays(4)
        );

        $this->assertFalse($isAvailable);
    }

    #[Test]
    public function it_gets_available_assets_by_category(): void
    {
        $category = AssetCategory::factory()->create();
        Asset::factory()->count(3)->available()->create(['category_id' => $category->id]);
        Asset::factory()->count(2)->create([
            'category_id' => $category->id,
            'status' => AssetStatus::MAINTENANCE,
        ]);

        $availableAssets = $this->service->getAvailableAssetsByCategory($category->id);

        $this->assertCount(3, $availableAssets);
    }

    #[Test]
    public function it_reserves_asset_successfully(): void
    {
        $asset = Asset::factory()->available()->create();
        $loanApplication = LoanApplication::factory()->create();

        $result = $this->service->reserveAsset($asset->id, $loanApplication->id);

        $this->assertTrue($result);
        $this->assertEquals(AssetStatus::RESERVED, $asset->fresh()->status);
    }

    #[Test]
    public function it_releases_asset_reservation(): void
    {
        $asset = Asset::factory()->create(['status' => AssetStatus::RESERVED]);
        $loanApplication = LoanApplication::factory()->create();

        $result = $this->service->releaseReservation($asset->id, $loanApplication->id);

        $this->assertTrue($result);
        $this->assertEquals(AssetStatus::AVAILABLE, $asset->fresh()->status);
    }

    #[Test]
    public function it_calculates_asset_utilization_rate(): void
    {
        $asset = Asset::factory()->create();

        // Create loan history (5 loans of 5 days each = 25 days utilized out of 30 days = 83%)
        LoanApplication::factory()->count(5)->create([
            'loan_start_date' => now()->subDays(29), // Within the 30-day window
            'loan_end_date' => now()->subDays(24),    // 5-day loan duration
            'status' => 'completed',
        ])->each(function ($loan) use ($asset) {
            $loan->loanItems()->create([
                'asset_id' => $asset->id,
                'quantity' => 1,
                'unit_value' => 1000.00,
                'total_value' => 1000.00,
            ]);
        });

        $utilizationRate = $this->service->calculateUtilizationRate($asset->id, 30);

        $this->assertGreaterThan(0, $utilizationRate);
        $this->assertLessThanOrEqual(100, $utilizationRate);
    }

    #[Test]
    public function it_gets_asset_loan_history(): void
    {
        $asset = Asset::factory()->create();

        LoanApplication::factory()->count(3)->create()->each(function ($loan) use ($asset) {
            $loan->loanItems()->create([
                'asset_id' => $asset->id,
                'quantity' => 1,
                'unit_value' => 1000.00,
                'total_value' => 1000.00,
            ]);
        });

        $history = $this->service->getAssetLoanHistory($asset->id);

        $this->assertCount(3, $history);
    }

    #[Test]
    public function it_identifies_assets_requiring_maintenance(): void
    {
        Asset::factory()->count(2)->create(['status' => AssetStatus::MAINTENANCE]);
        Asset::factory()->count(3)->available()->create();

        $maintenanceAssets = $this->service->getAssetsRequiringMaintenance();

        $this->assertCount(2, $maintenanceAssets);
    }

    #[Test]
    public function it_checks_multiple_assets_availability(): void
    {
        $asset1 = Asset::factory()->available()->create();
        $asset2 = Asset::factory()->available()->create();
        $asset3 = Asset::factory()->create(['status' => AssetStatus::MAINTENANCE]);

        $assetIds = [$asset1->id, $asset2->id, $asset3->id];
        $availability = $this->service->checkMultipleAssetsAvailability($assetIds);

        $this->assertTrue($availability[$asset1->id]);
        $this->assertTrue($availability[$asset2->id]);
        $this->assertFalse($availability[$asset3->id]);
    }
}

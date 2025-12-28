<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\LoanApplication;
use App\Services\AssetAvailabilityService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Asset Management Service Tests
 *
 * Tests asset availability checking using AssetAvailabilityService.
 *
 * @see D03-FR-003.1 Asset availability checking
 * @see D03-FR-003.2 Asset reservation
 * Requirements: 3.1, 3.2, 7.2, 16.3
 */
class AssetManagementServiceTest extends TestCase
{
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
        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        $result = $this->service->checkAssetAvailability($asset->id, $startDate, $endDate);

        $this->assertTrue($result['available']);
    }

    #[Test]
    public function it_detects_unavailable_assets(): void
    {
        $asset = Asset::factory()->create(['status' => AssetStatus::LOANED]);
        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        $result = $this->service->checkAssetAvailability($asset->id, $startDate, $endDate);

        $this->assertFalse($result['available']);
    }

    #[Test]
    public function it_checks_availability_for_date_range(): void
    {
        $asset = Asset::factory()->available()->create();
        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        $availability = $this->service->checkAvailability(
            [$asset->id],
            $startDate,
            $endDate
        );

        $this->assertTrue($availability[$asset->id]);
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

        $startDate = now()->addDays(3)->format('Y-m-d');
        $endDate = now()->addDays(4)->format('Y-m-d');

        $availability = $this->service->checkAvailability([$asset->id], $startDate, $endDate);

        $this->assertFalse($availability[$asset->id]);
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

        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        $availableCount = $this->service->getAvailableAssetCount($category->id, $startDate, $endDate);

        $this->assertEquals(3, $availableCount);
    }

    #[Test]
    public function it_finds_available_asset_in_category(): void
    {
        $category = AssetCategory::factory()->create();
        $asset = Asset::factory()->available()->create(['category_id' => $category->id]);

        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        $foundAsset = $this->service->findAvailableAsset($category->id, $startDate, $endDate);

        $this->assertNotNull($foundAsset);
        $this->assertEquals($asset->id, $foundAsset->id);
    }

    #[Test]
    public function it_returns_null_when_no_available_asset(): void
    {
        $category = AssetCategory::factory()->create();
        Asset::factory()->create([
            'category_id' => $category->id,
            'status' => AssetStatus::MAINTENANCE,
        ]);

        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        $foundAsset = $this->service->findAvailableAsset($category->id, $startDate, $endDate);

        $this->assertNull($foundAsset);
    }

    #[Test]
    public function it_checks_category_availability(): void
    {
        $category = AssetCategory::factory()->create();
        Asset::factory()->count(5)->available()->create(['category_id' => $category->id]);

        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        $result = $this->service->checkCategoryAvailability($category->id, $startDate, $endDate, 3);

        $this->assertTrue($result['available']);
        $this->assertEquals(5, $result['count']);
    }

    #[Test]
    public function it_detects_insufficient_category_availability(): void
    {
        $category = AssetCategory::factory()->create();
        Asset::factory()->count(2)->available()->create(['category_id' => $category->id]);

        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        $result = $this->service->checkCategoryAvailability($category->id, $startDate, $endDate, 5);

        $this->assertFalse($result['available']);
        $this->assertEquals(2, $result['count']);
    }

    #[Test]
    public function it_gets_alternative_assets(): void
    {
        $category = AssetCategory::factory()->create();
        Asset::factory()->count(3)->available()->create(['category_id' => $category->id]);

        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        $alternatives = $this->service->getAlternativeAssets($category->id, $startDate, $endDate, 5);

        $this->assertCount(3, $alternatives);
    }

    #[Test]
    public function it_checks_multiple_assets_availability(): void
    {
        $asset1 = Asset::factory()->available()->create();
        $asset2 = Asset::factory()->available()->create();
        $asset3 = Asset::factory()->create(['status' => AssetStatus::MAINTENANCE]);

        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        $assetIds = [$asset1->id, $asset2->id, $asset3->id];
        $availability = $this->service->checkAvailability($assetIds, $startDate, $endDate);

        $this->assertTrue($availability[$asset1->id]);
        $this->assertTrue($availability[$asset2->id]);
        $this->assertFalse($availability[$asset3->id]);
    }
}

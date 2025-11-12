<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use App\Services\AssetAvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Asset Availability Service Test
 *
 * Comprehensive tests for asset availability checking and performance optimization.
 *
 * @see D03-FR-003.4 Real-time availability checking
 * @see D03-FR-017.4 Asset availability checker
 * @see D04 §2.5 Asset availability service
 */
class AssetAvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    private AssetAvailabilityService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AssetAvailabilityService;
    }

    public function test_it_checks_asset_availability_for_date_range(): void
    {
        // Arrange
        $asset = Asset::factory()->available()->create();
        $startDate = now()->addDays(5)->format('Y-m-d');
        $endDate = now()->addDays(10)->format('Y-m-d');

        // Act
        $availability = $this->service->checkAvailability(
            [$asset->id],
            $startDate,
            $endDate
        );

        // Assert
        $this->assertIsArray($availability);
        $this->assertArrayHasKey($asset->id, $availability);
        $this->assertTrue($availability[$asset->id]);
    }

    public function test_it_detects_unavailable_asset_with_conflicting_loan(): void
    {
        // Arrange
        $asset = Asset::factory()->available()->create();

        // Create conflicting loan
        $application = LoanApplication::factory()->create([
            'loan_start_date' => now()->addDays(5),
            'loan_end_date' => now()->addDays(10),
            'status' => LoanStatus::APPROVED,
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
        ]);

        $startDate = now()->addDays(7)->format('Y-m-d');
        $endDate = now()->addDays(12)->format('Y-m-d');

        // Act
        $availability = $this->service->checkAvailability(
            [$asset->id],
            $startDate,
            $endDate
        );

        // Assert
        $this->assertFalse($availability[$asset->id]);
    }

    public function test_it_detects_unavailable_asset_when_asset_status_is_not_available(): void
    {
        // Arrange
        $asset = Asset::factory()->create([
            'status' => AssetStatus::MAINTENANCE,
        ]);

        $startDate = now()->addDays(5)->format('Y-m-d');
        $endDate = now()->addDays(10)->format('Y-m-d');

        // Act
        $availability = $this->service->checkAvailability(
            [$asset->id],
            $startDate,
            $endDate
        );

        // Assert
        $this->assertFalse($availability[$asset->id]);
    }

    public function test_it_excludes_specific_application_from_availability_check(): void
    {
        // Arrange
        $asset = Asset::factory()->available()->create();

        $application = LoanApplication::factory()->create([
            'loan_start_date' => now()->addDays(5),
            'loan_end_date' => now()->addDays(10),
            'status' => LoanStatus::APPROVED,
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
        ]);

        $startDate = now()->addDays(7)->format('Y-m-d');
        $endDate = now()->addDays(12)->format('Y-m-d');

        // Act - Exclude the conflicting application
        $availability = $this->service->checkAvailability(
            [$asset->id],
            $startDate,
            $endDate,
            $application->id
        );

        // Assert - Should be available when excluding the conflicting application
        $this->assertTrue($availability[$asset->id]);
    }

    public function test_it_checks_multiple_assets_availability(): void
    {
        // Arrange
        $asset1 = Asset::factory()->available()->create();
        $asset2 = Asset::factory()->available()->create();
        $asset3 = Asset::factory()->available()->create();

        // Create conflicting loan for asset2
        $application = LoanApplication::factory()->create([
            'loan_start_date' => now()->addDays(5),
            'loan_end_date' => now()->addDays(10),
            'status' => LoanStatus::ISSUED,
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset2->id,
        ]);

        $startDate = now()->addDays(7)->format('Y-m-d');
        $endDate = now()->addDays(12)->format('Y-m-d');

        // Act
        $availability = $this->service->checkAvailability(
            [$asset1->id, $asset2->id, $asset3->id],
            $startDate,
            $endDate
        );

        // Assert
        $this->assertTrue($availability[$asset1->id]);
        $this->assertFalse($availability[$asset2->id]);
        $this->assertTrue($availability[$asset3->id]);
    }

    public function test_it_detects_conflict_when_requested_period_overlaps_start_of_existing_loan(): void
    {
        // Arrange
        $asset = Asset::factory()->available()->create();

        $application = LoanApplication::factory()->create([
            'loan_start_date' => now()->addDays(10),
            'loan_end_date' => now()->addDays(15),
            'status' => LoanStatus::APPROVED,
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
        ]);

        // Request overlaps start of existing loan
        $startDate = now()->addDays(8)->format('Y-m-d');
        $endDate = now()->addDays(12)->format('Y-m-d');

        // Act
        $availability = $this->service->checkAvailability(
            [$asset->id],
            $startDate,
            $endDate
        );

        // Assert
        $this->assertFalse($availability[$asset->id]);
    }

    public function test_it_detects_conflict_when_requested_period_overlaps_end_of_existing_loan(): void
    {
        // Arrange
        $asset = Asset::factory()->available()->create();

        $application = LoanApplication::factory()->create([
            'loan_start_date' => now()->addDays(5),
            'loan_end_date' => now()->addDays(10),
            'status' => LoanStatus::IN_USE,
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
        ]);

        // Request overlaps end of existing loan
        $startDate = now()->addDays(8)->format('Y-m-d');
        $endDate = now()->addDays(15)->format('Y-m-d');

        // Act
        $availability = $this->service->checkAvailability(
            [$asset->id],
            $startDate,
            $endDate
        );

        // Assert
        $this->assertFalse($availability[$asset->id]);
    }

    public function test_it_detects_conflict_when_requested_period_encompasses_existing_loan(): void
    {
        // Arrange
        $asset = Asset::factory()->available()->create();

        $application = LoanApplication::factory()->create([
            'loan_start_date' => now()->addDays(8),
            'loan_end_date' => now()->addDays(10),
            'status' => LoanStatus::APPROVED,
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
        ]);

        // Request encompasses existing loan
        $startDate = now()->addDays(5)->format('Y-m-d');
        $endDate = now()->addDays(15)->format('Y-m-d');

        // Act
        $availability = $this->service->checkAvailability(
            [$asset->id],
            $startDate,
            $endDate
        );

        // Assert
        $this->assertFalse($availability[$asset->id]);
    }

    public function test_it_allows_availability_when_requested_period_is_before_existing_loan(): void
    {
        // Arrange
        $asset = Asset::factory()->available()->create();

        $application = LoanApplication::factory()->create([
            'loan_start_date' => now()->addDays(15),
            'loan_end_date' => now()->addDays(20),
            'status' => LoanStatus::APPROVED,
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
        ]);

        // Request is before existing loan
        $startDate = now()->addDays(5)->format('Y-m-d');
        $endDate = now()->addDays(10)->format('Y-m-d');

        // Act
        $availability = $this->service->checkAvailability(
            [$asset->id],
            $startDate,
            $endDate
        );

        // Assert
        $this->assertTrue($availability[$asset->id]);
    }

    public function test_it_allows_availability_when_requested_period_is_after_existing_loan(): void
    {
        // Arrange
        $asset = Asset::factory()->available()->create();

        $application = LoanApplication::factory()->create([
            'loan_start_date' => now()->addDays(5),
            'loan_end_date' => now()->addDays(10),
            'status' => LoanStatus::APPROVED,
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
        ]);

        // Request is after existing loan
        $startDate = now()->addDays(15)->format('Y-m-d');
        $endDate = now()->addDays(20)->format('Y-m-d');

        // Act
        $availability = $this->service->checkAvailability(
            [$asset->id],
            $startDate,
            $endDate
        );

        // Assert
        $this->assertTrue($availability[$asset->id]);
    }

    public function test_it_gets_availability_calendar_with_booked_dates(): void
    {
        // Arrange
        $asset = Asset::factory()->available()->create();

        $application1 = LoanApplication::factory()->create([
            'application_number' => 'LA2025010001',
            'applicant_name' => 'Ahmad bin Ali',
            'loan_start_date' => now()->addDays(5),
            'loan_end_date' => now()->addDays(10),
            'status' => LoanStatus::APPROVED,
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application1->id,
            'asset_id' => $asset->id,
        ]);

        $application2 = LoanApplication::factory()->create([
            'application_number' => 'LA2025010002',
            'applicant_name' => 'Siti binti Hassan',
            'loan_start_date' => now()->addDays(15),
            'loan_end_date' => now()->addDays(20),
            'status' => LoanStatus::ISSUED,
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application2->id,
            'asset_id' => $asset->id,
        ]);

        $startDate = now()->format('Y-m-d');
        $endDate = now()->addDays(30)->format('Y-m-d');

        // Act
        $calendar = $this->service->getAvailabilityCalendar($asset->id, $startDate, $endDate);

        // Assert
        $this->assertIsArray($calendar);
        $this->assertEquals($asset->id, $calendar['asset_id']);
        $this->assertFalse($calendar['available']);
        $this->assertCount(2, $calendar['booked_dates']);

        $this->assertEquals('LA2025010001', $calendar['booked_dates'][0]['application_number']);
        $this->assertEquals('Ahmad bin Ali', $calendar['booked_dates'][0]['applicant_name']);

        $this->assertEquals('LA2025010002', $calendar['booked_dates'][1]['application_number']);
        $this->assertEquals('Siti binti Hassan', $calendar['booked_dates'][1]['applicant_name']);
    }

    public function test_it_caches_availability_calendar_for_performance(): void
    {
        // Arrange
        Cache::flush();
        $asset = Asset::factory()->available()->create();

        $startDate = now()->format('Y-m-d');
        $endDate = now()->addDays(30)->format('Y-m-d');

        // Act - First call should cache
        $calendar1 = $this->service->getAvailabilityCalendar($asset->id, $startDate, $endDate);

        // Act - Second call should use cache
        $calendar2 = $this->service->getAvailabilityCalendar($asset->id, $startDate, $endDate);

        // Assert
        $this->assertEquals($calendar1, $calendar2);

        $cacheKey = "asset_calendar_{$asset->id}_{$startDate}_{$endDate}";
        $this->assertTrue(Cache::has($cacheKey));
    }

    public function test_it_gets_alternative_available_assets_from_same_category(): void
    {
        // Arrange
        $category = AssetCategory::factory()->create();

        $asset1 = Asset::factory()->available()->create(['category_id' => $category->id]);
        $asset2 = Asset::factory()->available()->create(['category_id' => $category->id]);
        $asset3 = Asset::factory()->available()->create(['category_id' => $category->id]);
        $asset4 = Asset::factory()->available()->create(['category_id' => $category->id]);

        // Create conflicting loan for asset2
        $application = LoanApplication::factory()->create([
            'loan_start_date' => now()->addDays(5),
            'loan_end_date' => now()->addDays(10),
            'status' => LoanStatus::APPROVED,
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset2->id,
        ]);

        $startDate = now()->addDays(7)->format('Y-m-d');
        $endDate = now()->addDays(12)->format('Y-m-d');

        // Act
        $alternatives = $this->service->getAlternativeAssets(
            $category->id,
            $startDate,
            $endDate,
            5
        );

        // Assert
        $this->assertCount(3, $alternatives); // asset1, asset3, asset4 (asset2 is unavailable)
        $this->assertTrue($alternatives->contains($asset1));
        $this->assertFalse($alternatives->contains($asset2));
        $this->assertTrue($alternatives->contains($asset3));
        $this->assertTrue($alternatives->contains($asset4));
    }

    public function test_it_limits_alternative_assets_to_specified_limit(): void
    {
        // Arrange
        $category = AssetCategory::factory()->create();

        Asset::factory()->available()->count(10)->create(['category_id' => $category->id]);

        $startDate = now()->addDays(5)->format('Y-m-d');
        $endDate = now()->addDays(10)->format('Y-m-d');

        // Act
        $alternatives = $this->service->getAlternativeAssets(
            $category->id,
            $startDate,
            $endDate,
            3
        );

        // Assert
        $this->assertCount(3, $alternatives);
    }

    public function test_it_clears_availability_cache_for_asset(): void
    {
        // Arrange
        Cache::flush();
        $asset = Asset::factory()->available()->create();

        $startDate = now()->format('Y-m-d');
        $endDate = now()->addDays(30)->format('Y-m-d');

        // Cache the calendar
        $this->service->getAvailabilityCalendar($asset->id, $startDate, $endDate);

        $cacheKey = "asset_calendar_{$asset->id}_{$startDate}_{$endDate}";
        $this->assertTrue(Cache::has($cacheKey));

        // Act
        $this->service->clearAvailabilityCache($asset->id);

        // Assert
        $this->assertFalse(Cache::has($cacheKey));
    }

    public function test_it_handles_non_existent_asset_gracefully(): void
    {
        // Arrange
        $nonExistentAssetId = 999999;
        $startDate = now()->addDays(5)->format('Y-m-d');
        $endDate = now()->addDays(10)->format('Y-m-d');

        // Act
        $availability = $this->service->checkAvailability(
            [$nonExistentAssetId],
            $startDate,
            $endDate
        );

        // Assert
        $this->assertFalse($availability[$nonExistentAssetId]);
    }

    public function test_it_only_considers_active_loan_statuses_for_conflicts(): void
    {
        // Arrange
        $asset = Asset::factory()->available()->create();

        // Create loans with various statuses
        $submittedApp = LoanApplication::factory()->create([
            'loan_start_date' => now()->addDays(5),
            'loan_end_date' => now()->addDays(10),
            'status' => LoanStatus::SUBMITTED, // Not active
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $submittedApp->id,
            'asset_id' => $asset->id,
        ]);

        $rejectedApp = LoanApplication::factory()->create([
            'loan_start_date' => now()->addDays(5),
            'loan_end_date' => now()->addDays(10),
            'status' => LoanStatus::REJECTED, // Not active
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $rejectedApp->id,
            'asset_id' => $asset->id,
        ]);

        $startDate = now()->addDays(7)->format('Y-m-d');
        $endDate = now()->addDays(12)->format('Y-m-d');

        // Act
        $availability = $this->service->checkAvailability(
            [$asset->id],
            $startDate,
            $endDate
        );

        // Assert - Should be available since only non-active loans exist
        $this->assertTrue($availability[$asset->id]);
    }
}



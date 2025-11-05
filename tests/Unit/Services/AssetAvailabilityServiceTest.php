<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use App\Services\AssetAvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Asset Availability Service Tests
 *
 * Tests real-time asset availability checking and performance optimization.
 *
 * @see D03-FR-003.4 Real-time availability checking
 * @see D03-FR-017.4 Asset availability checker
 * Requirements: 2.3, 9.2, 16.1, 7.2
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

    #[Test]
    public function it_returns_available_for_unbooked_asset(): void
    {
        // Arrange
        $asset = Asset::factory()->available()->create();
        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        // Act
        $availability = $this->service->checkAvailability(
            [$asset->id],
            $startDate,
            $endDate
        );

        // Assert
        $this->assertTrue($availability[$asset->id]);
    }

    #[Test]
    public function it_returns_unavailable_for_booked_asset(): void
    {
        // Arrange
        $asset = Asset::factory()->available()->create();
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::APPROVED,
            'loan_start_date' => now()->addDays(2),
            'loan_end_date' => now()->addDays(5),
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
        ]);

        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        // Act
        $availability = $this->service->checkAvailability(
            [$asset->id],
            $startDate,
            $endDate
        );

        // Assert
        $this->assertFalse($availability[$asset->id]);
    }

    #[Test]
    public function it_returns_unavailable_for_maintenance_asset(): void
    {
        // Arrange
        $asset = Asset::factory()->create([
            'status' => AssetStatus::MAINTENANCE,
        ]);

        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        // Act
        $availability = $this->service->checkAvailability(
            [$asset->id],
            $startDate,
            $endDate
        );

        // Assert
        $this->assertFalse($availability[$asset->id]);
    }

    #[Test]
    public function it_excludes_specific_application_from_availability_check(): void
    {
        // Arrange
        $asset = Asset::factory()->available()->create();
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::APPROVED,
            'loan_start_date' => now()->addDays(2),
            'loan_end_date' => now()->addDays(5),
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
        ]);

        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        // Act - Exclude the existing application
        $availability = $this->service->checkAvailability(
            [$asset->id],
            $startDate,
            $endDate,
            $application->id
        );

        // Assert - Should be available when excluding the conflicting application
        $this->assertTrue($availability[$asset->id]);
    }

    #[Test]
    public function it_detects_overlapping_date_ranges(): void
    {
        // Arrange
        $asset = Asset::factory()->available()->create();

        // Existing booking: Days 5-10
        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::ISSUED,
            'loan_start_date' => now()->addDays(5),
            'loan_end_date' => now()->addDays(10),
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $asset->id,
        ]);

        // Test various overlapping scenarios
        $scenarios = [
            // Overlaps at start
            ['start' => now()->addDays(3), 'end' => now()->addDays(6), 'expected' => false],
            // Overlaps at end
            ['start' => now()->addDays(9), 'end' => now()->addDays(12), 'expected' => false],
            // Completely within
            ['start' => now()->addDays(6), 'end' => now()->addDays(8), 'expected' => false],
            // Completely encompasses
            ['start' => now()->addDays(4), 'end' => now()->addDays(11), 'expected' => false],
            // Before booking
            ['start' => now()->addDays(1), 'end' => now()->addDays(4), 'expected' => true],
            // After booking
            ['start' => now()->addDays(11), 'end' => now()->addDays(15), 'expected' => true],
        ];

        foreach ($scenarios as $index => $scenario) {
            // Act
            $availability = $this->service->checkAvailability(
                [$asset->id],
                $scenario['start']->format('Y-m-d'),
                $scenario['end']->format('Y-m-d')
            );

            // Assert
            $this->assertEquals(
                $scenario['expected'],
                $availability[$asset->id],
                "Scenario {$index} failed: {$scenario['start']->format('Y-m-d')} to {$scenario['end']->format('Y-m-d')}"
            );
        }
    }

    #[Test]
    public function it_checks_multiple_assets_simultaneously(): void
    {
        // Arrange
        $availableAsset = Asset::factory()->available()->create();
        $bookedAsset = Asset::factory()->available()->create();
        $maintenanceAsset = Asset::factory()->create(['status' => AssetStatus::MAINTENANCE]);

        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::APPROVED,
            'loan_start_date' => now()->addDays(2),
            'loan_end_date' => now()->addDays(5),
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $bookedAsset->id,
        ]);

        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        // Act
        $availability = $this->service->checkAvailability(
            [$availableAsset->id, $bookedAsset->id, $maintenanceAsset->id],
            $startDate,
            $endDate
        );

        // Assert
        $this->assertTrue($availability[$availableAsset->id]);
        $this->assertFalse($availability[$bookedAsset->id]);
        $this->assertFalse($availability[$maintenanceAsset->id]);
    }

    #[Test]
    public function it_generates_availability_calendar_with_booked_dates(): void
    {
        // Arrange
        $asset = Asset::factory()->available()->create();

        $app1 = LoanApplication::factory()->create([
            'status' => LoanStatus::ISSUED,
            'loan_start_date' => now()->addDays(2),
            'loan_end_date' => now()->addDays(5),
            'applicant_name' => 'John Doe',
            'application_number' => 'LA2025010001',
        ]);

        $app2 = LoanApplication::factory()->create([
            'status' => LoanStatus::APPROVED,
            'loan_start_date' => now()->addDays(10),
            'loan_end_date' => now()->addDays(12),
            'applicant_name' => 'Jane Smith',
            'application_number' => 'LA2025010002',
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $app1->id,
            'asset_id' => $asset->id,
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $app2->id,
            'asset_id' => $asset->id,
        ]);

        $startDate = now()->format('Y-m-d');
        $endDate = now()->addDays(15)->format('Y-m-d');

        // Act
        $calendar = $this->service->getAvailabilityCalendar($asset->id, $startDate, $endDate);

        // Assert
        $this->assertEquals($asset->id, $calendar['asset_id']);
        $this->assertFalse($calendar['available']);
        $this->assertCount(2, $calendar['booked_dates']);
        $this->assertEquals('LA2025010001', $calendar['booked_dates'][0]['application_number']);
        $this->assertEquals('John Doe', $calendar['booked_dates'][0]['applicant_name']);
    }

    #[Test]
    public function it_caches_availability_calendar_for_performance(): void
    {
        // Arrange
        $asset = Asset::factory()->available()->create();
        $startDate = now()->format('Y-m-d');
        $endDate = now()->addDays(30)->format('Y-m-d');

        Cache::shouldReceive('remember')
            ->once()
            ->with(
                "asset_calendar_{$asset->id}_{$startDate}_{$endDate}",
                300,
                \Mockery::type('Closure')
            )
            ->andReturn([
                'asset_id' => $asset->id,
                'booked_dates' => [],
                'available' => true,
            ]);

        // Act
        $calendar = $this->service->getAvailabilityCalendar($asset->id, $startDate, $endDate);

        // Assert
        $this->assertEquals($asset->id, $calendar['asset_id']);
        $this->assertTrue($calendar['available']);
    }

    #[Test]
    public function it_finds_alternative_available_assets_in_same_category(): void
    {
        // Arrange
        $category = AssetCategory::factory()->create();

        $availableAsset1 = Asset::factory()->create([
            'category_id' => $category->id,
            'status' => AssetStatus::AVAILABLE,
        ]);

        $availableAsset2 = Asset::factory()->create([
            'category_id' => $category->id,
            'status' => AssetStatus::AVAILABLE,
        ]);

        $bookedAsset = Asset::factory()->create([
            'category_id' => $category->id,
            'status' => AssetStatus::AVAILABLE,
        ]);

        $application = LoanApplication::factory()->create([
            'status' => LoanStatus::ISSUED,
            'loan_start_date' => now()->addDays(2),
            'loan_end_date' => now()->addDays(5),
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $application->id,
            'asset_id' => $bookedAsset->id,
        ]);

        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        // Act
        $alternatives = $this->service->getAlternativeAssets(
            $category->id,
            $startDate,
            $endDate,
            5
        );

        // Assert
        $this->assertCount(2, $alternatives);
        $this->assertTrue($alternatives->contains($availableAsset1));
        $this->assertTrue($alternatives->contains($availableAsset2));
        $this->assertFalse($alternatives->contains($bookedAsset));
    }

    #[Test]
    public function it_limits_alternative_assets_to_specified_count(): void
    {
        // Arrange
        $category = AssetCategory::factory()->create();

        // Create 10 available assets
        Asset::factory()->count(10)->create([
            'category_id' => $category->id,
            'status' => AssetStatus::AVAILABLE,
        ]);

        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

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

    #[Test]
    public function it_only_considers_active_loan_statuses_for_availability(): void
    {
        // Arrange
        $asset = Asset::factory()->available()->create();

        // Create applications with various statuses
        $activeStatuses = [
            LoanStatus::APPROVED,
            LoanStatus::READY_ISSUANCE,
            LoanStatus::ISSUED,
            LoanStatus::IN_USE,
        ];

        $inactiveStatuses = [
            LoanStatus::SUBMITTED,
            LoanStatus::UNDER_REVIEW,
            LoanStatus::REJECTED,
            LoanStatus::RETURNED,
            LoanStatus::COMPLETED,
        ];

        // Create active loan
        $activeApp = LoanApplication::factory()->create([
            'status' => $activeStatuses[0],
            'loan_start_date' => now()->addDays(2),
            'loan_end_date' => now()->addDays(5),
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $activeApp->id,
            'asset_id' => $asset->id,
        ]);

        // Create inactive loan (should not affect availability)
        $inactiveApp = LoanApplication::factory()->create([
            'status' => $inactiveStatuses[0],
            'loan_start_date' => now()->addDays(2),
            'loan_end_date' => now()->addDays(5),
        ]);

        LoanItem::factory()->create([
            'loan_application_id' => $inactiveApp->id,
            'asset_id' => $asset->id,
        ]);

        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        // Act
        $availability = $this->service->checkAvailability(
            [$asset->id],
            $startDate,
            $endDate
        );

        // Assert - Should be unavailable due to active loan only
        $this->assertFalse($availability[$asset->id]);
    }

    #[Test]
    public function it_clears_availability_cache_for_asset(): void
    {
        // Arrange
        $asset = Asset::factory()->available()->create();

        Cache::shouldReceive('forget')
            ->once()
            ->with("asset_calendar_{$asset->id}_*");

        // Act
        $this->service->clearAvailabilityCache($asset->id);

        // Assert - Mock expectation verified
        $this->assertTrue(true);
    }

    #[Test]
    public function it_handles_non_existent_asset_gracefully(): void
    {
        // Arrange
        $nonExistentAssetId = 999999;
        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        // Act
        $availability = $this->service->checkAvailability(
            [$nonExistentAssetId],
            $startDate,
            $endDate
        );

        // Assert
        $this->assertFalse($availability[$nonExistentAssetId]);
    }

    #[Test]
    public function it_performs_availability_check_within_performance_target(): void
    {
        // Arrange - Performance test for 7.2 requirement
        $category = AssetCategory::factory()->create();
        $assets = Asset::factory()->count(50)->create([
            'category_id' => $category->id,
            'status' => AssetStatus::AVAILABLE,
        ]);

        // Create some bookings
        foreach ($assets->take(10) as $asset) {
            $application = LoanApplication::factory()->create([
                'status' => LoanStatus::ISSUED,
                'loan_start_date' => now()->addDays(2),
                'loan_end_date' => now()->addDays(5),
            ]);

            LoanItem::factory()->create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
            ]);
        }

        $assetIds = $assets->pluck('id')->toArray();
        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        // Act - Measure performance
        $startTime = microtime(true);
        $availability = $this->service->checkAvailability($assetIds, $startDate, $endDate);
        $endTime = microtime(true);

        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        // Assert - Should complete within 500ms for 50 assets (performance target)
        $this->assertLessThan(500, $executionTime, 'Availability check exceeded performance target');
        $this->assertCount(50, $availability);
    }
}

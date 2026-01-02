<?php

declare(strict_types=1);

namespace Tests\Feature\Filament\Resources;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Filament\Resources\Assets\Pages\ListAssets;
use App\Models\Asset;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Task 35.3: Unit test for needs_maintenance filter
 *
 * Tests that the needs_maintenance filter correctly returns assets that:
 * - Have status = 'maintenance' OR
 * - Have condition = 'damaged' OR
 * - Have next_maintenance_date within 30 days
 *
 * And excludes assets that don't match any of these criteria.
 *
 * @trace Requirements 35.3, 35.4
 */
class AssetNeedsMaintenanceFilterTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    #[Test]
    public function needs_maintenance_filter_returns_assets_with_maintenance_status(): void
    {
        // Create assets with maintenance status (should be included)
        $maintenanceAssets = Asset::factory()->count(2)->create([
            'status' => AssetStatus::MAINTENANCE,
            'condition' => AssetCondition::GOOD,
            'next_maintenance_date' => null,
        ]);

        // Create assets with available status (should be excluded)
        $availableAssets = Asset::factory()->count(2)->create([
            'status' => AssetStatus::AVAILABLE,
            'condition' => AssetCondition::GOOD,
            'next_maintenance_date' => null,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListAssets::class)
            ->call('loadTable')
            ->filterTable('needs_maintenance', true)
            ->assertCanSeeTableRecords($maintenanceAssets)
            ->assertCanNotSeeTableRecords($availableAssets);
    }

    #[Test]
    public function needs_maintenance_filter_returns_assets_with_damaged_condition(): void
    {
        // Create assets with damaged condition (should be included)
        $damagedAssets = Asset::factory()->count(2)->create([
            'status' => AssetStatus::AVAILABLE,
            'condition' => AssetCondition::DAMAGED,
            'next_maintenance_date' => null,
        ]);

        // Create assets with good condition (should be excluded)
        $goodAssets = Asset::factory()->count(2)->create([
            'status' => AssetStatus::AVAILABLE,
            'condition' => AssetCondition::GOOD,
            'next_maintenance_date' => null,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListAssets::class)
            ->call('loadTable')
            ->filterTable('needs_maintenance', true)
            ->assertCanSeeTableRecords($damagedAssets)
            ->assertCanNotSeeTableRecords($goodAssets);
    }

    #[Test]
    public function needs_maintenance_filter_returns_assets_with_upcoming_maintenance_date(): void
    {
        // Create assets with maintenance due within 30 days (should be included)
        $upcomingMaintenanceAssets = Asset::factory()->count(2)->create([
            'status' => AssetStatus::AVAILABLE,
            'condition' => AssetCondition::GOOD,
            'next_maintenance_date' => now()->addDays(15),
        ]);

        // Create assets with maintenance due beyond 30 days (should be excluded)
        $futureMaintenanceAssets = Asset::factory()->count(2)->create([
            'status' => AssetStatus::AVAILABLE,
            'condition' => AssetCondition::GOOD,
            'next_maintenance_date' => now()->addDays(60),
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListAssets::class)
            ->call('loadTable')
            ->filterTable('needs_maintenance', true)
            ->assertCanSeeTableRecords($upcomingMaintenanceAssets)
            ->assertCanNotSeeTableRecords($futureMaintenanceAssets);
    }

    #[Test]
    public function needs_maintenance_filter_returns_assets_with_overdue_maintenance_date(): void
    {
        // Create assets with overdue maintenance (should be included)
        $overdueAssets = Asset::factory()->count(2)->create([
            'status' => AssetStatus::AVAILABLE,
            'condition' => AssetCondition::GOOD,
            'next_maintenance_date' => now()->subDays(10),
        ]);

        // Create assets with no maintenance date (should be excluded)
        $noMaintenanceAssets = Asset::factory()->count(2)->create([
            'status' => AssetStatus::AVAILABLE,
            'condition' => AssetCondition::GOOD,
            'next_maintenance_date' => null,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListAssets::class)
            ->call('loadTable')
            ->filterTable('needs_maintenance', true)
            ->assertCanSeeTableRecords($overdueAssets)
            ->assertCanNotSeeTableRecords($noMaintenanceAssets);
    }

    #[Test]
    public function needs_maintenance_filter_excludes_assets_without_matching_criteria(): void
    {
        // Create assets that should NOT be included:
        // - Available status
        // - Good condition
        // - No maintenance date or maintenance date > 30 days
        $excludedAssets = Asset::factory()->count(3)->create([
            'status' => AssetStatus::AVAILABLE,
            'condition' => AssetCondition::GOOD,
            'next_maintenance_date' => null,
        ]);

        $excludedWithFutureDate = Asset::factory()->count(2)->create([
            'status' => AssetStatus::AVAILABLE,
            'condition' => AssetCondition::EXCELLENT,
            'next_maintenance_date' => now()->addDays(45),
        ]);

        // Create one asset that SHOULD be included
        $includedAsset = Asset::factory()->create([
            'status' => AssetStatus::MAINTENANCE,
            'condition' => AssetCondition::GOOD,
            'next_maintenance_date' => null,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListAssets::class)
            ->call('loadTable')
            ->filterTable('needs_maintenance', true)
            ->assertCanSeeTableRecords([$includedAsset])
            ->assertCanNotSeeTableRecords($excludedAssets)
            ->assertCanNotSeeTableRecords($excludedWithFutureDate);
    }

    #[Test]
    public function needs_maintenance_filter_uses_proper_query_grouping(): void
    {
        // This test verifies the fix for the OR precedence bug (Requirements 35.1, 35.2)
        // The filter should use: (status = 'maintenance' OR condition = 'damaged' OR (date condition))
        // NOT: status = 'maintenance' OR condition = 'damaged' OR date condition (without grouping)

        // Create an asset that only matches the date condition
        // If query grouping is wrong, this might incorrectly include/exclude assets
        $dateOnlyAsset = Asset::factory()->create([
            'status' => AssetStatus::LOANED, // Not maintenance
            'condition' => AssetCondition::FAIR, // Not damaged
            'next_maintenance_date' => now()->addDays(10), // Within 30 days
        ]);

        // Create an asset that matches status condition
        $statusAsset = Asset::factory()->create([
            'status' => AssetStatus::MAINTENANCE,
            'condition' => AssetCondition::GOOD,
            'next_maintenance_date' => now()->addDays(90), // Beyond 30 days
        ]);

        // Create an asset that matches condition
        $conditionAsset = Asset::factory()->create([
            'status' => AssetStatus::AVAILABLE,
            'condition' => AssetCondition::DAMAGED,
            'next_maintenance_date' => null,
        ]);

        // Create an asset that matches none
        $noMatchAsset = Asset::factory()->create([
            'status' => AssetStatus::AVAILABLE,
            'condition' => AssetCondition::EXCELLENT,
            'next_maintenance_date' => now()->addDays(60),
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListAssets::class)
            ->call('loadTable')
            ->filterTable('needs_maintenance', true)
            ->assertCanSeeTableRecords([$dateOnlyAsset, $statusAsset, $conditionAsset])
            ->assertCanNotSeeTableRecords([$noMatchAsset]);
    }

    #[Test]
    public function needs_maintenance_filter_handles_boundary_date_correctly(): void
    {
        // Test the exact 30-day boundary
        $exactlyAt30Days = Asset::factory()->create([
            'status' => AssetStatus::AVAILABLE,
            'condition' => AssetCondition::GOOD,
            'next_maintenance_date' => now()->addDays(30),
        ]);

        $justBeyond30Days = Asset::factory()->create([
            'status' => AssetStatus::AVAILABLE,
            'condition' => AssetCondition::GOOD,
            'next_maintenance_date' => now()->addDays(31),
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListAssets::class)
            ->call('loadTable')
            ->filterTable('needs_maintenance', true)
            ->assertCanSeeTableRecords([$exactlyAt30Days])
            ->assertCanNotSeeTableRecords([$justBeyond30Days]);
    }
}

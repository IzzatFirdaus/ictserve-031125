<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Widgets;

use App\Enums\AssetStatus;
use App\Models\Asset;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Asset Availability Widget
 *
 * Displays current asset availability and status for loan management.
 *
 * @trace Requirements 3.1, 3.3, 3.4, 8.1
 */
class AssetAvailabilityWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Available assets
        $availableAssets = Asset::where('status', AssetStatus::AVAILABLE)->count();

        // Loaned assets
        $loanedAssets = Asset::where('status', AssetStatus::LOANED)->count();

        // Assets in maintenance
        $maintenanceAssets = Asset::whereIn('status', [
            AssetStatus::MAINTENANCE,
            AssetStatus::DAMAGED,
        ])->count();

        // Total assets
        $totalAssets = Asset::count();

        // Calculate availability percentage
        $availabilityRate = $totalAssets > 0
            ? round(($availableAssets / $totalAssets) * 100, 1)
            : 0;

        return [
            Stat::make(__('Available Assets'), $availableAssets)
                ->description(__('Ready for loan'))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart($this->getAvailabilityTrend()),

            Stat::make(__('Loaned Assets'), $loanedAssets)
                ->description(__('Currently in use'))
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('info')
                ->chart($this->getLoanedTrend()),

            Stat::make(__('Maintenance'), $maintenanceAssets)
                ->description(__('Under repair/maintenance'))
                ->descriptionIcon('heroicon-o-wrench-screwdriver')
                ->color($maintenanceAssets > 5 ? 'warning' : 'gray')
                ->chart($this->getMaintenanceTrend()),

            Stat::make(__('Availability Rate'), $availabilityRate.'%')
                ->description(__('Assets ready vs total'))
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color($availabilityRate > 70 ? 'success' : ($availabilityRate > 50 ? 'warning' : 'danger')),
        ];
    }

    /**
     * Get 7-day availability trend
     */
    private function getAvailabilityTrend(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Asset::where('status', AssetStatus::AVAILABLE)
                ->whereDate('updated_at', '<=', $date)
                ->count();
            $data[] = $count;
        }

        return $data;
    }

    /**
     * Get 7-day loaned trend
     */
    private function getLoanedTrend(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Asset::where('status', AssetStatus::LOANED)
                ->whereDate('updated_at', '<=', $date)
                ->count();
            $data[] = $count;
        }

        return $data;
    }

    /**
     * Get 7-day maintenance trend
     */
    private function getMaintenanceTrend(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Asset::whereIn('status', [AssetStatus::MAINTENANCE, AssetStatus::DAMAGED])
                ->whereDate('updated_at', '<=', $date)
                ->count();
            $data[] = $count;
        }

        return $data;
    }
}

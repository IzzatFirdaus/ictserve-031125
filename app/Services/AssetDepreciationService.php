<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AssetDepreciationService
{
    /**
     * Calculate straight-line depreciation for an asset
     */
    public function calculateDepreciation(Asset $asset): ?float
    {
        if (! $asset->purchase_price || ! $asset->purchase_date) {
            return null;
        }

        $yearsElapsed = now()->diffInYears($asset->purchase_date);
        
        if ($yearsElapsed >= $asset->useful_life_years) {
            return $asset->purchase_price; // Fully depreciated
        }

        $annualDepreciation = $asset->purchase_price / $asset->useful_life_years;
        $totalDepreciation = $annualDepreciation * $yearsElapsed;

        return round($totalDepreciation, 2);
    }

    /**
     * Update current value and accumulated depreciation for an asset
     */
    public function updateAssetValue(Asset $asset): void
    {
        $depreciation = $this->calculateDepreciation($asset);

        if ($depreciation === null) {
            return;
        }

        $currentValue = max(0, $asset->purchase_price - $depreciation);

        $asset->update([
            'accumulated_depreciation' => $depreciation,
            'current_value' => $currentValue,
            'last_depreciation_calculation' => now(),
        ]);
    }

    /**
     * Batch update all assets with depreciation calculations
     */
    public function updateAllAssetValues(): int
    {
        $assets = Asset::whereNotNull('purchase_price')
            ->whereNotNull('purchase_date')
            ->get();

        $updated = 0;

        DB::transaction(function () use ($assets, &$updated) {
            foreach ($assets as $asset) {
                $this->updateAssetValue($asset);
                $updated++;
            }
        });

        return $updated;
    }

    /**
     * Generate depreciation report for all assets
     *
     * @return Collection<int, array{asset: Asset, depreciation: float, current_value: float}>
     */
    public function generateDepreciationReport(): Collection
    {
        return Asset::whereNotNull('purchase_price')
            ->whereNotNull('purchase_date')
            ->with(['category'])
            ->get()
            ->map(function (Asset $asset) {
                $depreciation = $this->calculateDepreciation($asset);
                $currentValue = $depreciation !== null
                    ? max(0, $asset->purchase_price - $depreciation)
                    : null;

                return [
                    'asset' => $asset,
                    'purchase_price' => $asset->purchase_price,
                    'depreciation' => $depreciation,
                    'current_value' => $currentValue,
                    'depreciation_percentage' => $depreciation && $asset->purchase_price
                        ? round(($depreciation / $asset->purchase_price) * 100, 2)
                        : 0,
                ];
            });
    }
}

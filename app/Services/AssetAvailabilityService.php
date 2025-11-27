<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetCategory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Asset Availability Service
 *
 * Handles real-time asset availability checking for loan applications.
 *
 * @see D03-FR-042 (Asset Loan Application)
 * @see D04 §5.2 (Loan Module Design)
 *
 * @version 1.0.0
 */
class AssetAvailabilityService
{
    /**
     * Cache TTL in seconds (5 minutes)
     */
    private const CACHE_TTL = 300;

    /**
     * Check availability of assets by category for a date range
     *
     * @param  int  $categoryId  Asset category ID
     * @param  string  $startDate  Loan start date (Y-m-d)
     * @param  string  $endDate  Loan end date (Y-m-d)
     * @param  int  $quantity  Requested quantity
     * @return array{available: bool, count: int, message: string}
     */
    public function checkCategoryAvailability(
        int $categoryId,
        string $startDate,
        string $endDate,
        int $quantity = 1
    ): array {
        $cacheKey = "asset_availability:{$categoryId}:{$startDate}:{$endDate}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($categoryId, $startDate, $endDate, $quantity) {
            $availableCount = $this->getAvailableAssetCount($categoryId, $startDate, $endDate);

            return [
                'available' => $availableCount >= $quantity,
                'count' => $availableCount,
                'message' => $availableCount >= $quantity
                    ? __('loan.availability.available', ['count' => $availableCount])
                    : __('loan.availability.insufficient', ['requested' => $quantity, 'available' => $availableCount]),
            ];
        });
    }

    /**
     * Get count of available assets for a category in a date range
     */
    public function getAvailableAssetCount(int $categoryId, string $startDate, string $endDate): int
    {
        // Get total assets in category that are operational
        $totalAssets = Asset::where('category_id', $categoryId)
            ->where('status', 'available')
            ->where('condition', '!=', 'damaged')
            ->count();

        // Get assets already booked for overlapping dates
        $bookedAssetIds = $this->getBookedAssetIds($categoryId, $startDate, $endDate);

        return max(0, $totalAssets - count($bookedAssetIds));
    }

    /**
     * Get IDs of assets that are booked during the specified date range
     */
    private function getBookedAssetIds(int $categoryId, string $startDate, string $endDate): array
    {
        return DB::table('loan_items')
            ->join('loan_applications', 'loan_items.loan_application_id', '=', 'loan_applications.id')
            ->join('assets', 'loan_items.asset_id', '=', 'assets.id')
            ->where('assets.category_id', $categoryId)
            ->whereIn('loan_applications.status', ['submitted', 'approved', 'in_use'])
            ->where(function ($query) use ($startDate, $endDate) {
                // Check for date overlap
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->where('loan_applications.loan_start_date', '<=', $endDate)
                        ->where('loan_applications.loan_end_date', '>=', $startDate);
                });
            })
            ->pluck('loan_items.asset_id')
            ->unique()
            ->toArray();
    }

    /**
     * Get availability summary for all categories
     *
     * @param  string  $startDate  Loan start date (Y-m-d)
     * @param  string  $endDate  Loan end date (Y-m-d)
     * @return Collection<int, array{id: int, name: string, available: int, total: int}>
     */
    public function getCategoryAvailabilitySummary(string $startDate, string $endDate): Collection
    {
        $categories = AssetCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return $categories->map(function ($category) use ($startDate, $endDate) {
            $totalAssets = Asset::where('category_id', $category->id)
                ->where('status', 'available')
                ->where('condition', '!=', 'damaged')
                ->count();

            $availableCount = $this->getAvailableAssetCount($category->id, $startDate, $endDate);

            return [
                'id' => $category->id,
                'name' => $category->name,
                'available' => $availableCount,
                'total' => $totalAssets,
                'percentage' => $totalAssets > 0 ? round(($availableCount / $totalAssets) * 100) : 0,
            ];
        });
    }

    /**
     * Find an available asset for a category and date range
     *
     * @param  int  $categoryId  Asset category ID
     * @param  string  $startDate  Loan start date (Y-m-d)
     * @param  string  $endDate  Loan end date (Y-m-d)
     */
    public function findAvailableAsset(int $categoryId, string $startDate, string $endDate): ?Asset
    {
        $bookedAssetIds = $this->getBookedAssetIds($categoryId, $startDate, $endDate);

        return Asset::where('category_id', $categoryId)
            ->where('status', 'available')
            ->where('condition', '!=', 'damaged')
            ->whereNotIn('id', $bookedAssetIds)
            ->first();
    }

    /**
     * Check if a specific asset is available for a date range
     *
     * @param  int  $assetId  Asset ID
     * @param  string  $startDate  Loan start date (Y-m-d)
     * @param  string  $endDate  Loan end date (Y-m-d)
     * @param  int|null  $excludeApplicationId  Exclude this application from conflict check
     * @return array{available: bool, conflicts: array}
     */
    public function checkAssetAvailability(
        int $assetId,
        string $startDate,
        string $endDate,
        ?int $excludeApplicationId = null
    ): array {
        $asset = Asset::find($assetId);

        if (! $asset) {
            return [
                'available' => false,
                'conflicts' => [],
                'message' => __('loan.availability.asset_not_found'),
            ];
        }

        // Check asset status
        if ($asset->status !== 'available' || $asset->condition === 'damaged') {
            return [
                'available' => false,
                'conflicts' => [],
                'message' => __('loan.availability.asset_unavailable'),
            ];
        }

        // Check for booking conflicts
        $conflicts = $this->getAssetConflicts($assetId, $startDate, $endDate, $excludeApplicationId);

        return [
            'available' => empty($conflicts),
            'conflicts' => $conflicts,
            'message' => empty($conflicts)
                ? __('loan.availability.asset_available')
                : __('loan.availability.asset_booked'),
        ];
    }

    /**
     * Get conflicting bookings for an asset
     */
    private function getAssetConflicts(
        int $assetId,
        string $startDate,
        string $endDate,
        ?int $excludeApplicationId = null
    ): array {
        $query = DB::table('loan_items')
            ->join('loan_applications', 'loan_items.loan_application_id', '=', 'loan_applications.id')
            ->where('loan_items.asset_id', $assetId)
            ->whereIn('loan_applications.status', ['submitted', 'approved', 'in_use'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where('loan_applications.loan_start_date', '<=', $endDate)
                    ->where('loan_applications.loan_end_date', '>=', $startDate);
            });

        if ($excludeApplicationId) {
            $query->where('loan_applications.id', '!=', $excludeApplicationId);
        }

        return $query->select([
            'loan_applications.application_number',
            'loan_applications.loan_start_date',
            'loan_applications.loan_end_date',
            'loan_applications.status',
        ])->get()->toArray();
    }

    /**
     * Clear availability cache for a category
     */
    public function clearCategoryCache(int $categoryId): void
    {
        // Clear all cache keys for this category
        // In production, use Redis SCAN or tagged caching
        Cache::forget("asset_availability:{$categoryId}:*");
    }

    /**
     * Get calendar data for asset availability visualization
     *
     * @param  int  $categoryId  Asset category ID
     * @param  string  $month  Month in Y-m format
     * @return array<string, array{available: int, total: int}>
     */
    public function getCalendarAvailability(int $categoryId, string $month): array
    {
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        $calendar = [];
        $currentDate = $startOfMonth->copy();

        while ($currentDate <= $endOfMonth) {
            $dateStr = $currentDate->format('Y-m-d');
            $availableCount = $this->getAvailableAssetCount(
                $categoryId,
                $dateStr,
                $dateStr
            );

            $totalAssets = Asset::where('category_id', $categoryId)
                ->where('status', 'available')
                ->where('condition', '!=', 'damaged')
                ->count();

            $calendar[$dateStr] = [
                'available' => $availableCount,
                'total' => $totalAssets,
                'percentage' => $totalAssets > 0 ? round(($availableCount / $totalAssets) * 100) : 0,
            ];

            $currentDate->addDay();
        }

        return $calendar;
    }
}

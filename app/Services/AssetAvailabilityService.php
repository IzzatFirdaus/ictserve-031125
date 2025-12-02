<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
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
            ->where('status', AssetStatus::AVAILABLE)
            ->where('condition', '!=', AssetCondition::DAMAGED)
            ->count();

        // Get assets already booked for overlapping dates
        $bookedAssetIds = $this->getBookedAssetIds($categoryId, $startDate, $endDate);

        return max(0, $totalAssets - count($bookedAssetIds));
    }

    /**
     * Get active loan statuses that block asset availability
     *
     * @return array<string>
     */
    private function getActiveStatusValues(): array
    {
        return [
            \App\Enums\LoanStatus::APPROVED->value,
            \App\Enums\LoanStatus::READY_ISSUANCE->value,
            \App\Enums\LoanStatus::ISSUED->value,
            \App\Enums\LoanStatus::IN_USE->value,
        ];
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
            ->whereIn('loan_applications.status', $this->getActiveStatusValues())
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
                ->where('status', AssetStatus::AVAILABLE)
                ->where('condition', '!=', AssetCondition::DAMAGED)
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
            ->where('status', AssetStatus::AVAILABLE)
            ->where('condition', '!=', AssetCondition::DAMAGED)
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

        // Check asset status using enum comparison
        if ($asset->status !== AssetStatus::AVAILABLE || $asset->condition === AssetCondition::DAMAGED) {
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
            ->whereIn('loan_applications.status', $this->getActiveStatusValues())
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
                ->where('status', AssetStatus::AVAILABLE)
                ->where('condition', '!=', AssetCondition::DAMAGED)
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

    /**
     * Check availability of multiple assets for a date range
     *
     * Returns an array keyed by asset ID with boolean availability status.
     *
     * @param  array<int>  $assetIds  Array of asset IDs to check
     * @param  string  $startDate  Loan start date (Y-m-d)
     * @param  string  $endDate  Loan end date (Y-m-d)
     * @param  int|null  $excludeApplicationId  Exclude this application from conflict check
     * @return array<int, bool> Array keyed by asset ID with availability status
     */
    public function checkAvailability(
        array $assetIds,
        string $startDate,
        string $endDate,
        ?int $excludeApplicationId = null
    ): array {
        $result = [];

        foreach ($assetIds as $assetId) {
            $availability = $this->checkAssetAvailability($assetId, $startDate, $endDate, $excludeApplicationId);
            $result[$assetId] = $availability['available'];
        }

        return $result;
    }

    /**
     * Get availability calendar for a specific asset
     *
     * @param  int  $assetId  Asset ID
     * @param  string  $startDate  Start date (Y-m-d)
     * @param  string  $endDate  End date (Y-m-d)
     * @return array{asset_id: int, available: bool, booked_dates: array}
     */
    public function getAvailabilityCalendar(int $assetId, string $startDate, string $endDate): array
    {
        $cacheKey = "asset_calendar_{$assetId}_{$startDate}_{$endDate}";
        $keysKey = "asset_calendar_keys_{$assetId}";

        // Track cache keys for this asset
        $existingKeys = Cache::get($keysKey, []);
        if (! \in_array($cacheKey, $existingKeys, true)) {
            $existingKeys[] = $cacheKey;
            Cache::put($keysKey, $existingKeys, 86400); // 24 hours
        }

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($assetId, $startDate, $endDate) {
            $asset = Asset::find($assetId);

            if (! $asset) {
                return [
                    'asset_id' => $assetId,
                    'available' => false,
                    'booked_dates' => [],
                ];
            }

            // Get all booked dates for this asset with application info
            $bookedDates = $this->getBookedDatesForAsset($assetId, $startDate, $endDate);

            // Check overall availability
            $availability = $this->checkAssetAvailability($assetId, $startDate, $endDate);

            return [
                'asset_id' => $assetId,
                'available' => $availability['available'],
                'booked_dates' => $bookedDates,
            ];
        });
    }

    /**
     * Get booked dates for a specific asset with application info
     *
     * @param  int  $assetId  Asset ID
     * @param  string  $startDate  Start date (Y-m-d)
     * @param  string  $endDate  End date (Y-m-d)
     * @return array<array{start_date: string, end_date: string, application_number: string, applicant_name: string}>
     */
    private function getBookedDatesForAsset(int $assetId, string $startDate, string $endDate): array
    {
        $bookings = DB::table('loan_items')
            ->join('loan_applications', 'loan_items.loan_application_id', '=', 'loan_applications.id')
            ->where('loan_items.asset_id', $assetId)
            ->whereIn('loan_applications.status', $this->getActiveStatusValues())
            ->where('loan_applications.loan_start_date', '<=', $endDate)
            ->where('loan_applications.loan_end_date', '>=', $startDate)
            ->select([
                'loan_applications.loan_start_date',
                'loan_applications.loan_end_date',
                'loan_applications.application_number',
                'loan_applications.applicant_name',
            ])
            ->get();

        return $bookings->map(function ($booking) {
            return [
                'start_date' => $booking->loan_start_date,
                'end_date' => $booking->loan_end_date,
                'application_number' => $booking->application_number,
                'applicant_name' => $booking->applicant_name,
            ];
        })->toArray();
    }

    /**
     * Get alternative available assets from the same category
     *
     * @param  int  $categoryId  Category ID
     * @param  string  $startDate  Start date (Y-m-d)
     * @param  string  $endDate  End date (Y-m-d)
     * @param  int  $limit  Maximum number of alternatives to return
     * @return Collection<int, Asset>
     */
    public function getAlternativeAssets(int $categoryId, string $startDate, string $endDate, int $limit = 5): Collection
    {
        $bookedAssetIds = $this->getBookedAssetIds($categoryId, $startDate, $endDate);

        return Asset::where('category_id', $categoryId)
            ->where('status', AssetStatus::AVAILABLE)
            ->where('condition', '!=', AssetCondition::DAMAGED)
            ->whereNotIn('id', $bookedAssetIds)
            ->limit($limit)
            ->get();
    }

    /**
     * Clear availability cache for a specific asset
     *
     * @param  int  $assetId  Asset ID
     */
    public function clearAvailabilityCache(int $assetId): void
    {
        // Get all cache keys for this asset and clear them
        $keysKey = "asset_calendar_keys_{$assetId}";
        $keys = Cache::get($keysKey, []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget($keysKey);
    }
}

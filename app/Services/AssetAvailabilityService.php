<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Models\Asset;
use App\Models\LoanApplication;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Asset Availability Service
 *
 * Manages real-time asset availability checking and booking calendar integration.
 *
 * @see D03-FR-003.4 Real-time availability checking
 * @see D03-FR-017.4 Asset availability checker
 * @see D04 §2.5 Asset availability service
 */
class AssetAvailabilityService
{
    /**
     * Check availability of assets for given date range
     *
     * @param array $assetIds Array of asset IDs to check
     * @param string $startDate Start date (Y-m-d format)
     * @param string $endDate End date (Y-m-d format)
     * @param int|null $excludeApplicationId Exclude specific application from check
     * @return array Asset availability status [asset_id => bool]
     */
    public function checkAvailability(
        array $assetIds,
        string $startDate,
        string $endDate,
        ?int $excludeApplicationId = null
    ): array {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $availability = [];

        foreach ($assetIds as $assetId) {
            $availability[$assetId] = $this->isAssetAvailable(
                $assetId,
                $start,
                $end,
                $excludeApplicationId
            );
        }

        return $availability;
    }

    /**
     * Check if specific asset is available for date range
     *
     * @param int $assetId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int|null $excludeApplicationId
     * @return bool
     */
    private function isAssetAvailable(
        int $assetId,
        Carbon $startDate,
        Carbon $endDate,
        ?int $excludeApplicationId = null
    ): bool {
        // Check asset status
        $asset = Asset::find($assetId);

        if (! $asset || ! $asset->isAvailable()) {
            return false;
        }

        // Check for conflicting loan applications
        $conflictingLoans = LoanApplication::whereHas('loanItems', function ($query) use ($assetId) {
            $query->where('asset_id', $assetId);
        })
            ->whereIn('status', [
                LoanStatus::APPROVED,
                LoanStatus::READY_ISSUANCE,
                LoanStatus::ISSUED,
                LoanStatus::IN_USE,
            ])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('loan_start_date', [$startDate, $endDate])
                    ->orWhereBetween('loan_end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('loan_start_date', '<=', $startDate)
                            ->where('loan_end_date', '>=', $endDate);
                    });
            });

        if ($excludeApplicationId) {
            $conflictingLoans->where('id', '!=', $excludeApplicationId);
        }

        return $conflictingLoans->count() === 0;
    }

    /**
     * Get availability calendar for asset
     *
     * @param int $assetId
     * @param string $startDate
     * @param string $endDate
     * @return array Calendar data with booked dates
     */
    public function getAvailabilityCalendar(int $assetId, string $startDate, string $endDate): array
    {
        $cacheKey = "asset_calendar_{$assetId}_{$startDate}_{$endDate}";

        return Cache::remember($cacheKey, 300, function () use ($assetId, $startDate, $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            $bookedDates = [];

            // Get all active loans for this asset in date range
            $loans = LoanApplication::whereHas('loanItems', function ($query) use ($assetId) {
                $query->where('asset_id', $assetId);
            })
                ->whereIn('status', [
                    LoanStatus::APPROVED,
                    LoanStatus::READY_ISSUANCE,
                    LoanStatus::ISSUED,
                    LoanStatus::IN_USE,
                ])
                ->where(function ($query) use ($start, $end) {
                    $query->whereBetween('loan_start_date', [$start, $end])
                        ->orWhereBetween('loan_end_date', [$start, $end])
                        ->orWhere(function ($q) use ($start, $end) {
                            $q->where('loan_start_date', '<=', $start)
                                ->where('loan_end_date', '>=', $end);
                        });
                })
                ->get();

            foreach ($loans as $loan) {
                $loanStart = Carbon::parse($loan->loan_start_date);
                $loanEnd = Carbon::parse($loan->loan_end_date);

                $bookedDates[] = [
                    'start' => $loanStart->format('Y-m-d'),
                    'end' => $loanEnd->format('Y-m-d'),
                    'application_number' => $loan->application_number,
                    'applicant_name' => $loan->applicant_name,
                ];
            }

            return [
                'asset_id' => $assetId,
                'booked_dates' => $bookedDates,
                'available' => count($bookedDates) === 0,
            ];
        });
    }

    /**
     * Get alternative available assets for same category
     *
     * @param int $categoryId
     * @param string $startDate
     * @param string $endDate
     * @param int $limit
     * @return Collection
     */
    public function getAlternativeAssets(
        int $categoryId,
        string $startDate,
        string $endDate,
        int $limit = 5
    ): Collection {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $assets = Asset::where('category_id', $categoryId)
            ->where('status', AssetStatus::AVAILABLE)
            ->get();

        $availableAssets = $assets->filter(function ($asset) use ($start, $end) {
            return $this->isAssetAvailable($asset->id, $start, $end);
        });

        return $availableAssets->take($limit);
    }

    /**
     * Clear availability cache for asset
     *
     * @param int $assetId
     * @return void
     */
    public function clearAvailabilityCache(int $assetId): void
    {
        Cache::forget("asset_calendar_{$assetId}_*");
    }
}

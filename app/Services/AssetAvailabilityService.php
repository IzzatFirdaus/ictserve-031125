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
     * Optimized: Single query for all assets, eager loading
     *
     * @param  array  $assetIds  Array of asset IDs to check
     * @param  string  $startDate  Start date (Y-m-d format)
     * @param  string  $endDate  End date (Y-m-d format)
     * @param  int|null  $excludeApplicationId  Exclude specific application from check
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

        // Optimization: Load all assets in single query
        $assets = Asset::whereIn('id', $assetIds)
            ->select('id', 'status')
            ->get()
            ->keyBy('id');

        // Optimization: Load all conflicting loans in single query
        $conflictingLoans = LoanApplication::with(['loanItems:loan_application_id,asset_id'])
            ->whereHas('loanItems', function ($query) use ($assetIds) {
                $query->whereIn('asset_id', $assetIds);
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
            ->when($excludeApplicationId, function ($query, $exclude) {
                $query->where('id', '!=', $exclude);
            })
            ->select('id', 'loan_start_date', 'loan_end_date', 'status')
            ->get();

        // Group conflicting loans by asset_id
        $assetConflicts = [];
        foreach ($conflictingLoans as $loan) {
            foreach ($loan->loanItems as $item) {
                $assetConflicts[$item->asset_id] = true;
            }
        }

        // Build availability array
        $availability = [];
        foreach ($assetIds as $assetId) {
            $asset = $assets->get($assetId);
            $availability[$assetId] = $asset && $asset->isAvailable() && ! isset($assetConflicts[$assetId]);
        }

        return $availability;
    }

    /**
     * Check if specific asset is available for date range
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
     */
    public function clearAvailabilityCache(int $assetId): void
    {
        Cache::forget("asset_calendar_{$assetId}_*");
    }

    /**
     * Check if single asset is available (simple status check)
     */
    public function isAvailable(int $assetId): bool
    {
        $asset = Asset::find($assetId);

        return $asset && $asset->isAvailable();
    }

    /**
     * Check if asset is available for specific date range
     */
    public function isAvailableForDateRange(int $assetId, $startDate, $endDate): bool
    {
        $start = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $end = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        return $this->isAssetAvailable($assetId, $start, $end);
    }

    /**
     * Get available assets by category
     */
    public function getAvailableAssetsByCategory(int $categoryId): Collection
    {
        return Asset::where('category_id', $categoryId)
            ->where('status', AssetStatus::AVAILABLE)
            ->get();
    }

    /**
     * Reserve asset for loan application
     */
    public function reserveAsset(int $assetId, int $loanApplicationId): bool
    {
        $asset = Asset::find($assetId);
        if (! $asset || ! $asset->isAvailable()) {
            return false;
        }

        return $asset->update(['status' => AssetStatus::RESERVED]);
    }

    /**
     * Release asset reservation
     */
    public function releaseReservation(int $assetId, int $loanApplicationId): bool
    {
        $asset = Asset::find($assetId);
        if (! $asset || $asset->status !== AssetStatus::RESERVED) {
            return false;
        }

        return $asset->update(['status' => AssetStatus::AVAILABLE]);
    }

    /**
     * Calculate asset utilization rate over period
     */
    public function calculateUtilizationRate(int $assetId, int $days): float
    {
        $startDate = now()->subDays($days);

        $totalDaysLoaned = LoanApplication::whereHas('loanItems', function ($query) use ($assetId) {
            $query->where('asset_id', $assetId);
        })
            ->where('loan_start_date', '>=', $startDate)
            ->get()
            ->sum(function ($loan) {
                return Carbon::parse($loan->loan_start_date)
                    ->diffInDays(Carbon::parse($loan->loan_end_date));
            });

        return min(100, ($totalDaysLoaned / $days) * 100);
    }

    /**
     * Get asset loan history
     */
    public function getAssetLoanHistory(int $assetId): Collection
    {
        return LoanApplication::whereHas('loanItems', function ($query) use ($assetId) {
            $query->where('asset_id', $assetId);
        })
            ->orderBy('loan_start_date', 'desc')
            ->get();
    }

    /**
     * Get assets requiring maintenance
     */
    public function getAssetsRequiringMaintenance(): Collection
    {
        return Asset::where('status', AssetStatus::MAINTENANCE)->get();
    }

    /**
     * Check multiple assets availability (simple status check)
     */
    public function checkMultipleAssetsAvailability(array $assetIds): array
    {
        $assets = Asset::whereIn('id', $assetIds)->get()->keyBy('id');

        $availability = [];
        foreach ($assetIds as $assetId) {
            $asset = $assets->get($assetId);
            $availability[$assetId] = $asset && $asset->isAvailable();
        }

        return $availability;
    }
}

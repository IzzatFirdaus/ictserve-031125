<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Asset Utilization Analytics Service
 *
 * Calculates asset utilization metrics including loan frequency, average duration,
 * and maintenance costs.
 *
 * @trace Requirements 3.5
 */
class AssetUtilizationService
{
    /**
     * Calculate comprehensive utilization metrics for an asset.
     */
    public function calculateUtilizationMetrics(Asset $asset): array
    {
        return Cache::remember(
            "asset.utilization.{$asset->id}",
            now()->addMinutes(30),
            fn () => [
                'loan_frequency' => $this->calculateLoanFrequency($asset),
                'average_loan_duration' => $this->calculateAverageLoanDuration($asset),
                'total_loans' => $this->getTotalLoans($asset),
                'active_loans' => $this->getActiveLoans($asset),
                'maintenance_frequency' => $this->calculateMaintenanceFrequency($asset),
                'utilization_rate' => $this->calculateUtilizationRate($asset),
                'availability_percentage' => $this->calculateAvailabilityPercentage($asset),
                'last_loan_date' => $this->getLastLoanDate($asset),
                'next_available_date' => $this->getNextAvailableDate($asset),
            ]
        );
    }

    /**
     * Calculate loan frequency (loans per month).
     */
    protected function calculateLoanFrequency(Asset $asset): float
    {
        $firstLoan = $asset->loanItems()->oldest()->first();

        if (! $firstLoan) {
            return 0.0;
        }

        $monthsSinceFirstLoan = max(1, now()->diffInMonths($firstLoan->created_at));
        $totalLoans = $asset->loanItems()->count();

        return round($totalLoans / $monthsSinceFirstLoan, 2);
    }

    /**
     * Calculate average loan duration in days.
     */
    protected function calculateAverageLoanDuration(Asset $asset): float
    {
        $completedLoans = $asset->loanItems()
            ->whereHas('loanApplication', function ($query) {
                $query->whereIn('status', ['returned', 'completed'])
                    ->whereNotNull('loan_start_date')
                    ->whereNotNull('loan_end_date');
            })
            ->with('loanApplication')
            ->get();

        if ($completedLoans->isEmpty()) {
            return 0.0;
        }

        $totalDays = $completedLoans->sum(function ($loanItem) {
            $application = $loanItem->loanApplication;

            return $application->loan_start_date->diffInDays($application->loan_end_date);
        });

        return round($totalDays / $completedLoans->count(), 1);
    }

    /**
     * Get total number of loans for this asset.
     */
    protected function getTotalLoans(Asset $asset): int
    {
        return $asset->loanItems()->count();
    }

    /**
     * Get number of active loans for this asset.
     */
    protected function getActiveLoans(Asset $asset): int
    {
        return $asset->loanItems()
            ->whereHas('loanApplication', function ($query) {
                $query->whereIn('status', ['approved', 'issued']);
            })
            ->count();
    }

    /**
     * Calculate maintenance frequency (maintenance tickets per year).
     */
    protected function calculateMaintenanceFrequency(Asset $asset): float
    {
        $firstTicket = $asset->helpdeskTickets()
            ->where('category', 'maintenance')
            ->oldest()
            ->first();

        if (! $firstTicket) {
            return 0.0;
        }

        $yearsSinceFirstTicket = max(1, now()->diffInYears($firstTicket->created_at) ?: 1);
        $totalMaintenanceTickets = $asset->helpdeskTickets()
            ->where('category', 'maintenance')
            ->count();

        return round($totalMaintenanceTickets / $yearsSinceFirstTicket, 2);
    }

    /**
     * Calculate utilization rate (percentage of time asset is loaned).
     */
    protected function calculateUtilizationRate(Asset $asset): float
    {
        $firstLoan = $asset->loanItems()->oldest()->first();

        if (! $firstLoan) {
            return 0.0;
        }

        $totalDaysSinceFirstLoan = now()->diffInDays($firstLoan->created_at);

        if ($totalDaysSinceFirstLoan === 0) {
            return 0.0;
        }

        $totalLoanedDays = $asset->loanItems()
            ->whereHas('loanApplication', function ($query) {
                $query->whereNotNull('loan_start_date')
                    ->whereNotNull('loan_end_date');
            })
            ->with('loanApplication')
            ->get()
            ->sum(function ($loanItem) {
                $application = $loanItem->loanApplication;

                return $application->loan_start_date->diffInDays($application->loan_end_date);
            });

        return round(($totalLoanedDays / $totalDaysSinceFirstLoan) * 100, 1);
    }

    /**
     * Calculate availability percentage (percentage of time asset is available).
     */
    protected function calculateAvailabilityPercentage(Asset $asset): float
    {
        return round(100 - $this->calculateUtilizationRate($asset), 1);
    }

    /**
     * Get last loan date.
     */
    protected function getLastLoanDate(Asset $asset): ?string
    {
        $lastLoan = $asset->loanItems()
            ->with('loanApplication')
            ->latest()
            ->first();

        return $lastLoan?->loanApplication?->loan_start_date?->format('d M Y');
    }

    /**
     * Get next available date (when current loan ends).
     */
    protected function getNextAvailableDate(Asset $asset): ?string
    {
        $activeLoan = $asset->loanItems()
            ->whereHas('loanApplication', function ($query) {
                $query->whereIn('status', ['approved', 'issued']);
            })
            ->with('loanApplication')
            ->latest()
            ->first();

        return $activeLoan?->loanApplication?->loan_end_date?->format('d M Y');
    }

    /**
     * Get top utilized assets.
     */
    public function getTopUtilizedAssets(int $limit = 10): Collection
    {
        return Asset::query()
            ->withCount('loanItems')
            ->orderBy('loan_items_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($asset) {
                $metrics = $this->calculateUtilizationMetrics($asset);

                return [
                    'asset' => $asset,
                    'metrics' => $metrics,
                ];
            });
    }

    /**
     * Get assets requiring maintenance.
     */
    public function getAssetsRequiringMaintenance(): Collection
    {
        return Asset::query()
            ->where(function ($query) {
                $query->where('status', 'maintenance')
                    ->orWhere('condition', 'damaged')
                    ->orWhere('condition', 'poor')
                    ->orWhere(function ($q) {
                        $q->whereNotNull('next_maintenance_date')
                            ->where('next_maintenance_date', '<=', now()->addDays(30));
                    });
            })
            ->with(['category', 'helpdeskTickets' => function ($query) {
                $query->where('category', 'maintenance')
                    ->latest()
                    ->limit(5);
            }])
            ->get();
    }

    /**
     * Clear utilization cache for an asset.
     */
    public function clearUtilizationCache(Asset $asset): void
    {
        Cache::forget("asset.utilization.{$asset->id}");
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\LoanStatus;
use App\Filament\Resources\Assets\AssetResource;
use App\Filament\Resources\Loans\LoanApplicationResource;
use App\Models\Asset;
use App\Models\LoanApplication;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

/**
 * Asset Loan Statistics Overview Widget
 *
 * Displays key metrics for asset loan applications including utilization,
 * approval workflow statistics, and overdue items. Uses WCAG 2.2 AA compliant
 * colors for all indicators with 5-minute caching strategy.
 *
 * @trace Requirements: Requirement 3.2, 4.1, 13.1
 *
 * @see D04 §3.2 Dashboard widgets
 * @see D12 UI/UX Design Guide - Compliant color palette
 */
class AssetLoanStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false; // Critical widget - load immediately

    protected ?string $pollingInterval = '30s'; // Real-time updates

    protected array|int|null $columns = 2; // 2-column grid layout

    /**
     * @return array<int, Stat>
     */
    protected function getStats(): array
    {
        $stats = Cache::remember('dashboard:loan-stats', 300, function () {
            $now = now()->toDateTimeString();

            // Optimized: Single query for loan stats
            /** @var LoanApplication|null $loanStats */
            $loanStats = LoanApplication::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN user_id IS NULL THEN 1 ELSE 0 END) as guest,
                SUM(CASE WHEN user_id IS NOT NULL THEN 1 ELSE 0 END) as authenticated,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = ? AND loan_end_date < ? THEN 1 ELSE 0 END) as overdue
            ', [LoanStatus::UNDER_REVIEW->value, LoanStatus::IN_USE->value, LoanStatus::IN_USE->value, $now])->first();

            $loanStatsArray = $loanStats instanceof LoanApplication ? $loanStats->toArray() : [];

            $totalApplications = isset($loanStatsArray['total']) && is_numeric($loanStatsArray['total']) ? (int) $loanStatsArray['total'] : 0;
            $guestApplications = isset($loanStatsArray['guest']) && is_numeric($loanStatsArray['guest']) ? (int) $loanStatsArray['guest'] : 0;
            $authenticatedApplications = isset($loanStatsArray['authenticated']) && is_numeric($loanStatsArray['authenticated']) ? (int) $loanStatsArray['authenticated'] : 0;
            $pendingApproval = isset($loanStatsArray['pending']) && is_numeric($loanStatsArray['pending']) ? (int) $loanStatsArray['pending'] : 0;
            $activeLoans = isset($loanStatsArray['active']) && is_numeric($loanStatsArray['active']) ? (int) $loanStatsArray['active'] : 0;
            $overdueItems = isset($loanStatsArray['overdue']) && is_numeric($loanStatsArray['overdue']) ? (int) $loanStatsArray['overdue'] : 0;

            // Optimized: Single query for asset stats
            /** @var Asset|null $assetStats */
            $assetStats = Asset::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "available" THEN 1 ELSE 0 END) as available,
                SUM(CASE WHEN status = "loaned" THEN 1 ELSE 0 END) as loaned
            ')->first();

            $assetStatsArray = $assetStats instanceof Asset ? $assetStats->toArray() : [];

            $totalAssets = isset($assetStatsArray['total']) && is_numeric($assetStatsArray['total']) ? (int) $assetStatsArray['total'] : 0;
            $availableAssets = isset($assetStatsArray['available']) && is_numeric($assetStatsArray['available']) ? (int) $assetStatsArray['available'] : 0;
            $loanedAssets = isset($assetStatsArray['loaned']) && is_numeric($assetStatsArray['loaned']) ? (int) $assetStatsArray['loaned'] : 0;
            $utilizationRate = $totalAssets > 0
                ? round(($loanedAssets / $totalAssets) * 100, 1)
                : 0;

            $guestPercentage = $totalApplications > 0
                ? round(($guestApplications / $totalApplications) * 100, 1)
                : 0;
            $authenticatedPercentage = $totalApplications > 0
                ? round(($authenticatedApplications / $totalApplications) * 100, 1)
                : 0;

            return [
                Stat::make(__('widgets.total_loan_applications'), $totalApplications)
                    ->description(__('widgets.all_loan_applications'))
                    ->descriptionIcon('heroicon-o-document-text')
                    ->color('primary')
                    ->chart($this->getLoanTrendData()),

                Stat::make(__('widgets.guest_applications'), $guestApplications)
                    ->description(__('widgets.of_total_applications', ['percentage' => $guestPercentage]))
                    ->descriptionIcon('heroicon-o-user')
                    ->color('warning')
                    ->extraAttributes([
                        'class' => 'cursor-pointer',
                    ])
                    ->url(LoanApplicationResource::getUrl('index', [
                        'tableFilters' => ['submission_type' => ['value' => 'guest']],
                    ])),

                Stat::make(__('widgets.authenticated_applications'), $authenticatedApplications)
                    ->description(__('widgets.of_total_applications', ['percentage' => $authenticatedPercentage]))
                    ->descriptionIcon('heroicon-o-user-circle')
                    ->color('success')
                    ->extraAttributes([
                        'class' => 'cursor-pointer',
                    ])
                    ->url(LoanApplicationResource::getUrl('index', [
                        'tableFilters' => ['submission_type' => ['value' => 'authenticated']],
                    ])),

                Stat::make(__('widgets.pending_applications'), $pendingApproval)
                    ->description(__('widgets.under_review'))
                    ->descriptionIcon('heroicon-o-clock')
                    ->color('warning')
                    ->url(LoanApplicationResource::getUrl('index', [
                        'tableFilters' => ['status' => ['value' => 'under_review']],
                    ])),

                Stat::make(__('widgets.active_loans'), $activeLoans)
                    ->description(__('widgets.assets_currently_borrowed'))
                    ->descriptionIcon('heroicon-o-arrow-path')
                    ->color('info')
                    ->url(LoanApplicationResource::getUrl('index', [
                        'tableFilters' => ['status' => ['value' => 'in_use']],
                    ])),

                Stat::make(__('widgets.overdue_items'), $overdueItems)
                    ->description(__('widgets.requires_immediate_attention'))
                    ->descriptionIcon($overdueItems > 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle')
                    ->color($overdueItems > 0 ? 'danger' : 'success')
                    ->url(LoanApplicationResource::getUrl('index', [
                        'tableFilters' => ['overdue' => ['isActive' => true]],
                    ])),

                Stat::make(__('widgets.asset_utilization_rate'), "{$utilizationRate}%")
                    ->description(__('widgets.assets_borrowed_of_total', ['borrowed' => $loanedAssets, 'total' => $totalAssets]))
                    ->descriptionIcon('heroicon-o-chart-bar')
                    ->color($utilizationRate > 75 ? 'success' : ($utilizationRate > 50 ? 'warning' : 'gray'))
                    ->chart($this->getUtilizationTrendData()),

                Stat::make(__('widgets.available_assets'), $availableAssets)
                    ->description(__('widgets.ready_for_loan'))
                    ->descriptionIcon('heroicon-o-check-circle')
                    ->color('success')
                    ->url(AssetResource::getUrl('index', [
                        'tableFilters' => ['status' => ['value' => 'available']],
                    ])),
            ];
        });

        /** @var array<int, Stat> $stats */
        return $stats;
    }

    /**
     * Get loan application trend data for the last 7 days
     *
     * @return array<int, int>
     */
    protected function getLoanTrendData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $count = LoanApplication::whereDate('created_at', $date)->count();
            $data[] = $count;
        }

        return $data;
    }

    /**
     * Get asset utilization trend data for the last 7 days
     *
     * @return array<int, float|int>
     */
    protected function getUtilizationTrendData(): array
    {
        $data = [];
        $totalAssets = Asset::count();

        if ($totalAssets === 0) {
            return array_fill(0, 7, 0);
        }

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $loanedCount = Asset::where('status', 'loaned')
                ->whereDate('updated_at', '<=', $date)
                ->count();
            $utilizationRate = round(($loanedCount / $totalAssets) * 100, 1);
            $data[] = $utilizationRate;
        }

        return $data;
    }
}

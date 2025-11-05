<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\LoanStatus;
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

    protected ?string $pollingInterval = '300s'; // 5-minute real-time updates

    protected function getStats(): array
    {
        /** @var array<Stat> */
        $stats = Cache::remember('asset-loan-stats-overview', 300, function () {
            $totalApplications = LoanApplication::count();
            $guestApplications = LoanApplication::whereNull('user_id')->count();
            $authenticatedApplications = LoanApplication::whereNotNull('user_id')->count();

            // Approval workflow statistics
            $pendingApproval = LoanApplication::where('status', LoanStatus::UNDER_REVIEW)->count();
            $approved = LoanApplication::where('status', LoanStatus::APPROVED)->count();
            $rejected = LoanApplication::where('status', LoanStatus::REJECTED)->count();

            // Active loans and overdue items
            $activeLoans = LoanApplication::where('status', LoanStatus::IN_USE)->count();
            $overdueItems = LoanApplication::where('status', LoanStatus::IN_USE)
                ->where('loan_end_date', '<', now())
                ->count();

            // Asset utilization metrics
            $totalAssets = Asset::count();
            $availableAssets = Asset::where('status', 'available')->count();
            $loanedAssets = Asset::where('status', 'loaned')->count();
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
                Stat::make('Jumlah Permohonan', $totalApplications)
                    ->description('Semua permohonan pinjaman')
                    ->descriptionIcon('heroicon-o-document-text')
                    ->color('primary')
                    ->chart($this->getLoanTrendData()),

                Stat::make('Permohonan Tetamu', $guestApplications)
                    ->description("{$guestPercentage}% daripada jumlah permohonan")
                    ->descriptionIcon('heroicon-o-user')
                    ->color('warning')
                    ->extraAttributes([
                        'class' => 'cursor-pointer',
                    ])
                    ->url(route('filament.admin.resources.loans.loan-applications.index', [
                        'tableFilters' => ['submission_type' => ['value' => 'guest']],
                    ])),

                Stat::make('Permohonan Berdaftar', $authenticatedApplications)
                    ->description("{$authenticatedPercentage}% daripada jumlah permohonan")
                    ->descriptionIcon('heroicon-o-user-circle')
                    ->color('success')
                    ->extraAttributes([
                        'class' => 'cursor-pointer',
                    ])
                    ->url(route('filament.admin.resources.loans.loan-applications.index', [
                        'tableFilters' => ['submission_type' => ['value' => 'authenticated']],
                    ])),

                Stat::make('Menunggu Kelulusan', $pendingApproval)
                    ->description('Permohonan dalam semakan')
                    ->descriptionIcon('heroicon-o-clock')
                    ->color('warning')
                    ->url(route('filament.admin.resources.loans.loan-applications.index', [
                        'tableFilters' => ['status' => ['value' => 'under_review']],
                    ])),

                Stat::make('Pinjaman Aktif', $activeLoans)
                    ->description('Aset sedang dipinjam')
                    ->descriptionIcon('heroicon-o-arrow-path')
                    ->color('info')
                    ->url(route('filament.admin.resources.loans.loan-applications.index', [
                        'tableFilters' => ['status' => ['value' => 'in_use']],
                    ])),

                Stat::make('Item Tertunggak', $overdueItems)
                    ->description('Memerlukan tindakan segera')
                    ->descriptionIcon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->url(route('filament.admin.resources.loans.loan-applications.index', [
                        'tableFilters' => ['overdue' => ['isActive' => true]],
                    ])),

                Stat::make('Kadar Penggunaan Aset', "{$utilizationRate}%")
                    ->description("{$loanedAssets} daripada {$totalAssets} aset dipinjam")
                    ->descriptionIcon('heroicon-o-chart-bar')
                    ->color($utilizationRate > 75 ? 'success' : ($utilizationRate > 50 ? 'warning' : 'gray'))
                    ->chart($this->getUtilizationTrendData()),

                Stat::make('Aset Tersedia', $availableAssets)
                    ->description('Boleh dipinjam')
                    ->descriptionIcon('heroicon-o-check-circle')
                    ->color('success')
                    ->url(route('filament.admin.resources.assets.index', [
                        'tableFilters' => ['status' => ['value' => 'available']],
                    ])),
            ];
        });

        return $stats;
    }

    /**
     * Get loan application trend data for the last 7 days
     *
     * @return array<int>
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
     * @return array<float>
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

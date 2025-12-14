<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Widgets;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Loan Analytics Widget
 *
 * Displays comprehensive loan application analytics and reporting metrics.
 *
 * @trace Requirements 3.1, 3.3, 3.4, 8.1, 8.2
 */
class LoanAnalyticsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Total applications
        $totalApplications = LoanApplication::count();

        // Pending approval
        $pendingApproval = LoanApplication::whereIn('status', [
            LoanStatus::SUBMITTED,
            LoanStatus::UNDER_REVIEW,
            LoanStatus::PENDING_INFO,
        ])->count();

        // Active loans
        $activeLoans = LoanApplication::whereIn('status', [
            LoanStatus::APPROVED,
            LoanStatus::IN_USE,
            LoanStatus::RETURN_DUE,
        ])->count();

        // Overdue loans
        $overdueLoans = LoanApplication::where('status', LoanStatus::OVERDUE)->count();

        // Approval rate (last 30 days)
        $recentApplications = LoanApplication::where('created_at', '>=', now()->subDays(30))->count();
        $recentApproved = LoanApplication::where('created_at', '>=', now()->subDays(30))
            ->where('status', LoanStatus::APPROVED)
            ->count();
        $approvalRate = $recentApplications > 0 ? round(($recentApproved / $recentApplications) * 100, 1) : 0;

        // Average approval time (in hours)
        $avgApprovalTime = LoanApplication::whereNotNull('approved_at')
            ->where('created_at', '>=', now()->subDays(90))
            ->get()
            ->avg(function ($application) {
                return Carbon::parse($application->created_at)->diffInHours($application->approved_at);
            });
        $avgApprovalTime = $avgApprovalTime ? round($avgApprovalTime, 1) : 0;

        // Total value of active loans
        $totalValue = (float) LoanApplication::whereIn('status', [
            LoanStatus::APPROVED,
            LoanStatus::IN_USE,
            LoanStatus::RETURN_DUE,
        ])->sum('total_value');

        // Completed loans (last 30 days)
        $completedLoans = LoanApplication::where('status', LoanStatus::COMPLETED)
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();

        return [
            Stat::make('Jumlah Permohonan', $totalApplications)
                ->description('Sepanjang masa')
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('primary'),

            Stat::make('Menunggu Kelulusan', $pendingApproval)
                ->description('Menunggu keputusan')
                ->descriptionIcon('heroicon-o-clock')
                ->color($pendingApproval > 10 ? 'warning' : 'info'),

            Stat::make('Pinjaman Aktif', $activeLoans)
                ->description('Sedang digunakan')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success'),

            Stat::make('Pinjaman Tertunggak', $overdueLoans)
                ->description('Melepasi tarikh pulang')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($overdueLoans > 0 ? 'danger' : 'success'),

            Stat::make('Kadar Kelulusan', $approvalRate.'%')
                ->description('30 hari terakhir')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color($approvalRate > 70 ? 'success' : ($approvalRate > 50 ? 'warning' : 'danger')),

            Stat::make('Purata Masa Kelulusan', $avgApprovalTime.' jam')
                ->description('90 hari terakhir')
                ->descriptionIcon('heroicon-o-clock')
                ->color($avgApprovalTime < 24 ? 'success' : ($avgApprovalTime < 48 ? 'warning' : 'danger')),

            Stat::make('Jumlah Nilai Aktif', 'RM '.number_format($totalValue, 2))
                ->description('Aset dalam edaran')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('info'),

            Stat::make('Selesai (30 hari)', $completedLoans)
                ->description('Berjaya dipulangkan')
                ->descriptionIcon('heroicon-o-check-badge')
                ->color('success'),
        ];
    }
}

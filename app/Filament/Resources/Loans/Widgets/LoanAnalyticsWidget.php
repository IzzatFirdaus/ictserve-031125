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
        $totalValue = LoanApplication::whereIn('status', [
            LoanStatus::APPROVED,
            LoanStatus::IN_USE,
            LoanStatus::RETURN_DUE,
        ])->sum('total_value');

        // Completed loans (last 30 days)
        $completedLoans = LoanApplication::where('status', LoanStatus::COMPLETED)
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();

        return [
            Stat::make(__('Total Applications'), $totalApplications)
                ->description(__('All time'))
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('primary'),

            Stat::make(__('Pending Approval'), $pendingApproval)
                ->description(__('Awaiting decision'))
                ->descriptionIcon('heroicon-o-clock')
                ->color($pendingApproval > 10 ? 'warning' : 'info'),

            Stat::make(__('Active Loans'), $activeLoans)
                ->description(__('Currently in use'))
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success'),

            Stat::make(__('Overdue Loans'), $overdueLoans)
                ->description(__('Past due date'))
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($overdueLoans > 0 ? 'danger' : 'success'),

            Stat::make(__('Approval Rate'), $approvalRate.'%')
                ->description(__('Last 30 days'))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color($approvalRate > 70 ? 'success' : ($approvalRate > 50 ? 'warning' : 'danger')),

            Stat::make(__('Avg Approval Time'), $avgApprovalTime.' '.__('hours'))
                ->description(__('Last 90 days'))
                ->descriptionIcon('heroicon-o-clock')
                ->color($avgApprovalTime < 24 ? 'success' : ($avgApprovalTime < 48 ? 'warning' : 'danger')),

            Stat::make(__('Total Active Value'), 'RM '.number_format($totalValue, 2))
                ->description(__('Assets in circulation'))
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('info'),

            Stat::make(__('Completed (30d)'), $completedLoans)
                ->description(__('Successfully returned'))
                ->descriptionIcon('heroicon-o-check-badge')
                ->color('success'),
        ];
    }
}

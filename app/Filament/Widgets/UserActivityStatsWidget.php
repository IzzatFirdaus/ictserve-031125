<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Component name: User Activity Stats Widget
 * Description: Statistics widget showing user activity metrics
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-004.5 (User Activity Monitoring)
 * @trace D04 §3.3 (User Management Dashboard)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 *
 * @version 1.0.0
 *
 * @created 2025-11-07
 */
class UserActivityStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    /**
     * Widget is only visible to superusers.
     */
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('superuser') ?? false;
    }

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $recentlyActive = User::where('last_login_at', '>=', now()->subDays(7))->count();
        $inactiveUsers = User::where(function ($query) {
            $query->whereNull('last_login_at')
                ->orWhere('last_login_at', '<=', now()->subDays(30));
        })->count();

        // Calculate trends
        $lastWeekActive = User::where('last_login_at', '>=', now()->subDays(14))
            ->where('last_login_at', '<', now()->subDays(7))
            ->count();

        $activityTrend = $lastWeekActive > 0
            ? (($recentlyActive - $lastWeekActive) / $lastWeekActive) * 100
            : 0;

        return [
            Stat::make(__('Total Users'), $totalUsers)
                ->description(__('All registered users'))
                ->descriptionIcon('heroicon-o-users')
                ->color('primary')
                ->chart($this->getUserGrowthChart()),

            Stat::make(__('Active Users'), $activeUsers)
                ->description(__('Users with active accounts'))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make(__('Recently Active'), $recentlyActive)
                ->description(__('Logged in within 7 days'))
                ->descriptionIcon('heroicon-o-clock')
                ->color('info')
                ->chart($this->getActivityChart())
                ->descriptionColor($activityTrend >= 0 ? 'success' : 'danger')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make(__('Inactive Users'), $inactiveUsers)
                ->description(__('No login for 30+ days'))
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('warning'),
        ];
    }

    /**
     * Get user growth chart data for the last 7 days.
     */
    private function getUserGrowthChart(): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $count = User::where('created_at', '<=', $date)->count();
            $data[] = $count;
        }

        return $data;
    }

    /**
     * Get activity chart data for the last 7 days.
     */
    private function getActivityChart(): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = User::whereDate('last_login_at', $date->toDateString())->count();
            $data[] = $count;
        }

        return $data;
    }
}

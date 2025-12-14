<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\AssetUtilizationWidget;
use App\Filament\Widgets\CriticalAlertsWidget;
use App\Filament\Widgets\HelpdeskStatsOverview;
use App\Filament\Widgets\LoanApprovalQueueWidget;
use App\Filament\Widgets\QuickActionsWidget;
use App\Filament\Widgets\RecentActivityFeedWidget;
use App\Filament\Widgets\ResolutionTimeChart;
use App\Filament\Widgets\TicketsByStatusChart;
use App\Filament\Widgets\TicketVolumeChart;
use App\Filament\Widgets\UnifiedAnalyticsChart;
use App\Filament\Widgets\UnifiedDashboardOverview;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

/**
 * Admin Dashboard - Unified Dashboard with Real-Time Widgets
 *
 * Implements comprehensive dashboard combining helpdesk and asset loan metrics
 * with real-time updates via Laravel Reverb WebSocket integration.
 *
 * Features:
 * - Statistics widgets with 300-second refresh (UnifiedDashboardOverview)
 * - Trend charts and utilization analytics (UnifiedAnalyticsChart, AssetUtilizationWidget)
 * - Activity feed with real-time updates (RecentActivityFeedWidget)
 * - Critical alerts and quick actions (CriticalAlertsWidget, QuickActionsWidget)
 *
 * @trace Requirements: 8.4, 10.2, 10.3
 *
 * @see D04 §3.2 Dashboard widgets
 * @see D16 Broadcasting Setup - Laravel Reverb integration
 */
class AdminDashboard extends BaseDashboard
{
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?int $navigationSort = -2;

    /**
     * @return int|array<string, int>
     */
    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 2,
            'lg' => 3,
            'xl' => 3,
            '2xl' => 4,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Unified overview with 300s polling - full width
            UnifiedDashboardOverview::class,

            // Critical alerts - requires immediate attention
            CriticalAlertsWidget::class,

            // Module-specific stats
            HelpdeskStatsOverview::class,
            LoanApprovalQueueWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // Quick actions for common workflows
            QuickActionsWidget::class,

            // Unified analytics chart - full width trend visualization
            UnifiedAnalyticsChart::class,

            // Supporting charts in 2-column layout
            TicketVolumeChart::class,
            ResolutionTimeChart::class,

            // Ticket breakdown and asset utilization side-by-side
            TicketsByStatusChart::class,
            AssetUtilizationWidget::class,

            // Recent activity feed with real-time WebSocket updates
            RecentActivityFeedWidget::class,
        ];
    }

    public function getTitle(): string
    {
        return __('Papan Pemuka Pentadbir');
    }

    public static function getNavigationLabel(): string
    {
        return __('Papan Pemuka');
    }
}

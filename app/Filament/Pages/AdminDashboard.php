<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\CriticalAlertsWidget;
use App\Filament\Widgets\CrossModuleIntegrationChart;
use App\Filament\Widgets\LoanAnalyticsWidget;
use App\Filament\Widgets\RecentActivityFeedWidget;
use App\Filament\Widgets\ResolutionTimeChart;
use App\Filament\Widgets\TicketsByStatusChart;
use App\Filament\Widgets\TicketVolumeChart;
use App\Filament\Widgets\UnifiedAnalyticsChart;
use App\Filament\Widgets\UnifiedDashboardOverview;
use Filament\Pages\Page;

/**
 * Admin Dashboard - Unified Dashboard with Real-Time Widgets
 *
 * Implements comprehensive dashboard combining helpdesk and asset loan metrics
 * with real-time updates via Laravel Reverb WebSocket integration.
 *
 * Features:
 * - Statistics widgets with 300-second refresh (UnifiedDashboardOverview)
 * - Critical alerts and quick actions (CriticalAlertsWidget)
 * - Recent activity feed (RecentActivityFeedWidget)
 * - Comprehensive chart widgets with toggle resize functionality
 *
 * @trace Requirements: 8.4, 10.2, 10.3
 *
 * @see D04 §3.2 Dashboard widgets
 * @see D16 Broadcasting Setup - Laravel Reverb integration
 */
class AdminDashboard extends Page
{
    protected static ?int $navigationSort = -2;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected string $view = 'filament.pages.admin-dashboard';

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

    /**
     * Header widgets - Overview stats and critical alerts
     */
    public function getHeaderWidgets(): array
    {
        return [
            UnifiedDashboardOverview::class,
            CriticalAlertsWidget::class,
        ];
    }

    /**
     * Main content widgets - Activity feed and other non-chart widgets
     */
    public function getMainContentWidgets(): array
    {
        return [
            RecentActivityFeedWidget::class,
        ];
    }

    /**
     * Chart widgets - All chart/graph widgets with toggle resize functionality
     */
    public function getChartWidgets(): array
    {
        return [
            UnifiedAnalyticsChart::class,
            TicketsByStatusChart::class,
            TicketVolumeChart::class,
            ResolutionTimeChart::class,
            LoanAnalyticsWidget::class,
            CrossModuleIntegrationChart::class,
        ];
    }

    /**
     * Get all widgets for the dashboard
     * This method is called by Filament to render widgets
     */
    public function getWidgets(): array
    {
        return array_merge(
            $this->getHeaderWidgets(),
            $this->getMainContentWidgets(),
            $this->getChartWidgets()
        );
    }

    public function getTitle(): string
    {
        return ''; // Remove duplicate title - already shown in view template
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.dashboard');
    }
}

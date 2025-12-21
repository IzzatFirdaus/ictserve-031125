<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\AICostWidget;
use App\Filament\Widgets\AIHealthWidget;
use App\Filament\Widgets\AIPerformanceWidget;
use App\Filament\Widgets\CriticalAlertsWidget;
use App\Filament\Widgets\CrossModuleIntegrationChart;
use App\Filament\Widgets\LoanAnalyticsWidget;
use App\Filament\Widgets\PulseOverviewWidget;
use App\Filament\Widgets\QueueStatsWidget;
use App\Filament\Widgets\RecentActivityFeedWidget;
use App\Filament\Widgets\ResolutionTimeChart;
use App\Filament\Widgets\TicketsByStatusChart;
use App\Filament\Widgets\TicketVolumeChart;
use App\Filament\Widgets\UnifiedAnalyticsChart;
use App\Filament\Widgets\UnifiedDashboardOverview;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

/**
 * Admin Dashboard - Unified Dashboard with Real-Time Widgets
 *
 * Implements comprehensive dashboard combining helpdesk and asset loan metrics
 * with real-time updates via Laravel Reverb WebSocket integration.
 * Now includes AI performance monitoring widgets (D18 integration).
 *
 * Features:
 * - Statistics widgets with 300-second refresh (UnifiedDashboardOverview)
 * - AI performance, cost, and health monitoring widgets (admin/superuser only)
 * - Critical alerts and quick actions (CriticalAlertsWidget)
 * - Recent activity feed (RecentActivityFeedWidget)
 * - Comprehensive chart widgets with toggle resize functionality
 *
 * @trace Requirements: 8.4, 10.2, 10.3, R21 (Cloud Hybrid AI Dashboard Integration)
 *
 * @see D04 §3.2 Dashboard widgets
 * @see D16 Broadcasting Setup - Laravel Reverb integration
 * @see D18 AI Chatbot Ollama-Bedrock integration
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
     * Header widgets - Overview stats, AI monitoring, and critical alerts
     */
    public function getHeaderWidgets(): array
    {
        $widgets = [
            UnifiedDashboardOverview::class,
            PulseOverviewWidget::class,
            CriticalAlertsWidget::class,
        ];

        // Add AI widgets for admin and superuser roles only
        if ($this->canViewAIWidgets()) {
            $widgets = array_merge($widgets, [
                AIPerformanceWidget::class,
                AICostWidget::class,
                AIHealthWidget::class,
            ]);
        }

        return $widgets;
    }

    /**
     * Main content widgets - Activity feed and other non-chart widgets
     */
    public function getMainContentWidgets(): array
    {
        return [
            RecentActivityFeedWidget::class,
            QueueStatsWidget::class,
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
        return [
            ...$this->getHeaderWidgets(),
            ...$this->getMainContentWidgets(),
            ...$this->getChartWidgets(),
        ];
    }

    /**
     * Check if current user can view AI widgets
     */
    private function canViewAIWidgets(): bool
    {
        $user = Auth::user();
        return $user && $user->hasAnyRole(['admin', 'superuser']);
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

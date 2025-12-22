<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\CacheableWidget;
use App\Filament\Traits\WidgetMetadata;
use App\Services\UnifiedAnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Unified Dashboard Overview Widget
 *
 * Displays key metrics combining helpdesk and asset loan data.
 * Provides real-time system health and performance indicators
 * with 300-second polling interval for real-time updates.
 *
 * Features:
 * - System health score with trend visualization
 * - Active items requiring attention
 * - Helpdesk resolution rate with pending count
 * - Loan approval rate with pending approvals
 * - Asset utilization metrics
 * - Cached data with 5-minute TTL for performance
 *
 * @trace Requirements: 8.4, 10.2, 10.3, 13.1, 13.3
 *
 * @see D04 §3.2 Dashboard widgets
 * @see D12 §9 Performance optimization patterns
 */
class UnifiedDashboardOverview extends BaseWidget
{
    use CacheableWidget;
    use WidgetMetadata;

    /**
     * Polling interval for real-time updates (300 seconds = 5 minutes)
     * Matches the cache TTL for optimal performance
     */
    protected ?string $pollingInterval = '300s';

    protected int|string|array $columnSpan = 'full';

    /**
     * Sort order - display at top of dashboard
     */
    protected static ?int $sort = -10;

    /**
     * Widget roles - accessible to all authenticated users
     */
    

/**
 * @return array<string, mixed>
 */
public static function getWidgetRoles(): array
    {
        return ['staff', 'admin', 'superuser'];
    }

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets, D12 §9 Performance optimization patterns';
    }

    

/**
 * @return array<string, mixed>
 */
protected function getStats(): array
    {
        // Use cached data with 5-minute TTL for performance optimization
        $metrics = $this->cached(function () {
            $service = app(UnifiedAnalyticsService::class);

            return $service->getDashboardMetrics();
        }, 'dashboard-metrics');

        $helpdesk = $metrics['helpdesk'];
        $loans = $metrics['loans'];
        $assets = $metrics['assets'];
        $summary = $metrics['summary'];

        return [
            // System Health Overview
            Stat::make(__('widgets.overall_health_score'), $summary['overall_system_health'].'%')
                ->description(__('widgets.overall_health_description'))
                ->descriptionIcon('heroicon-m-heart')
                ->color($this->getHealthColor($summary['overall_system_health']))
                ->chart($this->getHealthTrendData()),

            // Active Items Requiring Attention
            Stat::make(__('widgets.active_items'), (string) $summary['total_active_items'])
                ->description(__('widgets.tickets_and_loans_active'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),

            // Issues Requiring Attention
            Stat::make(__('widgets.issues_requiring_action'), (string) $summary['total_issues_requiring_attention'])
                ->description(__('widgets.issues_need_action_description'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($summary['total_issues_requiring_attention'] > 0 ? 'warning' : 'success'),

            // Helpdesk Performance
            Stat::make('Kadar Penyelesaian Helpdesk', $helpdesk['resolution_rate'].'%')
                ->description($helpdesk['pending_tickets'].' tiket tertunda')
                ->descriptionIcon('heroicon-m-ticket')
                ->color($this->getPerformanceColor($helpdesk['resolution_rate']))
                ->url(route('staff.tickets.index')),

            // Loan Approval Performance
            Stat::make('Kadar Kelulusan Pinjaman', $loans['approval_rate'].'%')
                ->description($loans['pending_approval'].' menunggu kelulusan')
                ->descriptionIcon('heroicon-m-document-check')
                ->color($this->getPerformanceColor($loans['approval_rate']))
                ->url(route('staff.approvals.index')),

            // Asset Utilization
            Stat::make('Penggunaan Aset', $assets['utilization_rate'].'%')
                ->description($assets['available_assets'].' tersedia')
                ->descriptionIcon('heroicon-m-cube')
                ->color($this->getUtilizationColor($assets['utilization_rate']))
                ->url(route('staff.loans.index')),
        ];
    }

    private function getHealthColor(float $health): string
    {
        return match (true) {
            $health >= 90 => 'success',
            $health >= 75 => 'warning',
            $health >= 60 => 'danger',
            default => 'danger',
        };
    }

    private function getPerformanceColor(float $rate): string
    {
        return match (true) {
            $rate >= 85 => 'success',
            $rate >= 70 => 'warning',
            $rate >= 50 => 'danger',
            default => 'danger',
        };
    }

    private function getUtilizationColor(float $rate): string
    {
        return match (true) {
            $rate >= 80 => 'warning', // High utilization - may need more assets
            $rate >= 60 => 'success',  // Good utilization
            $rate >= 40 => 'warning', // Moderate utilization
            default => 'info',       // Low utilization
        };
    }

    /**
     * @return array<int, int>
     */
    

/**
 * @return array<string, mixed>
 */
private function getHealthTrendData(): array
    {
        // Simple trend data - could be enhanced with historical data
        return [65, 70, 75, 80, 85, 88, 90];
    }
}

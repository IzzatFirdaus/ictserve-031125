<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\UnifiedAnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Unified Dashboard Overview Widget
 *
 * Displays key metrics combining helpdesk and asset loan data.
 * Provides real-time system health and performance indicators.
 *
 * Requirements: 13.1, 4.1, 4.2, 13.3
 */
class UnifiedDashboardOverview extends BaseWidget
{
    protected ?string $pollingInterval = '300s';

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $service = app(UnifiedAnalyticsService::class);
        $metrics = $service->getDashboardMetrics();

        $helpdesk = $metrics['helpdesk'];
        $loans = $metrics['loans'];
        $assets = $metrics['assets'];
        $summary = $metrics['summary'];

        return [
            // System Health Overview
            Stat::make('Kesihatan Sistem', $summary['overall_system_health'].'%')
                ->description('Skor kesihatan keseluruhan')
                ->descriptionIcon('heroicon-m-heart')
                ->color($this->getHealthColor($summary['overall_system_health']))
                ->chart($this->getHealthTrendData()),

            // Active Items Requiring Attention
            Stat::make('Item Aktif', (string) $summary['total_active_items'])
                ->description('Tiket & pinjaman aktif')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),

            // Issues Requiring Attention
            Stat::make('Perlu Perhatian', (string) $summary['total_issues_requiring_attention'])
                ->description('Isu yang perlu tindakan')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($summary['total_issues_requiring_attention'] > 0 ? 'warning' : 'success'),

            // Helpdesk Performance
            Stat::make('Kadar Penyelesaian Helpdesk', $helpdesk['resolution_rate'].'%')
                ->description($helpdesk['pending_tickets'].' tiket tertunda')
                ->descriptionIcon('heroicon-m-ticket')
                ->color($this->getPerformanceColor($helpdesk['resolution_rate'])),

            // Loan Approval Performance
            Stat::make('Kadar Kelulusan Pinjaman', $loans['approval_rate'].'%')
                ->description($loans['pending_approval'].' menunggu kelulusan')
                ->descriptionIcon('heroicon-m-document-check')
                ->color($this->getPerformanceColor($loans['approval_rate'])),

            // Asset Utilization
            Stat::make('Penggunaan Aset', $assets['utilization_rate'].'%')
                ->description($assets['available_assets'].' tersedia')
                ->descriptionIcon('heroicon-m-cube')
                ->color($this->getUtilizationColor($assets['utilization_rate'])),
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

    private function getHealthTrendData(): array
    {
        // Simple trend data - could be enhanced with historical data
        return [65, 70, 75, 80, 85, 88, 90];
    }
}

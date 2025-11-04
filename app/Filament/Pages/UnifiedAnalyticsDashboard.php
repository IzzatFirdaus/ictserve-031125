<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\EnhancedUnifiedAnalyticsChart;
use App\Filament\Widgets\UnifiedDashboardOverview;
use Filament\Pages\Page;

/**
 * Unified Analytics Dashboard Page
 *
 * Comprehensive dashboard combining loan and helpdesk metrics with drill-down capabilities.
 * Provides real-time data visualization and detailed analysis tools.
 *
 * Requirements: 13.1, 4.1, 4.2, 13.3
 */
class UnifiedAnalyticsDashboard extends Page
{
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-chart-bar-square';
    }

    public function getView(): string
    {
        return 'filament.pages.unified-analytics-dashboard';
    }

    public static function getNavigationLabel(): string
    {
        return 'Analitik Terpadu';
    }

    public function getTitle(): string
    {
        return 'Dashboard Analitik Terpadu';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Reports';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public function getColumns(): int|array
    {
        return 12;
    }

    public function getWidgets(): array
    {
        return [
            UnifiedDashboardOverview::class,
            EnhancedUnifiedAnalyticsChart::class,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UnifiedDashboardOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            EnhancedUnifiedAnalyticsChart::class,
        ];
    }
}

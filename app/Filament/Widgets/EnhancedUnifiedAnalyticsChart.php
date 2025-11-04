<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\UnifiedAnalyticsService;
use Filament\Widgets\ChartWidget;

/**
 * Enhanced Unified Analytics Chart Widget
 *
 * Displays comprehensive trends combining helpdesk, loan, and integration data.
 * Provides drill-down capabilities and real-time data visualization.
 *
 * Requirements: 13.1, 4.1, 4.2, 13.3
 */
class EnhancedUnifiedAnalyticsChart extends ChartWidget
{
    protected ?string $pollingInterval = '300s';

    protected ?string $heading = 'Analitik Terpadu - Trend 6 Bulan';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '400px';

    public ?string $filter = 'monthly_trends';

    protected function getFilters(): ?array
    {
        return [
            'monthly_trends' => 'Trend Bulanan',
            'asset_utilization' => 'Penggunaan Aset',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $service = app(UnifiedAnalyticsService::class);

        return match ($this->filter) {
            'asset_utilization' => $service->getAssetUtilizationTrends(30),
            default => $service->getMonthlyTrends(6),
        };
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => $this->filter === 'asset_utilization' ? 'Hari' : 'Bulan',
                    ],
                ],
                'y' => [
                    'display' => true,
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Bilangan',
                    ],
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
            'maintainAspectRatio' => false,
            'responsive' => true,
        ];
    }
}

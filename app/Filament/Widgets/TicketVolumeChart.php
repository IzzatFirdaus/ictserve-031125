<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\ThemeAwareChartColors;
use App\Filament\Traits\WidgetMetadata;
use App\Services\HelpdeskReportService;
use Filament\Widgets\ChartWidget;

/**
 * Ticket Volume Chart Widget
 *
 * Displays daily ticket volume trends for the last 30 days.
 * Uses theme-aware colors for WCAG 2.2 AA compliance.
 *
 * @trace Requirements: 3.5, 6.7 (Chart Theme Adaptation)
 *
 * @see D03 Software Requirements Specification - Requirements 8.1, 8.2
 */
class TicketVolumeChart extends ChartWidget
{
    use ThemeAwareChartColors;
    use WidgetMetadata;

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets, D12 WCAG 2.2 AA compliance';
    }

    protected ?string $heading = 'Volum Tiket (30 Hari Terakhir)';

    // Make the chart responsive so it can sit side-by-side with other charts
    // on wide screens but remain full width on mobile/tablet.
    protected int|string|array $columnSpan = [
        'default' => 12,
        'lg' => 6,
    ];

    protected ?string $pollingInterval = '300s';

    protected function getData(): array
    {
        $service = app(HelpdeskReportService::class);
        $trends = $service->getDailyTicketTrends(30);

        return [
            'datasets' => [
                [
                    'label' => 'Tiket Dicipta',
                    'data' => $trends['data'],
                    'backgroundColor' => $this->getChartPrimaryColor(0.1),
                    'borderColor' => $this->getChartPrimaryColor(1.0),
                    'fill' => true,
                ],
            ],
            'labels' => $trends['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'labels' => [
                        'color' => $this->getChartTextColor(),
                    ],
                ],
                'tooltip' => $this->getChartTooltipConfig(),
            ],
            'scales' => [
                'y' => $this->getChartYScaleOptions(),
                'x' => $this->getChartXScaleOptions(),
            ],
            'maintainAspectRatio' => false,
            'responsive' => true,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\HelpdeskReportService;
use Filament\Widgets\ChartWidget;

/**
 * Ticket Volume Chart Widget
 *
 * Displays daily ticket volume trends for the last 30 days.
 *
 * @see D03 Software Requirements Specification - Requirements 8.1, 8.2
 */
class TicketVolumeChart extends ChartWidget
{
    protected ?string $heading = 'Ticket Volume (Last 30 Days)';

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '300s';

    protected function getData(): array
    {
        $service = app(HelpdeskReportService::class);
        $trends = $service->getDailyTicketTrends(30);

        return [
            'datasets' => [
                [
                    'label' => 'Tickets Created',
                    'data' => $trends['data'],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
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
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}

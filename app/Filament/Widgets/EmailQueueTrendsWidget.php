<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\WidgetMetadata;
use App\Services\EmailQueueMonitoringService;
use Filament\Widgets\ChartWidget;

class EmailQueueTrendsWidget extends ChartWidget
{
    use WidgetMetadata;

    protected static ?int $sort = 2;

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets, D11 §9 Queue management';
    }

    public function getHeading(): ?string
    {
        return '7-Day Processing Trends';
    }

    protected function getData(): array
    {
        $service = app(EmailQueueMonitoringService::class);
        $trends = $service->getProcessingTrends(7);

        return [
            'datasets' => [
                [
                    'label' => 'Daily Job Volume',
                    'data' => array_column($trends, 'total_jobs'),
                    'borderColor' => 'rgb(0, 86, 179)',
                    'backgroundColor' => 'rgba(0, 86, 179, 0.1)',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Success Rate (%)',
                    'data' => array_column($trends, 'success_rate'),
                    'borderColor' => 'rgb(25, 135, 84)',
                    'backgroundColor' => 'rgba(25, 135, 84, 0.1)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => array_column($trends, 'date'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

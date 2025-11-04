<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\CrossModuleIntegration;
use Filament\Widgets\ChartWidget;

/**
 * Cross-Module Integration Chart Widget
 *
 * Displays asset-ticket linkage statistics and cross-module integration trends.
 * Uses WCAG 2.2 AA compliant colors for all visualizations.
 *
 * @trace Requirements: Requirement 3.2
 */
class CrossModuleIntegrationChart extends ChartWidget
{
    protected ?string $heading = 'Integrasi Silang Modul';

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $integrationTypes = [
            CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT => 'Laporan Kerosakan',
            CrossModuleIntegration::TYPE_MAINTENANCE_REQUEST => 'Penyelenggaraan',
            CrossModuleIntegration::TYPE_ASSET_TICKET_LINK => 'Pautan Aset-Tiket',
        ];

        $data = [];
        $labels = [];

        foreach ($integrationTypes as $type => $label) {
            $count = CrossModuleIntegration::where('integration_type', $type)->count();
            $data[] = $count;
            $labels[] = $label;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Bilangan Integrasi',
                    'data' => $data,
                    // WCAG 2.2 AA compliant colors
                    'backgroundColor' => [
                        'rgba(181, 12, 12, 0.7)',  // danger - asset damage (#b50c0c)
                        'rgba(255, 140, 0, 0.7)',  // warning - maintenance (#ff8c00)
                        'rgba(0, 86, 179, 0.7)',   // info - asset link (#0056b3)
                    ],
                    'borderColor' => [
                        'rgba(181, 12, 12, 1)',
                        'rgba(255, 140, 0, 1)',
                        'rgba(0, 86, 179, 1)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
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
            'maintainAspectRatio' => false,
        ];
    }
}

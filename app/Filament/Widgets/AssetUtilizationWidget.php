<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\AssetStatus;
use App\Models\Asset;
use Filament\Widgets\ChartWidget;

/**
 * Asset Utilization Widget
 *
 * Displays asset status distribution for admin dashboard.
 *
 * @trace D03-FR-013.1 (Analytics Dashboard)
 */
class AssetUtilizationWidget extends ChartWidget
{
    protected static ?string $heading = 'Asset Status Distribution';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $statusCounts = Asset::query()
            ->select('status', \DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $statuses = collect(AssetStatus::cases());

        return [
            'datasets' => [
                [
                    'data' => $statuses->map(fn($s) => $statusCounts[$s->value] ?? 0)->values(),
                    'backgroundColor' => [
                        '#10b981', // available - green
                        '#f59e0b', // loaned - amber
                        '#3b82f6', // maintenance - blue
                        '#ef4444', // retired - red
                    ],
                ],
            ],
            'labels' => $statuses->map(fn($s) => $s->label())->values()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

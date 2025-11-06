<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\HelpdeskTicket;
use Filament\Widgets\ChartWidget;

/**
 * Resolution Time Chart Widget
 *
 * Displays average resolution times by category.
 *
 * @see D03 Software Requirements Specification - Requirements 8.1, 8.2
 */
class ResolutionTimeChart extends ChartWidget
{
    protected ?string $heading = 'Average Resolution Time by Category (Hours)';

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '300s';

    protected function getData(): array
    {
        $data = HelpdeskTicket::whereNotNull('resolved_at')
            ->whereNotNull('category_id')
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(function ($tickets) {
                $avgHours = $tickets->map(function ($ticket) {
                    return $ticket->created_at->diffInHours($ticket->resolved_at);
                })->average();

                return [
                    'category' => $tickets->first()->category->name ?? 'Unknown',
                    'avg_hours' => round($avgHours, 1),
                ];
            })
            ->sortBy('avg_hours')
            ->values();

        return [
            'datasets' => [
                [
                    'label' => 'Average Hours',
                    'data' => $data->pluck('avg_hours')->toArray(),
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                    ],
                ],
            ],
            'labels' => $data->pluck('category')->toArray(),
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
                ],
            ],
        ];
    }
}

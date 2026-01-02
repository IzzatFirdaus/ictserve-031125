<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\WidgetMetadata;
use App\Filament\Widgets\Concerns\HandlesEmptyChartData;
use App\Models\HelpdeskTicket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Collection;

/**
 * Resolution Time Chart Widget
 *
 * Displays average resolution times by category.
 *
 * @see D03 Software Requirements Specification - Requirements 8.1, 8.2
 */
class ResolutionTimeChart extends ChartWidget
{
    use HandlesEmptyChartData;
    use WidgetMetadata;

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets, D12 WCAG 2.2 AA compliance';
    }

    protected ?string $heading = 'Purata Masa Penyelesaian mengikut Kategori (Jam)';

    // Allow this chart to be placed side-by-side with other widgets
    protected int|string|array $columnSpan = [
        'default' => 12,
        'lg' => 6,
    ];

    protected ?string $pollingInterval = '300s';

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        /** @var Collection<int, HelpdeskTicket> $tickets */
        $tickets = HelpdeskTicket::whereNotNull('resolved_at')
            ->whereNotNull('category_id')
            ->with('category')
            ->get();

        $grouped = $tickets->groupBy('category_id');

        $data = [];
        foreach ($grouped as $group) {
            $hours = [];
            foreach ($group as $ticket) {
                if ($ticket->created_at !== null && $ticket->resolved_at !== null) {
                    $hours[] = $ticket->created_at->diffInHours($ticket->resolved_at);
                }
            }

            $average = count($hours) > 0 ? round(array_sum($hours) / count($hours), 1) : 0;
            $firstTicket = $group->first();
            $categoryName = $firstTicket && $firstTicket->category ? $firstTicket->category->name : 'Tidak Diketahui';
            $data[] = [
                'category' => $categoryName,
                'avg_hours' => $average,
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Purata Jam',
                    'data' => array_column($data, 'avg_hours'),
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                    ],
                ],
            ],
            'labels' => array_column($data, 'category'),
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

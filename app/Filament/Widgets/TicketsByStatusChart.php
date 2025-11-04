<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\HelpdeskTicket;
use Filament\Widgets\ChartWidget;

/**
 * Tickets By Status Chart Widget
 *
 * Displays distribution of tickets by status using WCAG 2.2 AA compliant colors.
 * Provides visual breakdown of ticket statuses for quick insights.
 *
 * @trace Requirements: Requirement 3.2
 */
class TicketsByStatusChart extends ChartWidget
{
    protected ?string $heading = 'Tiket Mengikut Status';

    protected static ?int $sort = 2;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $statuses = [
            'open' => 'Terbuka',
            'assigned' => 'Ditugaskan',
            'in_progress' => 'Dalam Tindakan',
            'pending_user' => 'Menunggu Pengadu',
            'resolved' => 'Selesai',
            'closed' => 'Ditutup',
        ];

        $data = [];
        $labels = [];

        foreach ($statuses as $status => $label) {
            $count = HelpdeskTicket::where('status', $status)->count();
            $data[] = $count;
            $labels[] = $label;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Bilangan Tiket',
                    'data' => $data,
                    // WCAG 2.2 AA compliant colors
                    'backgroundColor' => [
                        'rgba(128, 128, 128, 0.7)', // gray - open
                        'rgba(0, 86, 179, 0.7)',    // primary - assigned (#0056b3)
                        'rgba(255, 140, 0, 0.7)',   // warning - in_progress (#ff8c00)
                        'rgba(108, 117, 125, 0.7)', // secondary - pending_user
                        'rgba(25, 135, 84, 0.7)',   // success - resolved (#198754)
                        'rgba(128, 128, 128, 0.7)', // gray - closed
                    ],
                    'borderColor' => [
                        'rgba(128, 128, 128, 1)',
                        'rgba(0, 86, 179, 1)',
                        'rgba(255, 140, 0, 1)',
                        'rgba(108, 117, 125, 1)',
                        'rgba(25, 135, 84, 1)',
                        'rgba(128, 128, 128, 1)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class UnifiedAnalyticsChart extends ChartWidget
{
    protected ?string $pollingInterval = '300s';

    protected ?string $heading = 'Analitik Bulanan (6 Bulan Terakhir)';

    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $months = collect(range(5, 0))
            ->map(fn (int $offset) => Carbon::now()->subMonths($offset)->startOfMonth())
            ->values();

        $startMonth = $months->first();
        if (! $startMonth instanceof Carbon) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $tickets = [];
        $loans = [];
        $labels = [];

        foreach ($months as $month) {
            $key = $month->format('Y-m');
            $ticketCounts = (int) HelpdeskTicket::whereDate('created_at', '>=', $month->startOfMonth()->toDateString())
                ->whereDate('created_at', '<=', $month->endOfMonth()->toDateString())
                ->count();
            $loanCountsValue = (int) LoanApplication::whereDate('created_at', '>=', $month->startOfMonth()->toDateString())
                ->whereDate('created_at', '<=', $month->endOfMonth()->toDateString())
                ->count();
            $labels[] = $month->translatedFormat('M Y');
            $tickets[] = $ticketCounts;
            $loans[] = $loanCountsValue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tiket Helpdesk',
                    'data' => $tickets,
                    'borderColor' => '#0056b3',
                    'backgroundColor' => 'rgba(0, 86, 179, 0.15)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Permohonan Pinjaman',
                    'data' => $loans,
                    'borderColor' => '#198754',
                    'backgroundColor' => 'rgba(25, 135, 84, 0.15)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }
}

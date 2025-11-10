<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\LoanApplication;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

/**
 * Loan Analytics Widget
 *
 * Displays loan statistics and trends for admin dashboard.
 *
 * @trace D03-FR-013.1 (Analytics Dashboard)
 */
class LoanAnalyticsWidget extends ChartWidget
{
    protected ?string $heading = 'Loan Applications Trend';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $months = collect(range(0, 5))->map(fn ($i) => now()->subMonths($i)->format('M Y'))->reverse();

        $data = LoanApplication::query()
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        return [
            'datasets' => [
                [
                    'label' => 'Applications',
                    'data' => $months->map(fn ($m) => $data[now()->parse($m)->format('Y-m')] ?? 0)->values(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $months->values()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

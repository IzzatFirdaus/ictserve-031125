<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\WidgetMetadata;
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
    use WidgetMetadata;

    protected ?string $heading = 'Trend Permohonan Pinjaman';

    protected static ?int $sort = 2;

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets, D12 WCAG 2.2 AA compliance';
    }

    protected function getData(): array
    {
        $months = collect(range(0, 5))->map(fn ($i) => now()->subMonths($i)->format('M Y'))->reverse();

        // Use database-agnostic date formatting
        $dateFormat = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : 'DATE_FORMAT(created_at, "%Y-%m")';

        $data = LoanApplication::query()
            ->select(
                DB::raw("{$dateFormat} as month"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        return [
            'datasets' => [
                [
                    'label' => 'Permohonan',
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

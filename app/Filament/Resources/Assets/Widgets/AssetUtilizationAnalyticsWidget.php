<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Widgets;

use App\Models\Asset;
use App\Services\AssetUtilizationService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Asset Utilization Analytics Widget
 *
 * Displays comprehensive utilization metrics for an asset.
 *
 * @trace Requirements 3.5
 */
class AssetUtilizationAnalyticsWidget extends BaseWidget
{
    public ?Asset $record = null;

    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        if (! $this->record) {
            return [];
        }

        $service = app(AssetUtilizationService::class);
        $metrics = $service->calculateUtilizationMetrics($this->record);

        return [
            Stat::make('Kekerapan Pinjaman', number_format($metrics['loan_frequency'], 2).' / bulan')
                ->description('Purata pinjaman setiap bulan')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info')
                ->chart($this->getLoanFrequencyChart()),

            Stat::make('Tempoh Pinjaman Purata', number_format($metrics['average_loan_duration'], 1).' hari')
                ->description('Purata tempoh setiap pinjaman')
                ->descriptionIcon('heroicon-o-clock')
                ->color('success'),

            Stat::make('Jumlah Pinjaman', $metrics['total_loans'])
                ->description($metrics['active_loans'].' pinjaman aktif')
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color($metrics['active_loans'] > 0 ? 'warning' : 'success'),

            Stat::make('Kadar Penggunaan', number_format($metrics['utilization_rate'], 1).'%')
                ->description('Peratusan masa dipinjam')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color($this->getUtilizationColor($metrics['utilization_rate']))
                ->chart($this->getUtilizationChart()),

            Stat::make('Ketersediaan', number_format($metrics['availability_percentage'], 1).'%')
                ->description('Peratusan masa tersedia')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color($metrics['availability_percentage'] > 70 ? 'success' : 'warning'),

            Stat::make('Kekerapan Penyelenggaraan', number_format($metrics['maintenance_frequency'], 2).' / tahun')
                ->description('Purata tiket penyelenggaraan setahun')
                ->descriptionIcon('heroicon-o-wrench-screwdriver')
                ->color($metrics['maintenance_frequency'] > 2 ? 'danger' : 'success'),

            Stat::make('Pinjaman Terakhir', $metrics['last_loan_date'] ?? 'Tiada')
                ->description($metrics['next_available_date'] ? 'Tersedia: '.$metrics['next_available_date'] : 'Tersedia sekarang')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color($metrics['next_available_date'] ? 'warning' : 'success'),
        ];
    }

    protected function getLoanFrequencyChart(): array
    {
        if (! $this->record) {
            return [];
        }

        // Get loan counts for last 6 months
        $loanCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = $this->record->loanItems()
                ->whereHas('loanApplication', function ($query) use ($month) {
                    $query->whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month);
                })
                ->count();
            $loanCounts[] = $count;
        }

        return $loanCounts;
    }

    protected function getUtilizationChart(): array
    {
        if (! $this->record) {
            return [];
        }

        // Get utilization percentage for last 6 months
        $utilizationData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $totalDays = $monthStart->diffInDays($monthEnd);
            $loanedDays = $this->record->loanItems()
                ->whereHas('loanApplication', function ($query) use ($monthStart, $monthEnd) {
                    $query->where(function ($q) use ($monthStart, $monthEnd) {
                        $q->whereBetween('loan_start_date', [$monthStart, $monthEnd])
                            ->orWhereBetween('loan_end_date', [$monthStart, $monthEnd]);
                    });
                })
                ->with('loanApplication')
                ->get()
                ->sum(function ($loanItem) use ($monthStart, $monthEnd) {
                    $start = max($loanItem->loanApplication->loan_start_date, $monthStart);
                    $end = min($loanItem->loanApplication->loan_end_date ?? now(), $monthEnd);

                    return $start->diffInDays($end);
                });

            $utilizationData[] = $totalDays > 0 ? round(($loanedDays / $totalDays) * 100) : 0;
        }

        return $utilizationData;
    }

    protected function getUtilizationColor(float $rate): string
    {
        return match (true) {
            $rate >= 80 => 'danger',
            $rate >= 60 => 'warning',
            $rate >= 40 => 'info',
            default => 'success',
        };
    }
}

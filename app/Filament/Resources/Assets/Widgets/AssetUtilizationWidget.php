<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Widgets;

use App\Models\Asset;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetUtilizationWidget extends BaseWidget
{
    /** @var int|string|array<string, int|null> */
    // On large screens show side-by-side with other charts when possible
    protected int|string|array $columnSpan = [
        'default' => 12,
        'lg' => 6,
    ];

    public ?int $assetId = null;

    public function mount(?int $assetId = null): void
    {
        $recordId = $assetId ?? request()->route('record');
        $this->assetId = is_numeric($recordId) ? (int) $recordId : null;
    }

    protected function getStats(): array
    {
        $asset = Asset::with(['loanItems.loanApplication'])->find($this->assetId);

        if (! $asset) {
            return [];
        }

        $totalLoans = $asset->loanItems()->count();
        $activeLoans = $asset->loanItems()
            ->whereHas('loanApplication', function ($query) {
                $query->where('status', 'active');
            })
            ->count();

        $daysLoaned = $asset->loanItems()
            ->whereHas('loanApplication', function ($query) {
                $query->where('start_date', '>=', now()->subDays(90))
                    ->whereIn('status', ['approved', 'active', 'completed']);
            })
            ->get()
            ->sum(function ($item) {
                $start = Carbon::parse($item->loanApplication->start_date);
                $end = Carbon::parse($item->loanApplication->end_date);

                return $start->diffInDays($end) + 1;
            });

        $utilizationRate = $daysLoaned > 0 ? round(($daysLoaned / 90) * 100, 1) : 0;

        $avgDuration = $asset->loanItems()
            ->whereHas('loanApplication', function ($query) {
                $query->whereIn('status', ['completed']);
            })
            ->get()
            ->avg(function ($item) {
                $start = Carbon::parse($item->loanApplication->start_date);
                $end = Carbon::parse($item->loanApplication->end_date);

                return $start->diffInDays($end) + 1;
            });

        $avgDuration = $avgDuration ? round($avgDuration, 1) : 0;
        $maintenanceTickets = $asset->helpdeskTickets()->count();

        return [
            Stat::make('Jumlah Pinjaman', $totalLoans)
                ->description('Jumlah pinjaman sepanjang masa')
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('primary'),

            Stat::make('Pinjaman Aktif', $activeLoans)
                ->description('Sedang dipinjam')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color($activeLoans > 0 ? 'warning' : 'success'),

            Stat::make('Kadar Penggunaan', $utilizationRate.'%')
                ->description('90 hari terakhir')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color($utilizationRate > 70 ? 'success' : ($utilizationRate > 40 ? 'warning' : 'danger')),

            Stat::make('Purata Tempoh Pinjaman', $avgDuration.' hari')
                ->description('Purata tempoh pinjaman')
                ->descriptionIcon('heroicon-o-clock')
                ->color('info'),

            Stat::make('Tiket Penyelenggaraan', $maintenanceTickets)
                ->description('Tiket helpdesk berkaitan')
                ->descriptionIcon('heroicon-o-wrench-screwdriver')
                ->color($maintenanceTickets > 5 ? 'danger' : ($maintenanceTickets > 2 ? 'warning' : 'success')),

            Stat::make('Nilai Semasa', 'RM '.number_format($asset->current_value, 2))
                ->description('Nilai selepas susut nilai')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('secondary'),
        ];
    }
}

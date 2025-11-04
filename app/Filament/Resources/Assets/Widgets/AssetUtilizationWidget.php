<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Widgets;

use App\Models\Asset;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetUtilizationWidget extends BaseWidget
{
    public ?int $assetId = null;

    public function mount(?int $assetId = null): void
    {
        $this->assetId = $assetId ?? request()->route('record');
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
            Stat::make(__('Total Loans'), $totalLoans)
                ->description(__('All time loan count'))
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('primary'),

            Stat::make(__('Active Loans'), $activeLoans)
                ->description(__('Currently on loan'))
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color($activeLoans > 0 ? 'warning' : 'success'),

            Stat::make(__('Utilization Rate'), $utilizationRate.'%')
                ->description(__('Last 90 days'))
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color($utilizationRate > 70 ? 'success' : ($utilizationRate > 40 ? 'warning' : 'danger')),

            Stat::make(__('Avg Loan Duration'), $avgDuration.' '.__('days'))
                ->description(__('Average loan period'))
                ->descriptionIcon('heroicon-o-clock')
                ->color('info'),

            Stat::make(__('Maintenance Tickets'), $maintenanceTickets)
                ->description(__('Related helpdesk tickets'))
                ->descriptionIcon('heroicon-o-wrench-screwdriver')
                ->color($maintenanceTickets > 5 ? 'danger' : ($maintenanceTickets > 2 ? 'warning' : 'success')),

            Stat::make(__('Current Value'), 'RM '.number_format($asset->current_value, 2))
                ->description(__('Depreciated value'))
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('secondary'),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\HelpdeskTicket;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HelpdeskStatsOverview extends BaseWidget
{
    protected ?string $pollingInterval = '300s';

    protected function getStats(): array
    {
        $totalTickets = HelpdeskTicket::count();
        $openTickets = HelpdeskTicket::whereNotIn('status', ['resolved', 'closed'])->count();
        $resolvedThisMonth = HelpdeskTicket::where('status', 'resolved')
            ->whereMonth('resolved_at', now()->month)
            ->whereYear('resolved_at', now()->year)
            ->count();

        $slaTotal = HelpdeskTicket::whereNotNull('sla_resolution_due_at')->count();
        $slaMet = HelpdeskTicket::whereNotNull('sla_resolution_due_at')
            ->whereNotNull('resolved_at')
            ->whereColumn('resolved_at', '<=', 'sla_resolution_due_at')
            ->count();
        $slaRate = $slaTotal > 0 ? round(($slaMet / $slaTotal) * 100, 1) : 100.0;

        return [
            Stat::make('Jumlah Tiket', (string) $totalTickets)
                ->description('Semua tiket dalam sistem')
                ->color(Color::Amber),
            Stat::make('Tiket Aktif', (string) $openTickets)
                ->description('Belum ditutup / diselesaikan')
                ->color(Color::Blue),
            Stat::make('SLA Dipenuhi', $slaRate.'%')
                ->description('Kadar pematuhan SLA')
                ->color($slaRate >= 90 ? Color::Emerald : Color::Rose),
            Stat::make('Selesai Bulan Ini', (string) $resolvedThisMonth)
                ->description('Tiket diselesaikan bulan semasa')
                ->color(Color::Green),
        ];
    }
}

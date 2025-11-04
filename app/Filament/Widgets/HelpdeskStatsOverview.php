<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\HelpdeskTicket;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Helpdesk Statistics Overview Widget
 *
 * Displays key metrics for helpdesk tickets including guest vs authenticated submission ratios.
 * Uses WCAG 2.2 AA compliant colors for all indicators.
 *
 * @trace Requirements: Requirement 3.2
 */
class HelpdeskStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalTickets = HelpdeskTicket::count();
        $guestTickets = HelpdeskTicket::whereNull('user_id')->count();
        $authenticatedTickets = HelpdeskTicket::whereNotNull('user_id')->count();
        $openTickets = HelpdeskTicket::where('status', 'open')->count();
        $resolvedTickets = HelpdeskTicket::where('status', 'resolved')->count();
        $slaBreached = HelpdeskTicket::whereNotNull('sla_resolution_due_at')
            ->where('sla_resolution_due_at', '<', now())
            ->whereNotIn('status', ['resolved', 'closed'])
            ->count();

        $guestPercentage = $totalTickets > 0 ? round(($guestTickets / $totalTickets) * 100, 1) : 0;
        $authenticatedPercentage = $totalTickets > 0 ? round(($authenticatedTickets / $totalTickets) * 100, 1) : 0;

        return [
            Stat::make('Jumlah Tiket', $totalTickets)
                ->description('Semua tiket dalam sistem')
                ->descriptionIcon('heroicon-o-inbox-stack')
                ->color('primary')
                ->chart($this->getTicketTrendData()),

            Stat::make('Tiket Tetamu', $guestTickets)
                ->description("{$guestPercentage}% daripada jumlah tiket")
                ->descriptionIcon('heroicon-o-user')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ])
                ->url(route('filament.admin.resources.helpdesk.helpdesk-tickets.index', [
                    'tableFilters' => ['submission_type' => ['value' => 'guest']],
                ])),

            Stat::make('Tiket Berdaftar', $authenticatedTickets)
                ->description("{$authenticatedPercentage}% daripada jumlah tiket")
                ->descriptionIcon('heroicon-o-user-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ])
                ->url(route('filament.admin.resources.helpdesk.helpdesk-tickets.index', [
                    'tableFilters' => ['submission_type' => ['value' => 'authenticated']],
                ])),

            Stat::make('Tiket Terbuka', $openTickets)
                ->description('Menunggu tindakan')
                ->descriptionIcon('heroicon-o-clock')
                ->color('gray')
                ->url(route('filament.admin.resources.helpdesk.helpdesk-tickets.index', [
                    'tableFilters' => ['status' => ['value' => 'open']],
                ])),

            Stat::make('Tiket Selesai', $resolvedTickets)
                ->description('Telah diselesaikan')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->url(route('filament.admin.resources.helpdesk.helpdesk-tickets.index', [
                    'tableFilters' => ['status' => ['value' => 'resolved']],
                ])),

            Stat::make('SLA Melebihi', $slaBreached)
                ->description('Memerlukan perhatian segera')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->url(route('filament.admin.resources.helpdesk.helpdesk-tickets.index', [
                    'tableFilters' => ['sla_breached' => ['isActive' => true]],
                ])),
        ];
    }

    /**
     * Get ticket trend data for the last 7 days
     */
    protected function getTicketTrendData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $count = HelpdeskTicket::whereDate('created_at', $date)->count();
            $data[] = $count;
        }

        return $data;
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\Helpdesk\HelpdeskTicketResource;
use App\Models\HelpdeskTicket;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\On;

/**
 * Helpdesk Statistics Overview Widget
 *
 * Displays key metrics for helpdesk tickets including:
 * - Open/In-Progress/Resolved counts
 * - SLA compliance percentage
 * - Real-time updates via Laravel Reverb WebSocket
 *
 * Uses WCAG 2.2 AA compliant colors for all indicators with 5-minute caching strategy.
 *
 * @trace Requirements: Requirement 5.1 (Admin Helpdesk Management)
 *
 * @see D04 §3.2 Dashboard widgets
 * @see D12 UI/UX Design Guide - Compliant color palette
 * @see D16 Broadcasting Setup - Laravel Reverb integration
 */
class HelpdeskStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false; // Critical widget - load immediately

    protected ?string $pollingInterval = '30s'; // Fallback polling for real-time updates

    protected int|string|array $columnSpan = 'full'; // Full width for stats overview

    protected array|int|null $columns = 2; // 2-column grid layout

    /**
     * Listen for ticket status changes via Laravel Reverb WebSocket
     * Refreshes widget stats when a ticket status is updated
     */
    #[On('echo-private:admin-dashboard,TicketStatsUpdated')]
    public function refreshOnTicketUpdate(): void
    {
        // Clear cache to force fresh data
        Cache::forget('dashboard:helpdesk-stats');
    }

    /**
     * Listen for new ticket creation via Laravel Reverb WebSocket
     */
    #[On('echo-private:admin-dashboard,TicketCreated')]
    public function refreshOnTicketCreated(): void
    {
        Cache::forget('dashboard:helpdesk-stats');
    }

    /**
     * Listen for SLA breach events via Laravel Reverb WebSocket
     */
    #[On('echo-private:admin-dashboard,SLABreachDetected')]
    public function refreshOnSLABreach(): void
    {
        Cache::forget('dashboard:helpdesk-stats');
    }

    /**
     * @return array<int, Stat>
     */
    protected function getStats(): array
    {
        $stats = Cache::remember('dashboard:helpdesk-stats', 300, fn () => $this->calculateStats());

        /** @var array<int, Stat> $stats */
        return $stats;
    }

    /**
     * Calculate helpdesk statistics with SLA compliance metrics
     *
     * @return array<Stat>
     */
    protected function calculateStats(): array
    {
        // Optimized: Single query with selectRaw for counts
        // Includes Open/In-Progress/Resolved counts per Requirement 5.1
        /** @var HelpdeskTicket|null $stats */
        $stats = HelpdeskTicket::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN user_id IS NULL THEN 1 ELSE 0 END) as guest,
            SUM(CASE WHEN user_id IS NOT NULL THEN 1 ELSE 0 END) as authenticated,
            SUM(CASE WHEN status = "open" THEN 1 ELSE 0 END) as open,
            SUM(CASE WHEN status = "in_progress" THEN 1 ELSE 0 END) as in_progress,
            SUM(CASE WHEN status = "resolved" THEN 1 ELSE 0 END) as resolved
        ')->first();

        $statsArray = $stats instanceof HelpdeskTicket ? $stats->toArray() : [];

        $totalTickets = isset($statsArray['total']) && is_numeric($statsArray['total']) ? (int) $statsArray['total'] : 0;
        $guestTickets = isset($statsArray['guest']) && is_numeric($statsArray['guest']) ? (int) $statsArray['guest'] : 0;
        $authenticatedTickets = isset($statsArray['authenticated']) && is_numeric($statsArray['authenticated']) ? (int) $statsArray['authenticated'] : 0;
        $openTickets = isset($statsArray['open']) && is_numeric($statsArray['open']) ? (int) $statsArray['open'] : 0;
        $inProgressTickets = isset($statsArray['in_progress']) && is_numeric($statsArray['in_progress']) ? (int) $statsArray['in_progress'] : 0;
        $resolvedTickets = isset($statsArray['resolved']) && is_numeric($statsArray['resolved']) ? (int) $statsArray['resolved'] : 0;

        $slaBreached = HelpdeskTicket::whereNotNull('sla_resolution_due_at')
            ->where('sla_resolution_due_at', '<', now())
            ->whereNotIn('status', ['resolved', 'closed'])
            ->count();

        $guestPercentage = $totalTickets > 0 ? round(($guestTickets / $totalTickets) * 100, 1) : 0;
        $authenticatedPercentage = $totalTickets > 0 ? round(($authenticatedTickets / $totalTickets) * 100, 1) : 0;

        // Calculate SLA compliance rate
        $totalWithSLA = HelpdeskTicket::whereNotNull('sla_resolution_due_at')->count();
        $slaCompliant = $totalWithSLA - $slaBreached;
        $slaComplianceRate = $totalWithSLA > 0
            ? round(($slaCompliant / $totalWithSLA) * 100, 1)
            : 100;

        return [
            Stat::make(__('widgets.total_tickets'), $totalTickets)
                ->description(__('widgets.all_tickets_in_system'))
                ->descriptionIcon('heroicon-o-inbox-stack')
                ->color('primary')
                ->chart($this->getTicketTrendData()),

            Stat::make(__('widgets.guest_tickets'), $guestTickets)
                ->description(__('widgets.of_total_tickets', ['percentage' => $guestPercentage]))
                ->descriptionIcon('heroicon-o-user')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ])
                ->url($this->getHelpdeskIndexUrl()),

            Stat::make(__('widgets.authenticated_tickets'), $authenticatedTickets)
                ->description(__('widgets.of_total_tickets', ['percentage' => $authenticatedPercentage]))
                ->descriptionIcon('heroicon-o-user-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ])
                ->url($this->getHelpdeskIndexUrl()),

            Stat::make(__('widgets.open_tickets'), $openTickets)
                ->description(__('widgets.waiting_for_action'))
                ->descriptionIcon('heroicon-o-clock')
                ->color('gray')
                ->url($this->getHelpdeskIndexUrl()),

            Stat::make(__('widgets.in_progress_tickets'), $inProgressTickets)
                ->description(__('widgets.currently_being_worked_on'))
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('info')
                ->url($this->getHelpdeskIndexUrl()),

            Stat::make(__('widgets.resolved_tickets'), $resolvedTickets)
                ->description(__('widgets.has_been_resolved'))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->url($this->getHelpdeskIndexUrl()),

            Stat::make(__('widgets.sla_breached'), $slaBreached)
                ->description(__('widgets.requires_immediate_attention'))
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->url($this->getHelpdeskIndexUrl()),

            Stat::make(__('widgets.sla_compliance'), "{$slaComplianceRate}%")
                ->description(__('widgets.of_tickets_comply_with_sla', ['compliant' => $slaCompliant, 'total' => $totalWithSLA]))
                ->descriptionIcon('heroicon-o-shield-check')
                ->color($slaComplianceRate >= 90 ? 'success' : ($slaComplianceRate >= 75 ? 'warning' : 'danger'))
                ->chart($this->getSLAComplianceTrendData()),
        ];
    }

    /**
     * Get the helpdesk tickets index URL safely
     * Returns null if route is not registered (e.g., in tests)
     */
    protected function getHelpdeskIndexUrl(): ?string
    {
        if (Route::has('filament.admin.operations.resources.helpdesk.helpdesk-tickets.index')) {
            return route('filament.admin.operations.resources.helpdesk.helpdesk-tickets.index');
        }

        try {
            return HelpdeskTicketResource::getUrl('index');
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Get ticket trend data for the last 7 days
     *
     * @return array<int, int>
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

    /**
     * Get SLA compliance trend data for the last 7 days
     *
     * @return array<int, float|int>
     */
    protected function getSLAComplianceTrendData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $totalWithSLA = HelpdeskTicket::whereNotNull('sla_resolution_due_at')
                ->whereDate('created_at', '<=', $date)
                ->count();

            if ($totalWithSLA === 0) {
                $data[] = 100;

                continue;
            }

            $breached = HelpdeskTicket::whereNotNull('sla_resolution_due_at')
                ->where('sla_resolution_due_at', '<', $date->endOfDay())
                ->whereNotIn('status', ['resolved', 'closed'])
                ->whereDate('created_at', '<=', $date)
                ->count();

            $compliant = $totalWithSLA - $breached;
            $complianceRate = round(($compliant / $totalWithSLA) * 100, 1);
            $data[] = $complianceRate;
        }

        return $data;
    }
}

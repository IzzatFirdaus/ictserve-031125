<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

/**
 * Critical Alerts Widget
 *
 * Displays notification badges for SLA breaches, overdue returns, and pending approvals.
 * Provides click-to-action functionality with real-time updates.
 *
 * @trace Requirements 6.5
 */
class CriticalAlertsWidget extends Widget
{
    protected string $view = 'filament.widgets.critical-alerts';

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '60s';

    protected function getViewData(): array
    {
        return [
            'alerts' => $this->getCriticalAlerts(),
        ];
    }

    protected function getCriticalAlerts(): Collection
    {
        $alerts = collect();

        // SLA Breaches (15-minute detection)
        $slaBreaches = HelpdeskTicket::query()
            ->where(function ($query) {
                $query->where('sla_response_due_at', '<', now())
                    ->whereNull('responded_at')
                    ->orWhere(function ($q) {
                        $q->where('sla_resolution_due_at', '<', now())
                            ->whereNull('resolved_at');
                    });
            })
            ->where('status', '!=', 'closed')
            ->count();

        if ($slaBreaches > 0) {
            $alerts->push([
                'type' => 'sla_breach',
                'title' => 'SLA Breaches',
                'count' => $slaBreaches,
                'message' => "{$slaBreaches} ticket(s) have breached SLA",
                'color' => 'danger',
                'icon' => 'heroicon-o-exclamation-triangle',
                'url' => route('filament.admin.resources.helpdesk.helpdesk-tickets.index', ['tableFilters' => ['sla_breach' => true]]),
            ]);
        }

        // Overdue Returns (24 hours before due date)
        $overdueReturns = LoanApplication::query()
            ->where('status', 'in_use')
            ->where('expected_return_date', '<', now()->addDay())
            ->count();

        if ($overdueReturns > 0) {
            $alerts->push([
                'type' => 'overdue_return',
                'title' => 'Overdue Returns',
                'count' => $overdueReturns,
                'message' => "{$overdueReturns} loan(s) overdue or due soon",
                'color' => 'warning',
                'icon' => 'heroicon-o-clock',
                'url' => route('filament.admin.resources.loans.loan-applications.index', ['tableFilters' => ['overdue' => true]]),
            ]);
        }

        // Pending Approvals (48 hours without response)
        $pendingApprovals = LoanApplication::query()
            ->where('status', 'pending_approval')
            ->where('created_at', '<', now()->subHours(48))
            ->count();

        if ($pendingApprovals > 0) {
            $alerts->push([
                'type' => 'pending_approval',
                'title' => 'Pending Approvals',
                'count' => $pendingApprovals,
                'message' => "{$pendingApprovals} approval(s) pending >48h",
                'color' => 'info',
                'icon' => 'heroicon-o-document-check',
                'url' => route('filament.admin.resources.loans.loan-applications.index', ['tableFilters' => ['status' => 'pending_approval']]),
            ]);
        }

        return $alerts;
    }
}

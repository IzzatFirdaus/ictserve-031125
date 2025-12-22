<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\Helpdesk\HelpdeskTicketResource;
use App\Filament\Resources\Loans\LoanApplicationResource;
use App\Filament\Traits\CacheableWidget;
use App\Filament\Traits\WidgetMetadata;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

/**
 * Critical Alerts Widget
 *
 * Displays notification badges for SLA breaches, overdue returns, and pending approvals.
 * Provides click-to-action functionality with real-time updates.
 *
 * Features:
 * - SLA breach detection (15-minute threshold)
 * - Overdue return alerts (24 hours before due date)
 * - Pending approval notifications (48 hours without response)
 * - Click-to-action navigation to filtered resource lists
 * - Real-time updates with 60-second polling
 *
 * @trace Requirements: 8.4, 10.2, 10.3
 *
 * @see D04 §3.2 Dashboard widgets
 */
class CriticalAlertsWidget extends Widget
{
    use CacheableWidget;
    use WidgetMetadata;

    protected static bool $isLazy = true; // Non-critical - lazy load

    protected string $view = 'filament.widgets.critical-alerts';

    protected int|string|array $columnSpan = 'full';

    /**
     * Polling interval for real-time alert updates (60 seconds)
     * More frequent than dashboard stats for critical alerts
     */
    protected ?string $pollingInterval = '60s';

    /**
     * Sort order - display after overview stats
     */
    protected static ?int $sort = -5;

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets, D16 Broadcasting Setup - Laravel Reverb integration';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'alerts' => $this->getCriticalAlerts(),
        ];
    }

    /**
     * Get critical alerts with caching for performance
     *
     * @return Collection<int, array<string, mixed>>
     */
    protected function getCriticalAlerts(): Collection
    {
        return $this->cached(function () {
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
                    'title' => __('Pelanggaran SLA'),
                    'count' => $slaBreaches,
                    'message' => __(':count tiket telah melanggar SLA', ['count' => $slaBreaches]),
                    'color' => 'danger',
                    'icon' => 'heroicon-o-exclamation-triangle',
                    'url' => $this->getHelpdeskIndexUrl(['tableFilters' => ['sla_breach' => true]]),
                ]);
            }

            // Overdue Returns (24 hours before due date)
            $overdueReturns = LoanApplication::query()
                ->where('status', 'in_use')
                ->where('loan_end_date', '<', now()->addDay())
                ->count();

            if ($overdueReturns > 0) {
                $alerts->push([
                    'type' => 'overdue_return',
                    'title' => __('Pemulangan Tertunggak'),
                    'count' => $overdueReturns,
                    'message' => __(':count pinjaman tertunggak atau hampir tamat', ['count' => $overdueReturns]),
                    'color' => 'warning',
                    'icon' => 'heroicon-o-clock',
                    'url' => $this->getLoanApplicationIndexUrl(['tableFilters' => ['overdue' => true]]),
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
                    'title' => __('Kelulusan Tertunda'),
                    'count' => $pendingApprovals,
                    'message' => __(':count kelulusan tertunda >48 jam', ['count' => $pendingApprovals]),
                    'color' => 'info',
                    'icon' => 'heroicon-o-document-check',
                    'url' => $this->getLoanApplicationIndexUrl(['tableFilters' => ['status' => 'pending_approval']]),
                ]);
            }

            return $alerts;
        }, 'critical-alerts');
    }

    /**
     * Get the helpdesk tickets index URL safely
     *
     * @param  array<string, mixed>  $params
     */

    /**
     * @param  array<string, mixed>  $params
     */
    protected function getHelpdeskIndexUrl(array $params = []): ?string
    {
        if (Route::has('filament.admin.operations.resources.helpdesk.helpdesk-tickets.index')) {
            return route('filament.admin.operations.resources.helpdesk.helpdesk-tickets.index', $params);
        }

        try {
            return HelpdeskTicketResource::getUrl('index', $params);
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Get the loan applications index URL safely
     *
     * @param  array<string, mixed>  $params
     */

    /**
     * @param  array<string, mixed>  $params
     */
    protected function getLoanApplicationIndexUrl(array $params = []): ?string
    {
        if (Route::has('filament.admin.operations.resources.loans.loan-applications.index')) {
            return route('filament.admin.operations.resources.loans.loan-applications.index', $params);
        }

        try {
            return LoanApplicationResource::getUrl('index', $params);
        } catch (\Exception) {
            return null;
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\HelpdeskTicket;
use App\Services\DashboardService;

/**
 * Helpdesk Ticket Cache Observer
 *
 * Automatically clears dashboard cache when tickets are modified.
 */
class HelpdeskTicketCacheObserver
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    public function created(HelpdeskTicket $ticket): void
    {
        $this->dashboardService->clearHelpdeskCache();
    }

    public function updated(HelpdeskTicket $ticket): void
    {
        $this->dashboardService->clearHelpdeskCache();
    }

    public function deleted(HelpdeskTicket $ticket): void
    {
        $this->dashboardService->clearHelpdeskCache();
    }
}

<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\LoanApplication;
use App\Services\DashboardService;

/**
 * Loan Application Cache Observer
 *
 * Automatically clears dashboard cache when loan applications are modified.
 */
class LoanApplicationCacheObserver
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    public function created(LoanApplication $loan): void
    {
        $this->dashboardService->clearLoanCache();
    }

    public function updated(LoanApplication $loan): void
    {
        $this->dashboardService->clearLoanCache();
    }

    public function deleted(LoanApplication $loan): void
    {
        $this->dashboardService->clearLoanCache();
    }
}

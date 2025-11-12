<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

/**
 * Alert Service
 *
 * Manages configurable alerts for overdue returns and critical events.
 *
 * @trace D03-FR-013.4 (Configurable Alert System)
 */
class AlertService
{
    public function checkOverdueReturns(): Collection
    {
        return LoanApplication::query()
            ->where('status', 'in_use')
            ->where('loan_end_date', '<', now())
            ->with(['user', 'loanItems.asset'])
            ->get();
    }

    public function checkUpcomingReturns(int $daysThreshold = 3): Collection
    {
        return LoanApplication::query()
            ->where('status', 'in_use')
            ->whereBetween('loan_end_date', [now(), now()->addDays($daysThreshold)])
            ->with(['user', 'loanItems.asset'])
            ->get();
    }

    public function checkPendingApprovals(int $hoursThreshold = 48): Collection
    {
        return LoanApplication::query()
            ->whereIn('status', ['submitted', 'under_review'])
            ->where('created_at', '<', now()->subHours($hoursThreshold))
            ->with(['user'])
            ->get();
    }

    public function checkLowAssetAvailability(int $threshold = 2): array
    {
        // Get all categories with asset counts, then filter in PHP to avoid SQLite HAVING clause issues
        return \App\Models\AssetCategory::query()
            ->withCount(['assets' => fn($q) => $q->where('status', 'available')])
            ->get()
            ->filter(fn($category) => $category->assets_count <= $threshold)
            ->map(fn($category) => [
                'category' => $category->name,
                'available_count' => $category->assets_count,
                'threshold' => $threshold,
            ])
            ->values()
            ->toArray();
    }

    public function sendOverdueAlerts(): int
    {
        $overdueLoans = $this->checkOverdueReturns();
        $count = 0;

        foreach ($overdueLoans as $loan) {
            if ($loan->user) {
                // Send notification to user
                $count++;
            }
        }

        // Notify admins
        $this->notifyAdmins('overdue_loans', [
            'count' => $overdueLoans->count(),
            'loans' => $overdueLoans,
        ]);

        return $count;
    }

    public function sendUpcomingReturnReminders(): int
    {
        $upcomingReturns = $this->checkUpcomingReturns();
        $count = 0;

        foreach ($upcomingReturns as $loan) {
            if ($loan->user) {
                // Send reminder notification
                $count++;
            }
        }

        return $count;
    }

    protected function notifyAdmins(string $type, array $data): void
    {
        $admins = User::role(['admin', 'superuser'])->get();

        foreach ($admins as $admin) {
            // Send admin notification
        }
    }
}

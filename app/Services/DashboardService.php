<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LoanStatus;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\PortalActivity;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Dashboard Service
 *
 * Provides dashboard statistics and activity data for authenticated portal users.
 * Implements caching strategies for performance optimization.
 *
 * @see .kiro/specs/staff-dashboard-profile/design.md - Dashboard Service Design
 * @see .kiro/specs/staff-dashboard-profile/requirements.md - Requirements 1.1, 1.2, 1.5
 */
class DashboardService
{
    /**
     * Cache TTL for dashboard statistics (5 minutes)
     */
    private const STATISTICS_CACHE_TTL = 300;

    /**
     * Get dashboard statistics for user with caching
     *
     * @return array<string, int>
     */
    public function getStatistics(User $user): array
    {
        /** @var array<string, int> */
        $statistics = Cache::remember(
            "portal.statistics.{$user->id}",
            self::STATISTICS_CACHE_TTL,
            fn (): array => [
                'open_tickets' => $this->getOpenTicketsCount($user),
                'pending_loans' => $this->getPendingLoansCount($user),
                'overdue_items' => $this->getOverdueItemsCount($user),
                'available_assets' => $this->getAvailableAssetsCount(),
            ]
        );

        return $statistics;
    }

    /**
     * Get recent activity for user
     *
     * @return Collection<PortalActivity>
     */
    public function getRecentActivity(User $user, int $limit = 10): Collection
    {
        return PortalActivity::where('user_id', $user->id)
            ->with('subject')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get role-specific widgets for user
     *
     * @return array<string, mixed>
     */
    public function getRoleSpecificWidgets(User $user): array
    {
        $widgets = [];

        if ($user->isApprover()) {
            $widgets['pending_approvals'] = $this->getPendingApprovalsCount($user);
        }

        if ($user->hasRole('admin')) {
            $widgets['system_overview'] = $this->getSystemOverview();
        }

        if ($user->hasRole('superuser')) {
            $widgets['system_configuration'] = $this->getSystemConfiguration();
        }

        return $widgets;
    }

    /**
     * Get count of open tickets for user
     */
    private function getOpenTicketsCount(User $user): int
    {
        return HelpdeskTicket::where('user_id', $user->id)
            ->whereIn('status', ['open', 'assigned', 'in_progress'])
            ->count();
    }

    /**
     * Get count of pending loan applications for user
     */
    private function getPendingLoansCount(User $user): int
    {
        return LoanApplication::where('user_id', $user->id)
            ->whereIn('status', [LoanStatus::SUBMITTED, LoanStatus::APPROVED])
            ->count();
    }

    /**
     * Get count of overdue items for user
     */
    private function getOverdueItemsCount(User $user): int
    {
        return LoanApplication::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'issued', 'in_use', 'return_due'])
            ->where('loan_end_date', '<', now())
            ->whereNotIn('status', ['returned', 'completed'])
            ->count();
    }

    /**
     * Get count of available assets
     */
    private function getAvailableAssetsCount(): int
    {
        // This would query the Asset model when implemented
        // For now, return 0 as placeholder
        return 0;
    }

    /**
     * Get count of pending approvals for approver
     */
    private function getPendingApprovalsCount(User $user): int
    {
        return LoanApplication::where('status', LoanStatus::SUBMITTED)
            ->whereNull('approved_by_name')
            ->count();
    }

    /**
     * Get system overview for admin
     *
     * @return array<string, int>
     */
    private function getSystemOverview(): array
    {
        return [
            'total_users' => User::count(),
            'total_tickets' => HelpdeskTicket::count(),
            'total_loans' => LoanApplication::count(),
            'active_tickets' => HelpdeskTicket::whereIn('status', ['submitted', 'assigned', 'in_progress'])->count(),
        ];
    }

    /**
     * Get system configuration for superuser
     *
     * @return array<string, mixed>
     */
    private function getSystemConfiguration(): array
    {
        return [
            'cache_enabled' => config('cache.default') !== 'array',
            'queue_enabled' => config('queue.default') !== 'sync',
            'broadcasting_enabled' => config('broadcasting.default') !== 'null',
        ];
    }

    /**
     * Invalidate statistics cache for user
     */
    public function invalidateStatisticsCache(User $user): void
    {
        Cache::forget("portal.statistics.{$user->id}");
    }
}

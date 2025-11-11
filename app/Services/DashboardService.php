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
     * @return array<string, mixed>
     */
    public function getStatistics(User $user): array
    {
        /** @var array<string, mixed> */
        $statistics = Cache::remember(
            "portal.statistics.{$user->id}",
            self::STATISTICS_CACHE_TTL,
            fn (): array => [
                'summary' => [
                    'total_submissions' => $this->getTotalSubmissionsCount($user),
                    'pending_actions' => $this->getPendingActionsCount($user),
                    'recent_updates' => $this->getRecentUpdatesCount($user),
                    'profile_completeness' => $this->getProfileCompletenessPercentage($user),
                ],
                'helpdesk' => [
                    'total' => $this->getTotalHelpdeskTicketsCount($user),
                    'pending' => $this->getPendingHelpdeskTicketsCount($user),
                    'resolved' => $this->getResolvedHelpdeskTicketsCount($user),
                    'avg_resolution_time' => $this->getAvgResolutionTime($user),
                ],
                'loans' => [
                    'total' => $this->getTotalLoansCount($user),
                    'pending' => $this->getPendingLoansCount($user),
                    'approved' => $this->getApprovedLoansCount($user),
                    'avg_approval_time' => $this->getAvgApprovalTime($user),
                ],
                'activity' => $this->getRecentActivityData($user),
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
     * Get total submissions count (tickets + loans) for user
     */
    private function getTotalSubmissionsCount(User $user): int
    {
        $ticketsCount = HelpdeskTicket::where('user_id', $user->id)->count();
        $loansCount = LoanApplication::where('user_id', $user->id)->count();

        return $ticketsCount + $loansCount;
    }

    /**
     * Get pending actions count (open tickets + pending loans)
     */
    private function getPendingActionsCount(User $user): int
    {
        $openTickets = $this->getOpenTicketsCount($user);
        $pendingLoans = $this->getPendingLoansCount($user);

        return $openTickets + $pendingLoans;
    }

    /**
     * Get recent updates count (last 7 days)
     */
    private function getRecentUpdatesCount(User $user): int
    {
        $recentTickets = HelpdeskTicket::where('user_id', $user->id)
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        $recentLoans = LoanApplication::where('user_id', $user->id)
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        return $recentTickets + $recentLoans;
    }

    /**
     * Get profile completeness percentage
     */
    private function getProfileCompletenessPercentage(User $user): int
    {
        $fields = ['name', 'email', 'phone', 'department_id', 'position'];
        $filledFields = 0;

        foreach ($fields as $field) {
            if (! empty($user->$field)) {
                $filledFields++;
            }
        }

        return (int) round(($filledFields / count($fields)) * 100);
    }

    /**
     * Get total helpdesk tickets count for user
     */
    private function getTotalHelpdeskTicketsCount(User $user): int
    {
        return HelpdeskTicket::where('user_id', $user->id)->count();
    }

    /**
     * Get pending helpdesk tickets count
     */
    private function getPendingHelpdeskTicketsCount(User $user): int
    {
        return HelpdeskTicket::where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'assigned', 'in_progress'])
            ->count();
    }

    /**
     * Get resolved helpdesk tickets count
     */
    private function getResolvedHelpdeskTicketsCount(User $user): int
    {
        return HelpdeskTicket::where('user_id', $user->id)
            ->whereIn('status', ['resolved', 'closed'])
            ->count();
    }

    /**
     * Get average resolution time in hours
     */
    private function getAvgResolutionTime(User $user): ?int
    {
        $resolvedTickets = HelpdeskTicket::where('user_id', $user->id)
            ->whereIn('status', ['resolved', 'closed'])
            ->whereNotNull('resolved_at')
            ->get();

        if ($resolvedTickets->isEmpty()) {
            return null;
        }

        $totalHours = $resolvedTickets->sum(function ($ticket) {
            return $ticket->created_at->diffInHours($ticket->resolved_at);
        });

        return (int) round($totalHours / $resolvedTickets->count());
    }

    /**
     * Get total loans count for user
     */
    private function getTotalLoansCount(User $user): int
    {
        return LoanApplication::where('user_id', $user->id)->count();
    }

    /**
     * Get approved loans count
     */
    private function getApprovedLoansCount(User $user): int
    {
        return LoanApplication::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'issued', 'in_use', 'completed'])
            ->count();
    }

    /**
     * Get average approval time in hours
     */
    private function getAvgApprovalTime(User $user): ?int
    {
        $approvedLoans = LoanApplication::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'issued', 'in_use', 'completed'])
            ->whereNotNull('approved_at')
            ->get();

        if ($approvedLoans->isEmpty()) {
            return null;
        }

        $totalHours = $approvedLoans->sum(function ($loan) {
            return $loan->created_at->diffInHours($loan->approved_at);
        });

        return (int) round($totalHours / $approvedLoans->count());
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
     * Get recent activity data
     *
     * @return array<int, array<string, mixed>>
     */
    private function getRecentActivityData(User $user): array
    {
        $activities = PortalActivity::where('user_id', $user->id)
            ->with('subject')
            ->latest()
            ->limit(5)
            ->get();

        return $activities->map(function ($activity) {
            return [
                'type' => $activity->activity_type ?? 'unknown',
                'subject_title' => $activity->subject?->title ?? $activity->subject?->name ?? null,
                'created_at_human' => $activity->created_at->diffForHumans(),
            ];
        })->toArray();
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

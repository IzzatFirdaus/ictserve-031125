<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Dashboard Service
 *
 * Centralized cache management for Filament dashboard widgets.
 * Provides cache invalidation methods for real-time updates.
 *
 * @trace D11 (Technical Design - Performance Optimization)
 */
class DashboardService
{
    /**
     * Get user statistics with caching
     *
     * @return array<string, mixed>
     */
    public function getStatistics(User $user): array
    {
        /** @var array<string, mixed> $stats */
        $stats = Cache::remember("portal.statistics.{$user->id}", 300, function () use ($user): array {
            $helpdeskPending = HelpdeskTicket::where('user_id', $user->id)
                ->whereIn('status', ['open', 'in_progress'])
                ->count();

            $helpdeskTotal = HelpdeskTicket::where('user_id', $user->id)->count();
            $helpdeskResolved = HelpdeskTicket::where('user_id', $user->id)
                ->where('status', 'resolved')
                ->count();

            $loansPending = LoanApplication::where('user_id', $user->id)
                ->whereIn('status', ['submitted', 'under_review'])
                ->count();

            $loansTotal = LoanApplication::where('user_id', $user->id)->count();
            $loansApproved = LoanApplication::where('user_id', $user->id)
                ->where('status', 'approved')
                ->count();

            return [
                'helpdesk' => [
                    'pending' => $helpdeskPending,
                    'total' => $helpdeskTotal,
                    'resolved' => $helpdeskResolved,
                    'avg_resolution_time' => $this->calculateAverageResolutionTime($user),
                ],
                'loans' => [
                    'pending' => $loansPending,
                    'total' => $loansTotal,
                    'approved' => $loansApproved,
                    'avg_approval_time' => $this->calculateAverageLoanApprovalTime($user),
                ],
                'summary' => [
                    'total_submissions' => $helpdeskTotal + $loansTotal,
                ],
                'activity' => $this->getRecentActivity($user, 5)->toArray(),
            ];
        });
        
        return $stats;

    /**
     * Get recent activity for user
     *
     * @return Collection<int, mixed>
     */
    public function getRecentActivity(User $user, int $limit = 10): Collection
    {
        /** @var Collection<int, mixed> $activities */
        $activities = $user->portalActivities()
            ->latest()
            ->limit($limit)
            ->get();
            
        return $activities;

    /**
     * Get role-specific widgets
     *
     * @return array<string, mixed>
     */
    public function getRoleSpecificWidgets(User $user): array
    {
        $widgets = [];

        if ($user->hasRole('approver')) {
            $widgets['pending_approvals'] = LoanApplication::whereIn('status', ['submitted', 'under_review'])
                ->count();
        }

        if ($user->hasRole('admin')) {
            $widgets['system_overview'] = [
                'total_tickets' => HelpdeskTicket::count(),
                'total_loans' => LoanApplication::count(),
            ];
        }

        return $widgets;
    }

    /**
     * Invalidate statistics cache for a specific user
     */
    public function invalidateStatisticsCache(User $user): void
    {
        Cache::forget("portal.statistics.{$user->id}");
    }

    /**
     * Clear all dashboard caches
     */
    public function clearAllCaches(): void
    {
        Cache::forget('dashboard:helpdesk-stats');
        Cache::forget('dashboard:loan-stats');
        Cache::forget('dashboard:asset-stats');
    }

    /**
     * Clear helpdesk-related caches
     */
    public function clearHelpdeskCache(): void
    {
        Cache::forget('dashboard:helpdesk-stats');
    }

    /**
     * Clear loan-related caches
     */
    public function clearLoanCache(): void
    {
        Cache::forget('dashboard:loan-stats');
    }

    /**
     * Clear asset-related caches
     */
    public function clearAssetCache(): void
    {
        Cache::forget('dashboard:asset-stats');
    }

    /**
     * Calculate average resolution time for user's tickets (in hours)
     */
    private function calculateAverageResolutionTime(User $user): ?float
    {
        $resolvedTickets = HelpdeskTicket::where('user_id', $user->id)
            ->where('status', 'resolved')
            ->whereNotNull('resolved_at')
            ->whereNotNull('created_at')
            ->get();

        if ($resolvedTickets->isEmpty()) {
            return null;
        }

        $totalHours = 0;
        foreach ($resolvedTickets as $ticket) {
            $createdAt = Carbon::parse($ticket->created_at);
            $resolvedAt = Carbon::parse($ticket->resolved_at);
            $totalHours += $createdAt->diffInHours($resolvedAt);
        }

        return round($totalHours / $resolvedTickets->count(), 1);
    }

    /**
     * Calculate average approval time for user's loans (in hours)
     */
    private function calculateAverageLoanApprovalTime(User $user): ?float
    {
        $approvedLoans = LoanApplication::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereNotNull('approved_at')
            ->whereNotNull('created_at')
            ->get();

        if ($approvedLoans->isEmpty()) {
            return null;
        }

        $totalHours = 0;
        foreach ($approvedLoans as $loan) {
            $createdAt = Carbon::parse($loan->created_at);
            $approvedAt = Carbon::parse($loan->approved_at);
            $totalHours += $createdAt->diffInHours($approvedAt);
        }

        return round($totalHours / $approvedLoans->count(), 1);
    }
}

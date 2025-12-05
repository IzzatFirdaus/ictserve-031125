<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asset;
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
 * Uses Redis caching with 5-minute TTL per D11 performance guidelines.
 *
 * Features:
 * - 5-minute cache for dashboard statistics per D11
 * - 5-minute cache for asset availability per D11
 * - Cache invalidation on real-time events
 * - Redis driver recommended for production
 *
 * @see D11 Technical Design - Performance Optimization
 * @see D12 §2 Real-time features
 *
 * @requirements 10.3 Redis caching for statistics
 *
 * @trace D11 (Technical Design - Performance Optimization)
 */
class DashboardService
{
    /**
     * Cache TTL in seconds (5 minutes per D11)
     */
    private const CACHE_TTL = 300;

    /**
     * Get user statistics with Redis caching (5-minute TTL per D11)
     *
     * @return array<string, mixed>
     */
    public function getStatistics(User $user): array
    {
        /** @var array<string, mixed> $stats */
        $stats = $this->cache()->remember("portal.statistics.{$user->id}", self::CACHE_TTL, function () use ($user): array {
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
    }

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
    }

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
        $this->cache()->forget("portal.statistics.{$user->id}");
    }

    /**
     * Clear all dashboard caches
     */
    public function clearAllCaches(): void
    {
        $this->cache()->forget('dashboard:helpdesk-stats');
        $this->cache()->forget('dashboard:loan-stats');
        $this->cache()->forget('dashboard:asset-stats');
    }

    /**
     * Clear helpdesk-related caches
     */
    public function clearHelpdeskCache(): void
    {
        $this->cache()->forget('dashboard:helpdesk-stats');
    }

    /**
     * Clear loan-related caches
     */
    public function clearLoanCache(): void
    {
        $this->cache()->forget('dashboard:loan-stats');
    }

    /**
     * Clear asset-related caches
     */
    public function clearAssetCache(): void
    {
        $this->cache()->forget('dashboard:asset-stats');
        $this->cache()->forget('dashboard:asset-availability');
    }

    /**
     * Get asset availability statistics with Redis caching (5-minute TTL per D11)
     *
     * @return array<string, mixed>
     */
    public function getAssetAvailability(): array
    {
        /** @var array<string, mixed> $availability */
        $availability = $this->cache()->remember('dashboard:asset-availability', self::CACHE_TTL, function (): array {
            $totalAssets = Asset::count();
            $availableAssets = Asset::where('status', 'available')->count();
            $onLoanAssets = Asset::where('status', 'on_loan')->count();
            $maintenanceAssets = Asset::where('status', 'maintenance')->count();
            $retiredAssets = Asset::where('status', 'retired')->count();

            return [
                'total' => $totalAssets,
                'available' => $availableAssets,
                'on_loan' => $onLoanAssets,
                'maintenance' => $maintenanceAssets,
                'retired' => $retiredAssets,
                'availability_rate' => $totalAssets > 0 ? round(($availableAssets / $totalAssets) * 100, 1) : 0,
            ];
        });

        return $availability;
    }

    /**
     * Get dashboard statistics with Redis caching (5-minute TTL per D11)
     *
     * @return array<string, int>
     */
    public function getDashboardStats(): array
    {
        /** @var array<string, int> $stats */
        $stats = $this->cache()->remember('dashboard:stats', self::CACHE_TTL, fn (): array => [
            'total_tickets' => HelpdeskTicket::count(),
            'open_tickets' => HelpdeskTicket::whereIn('status', ['open', 'assigned', 'in_progress'])->count(),
            'resolved_tickets' => HelpdeskTicket::where('status', 'resolved')->count(),
            'total_loans' => LoanApplication::count(),
            'pending_loans' => LoanApplication::whereIn('status', ['submitted', 'under_review'])->count(),
            'approved_loans' => LoanApplication::where('status', 'approved')->count(),
            'overdue_loans' => LoanApplication::where('status', 'overdue')->count(),
        ]);

        return $stats;
    }

    /**
     * Invalidate all user-specific caches
     */
    public function invalidateUserCache(User $user): void
    {
        $this->cache()->forget("portal.statistics.{$user->id}");
    }

    /**
     * Resolve the cache repository, honoring the configured default store.
     */
    private function cache(): \Illuminate\Cache\Repository
    {
        /** @var string $store */
        $store = config('cache.default', 'redis');

        return Cache::store($store);
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

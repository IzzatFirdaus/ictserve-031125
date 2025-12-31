<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Notification Query Optimizer
 *
 * Provides optimized database queries for notification operations
 * to improve performance under high load.
 *
 * @see Requirements 8.4 - Pagination for large notification lists
 *
 * @trace D03 SRS-FR-043 (notification performance)
 */
class NotificationQueryOptimizer
{
    /**
     * Default page size for pagination.
     */
    private const DEFAULT_PAGE_SIZE = 20;

    /**
     * Maximum page size allowed.
     */
    private const MAX_PAGE_SIZE = 100;

    /**
     * Get paginated notifications for a user with optimized query.
     */
    public function getPaginatedNotifications(
        User $user,
        int $page = 1,
        int $perPage = self::DEFAULT_PAGE_SIZE,
        ?string $category = null,
        bool $unreadOnly = false
    ): LengthAwarePaginator {
        $perPage = min($perPage, self::MAX_PAGE_SIZE);

        $query = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', $user->getMorphClass());

        if ($unreadOnly) {
            $query->whereNull('read_at');
        }

        if ($category !== null) {
            $query->where(function ($q) use ($category): void {
                $q->whereRaw("JSON_EXTRACT(data, '$.type') LIKE ?", ["%{$category}%"]);
            });
        }

        $total = $query->count();

        $notifications = $query
            ->orderBy('created_at', 'desc')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return new LengthAwarePaginator(
            $notifications,
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );
    }

    /**
     * Get notification counts by category for a user.
     *
     * @return array<string, int>
     */
    public function getCountsByCategory(User $user): array
    {
        $results = DB::table('notifications')
            ->select(DB::raw("JSON_EXTRACT(data, '$.type') as type, COUNT(*) as count"))
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', $user->getMorphClass())
            ->whereNull('read_at')
            ->groupBy(DB::raw("JSON_EXTRACT(data, '$.type')"))
            ->get();

        $counts = [
            'tickets' => 0,
            'loans' => 0,
            'system' => 0,
            'total' => 0,
        ];

        foreach ($results as $result) {
            $type = trim($result->type ?? '', '"');
            $count = (int) $result->count;
            $counts['total'] += $count;

            if (str_contains($type, 'ticket')) {
                $counts['tickets'] += $count;
            } elseif (str_contains($type, 'loan') || str_contains($type, 'approval')) {
                $counts['loans'] += $count;
            } else {
                $counts['system'] += $count;
            }
        }

        return $counts;
    }

    /**
     * Bulk mark notifications as read with optimized query.
     *
     * @param  array<string>  $notificationIds
     */
    public function bulkMarkAsRead(User $user, array $notificationIds): int
    {
        return DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', $user->getMorphClass())
            ->whereIn('id', $notificationIds)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Bulk delete notifications with optimized query.
     *
     * @param  array<string>  $notificationIds
     */
    public function bulkDelete(User $user, array $notificationIds): int
    {
        return DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', $user->getMorphClass())
            ->whereIn('id', $notificationIds)
            ->delete();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(User $user): int
    {
        return DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', $user->getMorphClass())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Delete old read notifications (cleanup).
     */
    public function deleteOldReadNotifications(int $daysOld = 30): int
    {
        return DB::table('notifications')
            ->whereNotNull('read_at')
            ->where('read_at', '<', now()->subDays($daysOld))
            ->delete();
    }

    /**
     * Get notification statistics for monitoring.
     *
     * @return array<string, mixed>
     */
    public function getStatistics(): array
    {
        $stats = DB::table('notifications')
            ->select([
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN read_at IS NULL THEN 1 ELSE 0 END) as unread_count'),
                DB::raw('SUM(CASE WHEN read_at IS NOT NULL THEN 1 ELSE 0 END) as read_count'),
                DB::raw('MIN(created_at) as oldest'),
                DB::raw('MAX(created_at) as newest'),
            ])
            ->first();

        return [
            'total' => (int) ($stats->total ?? 0),
            'unread' => (int) ($stats->unread_count ?? 0),
            'read' => (int) ($stats->read_count ?? 0),
            'oldest' => $stats->oldest,
            'newest' => $stats->newest,
        ];
    }

    /**
     * Search notifications with optimized full-text search.
     *
     * @return Collection<int, object>
     */
    public function searchNotifications(
        User $user,
        string $query,
        int $limit = 20
    ): Collection {
        $searchTerm = '%'.$query.'%';

        return DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', $user->getMorphClass())
            ->where(function ($q) use ($searchTerm): void {
                $q->whereRaw("JSON_EXTRACT(data, '$.title') LIKE ?", [$searchTerm])
                    ->orWhereRaw("JSON_EXTRACT(data, '$.message') LIKE ?", [$searchTerm]);
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get notifications created within a date range.
     *
     * @return Collection<int, object>
     */
    public function getNotificationsInRange(
        User $user,
        string $startDate,
        string $endDate,
        int $limit = 100
    ): Collection {
        return DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', $user->getMorphClass())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}

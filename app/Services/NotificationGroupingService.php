<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NotificationGroupingService
{
    public function getGroupedNotifications(User $user): Collection
    {
        // Get user's notification preferences
        $preferences = $user->notificationPreference;

        if (! $preferences || ! $preferences->group_notifications) {
            // Return ungrouped notifications
            return $user->notifications()
                ->whereNull('read_at')
                ->latest()
                ->limit(50)
                ->get();
        }

        // Group notifications by type and time window (last hour)
        return DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->whereNull('read_at')
            ->where('created_at', '>=', now()->subHour())
            ->select(
                'type',
                DB::raw('COUNT(*) as count'),
                DB::raw('MAX(created_at) as latest_created_at'),
                DB::raw('MIN(created_at) as earliest_created_at')
            )
            ->groupBy('type')
            ->get()
            ->map(function ($group) use ($user) {
                return [
                    'type' => $group->type,
                    'count' => $group->count,
                    'latest_at' => $group->latest_created_at,
                    'earliest_at' => $group->earliest_created_at,
                    'notifications' => $user->notifications()
                        ->where('type', $group->type)
                        ->whereNull('read_at')
                        ->where('created_at', '>=', now()->subHour())
                        ->latest()
                        ->get(),
                ];
            });
    }

    public function shouldSendNotification(User $user): bool
    {
        $preferences = $user->notificationPreference;

        if (! $preferences) {
            return true;
        }

        // Check quiet hours
        if ($preferences->isInQuietHours()) {
            return false;
        }

        return true;
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\NotificationSchedulingServiceInterface;
use App\Models\ScheduledNotification;
use App\Models\User;
use App\Notifications\DigestNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Notification Scheduling Service Implementation
 *
 * Handles scheduling notifications for future delivery, managing recurring
 * notifications, and compiling notification digests.
 *
 * @see Requirements 2.7 - Notification scheduling for future delivery
 */
class NotificationSchedulingService implements NotificationSchedulingServiceInterface
{
    private const array VALID_RECURRENCE_PATTERNS = [
        ScheduledNotification::RECURRENCE_DAILY,
        ScheduledNotification::RECURRENCE_WEEKLY,
        ScheduledNotification::RECURRENCE_MONTHLY,
    ];

    private const int MAX_RETRY_COUNT = 3;

    public function schedule(
        User $user,
        Notification $notification,
        Carbon $scheduledAt,
        array $meta = []
    ): string {
        $scheduleId = (string) Str::uuid();

        ScheduledNotification::create([
            'schedule_id' => $scheduleId,
            'user_id' => $user->id,
            'notification_class' => $notification::class,
            'notification_data' => $this->serializeNotification($notification),
            'notification_type' => $meta['type'] ?? null,
            'priority' => $meta['priority'] ?? 'normal',
            'channels' => $meta['channels'] ?? null,
            'scheduled_at' => $scheduledAt,
            'status' => ScheduledNotification::STATUS_PENDING,
            'is_recurring' => false,
            'metadata' => $meta,
        ]);

        Log::channel('single')->info('Notification scheduled', [
            'action' => 'notification_scheduled',
            'schedule_id' => $scheduleId,
            'user_id' => $user->id,
            'notification_class' => $notification::class,
            'scheduled_at' => $scheduledAt->toIso8601String(),
        ]);

        return $scheduleId;
    }

    public function scheduleRecurring(
        User $user,
        Notification $notification,
        Carbon $startAt,
        string $pattern,
        array $meta = []
    ): string {
        if (! \in_array($pattern, self::VALID_RECURRENCE_PATTERNS, true)) {
            throw new \InvalidArgumentException(
                'Invalid recurrence pattern. Must be one of: '.\implode(', ', self::VALID_RECURRENCE_PATTERNS)
            );
        }

        $scheduleId = (string) Str::uuid();

        ScheduledNotification::create([
            'schedule_id' => $scheduleId,
            'user_id' => $user->id,
            'notification_class' => $notification::class,
            'notification_data' => $this->serializeNotification($notification),
            'notification_type' => $meta['type'] ?? null,
            'priority' => $meta['priority'] ?? 'normal',
            'channels' => $meta['channels'] ?? null,
            'scheduled_at' => $startAt,
            'status' => ScheduledNotification::STATUS_PENDING,
            'is_recurring' => true,
            'recurrence_pattern' => $pattern,
            'next_occurrence_at' => $this->calculateNextOccurrence($startAt, $pattern),
            'metadata' => $meta,
        ]);

        Log::channel('single')->info('Recurring notification scheduled', [
            'action' => 'recurring_notification_scheduled',
            'schedule_id' => $scheduleId,
            'user_id' => $user->id,
            'notification_class' => $notification::class,
            'pattern' => $pattern,
            'start_at' => $startAt->toIso8601String(),
        ]);

        return $scheduleId;
    }

    public function cancel(string $scheduleId): bool
    {
        $scheduled = ScheduledNotification::where('schedule_id', $scheduleId)->first();

        if ($scheduled === null) {
            return false;
        }

        if (! $scheduled->isPending()) {
            return false;
        }

        $scheduled->markAsCancelled();

        Log::channel('single')->info('Scheduled notification cancelled', [
            'action' => 'scheduled_notification_cancelled',
            'schedule_id' => $scheduleId,
            'user_id' => $scheduled->user_id,
        ]);

        return true;
    }

    public function get(string $scheduleId): ?ScheduledNotification
    {
        return ScheduledNotification::where('schedule_id', $scheduleId)->first();
    }

    /**
     * @return Collection<int, ScheduledNotification>
     */
    public function getPendingForUser(User $user): Collection
    {
        return ScheduledNotification::forUser($user->id)
            ->pending()
            ->orderBy('scheduled_at')
            ->get();
    }

    /**
     * @return array{processed: int, failed: int, errors: array<string, string>}
     */
    public function processDueNotifications(): array
    {
        $result = ['processed' => 0, 'failed' => 0, 'errors' => []];

        $dueNotifications = ScheduledNotification::due()
            ->with('user')
            ->get();

        foreach ($dueNotifications as $scheduled) {
            try {
                $this->sendScheduledNotification($scheduled);
                $result['processed']++;

                if ($scheduled->is_recurring) {
                    $this->scheduleNextOccurrence($scheduled);
                }
            } catch (\Exception $e) {
                $result['failed']++;
                $result['errors'][$scheduled->schedule_id] = $e->getMessage();

                $scheduled->markAsFailed($e->getMessage());

                Log::channel('single')->error('Failed to send scheduled notification', [
                    'action' => 'scheduled_notification_failed',
                    'schedule_id' => $scheduled->schedule_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $result;
    }

    /**
     * @return array{users_processed: int, notifications_sent: int}
     */
    public function compileAndSendDigests(string $frequency): array
    {
        if (! \in_array($frequency, ['daily', 'weekly'], true)) {
            throw new \InvalidArgumentException('Invalid digest frequency. Must be daily or weekly.');
        }

        $result = ['users_processed' => 0, 'notifications_sent' => 0];

        $users = $this->getUsersForDigest($frequency);

        foreach ($users as $user) {
            $notifications = $this->getDigestNotifications($user, $frequency);

            if ($notifications->isEmpty()) {
                continue;
            }

            try {
                $user->notify(new DigestNotification($notifications, $frequency));
                $result['users_processed']++;
                $result['notifications_sent'] += $notifications->count();

                $this->markNotificationsAsDigested($notifications);
            } catch (\Exception $e) {
                Log::channel('single')->error('Failed to send digest notification', [
                    'action' => 'digest_notification_failed',
                    'user_id' => $user->id,
                    'frequency' => $frequency,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $result;
    }

    /**
     * @return Collection<int, \Illuminate\Notifications\DatabaseNotification>
     */
    public function getDigestNotifications(User $user, string $frequency): Collection
    {
        $since = match ($frequency) {
            'daily' => now()->subDay(),
            'weekly' => now()->subWeek(),
            default => now()->subDay(),
        };

        return $user->notifications()
            ->where('created_at', '>=', $since)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function reschedule(string $scheduleId, ?Carbon $newScheduledAt = null): bool
    {
        $scheduled = ScheduledNotification::where('schedule_id', $scheduleId)->first();

        if ($scheduled === null) {
            return false;
        }

        if ($scheduled->retry_count >= self::MAX_RETRY_COUNT) {
            return false;
        }

        $newTime = $newScheduledAt ?? $this->calculateBackoffTime($scheduled->retry_count);

        $scheduled->update([
            'scheduled_at' => $newTime,
            'status' => ScheduledNotification::STATUS_PENDING,
            'error_message' => null,
        ]);

        Log::channel('single')->info('Scheduled notification rescheduled', [
            'action' => 'scheduled_notification_rescheduled',
            'schedule_id' => $scheduleId,
            'new_scheduled_at' => $newTime->toIso8601String(),
            'retry_count' => $scheduled->retry_count,
        ]);

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeNotification(Notification $notification): array
    {
        return [
            'class' => $notification::class,
            'data' => \method_exists($notification, 'toArray')
                ? $notification->toArray(new User)
                : [],
        ];
    }

    private function sendScheduledNotification(ScheduledNotification $scheduled): void
    {
        $user = $scheduled->user;

        if ($user === null) {
            throw new \RuntimeException('User not found for scheduled notification.');
        }

        $notificationClass = $scheduled->notification_class;
        $notificationData = $scheduled->notification_data;

        if (! \class_exists($notificationClass)) {
            throw new \RuntimeException("Notification class {$notificationClass} not found.");
        }

        $notification = new $notificationClass(...($notificationData['constructor_args'] ?? []));

        $user->notify($notification);

        $scheduled->markAsSent();
    }

    private function scheduleNextOccurrence(ScheduledNotification $scheduled): void
    {
        if (! $scheduled->is_recurring || $scheduled->recurrence_pattern === null) {
            return;
        }

        $nextOccurrence = $this->calculateNextOccurrence(
            $scheduled->scheduled_at,
            $scheduled->recurrence_pattern
        );

        ScheduledNotification::create([
            'schedule_id' => (string) Str::uuid(),
            'user_id' => $scheduled->user_id,
            'notification_class' => $scheduled->notification_class,
            'notification_data' => $scheduled->notification_data,
            'notification_type' => $scheduled->notification_type,
            'priority' => $scheduled->priority,
            'channels' => $scheduled->channels,
            'scheduled_at' => $nextOccurrence,
            'status' => ScheduledNotification::STATUS_PENDING,
            'is_recurring' => true,
            'recurrence_pattern' => $scheduled->recurrence_pattern,
            'next_occurrence_at' => $this->calculateNextOccurrence($nextOccurrence, $scheduled->recurrence_pattern),
            'metadata' => $scheduled->metadata,
        ]);
    }

    private function calculateNextOccurrence(Carbon $from, string $pattern): Carbon
    {
        return match ($pattern) {
            ScheduledNotification::RECURRENCE_DAILY => $from->copy()->addDay(),
            ScheduledNotification::RECURRENCE_WEEKLY => $from->copy()->addWeek(),
            ScheduledNotification::RECURRENCE_MONTHLY => $from->copy()->addMonth(),
            default => $from->copy()->addDay(),
        };
    }

    private function calculateBackoffTime(int $retryCount): Carbon
    {
        $delayMinutes = (int) \pow(2, $retryCount) * 5;

        return now()->addMinutes($delayMinutes);
    }

    /**
     * @return Collection<int, User>
     */
    private function getUsersForDigest(string $frequency): Collection
    {
        return User::whereJsonContains('notification_preferences->digest_frequency', $frequency)
            ->whereJsonContains('notification_preferences->email_enabled', true)
            ->get();
    }

    /**
     * @param  Collection<int, \Illuminate\Notifications\DatabaseNotification>  $notifications
     */
    private function markNotificationsAsDigested(Collection $notifications): void
    {
        DB::table('notifications')
            ->whereIn('id', $notifications->pluck('id'))
            ->update(['data->digested_at' => now()->toIso8601String()]);
    }
}

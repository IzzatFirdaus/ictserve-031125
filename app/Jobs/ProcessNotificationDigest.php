<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\NotificationDigest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * ProcessNotificationDigest Job
 *
 * Processes and sends notification digests to users based on their preferences.
 * Supports daily and weekly digest frequencies per D16 notification preferences.
 * Aggregates unread notifications and sends consolidated email summaries.
 *
 * @trace D03-FR-008.1; D04 §12.1; D16
 * @trace Requirements 10.4, 13.3, 17.5
 */
class ProcessNotificationDigest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Number of seconds to wait before retrying.
     */
    public int $backoff = 30;

    /**
     * @param  array<string,mixed>  $payload  Digest processing data
     *                                        - frequency: string (daily|weekly) - required
     *                                        - user_id: int (optional, process single user)
     *                                        - force: bool (optional, send even if no notifications)
     */
    

/**
 * @param array<string, mixed> $payload
 */
public function __construct(private array $payload)
    {
        $this->onQueue('digests');
    }

    public function handle(): void
    {
        $frequency = $this->payload['frequency'] ?? 'daily';
        $userId = $this->payload['user_id'] ?? null;
        $force = $this->payload['force'] ?? false;

        if (! in_array($frequency, ['daily', 'weekly'], true)) {
            Log::warning('ProcessNotificationDigest invalid frequency', [
                'frequency' => $frequency,
            ]);

            return;
        }

        try {
            if ($userId !== null) {
                $this->processUserDigest($userId, $frequency, $force);
            } else {
                $this->processAllDigests($frequency, $force);
            }
        } catch (\Throwable $e) {
            Log::error('Failed processing notification digest', [
                'frequency' => $frequency,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            $this->fail($e);
        }
    }

    /**
     * Process digest for a single user.
     */
    private function processUserDigest(int $userId, string $frequency, bool $force): void
    {
        $user = User::find($userId);
        if (! $user instanceof User) {
            Log::warning('ProcessNotificationDigest user not found', [
                'user_id' => $userId,
            ]);

            return;
        }

        if (! $this->userWantsDigest($user, $frequency)) {
            Log::debug('ProcessNotificationDigest user does not want digest', [
                'user_id' => $userId,
                'frequency' => $frequency,
            ]);

            return;
        }

        $notifications = $this->getUnreadNotifications($user, $frequency);

        if ($notifications->isEmpty() && ! $force) {
            Log::debug('ProcessNotificationDigest no notifications for user', [
                'user_id' => $userId,
            ]);

            return;
        }

        $this->sendDigest($user, $notifications);
    }

    /**
     * Process digests for all eligible users.
     */
    private function processAllDigests(string $frequency, bool $force): void
    {
        $users = $this->getEligibleUsers($frequency);

        $processedCount = 0;
        $skippedCount = 0;

        foreach ($users as $user) {
            $notifications = $this->getUnreadNotifications($user, $frequency);

            if ($notifications->isEmpty() && ! $force) {
                $skippedCount++;

                continue;
            }

            $this->sendDigest($user, $notifications);
            $processedCount++;
        }

        Log::info('ProcessNotificationDigest batch completed', [
            'frequency' => $frequency,
            'processed' => $processedCount,
            'skipped' => $skippedCount,
        ]);
    }

    /**
     * Check if user wants digest notifications at given frequency.
     */
    private function userWantsDigest(User $user, string $frequency): bool
    {
        $preferences = $user->notification_preferences ?? [];

        $digestFrequency = $preferences['digest_frequency'] ?? 'immediate';

        return $digestFrequency === $frequency;
    }

    /**
     * Get users eligible for digest at given frequency.
     *
     * @return Collection<int, User>
     */
    private function getEligibleUsers(string $frequency): Collection
    {
        return User::query()
            ->whereNotNull('email_verified_at')
            ->where(function ($query) use ($frequency) {
                $query->whereJsonContains('notification_preferences->digest_frequency', $frequency)
                    ->orWhereRaw(
                        "JSON_EXTRACT(notification_preferences, '$.digest_frequency') = ?",
                        [$frequency]
                    );
            })
            ->get();
    }

    /**
     * Get unread notifications for user within digest period.
     *
     * @return Collection<int, object>
     */
    private function getUnreadNotifications(User $user, string $frequency): Collection
    {
        $since = $frequency === 'daily'
            ? now()->subDay()
            : now()->subWeek();

        return DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->where('created_at', '>=', $since)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Send digest email to user.
     *
     * @param  Collection<int, object>  $notifications
     */
    private function sendDigest(User $user, Collection $notifications): void
    {
        Mail::to($user->email, $user->name)
            ->queue(new NotificationDigest($user, $notifications));

        Log::info('Notification digest queued', [
            'user_id' => $user->id,
            'notification_count' => $notifications->count(),
        ]);

        // Mark notifications as included in digest
        if ($notifications->isNotEmpty()) {
            $notificationIds = $notifications->pluck('id')->toArray();
            DB::table('notifications')
                ->whereIn('id', $notificationIds)
                ->update(['read_at' => now()]);
        }
    }
}

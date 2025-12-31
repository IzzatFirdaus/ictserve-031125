<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\ScheduledNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;

/**
 * Notification Scheduling Service Interface
 *
 * Provides functionality to schedule notifications for future delivery,
 * manage recurring notifications, and compile notification digests.
 *
 * @see Requirements 2.7 - Notification scheduling for future delivery
 */
interface NotificationSchedulingServiceInterface
{
    /**
     * Schedule a notification for future delivery
     *
     * @param  User  $user  The recipient user
     * @param  Notification  $notification  The notification to send
     * @param  Carbon  $scheduledAt  When to send the notification
     * @param  array<string, mixed>  $meta  Additional metadata
     * @return string The schedule ID for tracking/cancellation
     */
    public function schedule(
        User $user,
        Notification $notification,
        Carbon $scheduledAt,
        array $meta = []
    ): string;

    /**
     * Schedule a recurring notification
     *
     * @param  User  $user  The recipient user
     * @param  Notification  $notification  The notification to send
     * @param  Carbon  $startAt  When to start the recurrence
     * @param  string  $pattern  Recurrence pattern (daily, weekly, monthly)
     * @param  array<string, mixed>  $meta  Additional metadata
     * @return string The schedule ID
     */
    public function scheduleRecurring(
        User $user,
        Notification $notification,
        Carbon $startAt,
        string $pattern,
        array $meta = []
    ): string;

    /**
     * Cancel a scheduled notification
     *
     * @param  string  $scheduleId  The schedule ID to cancel
     * @return bool True if cancelled successfully
     */
    public function cancel(string $scheduleId): bool;

    /**
     * Get a scheduled notification by ID
     *
     * @param  string  $scheduleId  The schedule ID
     */
    public function get(string $scheduleId): ?ScheduledNotification;

    /**
     * Get all pending scheduled notifications for a user
     *
     * @param  User  $user  The user
     * @return \Illuminate\Database\Eloquent\Collection<int, ScheduledNotification>
     */
    public function getPendingForUser(User $user): \Illuminate\Database\Eloquent\Collection;

    /**
     * Process all due scheduled notifications
     *
     * @return array{processed: int, failed: int, errors: array<string, string>}
     */
    public function processDueNotifications(): array;

    /**
     * Compile and send digest notifications for users
     *
     * @param  string  $frequency  The digest frequency (daily, weekly)
     * @return array{users_processed: int, notifications_sent: int}
     */
    public function compileAndSendDigests(string $frequency): array;

    /**
     * Get digest notifications for a user
     *
     * @param  User  $user  The user
     * @param  string  $frequency  The digest frequency
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Notifications\DatabaseNotification>
     */
    public function getDigestNotifications(User $user, string $frequency): \Illuminate\Database\Eloquent\Collection;

    /**
     * Reschedule a failed notification
     *
     * @param  string  $scheduleId  The schedule ID
     * @param  Carbon|null  $newScheduledAt  New scheduled time (defaults to exponential backoff)
     * @return bool True if rescheduled successfully
     */
    public function reschedule(string $scheduleId, ?Carbon $newScheduledAt = null): bool;
}

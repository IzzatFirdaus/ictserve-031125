<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\User;

/**
 * Notification Preference Service Interface for ICTServe v3.5.0
 *
 * Provides functionality to manage user notification preferences including
 * email frequency settings (immediate, daily digest, weekly digest) and
 * per-notification-type toggles.
 *
 * Key Features:
 * - Get and update user notification preferences
 * - Determine if email should be sent based on user preferences and notification type
 * - Support for digest frequency (immediate, daily, weekly)
 * - Integration with multi-channel notification system (email, database, broadcast)
 *
 * @see D16_BROADCASTING_SETUP.md WebSocket configuration
 * @see D03 SRS-ADM-006 Staff Dashboard notification preferences
 * @see Requirements 17.5 - Notification preferences configuration
 */
interface NotificationPreferenceServiceInterface
{
    /**
     * Get all notification preferences for a user
     *
     * Returns the complete notification preferences array including:
     * - Per-type toggles (ticket_updates, loan_approvals, etc.)
     * - Digest frequency setting
     * - Real-time notification toggle
     * - Channel preferences (email, in-app, broadcast)
     *
     * @param  User  $user  The user to get preferences for
     * @return array{
     *     ticket_updates: bool,
     *     ticket_assignments: bool,
     *     ticket_comments: bool,
     *     sla_alerts: bool,
     *     system_announcements: bool,
     *     loan_updates: bool,
     *     loan_approvals: bool,
     *     loan_reminders: bool,
     *     realtime_notifications: bool,
     *     digest_frequency: string,
     *     email_enabled: bool,
     *     in_app_enabled: bool
     * }
     *
     * @see Requirements 17.5 - Notification preferences
     */
    public function getPreferences(User $user): array;

    /**
     * Update notification preferences for a user
     *
     * Accepts partial or complete preference updates. Only provided
     * keys will be updated; existing preferences are preserved.
     *
     * Logs the preference change in audit trail for compliance.
     *
     * @param  User  $user  The user to update preferences for
     * @param  array<string, bool|string>  $preferences  Preferences to update
     *
     * @throws \InvalidArgumentException If invalid preference key or value provided
     *
     * @see Requirements 17.5 - Notification preferences configuration
     */

    /**
     * @param  array<string, mixed>  $preferences
     */
    public function updatePreferences(User $user, array $preferences): void;

    /**
     * Determine if email notification should be sent for a specific type
     *
     * Decision hierarchy:
     * 1. Critical notifications always send (bypass preferences)
     * 2. Check user's specific preference for this notification type
     * 3. Check if email channel is globally enabled for user
     * 4. Consider digest frequency (immediate sends now, digest queues)
     *
     * @param  User  $user  The recipient user
     * @param  string  $notificationType  The notification type (ticket_updates, loan_approvals, etc.)
     * @return bool True if email should be sent immediately
     *
     * @see Requirements 17.5 - Email frequency configuration
     */
    public function shouldSendEmail(User $user, string $notificationType): bool;

    /**
     * Get the digest frequency setting for a user
     *
     * Returns one of:
     * - 'immediate': Send notifications as they occur
     * - 'daily': Batch notifications into daily digest email
     * - 'weekly': Batch notifications into weekly digest email
     *
     * @param  User  $user  The user to check
     * @return string The digest frequency ('immediate', 'daily', 'weekly')
     *
     * @see Requirements 17.5 - Email frequency (immediate, daily digest, weekly digest)
     */
    public function getDigestFrequency(User $user): string;

    /**
     * Check if in-app notifications are enabled for a user
     *
     * In-app notifications appear in the notification center
     * within the staff dashboard.
     *
     * @param  User  $user  The user to check
     * @return bool True if in-app notifications are enabled
     */
    public function isInAppEnabled(User $user): bool;

    /**
     * Check if real-time (WebSocket) notifications are enabled
     *
     * Real-time notifications use Laravel Reverb for instant
     * browser notifications without page refresh.
     *
     * @param  User  $user  The user to check
     * @return bool True if real-time notifications are enabled
     */
    public function isRealtimeEnabled(User $user): bool;

    /**
     * Get notification channels for a specific notification type
     *
     * Returns array of channels that should be used based on
     * user preferences and notification type priority.
     *
     * @param  User  $user  The recipient user
     * @param  string  $notificationType  The notification type
     * @return array<int, string> Array of channels: ['mail', 'database', 'broadcast']
     */
    public function getChannelsForType(User $user, string $notificationType): array;

    /**
     * Reset user preferences to defaults
     *
     * Useful for troubleshooting or when user wants to start fresh.
     *
     * @param  User  $user  The user to reset preferences for
     */
    public function resetToDefaults(User $user): void;

    /**
     * Get available notification types with metadata
     *
     * Returns list of all notification types that can be configured,
     * with labels, descriptions, and whether they can be disabled.
     *
     * @return array<string, array{label: string, description: string, category: string, user_controllable: bool}>
     */
    public function getAvailableNotificationTypes(): array;

    /**
     * Check if a notification type should be queued for digest
     *
     * Based on user's digest frequency and notification type,
     * determines if notification should be sent immediately
     * or queued for digest processing.
     *
     * @param  User  $user  The recipient user
     * @param  string  $notificationType  The notification type
     * @return bool True if should be queued for digest
     */
    public function shouldQueueForDigest(User $user, string $notificationType): bool;

    /**
     * Set quiet hours for a user
     *
     * Configures the time period during which non-critical
     * notifications should be suppressed or delayed.
     *
     * @param  User  $user  The user to configure
     * @param  string  $start  Start time in H:i format (e.g., '22:00')
     * @param  string  $end  End time in H:i format (e.g., '07:00')
     * @param  string|null  $timezone  User's timezone (defaults to Asia/Kuala_Lumpur)
     *
     * @throws \InvalidArgumentException If time format is invalid
     *
     * @see Requirements 5.3 - Quiet hours configuration
     */
    public function setQuietHours(User $user, string $start, string $end, ?string $timezone = null): void;

    /**
     * Check if user is currently in quiet hours
     *
     * Determines if the current time falls within the user's
     * configured quiet hours period, considering their timezone.
     *
     * @param  User  $user  The user to check
     * @return bool True if currently in quiet hours
     *
     * @see Requirements 5.3 - Quiet hours enforcement
     */
    public function isInQuietHours(User $user): bool;

    /**
     * Disable quiet hours for a user
     *
     * @param  User  $user  The user to disable quiet hours for
     */
    public function disableQuietHours(User $user): void;

    /**
     * Bulk update preferences for multiple users
     *
     * Efficiently updates preferences for multiple users in a single operation.
     * Returns array of results with success/failure status per user.
     *
     * @param  array<int>  $userIds  Array of user IDs to update
     * @param  array<string, bool|string>  $preferences  Preferences to apply
     * @return array{success: array<int>, failed: array<int, string>}
     *
     * @see Requirements 5.7 - Bulk preference management
     */
    public function bulkUpdatePreferences(array $userIds, array $preferences): array;

    /**
     * Check if notification should be sent based on all preference rules
     *
     * Comprehensive check that considers:
     * - Notification type enabled/disabled
     * - Channel preferences
     * - Quiet hours
     * - Critical notification override
     * - Digest frequency
     *
     * @param  User  $user  The recipient user
     * @param  string  $notificationType  The notification type
     * @param  string  $channel  The delivery channel (mail, database, broadcast)
     * @param  string|null  $priority  Notification priority (critical, high, normal, low)
     * @return bool True if notification should be sent
     *
     * @see Requirements 5.1, 5.2, 5.3, 5.6
     */
    public function shouldSendNotification(
        User $user,
        string $notificationType,
        string $channel,
        ?string $priority = null
    ): bool;

    /**
     * Get user's timezone setting
     *
     * @param  User  $user  The user to get timezone for
     * @return string The timezone identifier (e.g., 'Asia/Kuala_Lumpur')
     */
    public function getUserTimezone(User $user): string;

    /**
     * Set user's timezone
     *
     * @param  User  $user  The user to set timezone for
     * @param  string  $timezone  Valid timezone identifier
     *
     * @throws \InvalidArgumentException If timezone is invalid
     */
    public function setUserTimezone(User $user, string $timezone): void;
}

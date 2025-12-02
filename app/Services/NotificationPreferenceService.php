<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\NotificationPreferenceServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Notification Preference Service Implementation for ICTServe v3.5.0
 *
 * Manages user notification preferences including email frequency settings
 * (immediate, daily digest, weekly digest) and per-notification-type toggles.
 *
 * This service:
 * - Retrieves and updates user notification preferences
 * - Determines notification channels based on user preferences
 * - Supports digest frequency for email batching
 * - Integrates with multi-channel notification system
 *
 * Security Considerations:
 * - All preference changes are logged for audit compliance
 * - Critical notifications bypass user preferences
 * - Preferences are validated before storage
 *
 * @see D16_BROADCASTING_SETUP.md WebSocket configuration
 * @see D03 SRS-ADM-006 Staff Dashboard notification preferences
 * @see Requirements 17.5 - Notification preferences configuration
 */
class NotificationPreferenceService implements NotificationPreferenceServiceInterface
{
    /**
     * Valid digest frequency values
     */
    private const DIGEST_IMMEDIATE = 'immediate';

    private const DIGEST_DAILY = 'daily';

    private const DIGEST_WEEKLY = 'weekly';

    /**
     * Default notification preferences
     *
     * @var array<string, bool|string>
     */
    private const DEFAULT_PREFERENCES = [
        'ticket_updates' => true,
        'ticket_assignments' => true,
        'ticket_comments' => true,
        'sla_alerts' => true,
        'system_announcements' => true,
        'loan_updates' => true,
        'loan_approvals' => true,
        'loan_reminders' => true,
        'realtime_notifications' => true,
        'digest_frequency' => self::DIGEST_IMMEDIATE,
        'email_enabled' => true,
        'in_app_enabled' => true,
    ];

    /**
     * Notification types that cannot be disabled (critical/compliance)
     *
     * @var array<int, string>
     */
    private const CRITICAL_TYPES = [
        'sla_alerts',
        'system_announcements',
        'security_alerts',
    ];

    /**
     * Valid preference keys for validation
     *
     * @var array<int, string>
     */
    private const VALID_PREFERENCE_KEYS = [
        'ticket_updates',
        'ticket_assignments',
        'ticket_comments',
        'sla_alerts',
        'system_announcements',
        'loan_updates',
        'loan_approvals',
        'loan_reminders',
        'realtime_notifications',
        'digest_frequency',
        'email_enabled',
        'in_app_enabled',
    ];

    /**
     * Get all notification preferences for a user
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
     */
    public function getPreferences(User $user): array
    {
        $storedPreferences = $user->notification_preferences ?? [];

        // Merge with defaults to ensure all keys exist
        $preferences = array_merge(self::DEFAULT_PREFERENCES, $storedPreferences);

        // Normalize boolean values
        foreach ($preferences as $key => $value) {
            if ($key === 'digest_frequency') {
                // Validate digest frequency value
                if (! in_array($value, [self::DIGEST_IMMEDIATE, self::DIGEST_DAILY, self::DIGEST_WEEKLY], true)) {
                    $preferences[$key] = self::DIGEST_IMMEDIATE;
                }
            } else {
                // Convert to boolean for toggle preferences
                $preferences[$key] = (bool) $value;
            }
        }

        /** @var array{ticket_updates: bool, ticket_assignments: bool, ticket_comments: bool, sla_alerts: bool, system_announcements: bool, loan_updates: bool, loan_approvals: bool, loan_reminders: bool, realtime_notifications: bool, digest_frequency: string, email_enabled: bool, in_app_enabled: bool} $preferences */
        return $preferences;
    }

    /**
     * Update notification preferences for a user
     *
     * @param  User  $user  The user to update preferences for
     * @param  array<string, bool|string>  $preferences  Preferences to update
     *
     * @throws \InvalidArgumentException If invalid preference key or value provided
     */
    public function updatePreferences(User $user, array $preferences): void
    {
        // Validate preference keys
        $invalidKeys = array_diff(array_keys($preferences), self::VALID_PREFERENCE_KEYS);
        if (! empty($invalidKeys)) {
            throw new \InvalidArgumentException(
                'Invalid preference keys: '.implode(', ', $invalidKeys)
            );
        }

        // Validate digest_frequency value if provided
        if (isset($preferences['digest_frequency'])) {
            $validFrequencies = [self::DIGEST_IMMEDIATE, self::DIGEST_DAILY, self::DIGEST_WEEKLY];
            if (! in_array($preferences['digest_frequency'], $validFrequencies, true)) {
                throw new \InvalidArgumentException(
                    'Invalid digest_frequency value. Must be one of: '.implode(', ', $validFrequencies)
                );
            }
        }

        // Get current preferences and merge with updates
        $currentPreferences = $this->getPreferences($user);
        $updatedPreferences = array_merge($currentPreferences, $preferences);

        // Normalize values
        foreach ($updatedPreferences as $key => $value) {
            if ($key !== 'digest_frequency') {
                $updatedPreferences[$key] = (bool) $value;
            }
        }

        // Store updated preferences
        $user->notification_preferences = $updatedPreferences;
        $user->save();

        // Log the preference change for audit
        $this->logPreferenceUpdate($user, $preferences);
    }

    /**
     * Determine if email notification should be sent for a specific type
     *
     * @param  User  $user  The recipient user
     * @param  string  $notificationType  The notification type
     * @return bool True if email should be sent immediately
     */
    public function shouldSendEmail(User $user, string $notificationType): bool
    {
        $preferences = $this->getPreferences($user);

        // Check if email is globally enabled
        if (! $preferences['email_enabled']) {
            return false;
        }

        // Critical notifications always send
        if ($this->isCriticalType($notificationType)) {
            return true;
        }

        // Check specific notification type preference
        if (isset($preferences[$notificationType]) && ! $preferences[$notificationType]) {
            return false;
        }

        // Check digest frequency - only send immediately if set to immediate
        $digestFrequency = $this->getDigestFrequency($user);

        return $digestFrequency === self::DIGEST_IMMEDIATE;
    }

    /**
     * Get the digest frequency setting for a user
     *
     * @param  User  $user  The user to check
     * @return string The digest frequency ('immediate', 'daily', 'weekly')
     */
    public function getDigestFrequency(User $user): string
    {
        $preferences = $this->getPreferences($user);
        $frequency = $preferences['digest_frequency'] ?? self::DIGEST_IMMEDIATE;

        // Validate and return
        if (in_array($frequency, [self::DIGEST_IMMEDIATE, self::DIGEST_DAILY, self::DIGEST_WEEKLY], true)) {
            return $frequency;
        }

        return self::DIGEST_IMMEDIATE;
    }

    /**
     * Check if in-app notifications are enabled for a user
     *
     * @param  User  $user  The user to check
     * @return bool True if in-app notifications are enabled
     */
    public function isInAppEnabled(User $user): bool
    {
        $preferences = $this->getPreferences($user);

        return $preferences['in_app_enabled'] ?? true;
    }

    /**
     * Check if real-time (WebSocket) notifications are enabled
     *
     * @param  User  $user  The user to check
     * @return bool True if real-time notifications are enabled
     */
    public function isRealtimeEnabled(User $user): bool
    {
        $preferences = $this->getPreferences($user);

        return $preferences['realtime_notifications'] ?? true;
    }

    /**
     * Get notification channels for a specific notification type
     *
     * @param  User  $user  The recipient user
     * @param  string  $notificationType  The notification type
     * @return array<int, string> Array of channels
     */
    public function getChannelsForType(User $user, string $notificationType): array
    {
        $channels = [];
        $preferences = $this->getPreferences($user);

        // Database channel is always included (audit trail requirement)
        $channels[] = 'database';

        // Check if this notification type is enabled
        $typeEnabled = $preferences[$notificationType] ?? true;
        $isCritical = $this->isCriticalType($notificationType);

        // Email channel
        if ($isCritical || ($typeEnabled && $this->shouldSendEmail($user, $notificationType))) {
            $channels[] = 'mail';
        }

        // Broadcast channel (WebSocket)
        if ($isCritical || ($typeEnabled && $this->isRealtimeEnabled($user))) {
            $channels[] = 'broadcast';
        }

        return $channels;
    }

    /**
     * Reset user preferences to defaults
     *
     * @param  User  $user  The user to reset preferences for
     */
    public function resetToDefaults(User $user): void
    {
        $user->notification_preferences = self::DEFAULT_PREFERENCES;
        $user->save();

        $this->logPreferenceUpdate($user, ['action' => 'reset_to_defaults']);
    }

    /**
     * Get available notification types with metadata
     *
     * @return array<string, array{label: string, description: string, category: string, user_controllable: bool}>
     */
    public function getAvailableNotificationTypes(): array
    {
        return [
            'ticket_updates' => [
                'label' => __('notifications.types.ticket_updates'),
                'description' => __('notifications.descriptions.ticket_updates'),
                'category' => 'helpdesk',
                'user_controllable' => true,
            ],
            'ticket_assignments' => [
                'label' => __('notifications.types.ticket_assignments'),
                'description' => __('notifications.descriptions.ticket_assignments'),
                'category' => 'helpdesk',
                'user_controllable' => true,
            ],
            'ticket_comments' => [
                'label' => __('notifications.types.ticket_comments'),
                'description' => __('notifications.descriptions.ticket_comments'),
                'category' => 'helpdesk',
                'user_controllable' => true,
            ],
            'sla_alerts' => [
                'label' => __('notifications.types.sla_alerts'),
                'description' => __('notifications.descriptions.sla_alerts'),
                'category' => 'system',
                'user_controllable' => false, // Critical - cannot be disabled
            ],
            'system_announcements' => [
                'label' => __('notifications.types.system_announcements'),
                'description' => __('notifications.descriptions.system_announcements'),
                'category' => 'system',
                'user_controllable' => false, // Critical - cannot be disabled
            ],
            'loan_updates' => [
                'label' => __('notifications.types.loan_updates'),
                'description' => __('notifications.descriptions.loan_updates'),
                'category' => 'loan',
                'user_controllable' => true,
            ],
            'loan_approvals' => [
                'label' => __('notifications.types.loan_approvals'),
                'description' => __('notifications.descriptions.loan_approvals'),
                'category' => 'loan',
                'user_controllable' => true,
            ],
            'loan_reminders' => [
                'label' => __('notifications.types.loan_reminders'),
                'description' => __('notifications.descriptions.loan_reminders'),
                'category' => 'loan',
                'user_controllable' => true,
            ],
        ];
    }

    /**
     * Check if a notification type should be queued for digest
     *
     * @param  User  $user  The recipient user
     * @param  string  $notificationType  The notification type
     * @return bool True if should be queued for digest
     */
    public function shouldQueueForDigest(User $user, string $notificationType): bool
    {
        // Critical notifications are never queued for digest
        if ($this->isCriticalType($notificationType)) {
            return false;
        }

        $preferences = $this->getPreferences($user);

        // Check if email is enabled and type is enabled
        if (! $preferences['email_enabled']) {
            return false;
        }

        if (isset($preferences[$notificationType]) && ! $preferences[$notificationType]) {
            return false;
        }

        // Queue for digest if not immediate
        $digestFrequency = $this->getDigestFrequency($user);

        return $digestFrequency !== self::DIGEST_IMMEDIATE;
    }

    /**
     * Check if notification type is critical (cannot be disabled)
     *
     * @param  string  $type  The notification type
     * @return bool True if critical
     */
    private function isCriticalType(string $type): bool
    {
        return in_array($type, self::CRITICAL_TYPES, true);
    }

    /**
     * Log preference update for audit compliance
     *
     * @param  User  $user  The user whose preferences were updated
     * @param  array<string, bool|string>  $changes  The changes made
     */
    private function logPreferenceUpdate(User $user, array $changes): void
    {
        Log::channel('single')->info('Notification preferences updated', [
            'action' => 'notification_preferences_updated',
            'user_id' => $user->id,
            'email' => $user->email,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}

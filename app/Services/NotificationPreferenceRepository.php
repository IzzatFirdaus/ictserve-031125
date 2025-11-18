<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Centralized notification preference management.
 *
 * This service:
 * - Determines if notifications should be sent based on user preferences
 * - Enforces critical notification rules (bypasses preferences for urgent notifications)
 * - Logs preference decisions for audit trail
 * - Provides preference statistics and analytics
 *
 * Uses config/notifications.php for type definitions and priority settings.
 *
 * Trace: D03 SRS-FR-043 (notification preferences), D04 §6.2 (notification service architecture)
 */
class NotificationPreferenceRepository
{
    /**
     * Determine if email notification should be sent to user.
     *
     * Decision hierarchy:
     * 1. Critical types ALWAYS send (bypass preferences)
     * 2. User preferences for specific type
     * 3. High-priority types send unless explicitly disabled
     * 4. Default to user's global email preference
     *
     * @param  User  $user  The recipient user
     * @param  string  $type  Notification type (ticket_updates, loan_approvals, etc.)
     * @param  string|null  $priority  Optional priority override (critical, high, normal, low)
     * @return bool True if email should be sent
     */
    public function shouldSendEmail(User $user, string $type, ?string $priority = null): bool
    {
        // Critical notifications always send (security/compliance requirement)
        if ($this->isCriticalType($type) || $priority === 'critical') {
            $this->logPreferenceDecision($user, $type, true, 'critical_bypass');

            return true;
        }

        // Check user's specific preference for this type
        if ($user->wantsEmailNotifications($type)) {
            $this->logPreferenceDecision($user, $type, true, 'user_preference_enabled');

            return true;
        }

        // High-priority notifications send unless user explicitly disabled them
        if ($this->isHighPriorityType($type) && $priority === 'high') {
            $hasExplicitDisable = $this->hasExplicitDisable($user, $type);
            if (! $hasExplicitDisable) {
                $this->logPreferenceDecision($user, $type, true, 'high_priority_default');

                return true;
            }
        }

        // User preference says no
        $this->logPreferenceDecision($user, $type, false, 'user_preference_disabled');

        return false;
    }

    /**
     * Determine if database notification should be sent.
     *
     * Database notifications are ALWAYS sent regardless of preferences because:
     * - They don't interrupt the user (passive storage)
     * - User can review them at their convenience
     * - Required for audit trail and notification history
     *
     * @param  User  $user  The recipient user
     * @param  string  $type  Notification type
     * @return bool Always true
     */
    public function shouldSendDatabaseNotification(User $user, string $type): bool
    {
        // Database notifications always sent (non-intrusive)
        $this->logPreferenceDecision($user, $type, true, 'database_always_enabled');

        return true;
    }

    /**
     * Determine if broadcast notification should be sent.
     *
     * Broadcast notifications (real-time UI updates via WebSockets) are sent based on:
     * - Critical types always broadcast
     * - User's real-time notification preference
     * - High-priority types default to enabled
     *
     * @param  User  $user  The recipient user
     * @param  string  $type  Notification type
     * @param  string|null  $priority  Optional priority override
     * @return bool True if broadcast should be sent
     */
    public function shouldSendBroadcast(User $user, string $type, ?string $priority = null): bool
    {
        // Critical always broadcasts for real-time alerts
        if ($this->isCriticalType($type) || $priority === 'critical') {
            $this->logPreferenceDecision($user, $type, true, 'critical_broadcast');

            return true;
        }

        // Check if user wants real-time notifications (default: true)
        $preferences = $user->getNotificationPreferences();
        $realtimeEnabled = $preferences['realtime_notifications'] ?? true;

        if ($realtimeEnabled) {
            $this->logPreferenceDecision($user, $type, true, 'realtime_enabled');

            return true;
        }

        $this->logPreferenceDecision($user, $type, false, 'realtime_disabled');

        return false;
    }

    /**
     * Check if notification type bypasses user preferences.
     *
     * @param  string  $type  Notification type
     * @param  string|null  $priority  Optional priority level
     * @return bool True if preferences should be bypassed
     */
    public function shouldBypassPreferences(string $type, ?string $priority = null): bool
    {
        return $this->isCriticalType($type) || $priority === 'critical';
    }

    /**
     * Check if notification type is critical.
     *
     * @param  string  $type  Notification type
     * @return bool True if critical
     */
    private function isCriticalType(string $type): bool
    {
        return in_array($type, config('notifications.critical_types', []), true);
    }

    /**
     * Check if notification type is high priority.
     *
     * @param  string  $type  Notification type
     * @return bool True if high priority
     */
    private function isHighPriorityType(string $type): bool
    {
        return in_array($type, config('notifications.high_priority_types', []), true);
    }

    /**
     * Check if user has explicitly disabled this notification type.
     *
     * This distinguishes between:
     * - User never set preference (null/undefined) → use defaults
     * - User explicitly disabled (false) → respect their choice
     *
     * @param  User  $user  The user to check
     * @param  string  $type  Notification type
     * @return bool True if user explicitly disabled
     */
    private function hasExplicitDisable(User $user, string $type): bool
    {
        $preferences = $user->getNotificationPreferences();

        // If key doesn't exist, not explicitly disabled (use default)
        if (! array_key_exists($type, $preferences)) {
            return false;
        }

        // If key exists and is false, explicitly disabled
        return $preferences[$type] === false;
    }

    /**
     * Log preference decision for audit trail.
     *
     * Logs to Laravel log for debugging and creates activity log record
     * for compliance/audit requirements.
     *
     * @param  User  $user  The user affected
     * @param  string  $type  Notification type
     * @param  bool  $allowed  Whether notification was allowed
     * @param  string  $reason  Decision reason (critical_bypass, user_preference_enabled, etc.)
     */
    private function logPreferenceDecision(User $user, string $type, bool $allowed, string $reason): void
    {
        $action = $allowed ? 'allowed' : 'blocked';

        Log::channel('notifications')->info("Notification {$action}", [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'notification_type' => $type,
            'decision' => $action,
            'reason' => $reason,
            'timestamp' => now()->toIso8601String(),
        ]);

        // Also log to audit log for long-term tracking
        Log::channel('notifications')->info('Notification audit trail', [
            'user_id' => $user->id,
            'notification_type' => $type,
            'decision' => $action,
            'reason' => $reason,
            'audit_type' => 'notification_preference',
        ]);
    }

    /**
     * Get notification preference statistics for user.
     *
     * Useful for:
     * - User preference management UI
     * - Admin analytics dashboard
     * - Debugging notification issues
     *
     * @param  User  $user  The user to analyze
     * @return array Statistics about notification preferences
     */
    public function getPreferenceStatistics(User $user): array
    {
        $preferences = $user->getNotificationPreferences();
        $enabled = array_filter($preferences, fn ($value) => $value === true);
        $disabled = array_filter($preferences, fn ($value) => $value === false);

        return [
            'total_preferences' => count($preferences),
            'enabled_count' => count($enabled),
            'disabled_count' => count($disabled),
            'enabled_types' => array_keys($enabled),
            'disabled_types' => array_keys($disabled),
            'critical_types_count' => count(config('notifications.critical_types', [])),
            'high_priority_types_count' => count(config('notifications.high_priority_types', [])),
        ];
    }

    /**
     * Get list of all available notification types.
     *
     * Used for:
     * - Building preference management UI
     * - Validating notification type inputs
     * - Generating documentation
     *
     * @return array List of notification types with metadata
     */
    public function getAvailableNotificationTypes(): array
    {
        // Load notification types from config with enriched metadata
        $types = config('notifications.types', []);
        $criticalTypes = config('notifications.critical_types', []);
        $highPriorityTypes = config('notifications.high_priority_types', []);

        $result = [];

        foreach ($types as $key => $metadata) {
            // Determine priority based on configuration
            $priority = 'normal';
            if (in_array($key, $criticalTypes, true)) {
                $priority = 'critical';
            } elseif (in_array($key, $highPriorityTypes, true)) {
                $priority = 'high';
            }

            $result[$key] = [
                'label' => $metadata['name'] ?? ucfirst(str_replace('_', ' ', $key)),
                'description' => $metadata['description'] ?? '',
                'category' => $metadata['category'] ?? 'system',
                'priority' => $priority,
                'user_controllable' => ! in_array($key, $criticalTypes, true),
            ];
        }

        return $result;
    }

    /**
     * Validate if notification type exists and is valid.
     *
     * @param  string  $type  Notification type to validate
     * @return bool True if valid
     */
    public function isValidNotificationType(string $type): bool
    {
        $availableTypes = $this->getAvailableNotificationTypes();

        return array_key_exists($type, $availableTypes);
    }

    /**
     * Get notification channels for this type based on user preferences.
     *
     * Returns array of channels that should be used for this notification.
     *
     * @param  User  $user  The recipient user
     * @param  string  $type  Notification type
     * @param  string|null  $priority  Optional priority override
     * @return array Array of channels: ['mail', 'database', 'broadcast']
     */
    public function getChannelsForNotification(User $user, string $type, ?string $priority = null): array
    {
        $channels = [];

        // Database always included (audit trail)
        $channels[] = 'database';

        // Email channel based on preferences
        if ($this->shouldSendEmail($user, $type, $priority)) {
            $channels[] = 'mail';
        }

        // Broadcast channel based on preferences
        if ($this->shouldSendBroadcast($user, $type, $priority)) {
            $channels[] = 'broadcast';
        }

        return $channels;
    }
}

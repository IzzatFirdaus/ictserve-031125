<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\NotificationPreferenceServiceInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Notification Preference Service Implementation for ICTServe v3.6.1
 *
 * Manages user notification preferences including email frequency settings
 * (immediate, daily digest, weekly digest), per-notification-type toggles,
 * quiet hours enforcement, and timezone-aware preference handling.
 *
 * @see D16_BROADCASTING_SETUP.md WebSocket configuration
 * @see D03 SRS-ADM-006 Staff Dashboard notification preferences
 * @see Requirements 5.1-5.7 - Notification preferences configuration
 */
class NotificationPreferenceService implements NotificationPreferenceServiceInterface
{
    private const DIGEST_IMMEDIATE = 'immediate';

    private const DIGEST_DAILY = 'daily';

    private const DIGEST_WEEKLY = 'weekly';

    private const DEFAULT_TIMEZONE = 'Asia/Kuala_Lumpur';

    /** @var array<string, bool|string|null> */
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
        'quiet_hours_enabled' => false,
        'quiet_hours_start' => null,
        'quiet_hours_end' => null,
        'timezone' => self::DEFAULT_TIMEZONE,
    ];

    /** @var array<int, string> */
    private const CRITICAL_TYPES = [
        'sla_alerts',
        'system_announcements',
        'security_alerts',
    ];

    /** @var array<int, string> */
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
        'quiet_hours_enabled',
        'quiet_hours_start',
        'quiet_hours_end',
        'timezone',
    ];

    /**
     * Get all notification preferences for a user
     *
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
     *     in_app_enabled: bool,
     *     quiet_hours_enabled: bool,
     *     quiet_hours_start: string|null,
     *     quiet_hours_end: string|null,
     *     timezone: string
     * }
     */
    public function getPreferences(User $user): array
    {
        $storedPreferences = $user->notification_preferences ?? [];
        $preferences = [...self::DEFAULT_PREFERENCES, ...$storedPreferences];

        foreach ($preferences as $key => $value) {
            if ($key === 'digest_frequency') {
                if (! \in_array($value, [self::DIGEST_IMMEDIATE, self::DIGEST_DAILY, self::DIGEST_WEEKLY], true)) {
                    $preferences[$key] = self::DIGEST_IMMEDIATE;
                }
            } elseif ($key === 'timezone') {
                if (empty($value) || ! $this->isValidTimezone((string) $value)) {
                    $preferences[$key] = self::DEFAULT_TIMEZONE;
                }
            } elseif (! \in_array($key, ['quiet_hours_start', 'quiet_hours_end'], true)) {
                $preferences[$key] = (bool) $value;
            }
        }

        /** @var array{ticket_updates: bool, ticket_assignments: bool, ticket_comments: bool, sla_alerts: bool, system_announcements: bool, loan_updates: bool, loan_approvals: bool, loan_reminders: bool, realtime_notifications: bool, digest_frequency: string, email_enabled: bool, in_app_enabled: bool, quiet_hours_enabled: bool, quiet_hours_start: string|null, quiet_hours_end: string|null, timezone: string} $preferences */
        return $preferences;
    }

    /**
     * @param  array<string, mixed>  $preferences
     */
    public function updatePreferences(User $user, array $preferences): void
    {
        $invalidKeys = \array_diff(\array_keys($preferences), self::VALID_PREFERENCE_KEYS);
        if (! empty($invalidKeys)) {
            throw new \InvalidArgumentException(
                'Invalid preference keys: '.\implode(', ', $invalidKeys)
            );
        }

        if (isset($preferences['digest_frequency'])) {
            $validFrequencies = [self::DIGEST_IMMEDIATE, self::DIGEST_DAILY, self::DIGEST_WEEKLY];
            if (! \in_array($preferences['digest_frequency'], $validFrequencies, true)) {
                throw new \InvalidArgumentException(
                    'Invalid digest_frequency value. Must be one of: '.\implode(', ', $validFrequencies)
                );
            }
        }

        if (isset($preferences['timezone']) && ! $this->isValidTimezone((string) $preferences['timezone'])) {
            throw new \InvalidArgumentException('Invalid timezone value.');
        }

        $currentPreferences = $this->getPreferences($user);
        $updatedPreferences = [...$currentPreferences, ...$preferences];

        foreach ($updatedPreferences as $key => $value) {
            if (! \in_array($key, ['digest_frequency', 'quiet_hours_start', 'quiet_hours_end', 'timezone'], true)) {
                $updatedPreferences[$key] = (bool) $value;
            }
        }

        $user->notification_preferences = $updatedPreferences;
        $user->save();

        $this->logPreferenceUpdate($user, $preferences);
    }

    public function shouldSendEmail(User $user, string $notificationType): bool
    {
        $preferences = $this->getPreferences($user);

        if (! $preferences['email_enabled']) {
            return false;
        }

        if ($this->isCriticalType($notificationType)) {
            return true;
        }

        if (isset($preferences[$notificationType]) && ! $preferences[$notificationType]) {
            return false;
        }

        return $this->getDigestFrequency($user) === self::DIGEST_IMMEDIATE;
    }

    public function getDigestFrequency(User $user): string
    {
        $preferences = $this->getPreferences($user);
        $frequency = $preferences['digest_frequency'] ?? self::DIGEST_IMMEDIATE;

        if (\in_array($frequency, [self::DIGEST_IMMEDIATE, self::DIGEST_DAILY, self::DIGEST_WEEKLY], true)) {
            return $frequency;
        }

        return self::DIGEST_IMMEDIATE;
    }

    public function isInAppEnabled(User $user): bool
    {
        return $this->getPreferences($user)['in_app_enabled'] ?? true;
    }

    public function isRealtimeEnabled(User $user): bool
    {
        return $this->getPreferences($user)['realtime_notifications'] ?? true;
    }

    /**
     * @return array<int, string>
     */
    public function getChannelsForType(User $user, string $notificationType): array
    {
        $channels = ['database'];
        $preferences = $this->getPreferences($user);

        $typeEnabled = $preferences[$notificationType] ?? true;
        $isCritical = $this->isCriticalType($notificationType);

        if ($isCritical || ($typeEnabled && $this->shouldSendEmail($user, $notificationType))) {
            $channels[] = 'mail';
        }

        if ($isCritical || ($typeEnabled && $this->isRealtimeEnabled($user))) {
            $channels[] = 'broadcast';
        }

        return $channels;
    }

    public function resetToDefaults(User $user): void
    {
        $user->notification_preferences = self::DEFAULT_PREFERENCES;
        $user->save();

        $this->logPreferenceUpdate($user, ['action' => 'reset_to_defaults']);
    }

    /**
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
                'user_controllable' => false,
            ],
            'system_announcements' => [
                'label' => __('notifications.types.system_announcements'),
                'description' => __('notifications.descriptions.system_announcements'),
                'category' => 'system',
                'user_controllable' => false,
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

    public function shouldQueueForDigest(User $user, string $notificationType): bool
    {
        if ($this->isCriticalType($notificationType)) {
            return false;
        }

        $preferences = $this->getPreferences($user);

        if (! $preferences['email_enabled']) {
            return false;
        }

        if (isset($preferences[$notificationType]) && ! $preferences[$notificationType]) {
            return false;
        }

        return $this->getDigestFrequency($user) !== self::DIGEST_IMMEDIATE;
    }

    /**
     * Set quiet hours for a user
     *
     * @throws \InvalidArgumentException If time format is invalid
     */
    public function setQuietHours(User $user, string $start, string $end, ?string $timezone = null): void
    {
        if (! $this->isValidTimeFormat($start) || ! $this->isValidTimeFormat($end)) {
            throw new \InvalidArgumentException('Invalid time format. Use H:i format (e.g., 22:00).');
        }

        if ($timezone !== null && ! $this->isValidTimezone($timezone)) {
            throw new \InvalidArgumentException('Invalid timezone.');
        }

        $preferences = $this->getPreferences($user);
        $preferences['quiet_hours_enabled'] = true;
        $preferences['quiet_hours_start'] = $start;
        $preferences['quiet_hours_end'] = $end;

        if ($timezone !== null) {
            $preferences['timezone'] = $timezone;
        }

        $user->notification_preferences = $preferences;
        $user->save();

        $this->logPreferenceUpdate($user, [
            'action' => 'set_quiet_hours',
            'start' => $start,
            'end' => $end,
            'timezone' => $timezone ?? $preferences['timezone'],
        ]);
    }

    /**
     * Check if user is currently in quiet hours (timezone-aware)
     */
    public function isInQuietHours(User $user): bool
    {
        $preferences = $this->getPreferences($user);

        if (! $preferences['quiet_hours_enabled']) {
            return false;
        }

        $start = $preferences['quiet_hours_start'];
        $end = $preferences['quiet_hours_end'];

        if ($start === null || $end === null) {
            return false;
        }

        $timezone = $preferences['timezone'] ?? self::DEFAULT_TIMEZONE;
        $now = Carbon::now($timezone)->format('H:i');

        if ($start < $end) {
            return $now >= $start && $now <= $end;
        }

        // Handle overnight quiet hours (e.g., 22:00 to 06:00)
        return $now >= $start || $now <= $end;
    }

    public function disableQuietHours(User $user): void
    {
        $preferences = $this->getPreferences($user);
        $preferences['quiet_hours_enabled'] = false;

        $user->notification_preferences = $preferences;
        $user->save();

        $this->logPreferenceUpdate($user, ['action' => 'disable_quiet_hours']);
    }

    /**
     * Bulk update preferences for multiple users
     *
     * @param  array<int>  $userIds
     * @param  array<string, bool|string>  $preferences
     * @return array{success: array<int>, failed: array<int, string>}
     */
    public function bulkUpdatePreferences(array $userIds, array $preferences): array
    {
        $result = ['success' => [], 'failed' => []];

        $invalidKeys = \array_diff(\array_keys($preferences), self::VALID_PREFERENCE_KEYS);
        if (! empty($invalidKeys)) {
            throw new \InvalidArgumentException(
                'Invalid preference keys: '.\implode(', ', $invalidKeys)
            );
        }

        DB::beginTransaction();

        try {
            /** @var \Illuminate\Database\Eloquent\Collection<int, User> $users */
            $users = User::whereIn('id', $userIds)->get();

            foreach ($users as $user) {
                try {
                    $this->updatePreferences($user, $preferences);
                    $result['success'][] = $user->id;
                } catch (\Exception $e) {
                    $result['failed'][$user->id] = $e->getMessage();
                }
            }

            DB::commit();

            Log::channel('single')->info('Bulk notification preferences updated', [
                'action' => 'bulk_notification_preferences_updated',
                'user_ids' => $userIds,
                'success_count' => \count($result['success']),
                'failed_count' => \count($result['failed']),
                'preferences' => $preferences,
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $result;
    }

    /**
     * Comprehensive check for notification delivery
     */
    public function shouldSendNotification(
        User $user,
        string $notificationType,
        string $channel,
        ?string $priority = null
    ): bool {
        // Critical notifications always go through
        if ($priority === 'critical' || $this->isCriticalType($notificationType)) {
            return true;
        }

        $preferences = $this->getPreferences($user);

        // Check if notification type is enabled
        if (isset($preferences[$notificationType]) && ! $preferences[$notificationType]) {
            return false;
        }

        // Check quiet hours for non-critical notifications
        if ($this->isInQuietHours($user)) {
            return false;
        }

        // Check channel-specific preferences
        return match ($channel) {
            'mail' => $preferences['email_enabled'] && $this->getDigestFrequency($user) === self::DIGEST_IMMEDIATE,
            'database' => $preferences['in_app_enabled'],
            'broadcast' => $preferences['realtime_notifications'],
            default => true,
        };
    }

    public function getUserTimezone(User $user): string
    {
        $preferences = $this->getPreferences($user);

        return $preferences['timezone'] ?? self::DEFAULT_TIMEZONE;
    }

    /**
     * @throws \InvalidArgumentException If timezone is invalid
     */
    public function setUserTimezone(User $user, string $timezone): void
    {
        if (! $this->isValidTimezone($timezone)) {
            throw new \InvalidArgumentException('Invalid timezone: '.$timezone);
        }

        $preferences = $this->getPreferences($user);
        $preferences['timezone'] = $timezone;

        $user->notification_preferences = $preferences;
        $user->save();

        $this->logPreferenceUpdate($user, ['action' => 'set_timezone', 'timezone' => $timezone]);
    }

    private function isCriticalType(string $type): bool
    {
        return \in_array($type, self::CRITICAL_TYPES, true);
    }

    private function isValidTimezone(string $timezone): bool
    {
        return \in_array($timezone, \DateTimeZone::listIdentifiers(), true);
    }

    private function isValidTimeFormat(string $time): bool
    {
        return (bool) \preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time);
    }

    /**
     * @param  array<string, mixed>  $changes
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

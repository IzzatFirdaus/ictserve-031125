<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\NotificationCreated;
use App\Models\User;
use App\Services\Notifications\EmailDispatcher;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Unified notification dispatcher implementing triple-channel pattern.
 *
 * This service orchestrates notifications across three channels:
 * 1. DATABASE: Always sent (passive storage, audit trail, user can review anytime)
 * 2. EMAIL: Sent based on user preferences (respects wantsEmailNotifications)
 * 3. BROADCAST: Real-time WebSocket notifications (for live UI updates)
 *
 * Decision flow:
 * - Check if notification is critical (bypasses preferences)
 * - Query user preferences via NotificationPreferenceRepository
 * - Dispatch to appropriate channels based on preferences
 * - Log all decisions for audit trail
 *
 * Trace: D03 SRS-FR-043 (notification system), D04 §6.2 (multi-channel architecture)
 * Pattern: Inspired by TicketNotificationService.sendMaintenanceNotification()
 */
class UnifiedNotificationDispatcher
{
    /**
     * Flag to indicate if the dispatcher is currently dispatching a notification.
     * Used by NotificationCreatedListener to avoid duplicate broadcasts.
     */
    private static bool $isDispatching = false;

    private static array $statistics = [
        'attempts' => 0,
        'failures' => 0,
        'total_dispatched' => 0,
        'by_channel' => [
            'database' => 0,
            'email' => 0,
            'broadcast' => 0,
        ],
        'by_type' => [],
    ];

    public function __construct(
        private NotificationPreferenceRepository $preferences,
        private EmailDispatcher $emailDispatcher
    ) {}

    /**
     * Check if the dispatcher is currently dispatching a notification.
     * Used by NotificationCreatedListener to avoid duplicate broadcasts.
     */
    public static function isDispatchingNotification(): bool
    {
        return self::$isDispatching;
    }

    /**
     * Dispatch notification to user across all applicable channels.
     *
     * This is the main entry point for sending notifications system-wide.
     * All notification code should eventually use this method for consistency.
     *
     * Channels dispatched:
     * - Database: ALWAYS (via $user->notify())
     * - Email: IF user preferences allow (via EmailDispatcher)
     * - Broadcast: IF user wants real-time updates (via NotificationCreated event)
     *
     * @param  User  $user  The notification recipient
     * @param  Notification  $notification  Laravel notification instance (for database + optional mail)
     * @param  Mailable|null  $mailable  Optional dedicated Mailable (if different from notification mail)
     * @param  array<string, mixed>  $meta  Metadata for email log (ticket_number, loan_id, etc.)
     * @param  string|null  $notificationType  Type for preference checking (ticket_updates, loan_approvals, etc.)
     * @param  string|null  $priority  Priority level (critical, high, normal, low)
     * @return array<string, mixed> Result of dispatch with channels used
     */

    /**
     * @param  array<string, mixed>  $meta
     */
    public function dispatch(
        User $user,
        Notification $notification,
        ?Mailable $mailable = null,
        array $meta = [],
        ?string $notificationType = null,
        ?string $priority = null
    ): array {
        $channelsUsed = [];
        $hadFailure = false;
        $notificationType = $notificationType ?? $this->inferNotificationType($notification);

        Log::channel('notifications')->info('UnifiedNotificationDispatcher starting', [
            'user_id' => $user->id,
            'notification_class' => \get_class($notification),
            'type' => $notificationType,
            'priority' => $priority,
            'has_mailable' => $mailable !== null,
        ]);

        // CHANNEL 1: DATABASE - ALWAYS send for audit trail
        // Set flag to prevent NotificationCreatedListener from dispatching duplicate broadcast
        self::$isDispatching = true;
        try {
            $user->notify($notification);
            $channelsUsed[] = 'database';

            Log::channel('notifications')->info('Database notification sent', [
                'user_id' => $user->id,
                'notification_class' => \get_class($notification),
            ]);
        } catch (\Exception $e) {
            $hadFailure = true;

            Log::channel('notifications')->error('Database notification failed', [
                'user_id' => $user->id,
                'notification_class' => \get_class($notification),
                'error' => $e->getMessage(),
            ]);
        } finally {
            self::$isDispatching = false;
        }

        // Get the database notification record for broadcast event
        $dbNotification = $user->notifications()->latest('created_at')->first();

        // DEBUG: Log if database notification was found
        if (! $dbNotification) {
            Log::channel('notifications')->warning('Database notification not found after $user->notify()', [
                'user_id' => $user->id,
                'total_notifications_count' => $user->notifications()->count(),
                'notification_class' => \get_class($notification),
            ]);
        }

        // CHANNEL 2: EMAIL - Send based on preferences
        if ($this->preferences->shouldSendEmail($user, $notificationType, $priority)) {
            try {
                // Use dedicated Mailable if provided, otherwise let notification handle it
                if ($mailable !== null) {
                    $this->emailDispatcher->queue(
                        $mailable,
                        $user->email,
                        $user->name,
                        $meta,
                        $notificationType,
                        $channelsUsed, // Pass actual channels used (email, database, broadcast)
                        $priority,
                        $priority === 'critical' // Bypass preferences for critical priority
                    );
                } else {
                    // Notification will send email via its via() and toMail() methods
                    // EmailLog tracking happens in Notification class if it implements LogsEmailDispatch trait
                }

                $channelsUsed[] = 'email';

                Log::channel('notifications')->info('Email notification queued', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'notification_type' => $notificationType,
                    'mailable_class' => $mailable ? get_class($mailable) : 'via_notification',
                ]);
            } catch (\Exception $e) {
                $hadFailure = true;

                Log::channel('notifications')->error('Email notification failed', [
                    'user_id' => $user->id,
                    'notification_type' => $notificationType,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            Log::channel('notifications')->info('Email notification skipped (user preference)', [
                'user_id' => $user->id,
                'notification_type' => $notificationType,
            ]);
        }

        // CHANNEL 3: BROADCAST - Send for real-time UI updates
        if ($this->preferences->shouldSendBroadcast($user, $notificationType, $priority)) {
            try {
                if ($dbNotification) {
                    event(new NotificationCreated($user, $dbNotification));
                    $channelsUsed[] = 'broadcast';

                    Log::channel('notifications')->info('Broadcast notification sent', [
                        'user_id' => $user->id,
                        'notification_id' => $dbNotification->id,
                        'notification_type' => $notificationType,
                    ]);
                }
            } catch (\Exception $e) {
                $hadFailure = true;

                Log::channel('notifications')->error('Broadcast notification failed', [
                    'user_id' => $user->id,
                    'notification_type' => $notificationType,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            Log::channel('notifications')->info('Broadcast notification skipped (user preference)', [
                'user_id' => $user->id,
                'notification_type' => $notificationType,
            ]);
        }

        $this->recordStatistics($notificationType, $channelsUsed, $hadFailure);

        return [
            'success' => count($channelsUsed) > 0,
            'channels_used' => $channelsUsed,
            'notification_type' => $notificationType,
            'priority' => $priority,
            'database_notification_id' => $dbNotification?->id,
        ];
    }

    /**
     * Dispatch notification to multiple users.
     *
     * Convenience method for bulk notifications (e.g., notify all admins, all approvers).
     *
     * @param  iterable<User>  $users  Collection or array of User models
     * @param  Notification  $notification  Notification to send
     * @param  Mailable|null  $mailable  Optional Mailable
     * @param  array<string, mixed>  $meta  Metadata
     * @param  string|null  $notificationType  Notification type
     * @param  string|null  $priority  Priority level
     * @return array<int, array<string, mixed>> Results for each user
     */

    /**
     * @param  array<string, mixed>  $meta
     */
    public function dispatchToMany(
        iterable $users,
        Notification $notification,
        ?Mailable $mailable = null,
        array $meta = [],
        ?string $notificationType = null,
        ?string $priority = null
    ): array {
        $results = [];

        foreach ($users as $user) {
            $results[$user->id] = $this->dispatch(
                $user,
                $notification,
                $mailable,
                $meta,
                $notificationType,
                $priority
            );
        }

        Log::channel('notifications')->info('Bulk notification dispatched', [
            'user_count' => count($results),
            'notification_class' => get_class($notification),
            'notification_type' => $notificationType,
        ]);

        return $results;
    }

    /**
     * Dispatch critical notification (bypasses all preferences).
     *
     * Use for:
     * - Security alerts (account compromised, suspicious activity)
     * - System maintenance (urgent downtime notifications)
     * - Compliance-required notifications (legal notices, data breach)
     * - Approval workflows (can't be disabled, part of business process)
     *
     * @param  User  $user  The recipient
     * @param  Notification  $notification  The notification
     * @param  Mailable|null  $mailable  Optional Mailable
     * @param  array<string, mixed>  $meta  Metadata
     * @param  string  $notificationType  Type (for logging)
     * @return array<string, mixed> Dispatch result
     */

    /**
     * @param  array<string, mixed>  $meta
     */
    public function dispatchCritical(
        User $user,
        Notification $notification,
        ?Mailable $mailable = null,
        array $meta = [],
        string $notificationType = 'critical_alert'
    ): array {
        return $this->dispatch(
            $user,
            $notification,
            $mailable,
            array_merge($meta, ['critical' => true]),
            $notificationType,
            'critical'  // Forces bypass of preferences
        );
    }

    /**
     * Dispatch email-only notification (no database or broadcast).
     *
     * Use cases:
     * - System-generated reports (not user-initiated, just info delivery)
     * - Automated exports (user requested, sent to email when ready)
     * - Scheduled digests (summary emails, don't need database storage)
     *
     * NOTE: This bypasses database storage - use sparingly!
     * Most notifications should go through dispatch() for audit trail.
     *
     * @param  User  $user  The recipient
     * @param  Mailable  $mailable  The email to send
     * @param  array<string, mixed>  $meta  Metadata for EmailLog
     * @param  string  $notificationType  Type for preference check
     * @return array<string, mixed> Dispatch result
     */

    /**
     * @param  array<string, mixed>  $meta
     */
    public function dispatchEmailOnly(
        User $user,
        Mailable $mailable,
        array $meta = [],
        string $notificationType = 'email_report'
    ): array {
        // Check preferences unless it's a critical type
        if (! $this->preferences->shouldSendEmail($user, $notificationType)) {
            Log::channel('notifications')->info('Email-only notification skipped (user preference)', [
                'user_id' => $user->id,
                'notification_type' => $notificationType,
            ]);

            $this->recordStatistics($notificationType, [], false);

            return [
                'success' => false,
                'channels_used' => [],
                'notification_type' => $notificationType,
                'reason' => 'user_preference_disabled',
            ];
        }

        try {
            $emailLog = $this->emailDispatcher->queue(
                $mailable,
                $user->email,
                $user->name,
                array_merge($meta, ['email_only' => true]),
                $notificationType,
                ['email'], // Email-only channel
                null, // Let inferPriority() handle it
                false // Not bypassing preferences
            );

            Log::channel('notifications')->info('Email-only notification queued', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'mailable_class' => get_class($mailable),
                'email_log_id' => $emailLog->id,
            ]);

            $this->recordStatistics($notificationType, ['email'], false);

            return [
                'success' => true,
                'channels_used' => ['email'],
                'notification_type' => $notificationType,
                'email_log_id' => $emailLog->id,
            ];
        } catch (\Exception $e) {
            $this->recordStatistics($notificationType, ['email'], true);

            Log::channel('notifications')->error('Email-only notification failed', [
                'user_id' => $user->id,
                'mailable_class' => get_class($mailable),
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'channels_used' => [],
                'notification_type' => $notificationType,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * @param  array<string>  $channelsUsed
     */

    /**
     * @param  array<string, mixed>  $channelsUsed
     */
    private function recordStatistics(string $notificationType, array $channelsUsed, bool $hadFailure): void
    {
        self::$statistics['attempts']++;

        if ($channelsUsed !== []) {
            self::$statistics['total_dispatched']++;
        }

        if ($hadFailure) {
            self::$statistics['failures']++;
        }

        foreach ($channelsUsed as $channel) {
            if (! isset(self::$statistics['by_channel'][$channel])) {
                self::$statistics['by_channel'][$channel] = 0;
            }

            self::$statistics['by_channel'][$channel]++;
        }

        if (! isset(self::$statistics['by_type'][$notificationType])) {
            self::$statistics['by_type'][$notificationType] = 0;
        }

        self::$statistics['by_type'][$notificationType]++;
    }

    /**
     * Infer notification type from notification class name.
     *
     * Maps notification classes to preference types:
     * - TicketStatusChanged → ticket_updates
     * - TicketAssigned → ticket_assignments
     * - NewComment → ticket_comments
     * - LoanApprovalRequired → loan_approvals
     * - etc.
     *
     * @param  Notification  $notification  The notification instance
     * @return string Inferred type
     */
    private function inferNotificationType(Notification $notification): string
    {
        $className = class_basename($notification);

        // Ticket-related notifications
        if (str_contains($className, 'TicketStatus') || str_contains($className, 'TicketUpdate')) {
            return 'ticket_updates';
        }
        if (str_contains($className, 'TicketAssign')) {
            return 'ticket_assignments';
        }
        if (str_contains($className, 'Comment')) {
            return 'ticket_comments';
        }
        if (str_contains($className, 'SLA')) {
            return 'sla_alerts';
        }

        // Loan-related notifications
        if (str_contains($className, 'LoanApproval') || str_contains($className, 'LoanApproved')) {
            return 'loan_approvals';
        }
        if (str_contains($className, 'LoanReminder') || str_contains($className, 'Overdue')) {
            return 'loan_reminders';
        }
        if (str_contains($className, 'LoanUpdate') || str_contains($className, 'LoanStatus')) {
            return 'loan_updates';
        }

        // System notifications
        if (str_contains($className, 'System') || str_contains($className, 'Announcement')) {
            return 'system_announcements';
        }

        // Default fallback
        return 'general_notification';
    }

    /**
     * Get dispatch statistics for monitoring and debugging.
     *
     * @return array Statistics about recent dispatches
     */
    public function getDispatchStatistics(): array
    {
        $stats = self::$statistics;
        $attempts = $stats['attempts'] > 0 ? $stats['attempts'] : 0;
        $failureRate = $attempts > 0 ? $stats['failures'] / $attempts : 0.0;

        return [
            'total_dispatched' => $stats['total_dispatched'],
            'by_channel' => $stats['by_channel'],
            'by_type' => $stats['by_type'],
            'failure_rate' => $failureRate,
        ];
    }
}

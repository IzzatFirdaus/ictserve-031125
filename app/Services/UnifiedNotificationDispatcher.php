<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\NotificationCreated;
use App\Models\User;
use App\Services\Notifications\EmailDispatcher;
use App\Services\Notifications\NotificationSecurityService;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Unified notification dispatcher implementing triple-channel pattern with Gmail API support.
 *
 * This service orchestrates notifications across three channels:
 * 1. DATABASE: Always sent (passive storage, audit trail, user can review anytime)
 * 2. EMAIL: Sent based on user preferences (supports Gmail API and SMTP fallback)
 * 3. BROADCAST: Real-time WebSocket notifications (for live UI updates)
 *
 * Gmail API Integration:
 * - Primary: Gmail API for @motac.gov.my users (when authenticated)
 * - Fallback: SMTP email when Gmail API is unavailable or quota exceeded
 * - Automatic method selection based on availability and verification status
 *
 * Decision flow:
 * - Check if notification is critical (bypasses preferences)
 * - Query user preferences via NotificationPreferenceRepository
 * - Select email delivery method (Gmail API or SMTP)
 * - Dispatch to appropriate channels based on preferences
 * - Log all decisions for audit trail
 *
 * Trace: D03 SRS-FR-043 (notification system), D04 §6.2 (multi-channel architecture)
 * Pattern: Inspired by TicketNotificationService.sendMaintenanceNotification()
 *
 * @see Requirements 10.1, 10.2, 10.3, 10.4
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
            'gmail_api' => 0,
            'smtp' => 0,
            'broadcast' => 0,
        ],
        'by_type' => [],
        'gmail_api_usage' => 0,
        'smtp_fallback_count' => 0,
    ];

    public function __construct(
        private NotificationPreferenceRepository $preferences,
        private EmailDispatcher $emailDispatcher,
        private ?GmailService $gmailService = null,
        private ?NotificationSecurityService $securityService = null
    ) {
        $this->securityService = $securityService ?? new NotificationSecurityService;
    }

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

        // Security: Sanitize metadata to prevent XSS and remove PII
        $sanitizedMeta = $this->securityService->sanitizeNotificationData($meta);

        // Security: Log dispatch attempt for audit trail
        $this->securityService->logSecurityEvent('notification_dispatch_started', [
            'notification_type' => $notificationType,
            'priority' => $priority,
            'channels_requested' => ['database', 'email', 'broadcast'],
        ], $user);

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

        // CHANNEL 2: EMAIL - Send based on preferences (Gmail API or SMTP)
        if ($this->preferences->shouldSendEmail($user, $notificationType, $priority)) {
            $emailResult = $this->sendEmailWithFallback(
                $user,
                $mailable,
                $notification,
                $sanitizedMeta,
                $notificationType,
                $channelsUsed,
                $priority
            );

            if ($emailResult['success']) {
                $channelsUsed[] = 'email';
                if ($emailResult['method'] === 'gmail_api') {
                    self::$statistics['gmail_api_usage']++;
                } elseif ($emailResult['method'] === 'smtp') {
                    self::$statistics['smtp_fallback_count']++;
                }

                Log::channel('notifications')->info('Email notification sent', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'notification_type' => $notificationType,
                    'method' => $emailResult['method'],
                    'mailable_class' => $mailable ? get_class($mailable) : 'via_notification',
                ]);
            } else {
                $hadFailure = true;

                Log::channel('notifications')->error('Email notification failed', [
                    'user_id' => $user->id,
                    'notification_type' => $notificationType,
                    'error' => $emailResult['error'] ?? 'Unknown error',
                    'method_attempted' => $emailResult['method'] ?? 'unknown',
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
            'gmail_api_usage' => $stats['gmail_api_usage'],
            'smtp_fallback_count' => $stats['smtp_fallback_count'],
            'gmail_api_percentage' => $stats['gmail_api_usage'] > 0
                ? round(($stats['gmail_api_usage'] / ($stats['gmail_api_usage'] + $stats['smtp_fallback_count'])) * 100, 2)
                : 0,
        ];
    }

    /**
     * Send email with Gmail API as primary and SMTP as fallback.
     *
     * @param  User  $user  The recipient
     * @param  Mailable|null  $mailable  Optional Mailable
     * @param  Notification  $notification  The notification
     * @param  array<string, mixed>  $meta  Metadata
     * @param  string|null  $notificationType  Notification type
     * @param  array<string>  $channelsUsed  Channels already used
     * @param  string|null  $priority  Priority level
     * @return array{success: bool, method: string, error?: string}
     */
    private function sendEmailWithFallback(
        User $user,
        ?Mailable $mailable,
        Notification $notification,
        array $meta,
        ?string $notificationType,
        array $channelsUsed,
        ?string $priority
    ): array {
        // Try Gmail API first if available and user is @motac.gov.my
        if ($this->shouldUseGmailApi($user)) {
            $gmailResult = $this->sendViaGmailApi($user, $mailable, $notification, $meta, $notificationType);

            if ($gmailResult['success']) {
                return $gmailResult;
            }

            // Log Gmail API failure and fall back to SMTP
            Log::channel('notifications')->warning('Gmail API failed, falling back to SMTP', [
                'user_id' => $user->id,
                'error' => $gmailResult['error'] ?? 'Unknown error',
            ]);
        }

        // Fall back to SMTP via EmailDispatcher
        return $this->sendViaSmtp($user, $mailable, $meta, $notificationType, $channelsUsed, $priority);
    }

    /**
     * Check if Gmail API should be used for this user.
     */
    private function shouldUseGmailApi(User $user): bool
    {
        // Check if Gmail service is available
        if (! $this->gmailService) {
            return false;
        }

        // Check if Gmail API is authenticated
        if (! $this->gmailService->isAuthenticated()) {
            return false;
        }

        // Check if user email is @motac.gov.my
        if (! str_ends_with(strtolower($user->email), '@motac.gov.my')) {
            return false;
        }

        // Check if Gmail API can send (quota and rate limits)
        if (! $this->gmailService->canSendEmail()) {
            return false;
        }

        return true;
    }

    /**
     * Send email via Gmail API.
     *
     * @return array{success: bool, method: string, message_id?: string, error?: string}
     */
    private function sendViaGmailApi(
        User $user,
        ?Mailable $mailable,
        Notification $notification,
        array $meta,
        ?string $notificationType
    ): array {
        try {
            // Get email content from mailable or notification
            $emailContent = $this->getEmailContent($mailable, $notification, $user);

            if (! $emailContent) {
                return [
                    'success' => false,
                    'method' => 'gmail_api',
                    'error' => 'Could not extract email content',
                ];
            }

            $messageId = $this->gmailService->sendEmail(
                $user->email,
                $emailContent['subject'],
                $emailContent['body'],
                config('mail.from.address'),
                $emailContent['attachments'] ?? []
            );

            // Log for audit
            activity('gmail_api_email')
                ->causedBy($user)
                ->withProperties([
                    'message_id' => $messageId,
                    'recipient' => $user->email,
                    'subject' => $emailContent['subject'],
                    'notification_type' => $notificationType,
                    'meta' => $meta,
                    'timestamp' => now()->toIso8601String(),
                ])
                ->log('Email sent via Gmail API');

            return [
                'success' => true,
                'method' => 'gmail_api',
                'message_id' => $messageId,
            ];
        } catch (GmailQuotaExceededException $e) {
            Log::channel('notifications')->warning('Gmail API quota exceeded', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'method' => 'gmail_api',
                'error' => 'Quota exceeded: '.$e->getMessage(),
            ];
        } catch (GmailRateLimitException $e) {
            Log::channel('notifications')->warning('Gmail API rate limited', [
                'user_id' => $user->id,
                'retry_after' => $e->getRetryAfterSeconds(),
            ]);

            return [
                'success' => false,
                'method' => 'gmail_api',
                'error' => 'Rate limited: '.$e->getMessage(),
            ];
        } catch (\Exception $e) {
            Log::channel('notifications')->error('Gmail API send failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'method' => 'gmail_api',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send email via SMTP (EmailDispatcher).
     *
     * @return array{success: bool, method: string, email_log_id?: int, error?: string}
     */
    private function sendViaSmtp(
        User $user,
        ?Mailable $mailable,
        array $meta,
        ?string $notificationType,
        array $channelsUsed,
        ?string $priority
    ): array {
        try {
            if ($mailable !== null) {
                $emailLog = $this->emailDispatcher->queue(
                    $mailable,
                    $user->email,
                    $user->name,
                    $meta,
                    $notificationType,
                    $channelsUsed,
                    $priority,
                    $priority === 'critical'
                );

                return [
                    'success' => true,
                    'method' => 'smtp',
                    'email_log_id' => $emailLog->id,
                ];
            }

            // Notification will send email via its via() and toMail() methods
            return [
                'success' => true,
                'method' => 'smtp',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'method' => 'smtp',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Extract email content from Mailable or Notification.
     *
     * @return array{subject: string, body: string, attachments?: array}|null
     */
    private function getEmailContent(?Mailable $mailable, Notification $notification, User $user): ?array
    {
        if ($mailable !== null) {
            try {
                $rendered = $mailable->render();
                $subject = 'No Subject';

                if (method_exists($mailable, 'envelope')) {
                    $envelope = $mailable->envelope();
                    $subject = $envelope->subject ?? 'No Subject';
                }

                return [
                    'subject' => $subject,
                    'body' => $rendered,
                    'attachments' => [],
                ];
            } catch (\Exception $e) {
                Log::channel('notifications')->error('Failed to render mailable', [
                    'mailable_class' => get_class($mailable),
                    'error' => $e->getMessage(),
                ]);

                return null;
            }
        }

        // Try to get content from notification's toMail method
        if (method_exists($notification, 'toMail')) {
            try {
                $mailMessage = $notification->toMail($user);

                if ($mailMessage) {
                    return [
                        'subject' => $mailMessage->subject ?? 'Notification',
                        'body' => $this->renderMailMessage($mailMessage),
                        'attachments' => [],
                    ];
                }
            } catch (\Exception $e) {
                Log::channel('notifications')->error('Failed to get notification mail content', [
                    'notification_class' => get_class($notification),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return null;
    }

    /**
     * Render a MailMessage to HTML.
     */
    private function renderMailMessage($mailMessage): string
    {
        // Simple HTML rendering of mail message
        $html = '<html><body>';

        if (property_exists($mailMessage, 'greeting') && $mailMessage->greeting) {
            $html .= '<h1>'.e($mailMessage->greeting).'</h1>';
        }

        if (property_exists($mailMessage, 'introLines')) {
            foreach ($mailMessage->introLines as $line) {
                $html .= '<p>'.e($line).'</p>';
            }
        }

        if (property_exists($mailMessage, 'actionText') && property_exists($mailMessage, 'actionUrl')) {
            $html .= '<p><a href="'.e($mailMessage->actionUrl).'">'.e($mailMessage->actionText).'</a></p>';
        }

        if (property_exists($mailMessage, 'outroLines')) {
            foreach ($mailMessage->outroLines as $line) {
                $html .= '<p>'.e($line).'</p>';
            }
        }

        $html .= '</body></html>';

        return $html;
    }

    /**
     * Get channel status for all notification channels.
     *
     * @return array{database: bool, email: array, broadcast: bool}
     */
    public function getChannelStatus(): array
    {
        $gmailStatus = $this->gmailService?->getHealthStatus() ?? ['is_authenticated' => false];

        return [
            'database' => true, // Always available
            'email' => [
                'smtp_available' => true,
                'gmail_api_available' => $gmailStatus['is_authenticated'] ?? false,
                'gmail_api_can_send' => $this->gmailService?->canSendEmail() ?? false,
                'gmail_quota' => $this->gmailService?->getQuotaUsage() ?? null,
                'preferred_method' => $this->gmailService?->isAuthenticated() ? 'gmail_api' : 'smtp',
            ],
            'broadcast' => config('broadcasting.default') !== null,
        ];
    }

    /**
     * Set user notification preferences.
     *
     * @param  User  $user  The user
     * @param  array<string, mixed>  $preferences  Preferences to set
     */
    public function setUserPreferences(User $user, array $preferences): void
    {
        $this->preferences->setPreferences($user, $preferences);

        Log::channel('notifications')->info('User notification preferences updated', [
            'user_id' => $user->id,
            'preferences' => $preferences,
        ]);
    }

    /**
     * Get user notification preferences.
     *
     * @param  User  $user  The user
     * @return array<string, mixed>
     */
    public function getUserPreferences(User $user): array
    {
        return $this->preferences->getPreferences($user);
    }
}

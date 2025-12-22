<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Jobs\RetryFailedEmail;
use App\Models\EmailLog;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Email dispatcher with intelligent retry logic.
 *
 * Responsibilities:
 * - Queue emails to email queue
 * - Create EmailLog tracking entries
 * - Configure retry behavior from config
 * - Schedule retry jobs for failed emails
 *
 * Retry strategy (from config/notifications.php):
 * - Max attempts: 3 (configurable)
 * - Backoff delays: [60s, 300s, 900s] (configurable)
 * - Permanent failure after 24 hours (configurable)
 *
 * Trace: D03 SRS-NFR-012 (email reliability), D04 §6.2 (notification architecture)
 */
class EmailDispatcher
{
    /**
     * Queue email to email queue and create tracking log.
     *
     * @param  Mailable  $mailable  The email to send
     * @param  string  $recipientEmail  Recipient email address
     * @param  string|null  $recipientName  Optional recipient name
     * @param  array  $meta  Additional metadata for logging
     * @param  string|null  $notificationType  Optional notification type from config
     * @param  array  $channels  Channels used in this notification dispatch (e.g., ['email', 'database'])
     * @param  string|null  $priority  Email priority (critical/high/normal/low)
     * @param  bool  $preferenceBypassed  Whether user preference was overridden
     * @return EmailLog The created email log entry
     */
    

/**
 * @param array<string, mixed> $channels
 */
public function queue(
        Mailable $mailable,
        string $recipientEmail,
        ?string $recipientName = null,
        array $meta = [],
        ?string $notificationType = null,
        array $channels = ['email'],
        ?string $priority = null,
        bool $preferenceBypassed = false
    ): EmailLog {
        // Load retry configuration
        $maxAttempts = config('notifications.email_retry.max_attempts', 3);
        $backoffDelays = config('notifications.email_retry.backoff_delays', [60, 300, 900]);

        // Infer priority if not explicitly provided
        $effectivePriority = $priority ?? $this->inferPriority($notificationType);

        // Calculate next retry time based on first backoff delay
        $nextRetryAt = now()->addSeconds($backoffDelays[0]);

        // Create email log for tracking
        $log = EmailLog::create([
            'recipient_email' => $recipientEmail,
            'recipient_name' => $recipientName,
            'subject' => method_exists($mailable, 'envelope')
                ? $mailable->envelope()->subject ?? 'No Subject'
                : 'No Subject',
            'mailable_class' => $mailable::class,
            'status' => 'queued',
            'queued_at' => now(),

            // Unified notification system tracking fields
            'channels' => $channels,
            'notification_type' => $notificationType,
            'priority' => $effectivePriority,
            'next_retry_at' => $nextRetryAt,
            'preference_bypassed' => $preferenceBypassed,

            'meta' => array_merge($meta, [
                'max_attempts' => $maxAttempts,
                'backoff_delays' => $backoffDelays,
            ]),
        ]);

        // Attach email log to mailable if supported
        if (method_exists($mailable, 'withEmailLog')) {
            $mailable->withEmailLog($log);
        }

        try {
            // Queue to email queue
            // Note: Retry configuration (tries/backoff) should be defined on the Mailable class itself
            // via public $tries property or tries() method, and public $backoff property or backoff() method
            Mail::to($recipientEmail, $recipientName)
                ->queue(
                    $mailable->onQueue(config('notifications.channels.email.queue', 'emails'))
                );

            Log::channel('notifications')->info('Email queued with retry config', [
                'email_log_id' => $log->id,
                'recipient' => $recipientEmail,
                'mailable' => $mailable::class,
                'notification_type' => $notificationType,
                'max_attempts' => $maxAttempts,
                'backoff_delays' => $backoffDelays,
            ]);

        } catch (\Throwable $exception) {
            $log->markAsFailed($exception->getMessage());

            Log::channel('notifications')->error('Failed to queue email', [
                'email_log_id' => $log->id,
                'recipient' => $recipientEmail,
                'mailable' => $mailable::class,
                'error' => $exception->getMessage(),
            ]);

            // Schedule retry job as fallback
            $this->scheduleRetry($log, $mailable, $meta);

            throw $exception;
        }

        return $log;
    }

    /**
     * Schedule retry job for failed email.
     *
     * This is a fallback mechanism when immediate queueing fails.
     * The retry job will handle exponential backoff and max attempts.
     *
     * @param  EmailLog  $log  The email log entry
     * @param  Mailable  $mailable  The mailable instance
     * @param  array  $meta  Additional metadata
     */
    

/**
 * @param array<string, mixed> $meta
 */
private function scheduleRetry(EmailLog $log, Mailable $mailable, array $meta): void
    {
        try {
            // Extract constructor args if available (for mailable recreation)
            $mailableData = $meta['mailable_data'] ?? [];

            RetryFailedEmail::dispatch($log, $mailable::class, $mailableData)
                ->delay(now()->addSeconds(60)); // First retry after 60 seconds

            Log::channel('notifications')->info('Scheduled retry job for failed email', [
                'email_log_id' => $log->id,
                'first_retry_delay' => '60 seconds',
            ]);

        } catch (\Throwable $exception) {
            Log::channel('notifications')->error('Failed to schedule retry job', [
                'email_log_id' => $log->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Infer email priority from notification type.
     *
     * Looks up the notification type in config/notifications.php
     * to determine its priority level.
     *
     * @param  string|null  $notificationType  The notification type
     * @return string The priority level (critical/high/normal/low)
     */
    private function inferPriority(?string $notificationType): string
    {
        if (! $notificationType) {
            return 'normal';
        }

        // Get all notification types from config
        $notificationTypes = config('notifications.notification_types', []);

        // Find matching notification type
        foreach ($notificationTypes as $type) {
            if ($type['id'] === $notificationType) {
                return $type['priority'] ?? 'normal';
            }
        }

        // Default to normal if type not found
        return 'normal';
    }
}

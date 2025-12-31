<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Jobs\RetryFailedEmail;
use App\Models\EmailLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Email dispatcher with intelligent retry logic and advanced features.
 *
 * Responsibilities:
 * - Queue emails to email queue
 * - Create EmailLog tracking entries
 * - Configure retry behavior from config
 * - Schedule retry jobs for failed emails
 * - Bulk email processing
 * - Email preview functionality
 * - Email validation
 * - Delivery metrics and reporting
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
     * @param  array<string, mixed>  $meta  Additional metadata for logging
     * @param  string|null  $notificationType  Optional notification type from config
     * @param  array<int, string>  $channels  Channels used in this notification dispatch (e.g., ['email', 'database'])
     * @param  string|null  $priority  Email priority (critical/high/normal/low)
     * @param  bool  $preferenceBypassed  Whether user preference was overridden
     * @return EmailLog The created email log entry
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
        // Validate email before queueing
        if (! $this->validateEmail($recipientEmail)) {
            throw new \InvalidArgumentException("Invalid email address: {$recipientEmail}");
        }

        // Load retry configuration
        $maxAttempts = config('notifications.email_retry.max_attempts', 3);
        $backoffDelays = config('notifications.email_retry.backoff_delays', [60, 300, 900]);

        // Infer priority if not explicitly provided
        $effectivePriority = $priority ?? $this->inferPriority($notificationType);

        // Calculate next retry time based on first backoff delay
        $nextRetryAt = now()->addSeconds($backoffDelays[0]);

        // Generate tracking ID for analytics
        $trackingId = Str::uuid()->toString();

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
                'tracking_id' => $trackingId,
            ]),
        ]);

        // Attach email log to mailable if supported
        if (method_exists($mailable, 'withEmailLog')) {
            $mailable->withEmailLog($log);
        }

        // Attach tracking ID to mailable if supported
        if (method_exists($mailable, 'withTrackingId')) {
            $mailable->withTrackingId($trackingId);
        }

        try {
            // Determine queue based on priority
            $queue = $this->getQueueForPriority($effectivePriority);

            // Queue to email queue
            Mail::to($recipientEmail, $recipientName)
                ->queue(
                    $mailable->onQueue($queue)
                );

            Log::channel('notifications')->info('Email queued with retry config', [
                'email_log_id' => $log->id,
                'recipient' => $recipientEmail,
                'mailable' => $mailable::class,
                'notification_type' => $notificationType,
                'priority' => $effectivePriority,
                'queue' => $queue,
                'max_attempts' => $maxAttempts,
                'backoff_delays' => $backoffDelays,
                'tracking_id' => $trackingId,
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
     * Queue bulk emails to multiple recipients.
     *
     * @param  Mailable  $mailable  The email template to send
     * @param  array<int, array{email: string, name?: string|null, data?: array<string, mixed>}>  $recipients  Array of recipient data
     * @param  array<string, mixed>  $meta  Additional metadata for logging
     * @param  string|null  $notificationType  Optional notification type from config
     * @param  string|null  $priority  Email priority (critical/high/normal/low)
     * @param  int  $batchSize  Number of emails to process per batch
     * @return array{success: int, failed: int, logs: array<int, EmailLog>, errors: array<int, array{email: string, error: string}>}
     */
    public function queueBulk(
        Mailable $mailable,
        array $recipients,
        array $meta = [],
        ?string $notificationType = null,
        ?string $priority = null,
        int $batchSize = 50
    ): array {
        $results = [
            'success' => 0,
            'failed' => 0,
            'logs' => [],
            'errors' => [],
        ];

        // Generate campaign ID for bulk tracking
        $campaignId = Str::uuid()->toString();
        $meta['campaign_id'] = $campaignId;
        $meta['bulk_send'] = true;
        $meta['total_recipients'] = \count($recipients);

        // Process in batches
        $batches = array_chunk($recipients, $batchSize);

        foreach ($batches as $batchIndex => $batch) {
            foreach ($batch as $recipient) {
                $email = $recipient['email'] ?? '';
                $name = $recipient['name'] ?? null;
                $recipientData = $recipient['data'] ?? [];

                // Skip invalid emails
                if (! $this->validateEmail($email)) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'email' => $email,
                        'error' => 'Invalid email address',
                    ];

                    continue;
                }

                try {
                    // Clone mailable for each recipient if it has custom data
                    $recipientMailable = clone $mailable;

                    // Apply recipient-specific data if mailable supports it
                    if (method_exists($recipientMailable, 'withRecipientData') && ! empty($recipientData)) {
                        $recipientMailable->withRecipientData($recipientData);
                    }

                    $log = $this->queue(
                        $recipientMailable,
                        $email,
                        $name,
                        array_merge($meta, ['batch_index' => $batchIndex]),
                        $notificationType,
                        ['email'],
                        $priority,
                        false
                    );

                    $results['success']++;
                    $results['logs'][] = $log;

                } catch (\Throwable $exception) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'email' => $email,
                        'error' => $exception->getMessage(),
                    ];

                    Log::channel('notifications')->error('Bulk email failed for recipient', [
                        'email' => $email,
                        'campaign_id' => $campaignId,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }

            // Small delay between batches to prevent overwhelming the queue
            if ($batchIndex < \count($batches) - 1) {
                usleep(100000); // 100ms delay
            }
        }

        Log::channel('notifications')->info('Bulk email dispatch completed', [
            'campaign_id' => $campaignId,
            'total_recipients' => \count($recipients),
            'success' => $results['success'],
            'failed' => $results['failed'],
        ]);

        return $results;
    }

    /**
     * Preview an email without sending it.
     *
     * @param  Mailable  $mailable  The email to preview
     * @param  array<string, mixed>  $data  Optional data to pass to the mailable
     * @return array{subject: string, html: string, text: string|null}
     */
    public function preview(Mailable $mailable, array $data = []): array
    {
        // Apply data to mailable if it supports it
        if (method_exists($mailable, 'withRecipientData') && ! empty($data)) {
            $mailable->withRecipientData($data);
        }

        // Render the mailable
        $rendered = $mailable->render();

        // Get subject
        $subject = 'No Subject';
        if (method_exists($mailable, 'envelope')) {
            $envelope = $mailable->envelope();
            $subject = $envelope->subject ?? 'No Subject';
        }

        // Try to extract plain text version
        $textContent = null;
        if (method_exists($mailable, 'content')) {
            $content = $mailable->content();
            if (property_exists($content, 'text') && $content->text) {
                $textContent = view($content->text, $mailable->viewData ?? [])->render();
            }
        }

        // Fallback: strip HTML tags for plain text
        if ($textContent === null) {
            $textContent = strip_tags(
                preg_replace('/<br\s*\/?>/i', "\n", $rendered) ?? $rendered
            );
        }

        return [
            'subject' => $subject,
            'html' => $rendered,
            'text' => $textContent,
        ];
    }

    /**
     * Validate an email address according to RFC 5322.
     *
     * @param  string  $email  The email address to validate
     * @return bool True if valid, false otherwise
     */
    public function validateEmail(string $email): bool
    {
        // Basic format validation using filter_var (RFC 5322 compliant)
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        // Additional checks for common issues
        $email = trim($email);

        // Check for empty string
        if ($email === '') {
            return false;
        }

        // Check for valid domain part
        $parts = explode('@', $email);
        if (\count($parts) !== 2) {
            return false;
        }

        [$local, $domain] = $parts;

        // Check local part length (max 64 characters)
        if (\strlen($local) > 64) {
            return false;
        }

        // Check domain length (max 255 characters)
        if (\strlen($domain) > 255) {
            return false;
        }

        // Check for valid domain format
        if (! preg_match('/^[a-zA-Z0-9]([a-zA-Z0-9-]*[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9-]*[a-zA-Z0-9])?)*$/', $domain)) {
            return false;
        }

        // Check for at least one dot in domain (TLD required)
        if (! str_contains($domain, '.')) {
            return false;
        }

        // Check TLD length (min 2 characters)
        $tld = substr($domain, strrpos($domain, '.') + 1);
        if (\strlen($tld) < 2) {
            return false;
        }

        return true;
    }

    /**
     * Get delivery metrics for a time period.
     *
     * @param  Carbon|null  $from  Start date (defaults to 30 days ago)
     * @param  Carbon|null  $to  End date (defaults to now)
     * @return array{
     *     total: int,
     *     queued: int,
     *     sent: int,
     *     delivered: int,
     *     failed: int,
     *     permanently_failed: int,
     *     delivery_rate: float,
     *     failure_rate: float,
     *     avg_delivery_time_seconds: float|null,
     *     by_priority: array<string, array{total: int, delivered: int, failed: int}>,
     *     by_notification_type: array<string, array{total: int, delivered: int, failed: int}>,
     *     daily_breakdown: array<string, array{total: int, delivered: int, failed: int}>
     * }
     */
    public function getDeliveryMetrics(?Carbon $from = null, ?Carbon $to = null): array
    {
        $from = $from ?? now()->subDays(30);
        $to = $to ?? now();

        // Base query
        $baseQuery = EmailLog::whereBetween('created_at', [$from, $to]);

        // Total counts by status
        $statusCounts = (clone $baseQuery)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $total = array_sum($statusCounts);
        $queued = $statusCounts['queued'] ?? 0;
        $sent = $statusCounts['sent'] ?? 0;
        $delivered = $statusCounts['delivered'] ?? 0;
        $failed = $statusCounts['failed'] ?? 0;
        $permanentlyFailed = $statusCounts['permanently_failed'] ?? 0;

        // Calculate rates
        $deliveryRate = $total > 0 ? round(($delivered / $total) * 100, 2) : 0.0;
        $failureRate = $total > 0 ? round((($failed + $permanentlyFailed) / $total) * 100, 2) : 0.0;

        // Average delivery time (for delivered emails)
        $avgDeliveryTime = (clone $baseQuery)
            ->where('status', 'delivered')
            ->whereNotNull('delivered_at')
            ->whereNotNull('queued_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, queued_at, delivered_at)) as avg_time')
            ->value('avg_time');

        // Metrics by priority
        $byPriority = (clone $baseQuery)
            ->select(
                'priority',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered"),
                DB::raw("SUM(CASE WHEN status IN ('failed', 'permanently_failed') THEN 1 ELSE 0 END) as failed")
            )
            ->groupBy('priority')
            ->get()
            ->keyBy('priority')
            ->map(fn ($row) => [
                'total' => (int) $row->total,
                'delivered' => (int) $row->delivered,
                'failed' => (int) $row->failed,
            ])
            ->toArray();

        // Metrics by notification type
        $byNotificationType = (clone $baseQuery)
            ->whereNotNull('notification_type')
            ->select(
                'notification_type',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered"),
                DB::raw("SUM(CASE WHEN status IN ('failed', 'permanently_failed') THEN 1 ELSE 0 END) as failed")
            )
            ->groupBy('notification_type')
            ->get()
            ->keyBy('notification_type')
            ->map(fn ($row) => [
                'total' => (int) $row->total,
                'delivered' => (int) $row->delivered,
                'failed' => (int) $row->failed,
            ])
            ->toArray();

        // Daily breakdown
        $dailyBreakdown = (clone $baseQuery)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered"),
                DB::raw("SUM(CASE WHEN status IN ('failed', 'permanently_failed') THEN 1 ELSE 0 END) as failed")
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(fn ($row) => [
                'total' => (int) $row->total,
                'delivered' => (int) $row->delivered,
                'failed' => (int) $row->failed,
            ])
            ->toArray();

        return [
            'total' => $total,
            'queued' => $queued,
            'sent' => $sent,
            'delivered' => $delivered,
            'failed' => $failed,
            'permanently_failed' => $permanentlyFailed,
            'delivery_rate' => $deliveryRate,
            'failure_rate' => $failureRate,
            'avg_delivery_time_seconds' => $avgDeliveryTime !== null ? (float) $avgDeliveryTime : null,
            'by_priority' => $byPriority,
            'by_notification_type' => $byNotificationType,
            'daily_breakdown' => $dailyBreakdown,
        ];
    }

    /**
     * Get the appropriate queue based on email priority.
     *
     * @param  string  $priority  The email priority
     * @return string The queue name
     */
    private function getQueueForPriority(string $priority): string
    {
        return match ($priority) {
            'critical' => config('notifications.channels.email.queue_critical', 'emails-critical'),
            'high' => config('notifications.channels.email.queue_high', 'emails-high'),
            'low' => config('notifications.channels.email.queue_low', 'emails-low'),
            default => config('notifications.channels.email.queue', 'emails'),
        };
    }

    /**
     * Schedule retry job for failed email.
     *
     * This is a fallback mechanism when immediate queueing fails.
     * The retry job will handle exponential backoff and max attempts.
     *
     * @param  EmailLog  $log  The email log entry
     * @param  Mailable  $mailable  The mailable instance
     * @param  array<string, mixed>  $meta  Additional metadata
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

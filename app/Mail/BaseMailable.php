<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Base mailable class with standardized retry logic and unified notification tracking.
 *
 * All application Mailables should extend this class to inherit:
 * - Automatic queueing (ShouldQueue)
 * - Exponential backoff retry logic
 * - EmailLog tracking with unified notification columns
 * - Standardized failure handling
 * - Integration with UnifiedNotificationDispatcher
 *
 * Configuration is loaded from config/notifications.php:
 * - tries: Max retry attempts (default: 3)
 * - backoff: Exponential backoff delays in seconds (default: [60, 300, 900])
 * - timeout: Job timeout (default: 120 seconds)
 *
 * Unified Tracking Integration:
 * The EmailLog model tracks emails with these unified notification columns:
 * - channels: json array (e.g., ["email"], ["email","broadcast"])
 * - notification_type: string (e.g., 'ticket_assigned', 'loan_approved')
 * - priority: enum (critical, high, normal, low)
 * - next_retry_at: timestamp for scheduled retries
 * - final_status: enum (delivered, permanently_failed, bounced, rejected)
 * - preference_bypassed: boolean (true for critical notifications)
 *
 * These columns enable:
 * - Multi-channel notification tracking (email, database, broadcast)
 * - Priority-based routing and scheduling
 * - Retry scheduling with next_retry_at
 * - Final delivery status reporting
 * - Preference bypass for critical notifications
 *
 * Usage:
 * ```php
 * class OrderShipped extends BaseMailable
 * {
 *     public function __construct(public Order $order)
 *     {
 *         parent::__construct(); // Load config
 *     }
 *
 *     public function build()
 *     {
 *         return $this->subject('Order Shipped')
 *             ->view('emails.orders.shipped');
 *     }
 * }
 * ```
 *
 * Retry Workflow:
 * 1. EmailDispatcher creates EmailLog with next_retry_at = now() + 60s
 * 2. Queue worker processes email job
 * 3. On success: markAsDelivered() sets final_status='delivered'
 * 4. On failure: RetryFailedEmail job reschedules with exponential backoff
 * 5. After max attempts: failed() sets final_status='permanently_failed'
 *
 * @see \App\Services\Notifications\EmailDispatcher For email dispatch and log creation
 * @see \App\Jobs\RetryFailedEmail For retry job logic
 * @see \App\Models\EmailLog For unified tracking columns
 * @see config/notifications.php For retry configuration
 *
 * Trace: D03 SRS-NFR-012 (email reliability), D04 §6.2 (notification architecture)
 *
 * @version 2.0.0 Enhanced with unified notification tracking
 */
abstract class BaseMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * Loaded from config/notifications.php
     */
    public int $tries;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * Loaded from config/notifications.php
     * Format: [60, 300, 900] = 1 min, 5 min, 15 min exponential backoff
     *
     * @var array<int, int>
     */
    public array $backoff;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout;

    /**
     * Associated email log for tracking delivery status.
     */
    protected ?EmailLog $emailLog = null;

    /**
     * Create a new message instance.
     *
     * Loads retry configuration from config file.
     */
    public function __construct()
    {
        $this->tries = config('notifications.email_retry.max_attempts', 3);
        $this->backoff = config('notifications.email_retry.backoff_delays', [60, 300, 900]);
        $this->timeout = config('notifications.channels.email.timeout', 120);

        // Set queue from config
        $this->onQueue(config('notifications.channels.email.queue', 'emails'));
    }

    /**
     * Attach email log for tracking.
     *
     * Called by EmailDispatcher to link this mailable with its tracking log.
     *
     * @param  EmailLog  $log  The email log entry
     */
    public function withEmailLog(EmailLog $log): self
    {
        $this->emailLog = $log;

        return $this;
    }

    /**
     * Handle a queued email's failure.
     *
     * Called by Laravel when the email job fails after all retry attempts.
     * Updates EmailLog with unified tracking columns:
     * - status: 'permanently_failed'
     * - final_status: 'permanently_failed' (unified tracking)
     * - next_retry_at: null (clear retry schedule)
     * - failed_at: current timestamp
     *
     * @param  \Throwable|null  $exception  The exception that caused the failure
     */
    public function failed(?\Throwable $exception): void
    {
        if ($this->emailLog) {
            $this->emailLog->update([
                'status' => 'permanently_failed',
                'final_status' => 'permanently_failed', // Unified tracking column
                'next_retry_at' => null, // Clear retry schedule
                'failed_at' => now(),
                'error_message' => $exception ? substr($exception->getMessage(), 0, 500) : 'Unknown error',
            ]);

            Log::channel('notifications')->error('Email permanently failed after retries', [
                'email_log_id' => $this->emailLog->id,
                'recipient' => $this->emailLog->recipient_email,
                'mailable' => static::class,
                'notification_type' => $this->emailLog->notification_type,
                'priority' => $this->emailLog->priority,
                'channels' => $this->emailLog->channels,
                'retry_attempts' => $this->emailLog->retry_attempts,
                'error' => $exception?->getMessage(),
                'trace' => $exception?->getTraceAsString(),
            ]);
        } else {
            Log::channel('notifications')->error('Email permanently failed (no log attached)', [
                'mailable' => static::class,
                'error' => $exception?->getMessage(),
            ]);
        }
    }

    /**
     * Mark email as successfully delivered.
     *
     * Updates EmailLog with unified tracking columns indicating successful delivery:
     * - status: 'sent'
     * - final_status: 'delivered' (unified tracking)
     * - sent_at: current timestamp
     * - next_retry_at: null (clear retry schedule)
     *
     * This method should be called after successful email transmission
     * to ensure proper tracking in the unified notification system.
     */
    protected function markAsDelivered(): void
    {
        if ($this->emailLog) {
            $this->emailLog->update([
                'status' => 'sent',
                'final_status' => 'delivered', // Unified tracking column
                'sent_at' => now(),
                'next_retry_at' => null, // Clear retry schedule
            ]);

            Log::channel('notifications')->info('Email delivered successfully', [
                'email_log_id' => $this->emailLog->id,
                'recipient' => $this->emailLog->recipient_email,
                'mailable' => static::class,
                'notification_type' => $this->emailLog->notification_type,
                'priority' => $this->emailLog->priority,
                'channels' => $this->emailLog->channels,
            ]);
        }
    }

    /**
     * Get the email log associated with this mailable.
     */
    public function getEmailLog(): ?EmailLog
    {
        return $this->emailLog;
    }
}

<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Retry failed email with exponential backoff.
 *
 * This job implements intelligent retry logic for failed emails:
 * - Exponential backoff delays from config
 * - Max retry attempts from config
 * - Automatic job deletion after max attempts
 * - Detailed logging for debugging
 *
 * Retry schedule (from config/notifications.php):
 * - Attempt 1: Immediate (handled by queue retry)
 * - Attempt 2: After 60 seconds
 * - Attempt 3: After 5 minutes
 * - Attempt 4: After 15 minutes
 * - After max attempts: Mark as permanently failed
 *
 * Trace: D03 SRS-NFR-012 (email reliability), D11 §5.3 (retry patterns)
 */
class RetryFailedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries;

    public int $timeout = 120;

    /** @var array<int, int> */
    public array $backoff;

    /**
     * Create retry job for failed email.
     *
     * @param  EmailLog  $emailLog  The failed email to retry
     * @param  string  $mailableClass  The mailable class to recreate
     * @param  array<string, mixed>  $mailableData  Data to reconstruct mailable
     */
    

/**
 * @param array<string, mixed> $mailableData
 */
public function __construct(
        public EmailLog $emailLog,
        public string $mailableClass,
        public array $mailableData = []
    ) {
        // Load retry configuration from config
        $tries = config('notifications.email_retry.max_attempts', 3);
        $this->tries = is_int($tries) ? $tries : 3;

        $backoff = config('notifications.email_retry.backoff_delays', [60, 300, 900]);
        $this->backoff = is_array($backoff) ? $backoff : [60, 300, 900];

        // Set queue to email queue
        $queueName = config('notifications.channels.email.queue', 'emails');
        if (is_string($queueName)) {
            $this->onQueue($queueName);
        }
    }

    /**
     * Execute the retry job.
     *
     * Implements exponential backoff retry logic with unified notification tracking:
     * - Tracks retry attempts with next_retry_at scheduling
     * - Updates final_status on success or permanent failure
     * - Logs all retry events with unified notification metadata
     */
    public function handle(): void
    {
        $currentAttempt = $this->emailLog->retry_attempts + 1;

        Log::channel('notifications')->info('Retrying failed email', [
            'email_log_id' => $this->emailLog->id,
            'recipient' => $this->emailLog->recipient_email,
            'notification_type' => $this->emailLog->notification_type,
            'priority' => $this->emailLog->priority,
            'channels' => $this->emailLog->channels,
            'mailable_class' => $this->mailableClass,
            'attempt' => $currentAttempt,
            'max_attempts' => $this->tries,
            'last_error' => $this->emailLog->error_message,
        ]);

        // Check if we've exceeded max attempts
        if ($currentAttempt > $this->tries) {
            $this->markAsPermanentlyFailed();

            return;
        }

        try {
            // Recreate the mailable instance
            $mailable = $this->recreateMailable();

            // Attach email log if mailable supports it
            if (method_exists($mailable, 'withEmailLog')) {
                $mailable->withEmailLog($this->emailLog);
            }

            // Attempt to send
            Mail::to($this->emailLog->recipient_email, $this->emailLog->recipient_name)
                ->send($mailable);

            // Success - mark as delivered with unified tracking
            $this->emailLog->update([
                'status' => 'sent',
                'final_status' => 'delivered', // Unified tracking: final delivery outcome
                'sent_at' => now(),
                'retry_attempts' => $currentAttempt,
                'last_retry_at' => now(),
                'next_retry_at' => null, // Clear retry schedule on success
                'error_message' => null,
            ]);

            Log::channel('notifications')->info('Email retry successful', [
                'email_log_id' => $this->emailLog->id,
                'recipient' => $this->emailLog->recipient_email,
                'notification_type' => $this->emailLog->notification_type,
                'priority' => $this->emailLog->priority,
                'channels' => $this->emailLog->channels,
                'attempt' => $currentAttempt,
            ]);

        } catch (\Throwable $exception) {
            // Calculate next retry time using exponential backoff
            $nextRetryDelay = $currentAttempt < $this->tries
                ? $this->backoff[min($currentAttempt - 1, count($this->backoff) - 1)]
                : null;

            $nextRetryAt = $nextRetryDelay ? now()->addSeconds($nextRetryDelay) : null;

            // Update retry attempt with next scheduled retry
            $this->emailLog->update([
                'retry_attempts' => $currentAttempt,
                'last_retry_at' => now(),
                'next_retry_at' => $nextRetryAt, // Schedule next retry with exponential backoff
                'error_message' => substr($exception->getMessage(), 0, 500),
            ]);

            Log::channel('notifications')->error('Email retry failed', [
                'email_log_id' => $this->emailLog->id,
                'recipient' => $this->emailLog->recipient_email,
                'notification_type' => $this->emailLog->notification_type,
                'priority' => $this->emailLog->priority,
                'channels' => $this->emailLog->channels,
                'attempt' => $currentAttempt,
                'max_attempts' => $this->tries,
                'error' => $exception->getMessage(),
                'next_retry_at' => $nextRetryAt?->toDateTimeString(),
                'next_retry_delay' => $nextRetryDelay ? "{$nextRetryDelay}s" : 'none',
            ]);

            // Re-throw to trigger backoff if more attempts available
            if ($currentAttempt < $this->tries) {
                throw $exception;
            }

            // No more retries - mark as permanently failed
            $this->markAsPermanentlyFailed();
        }
    }

    /**
     * Recreate mailable instance from stored data.
     *
     * @throws \Exception If mailable cannot be recreated
     */
    private function recreateMailable(): \Illuminate\Mail\Mailable
    {
        if (! class_exists($this->mailableClass)) {
            throw new \Exception("Mailable class {$this->mailableClass} does not exist");
        }

        // Try to recreate with stored data
        if (! empty($this->mailableData)) {
            /** @var \Illuminate\Mail\Mailable */
            return new $this->mailableClass(...$this->mailableData);
        }

        // Fallback: try no-arg constructor
        /** @var \Illuminate\Mail\Mailable */
        return new $this->mailableClass;
    }

    /**
     * Mark email as permanently failed after all retries exhausted.
     * Updates EmailLog with unified tracking columns.
     */
    private function markAsPermanentlyFailed(): void
    {
        $this->emailLog->update([
            'status' => 'permanently_failed',
            'final_status' => 'permanently_failed', // Unified tracking: final failure status
            'next_retry_at' => null, // Clear retry schedule
            'failed_at' => now(),
        ]);

        Log::channel('notifications')->warning('Email permanently failed after all retries', [
            'email_log_id' => $this->emailLog->id,
            'recipient' => $this->emailLog->recipient_email,
            'notification_type' => $this->emailLog->notification_type,
            'priority' => $this->emailLog->priority,
            'channels' => $this->emailLog->channels,
            'total_attempts' => $this->emailLog->retry_attempts,
            'preference_bypassed' => $this->emailLog->preference_bypassed,
        ]);

        // Optionally notify admins about permanent failure
        // This could be implemented as a separate notification
    }

    /**
     * Handle failed job after all retries exhausted.
     */
    public function failed(\Throwable $exception): void
    {
        $this->markAsPermanentlyFailed();

        Log::channel('notifications')->error('RetryFailedEmail job failed permanently', [
            'email_log_id' => $this->emailLog->id,
            'recipient' => $this->emailLog->recipient_email,
            'mailable_class' => $this->mailableClass,
            'error' => $exception->getMessage(),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

/**
 * Send Notification Job
 *
 * Handles sending notifications via multiple channels (email, database, WebSocket)
 * for the ICTServe system with proper error handling and retry logic.
 *
 * @see Requirements 6.1, 6.4, 6.5, 23.1, 23.6, 23.7
 */
class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out
     */
    public int $timeout = 120;

    /**
     * The backoff delays between retry attempts (exponential backoff)
     *
     * @var array<int>
     */
    public array $backoff = [10, 30, 60];

    /**
     * Create a new job instance
     *
     * @param  array<string, mixed>  $notificationData
     * @param  array<string>  $channels
     */
    

/**
 * @param array<string, mixed> $channels
 */
public function __construct(
        public string $notificationType,
        public array $notificationData,
        public ?User $user = null,
        public ?string $email = null,
        public array $channels = ['mail', 'database']
    ) {
        $this->onQueue('notifications');
    }

    /**
     * Execute the job
     */
    public function handle(): void
    {
        $startTime = microtime(true);

        Log::info('SendNotificationJob started', [
            'notification_type' => $this->notificationType,
            'user_id' => $this->user?->id,
            'email' => $this->email,
            'channels' => $this->channels,
            'attempt' => $this->attempts(),
        ]);

        try {
            foreach ($this->channels as $channel) {
                $this->sendViaChannel($channel);
            }

            $processingTime = microtime(true) - $startTime;

            Log::info('SendNotificationJob completed successfully', [
                'notification_type' => $this->notificationType,
                'user_id' => $this->user?->id,
                'email' => $this->email,
                'channels' => $this->channels,
                'processing_time' => $processingTime,
            ]);
        } catch (\Exception $e) {
            $this->handleFailure($e, microtime(true) - $startTime);
            throw $e;
        }
    }

    /**
     * Send notification via specific channel
     */
    private function sendViaChannel(string $channel): void
    {
        match ($channel) {
            'mail' => $this->sendEmail(),
            'database' => $this->sendDatabaseNotification(),
            'broadcast' => $this->sendBroadcastNotification(),
            default => Log::warning('Unknown notification channel', ['channel' => $channel]),
        };
    }

    /**
     * Send email notification
     */
    private function sendEmail(): void
    {
        $recipient = $this->email ?? $this->user?->email;

        if (! $recipient) {
            throw new \InvalidArgumentException('No email recipient specified');
        }

        // Use appropriate mail class based on notification type
        $mailClass = $this->getMailClass();

        if ($mailClass) {
            Mail::to($recipient)->send(new $mailClass($this->notificationData));
        } else {
            // Fallback to raw email
            Mail::raw($this->notificationData['message'] ?? 'ICTServe Notification', function ($message) use ($recipient) {
                $message->to($recipient)
                    ->subject($this->notificationData['subject'] ?? 'ICTServe Notification');
            });
        }
    }

    /**
     * Send database notification
     */
    private function sendDatabaseNotification(): void
    {
        if (! $this->user) {
            return; // Database notifications require authenticated user
        }

        $this->user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => $this->notificationType,
            'data' => $this->notificationData,
            'read_at' => null,
        ]);
    }

    /**
     * Send broadcast notification (WebSocket)
     */
    private function sendBroadcastNotification(): void
    {
        if (! $this->user) {
            return; // Broadcast notifications require authenticated user
        }

        // Use Laravel's broadcasting system
        broadcast(new \App\Events\NotificationSent(
            $this->user,
            $this->notificationType,
            $this->notificationData
        ));
    }

    /**
     * Get appropriate mail class for notification type
     */
    private function getMailClass(): ?string
    {
        return match ($this->notificationType) {
            'ticket_created' => \App\Mail\TicketCreatedMail::class,
            'ticket_updated' => \App\Mail\TicketUpdatedMail::class,
            'loan_approved' => \App\Mail\LoanApprovedMail::class,
            'loan_rejected' => \App\Mail\LoanRejectedMail::class,
            'asset_overdue' => \App\Mail\AssetOverdueMail::class,
            default => null,
        };
    }

    /**
     * Handle job failure
     */
    private function handleFailure(\Exception $e, float $processingTime): void
    {
        Log::error('SendNotificationJob failed', [
            'notification_type' => $this->notificationType,
            'user_id' => $this->user?->id,
            'email' => $this->email,
            'channels' => $this->channels,
            'attempt' => $this->attempts(),
            'max_tries' => $this->tries,
            'processing_time' => $processingTime,
            'error' => $e->getMessage(),
        ]);
    }

    /**
     * Handle permanent job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('SendNotificationJob permanently failed', [
            'notification_type' => $this->notificationType,
            'user_id' => $this->user?->id,
            'email' => $this->email,
            'error' => $exception->getMessage(),
        ]);

        // Optionally notify administrators about notification failures
        // This could be implemented as a separate notification system
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * Requirement 23.7: Job tagging for ICTServe operations
     *
     * @return array<string>
     */
    public function tags(): array
    {
        $tags = [
            'notifications',
            'type:'.$this->notificationType,
        ];

        if ($this->user) {
            $tags[] = 'user:'.$this->user->id;
        }

        if ($this->email) {
            $tags[] = 'email:'.$this->email;
        }

        foreach ($this->channels as $channel) {
            $tags[] = 'channel:'.$channel;
        }

        return $tags;
    }

    /**
     * Static factory methods for common notification types
     */
    

/**
 * @param array<string, mixed> $data
 */
public static function ticketCreated(array $data, ?User $user = null, ?string $email = null): self
    {
        return new self('ticket_created', $data, $user, $email);
    }

    

/**
 * @param array<string, mixed> $data
 */
public static function loanApproved(array $data, ?User $user = null, ?string $email = null): self
    {
        return new self('loan_approved', $data, $user, $email);
    }

    

/**
 * @param array<string, mixed> $data
 */
public static function assetOverdue(array $data, ?User $user = null, ?string $email = null): self
    {
        return new self('asset_overdue', $data, $user, $email);
    }
}

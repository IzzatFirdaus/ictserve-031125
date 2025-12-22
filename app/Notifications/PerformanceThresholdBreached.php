<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Performance Threshold Breached Notification
 *
 * Sent to admin and superuser users when performance thresholds are breached.
 *
 * @trace Requirements 16.3 - Automated alerting for performance threshold breaches
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 3.6.0
 */
class PerformanceThresholdBreached extends Notification implements ShouldQueue
{
    use Queueable;

    

/**
 * @param array<string, mixed> $data
 */
public function __construct(
        public string $title,
        public string $message,
        public string $severity,
        public array $data = []
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("[ICTServe] Amaran Prestasi: {$this->title}")
            ->greeting('Salam Sejahtera,')
            ->line($this->message);

        // Add severity-specific styling
        if ($this->severity === 'error') {
            $mail->error();
        }

        // Add metric details
        if (! empty($this->data)) {
            $mail->line('Butiran Metrik:');
            foreach ($this->data as $key => $value) {
                $mail->line("- {$key}: {$value}");
            }
        }

        $mail->action('Lihat Dashboard Pulse', route('pulse'))
            ->line('Sila semak dashboard Pulse untuk maklumat lanjut.')
            ->salutation('Sistem ICTServe');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'severity' => $this->severity,
            'data' => $this->data,
            'type' => 'performance_alert',
        ];
    }
}

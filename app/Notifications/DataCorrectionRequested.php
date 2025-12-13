<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DataCorrectionRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private User $requestor,
        private string $field,
        private string $currentValue,
        private string $requestedValue,
        private string $reason
    ) {
        $this->onQueue('notifications');
    }

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
        return (new MailMessage)
            ->subject('PDPA Data Correction Request')
            ->greeting('Hello '.$notifiable->name)
            ->line('A data correction request requires your review.')
            ->line('User: '.$this->requestor->name.' ('.$this->requestor->email.')')
            ->line('Field: '.$this->field)
            ->line('Current value: '.$this->currentValue)
            ->line('Requested value: '.$this->requestedValue)
            ->line('Reason: '.$this->reason)
            ->action('Review request', url('/admin'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'data_correction_requested',
            'requestor_id' => $this->requestor->id,
            'requestor_email' => $this->requestor->email,
            'requestor_name' => $this->requestor->name,
            'field' => $this->field,
            'current_value' => $this->currentValue,
            'requested_value' => $this->requestedValue,
            'reason' => $this->reason,
            'submitted_at' => now()->toIso8601String(),
        ];
    }
}

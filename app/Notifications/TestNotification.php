<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestNotification extends Notification
{
    use Queueable;

    public function __construct(private string $channel = 'in_app') {}

    public function via(object $notifiable): array
    {
        return [$this->channel === 'email' ? 'mail' : 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Test Notification')
            ->line('This is a test notification.')
            ->line('Channel: '.$this->channel);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Test Notification',
            'message' => 'This is a test notification.',
            'priority' => 'normal',
        ];
    }
}

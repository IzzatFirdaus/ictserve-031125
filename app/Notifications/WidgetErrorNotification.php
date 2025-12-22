<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Widget Error Notification
 *
 * Notifies administrators about critical widget errors that require attention.
 * Supports both email and database notification channels with Bahasa Melayu content.
 *
 * @trace Requirements: R7 (Widget Error Handling)
 *
 * @see D04 §3.2 Dashboard widgets
 * @see D11 §12.2 Error handling patterns
 *
 * @version 3.6.1
 */
class WidgetErrorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $widgetName,
        public string $errorMessage,
        public string $errorId,
        public string $widgetClass
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
            ->subject("Ralat Widget Kritikal - {$this->widgetName}")
            ->greeting("Assalamualaikum {$notifiable->name},")
            ->line("Ralat kritikal telah berlaku pada widget **{$this->widgetName}** dalam sistem ICTServe.")
            ->line('**Butiran Ralat:**')
            ->line("- Widget: {$this->widgetName}")
            ->line("- Mesej Ralat: {$this->errorMessage}")
            ->line("- ID Ralat: {$this->errorId}")
            ->line('- Masa: '.now()->format('d/m/Y H:i:s'))
            ->line('Sila semak dashboard pentadbir untuk maklumat lanjut dan tindakan yang diperlukan.')
            ->action('Lihat Dashboard', route('filament.admin.pages.dashboard'))
            ->line('Sistem akan cuba memulihkan widget secara automatik. Jika masalah berterusan, sila hubungi pasukan teknikal.')
            ->line('Terima kasih.')
            ->salutation("Sistem ICTServe v3.6.1\nBahagian Pengurusan MOTAC");
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'widget_error',
            'title' => "Ralat Widget: {$this->widgetName}",
            'message' => "Ralat kritikal pada widget {$this->widgetName}. ID: {$this->errorId}",
            'widget_name' => $this->widgetName,
            'widget_class' => $this->widgetClass,
            'error_message' => $this->errorMessage,
            'error_id' => $this->errorId,
            'severity' => 'critical',
            'timestamp' => now()->toISOString(),
            'action_url' => route('filament.admin.pages.dashboard'),
            'action_text' => 'Lihat Dashboard',
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        // Only send to users with admin or superuser roles
        return $notifiable->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Get the notification's tags for queuing.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return ['widget-error', $this->widgetClass, $this->errorId];
    }
}

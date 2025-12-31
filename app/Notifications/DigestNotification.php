<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Digest Notification
 *
 * Sends a compiled digest of notifications to users based on their
 * configured frequency (daily or weekly).
 */
class DigestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  Collection<int, \Illuminate\Notifications\DatabaseNotification>  $notifications
     */
    public function __construct(
        public Collection $notifications,
        public string $frequency
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $count = $this->notifications->count();
        $frequencyLabel = $this->frequency === 'daily' ? 'Harian' : 'Mingguan';

        $message = (new MailMessage)
            ->subject("Ringkasan Notifikasi {$frequencyLabel} - {$count} notifikasi")
            ->greeting("Salam, {$notifiable->name}!")
            ->line("Berikut adalah ringkasan notifikasi {$frequencyLabel} anda:");

        $grouped = $this->notifications->groupBy(fn ($n) => $n->data['type'] ?? 'general');

        foreach ($grouped as $type => $typeNotifications) {
            $typeLabel = $this->getTypeLabel($type);
            $message->line("**{$typeLabel}** ({$typeNotifications->count()})");

            foreach ($typeNotifications->take(5) as $notification) {
                $title = $notification->data['title'] ?? $notification->data['message'] ?? 'Notifikasi';
                $message->line("- {$title}");
            }

            if ($typeNotifications->count() > 5) {
                $remaining = $typeNotifications->count() - 5;
                $message->line("  ...dan {$remaining} lagi");
            }
        }

        return $message
            ->action('Lihat Semua Notifikasi', url('/notifications'))
            ->line('Terima kasih kerana menggunakan sistem kami.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'digest',
            'frequency' => $this->frequency,
            'notification_count' => $this->notifications->count(),
            'notification_ids' => $this->notifications->pluck('id')->toArray(),
        ];
    }

    private function getTypeLabel(string $type): string
    {
        return match ($type) {
            'ticket_updates' => 'Kemaskini Tiket',
            'ticket_assignments' => 'Tugasan Tiket',
            'ticket_comments' => 'Komen Tiket',
            'loan_updates' => 'Kemaskini Pinjaman',
            'loan_approvals' => 'Kelulusan Pinjaman',
            'loan_reminders' => 'Peringatan Pinjaman',
            'system_announcements' => 'Pengumuman Sistem',
            'sla_alerts' => 'Amaran SLA',
            default => 'Umum',
        };
    }
}

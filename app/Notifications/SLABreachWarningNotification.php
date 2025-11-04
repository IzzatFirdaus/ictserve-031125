<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * SLA Breach Warning Notification
 *
 * Sent when a ticket is within 25% of SLA breach threshold.
 *
 * @trace Requirements 8.4, 10.3, 13.3
 */
class SLABreachWarningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public HelpdeskTicket $ticket
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $timeRemaining = now()->diffForHumans($this->ticket->sla_resolution_due_at, true);

        return (new MailMessage)
            ->subject("⚠️ Amaran SLA: {$this->ticket->ticket_number}")
            ->greeting("Salam {$notifiable->name},")
            ->line('Tiket berikut hampir melebihi SLA:')
            ->line("**Nombor Tiket:** {$this->ticket->ticket_number}")
            ->line("**Subjek:** {$this->ticket->subject}")
            ->line('**Keutamaan:** '.ucfirst($this->ticket->priority))
            ->line("**Masa Berbaki:** {$timeRemaining}")
            ->line("**Tarikh Akhir:** {$this->ticket->sla_resolution_due_at?->format('d M Y, h:i A')}")
            ->action('Lihat Tiket', url("/admin/helpdesk-tickets/{$this->ticket->id}"))
            ->line('Sila ambil tindakan segera untuk mengelakkan pelanggaran SLA.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
            'sla_due_at' => $this->ticket->sla_resolution_due_at,
            'time_remaining' => now()->diffInMinutes($this->ticket->sla_resolution_due_at),
        ];
    }
}

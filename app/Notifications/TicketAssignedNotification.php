<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Ticket Assigned Notification
 *
 * Sent when a ticket is assigned to a user.
 *
 * @trace Requirements 10.1, 22.3
 */
class TicketAssignedNotification extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject("Tiket Ditugaskan: {$this->ticket->ticket_number}")
            ->greeting("Salam {$notifiable->name},")
            ->line('Anda telah ditugaskan untuk mengendalikan tiket berikut:')
            ->line("**Nombor Tiket:** {$this->ticket->ticket_number}")
            ->line("**Subjek:** {$this->ticket->subject}")
            ->line('**Keutamaan:** '.ucfirst($this->ticket->priority))
            ->line("**Kategori:** {$this->ticket->category?->name_ms}")
            ->line("**SLA Resolusi:** {$this->ticket->sla_resolution_due_at?->format('d M Y, h:i A')}")
            ->action('Lihat Tiket', url("/admin/helpdesk-tickets/{$this->ticket->id}"))
            ->line('Sila ambil tindakan segera untuk menyelesaikan tiket ini.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
            'priority' => $this->ticket->priority,
            'sla_due_at' => $this->ticket->sla_resolution_due_at,
        ];
    }
}

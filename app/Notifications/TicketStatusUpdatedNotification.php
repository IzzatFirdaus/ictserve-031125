<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Ticket Status Updated Notification
 *
 * Sent when ticket status changes (to submitter).
 *
 * @trace Requirements 10.1, 22.3
 */
class TicketStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public HelpdeskTicket $ticket,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabels = [
            'open' => 'Dibuka',
            'assigned' => 'Ditugaskan',
            'in_progress' => 'Dalam Proses',
            'pending_user' => 'Menunggu Maklum Balas',
            'resolved' => 'Selesai',
            'closed' => 'Ditutup',
        ];

        $oldStatusLabel = $statusLabels[$this->oldStatus] ?? ucfirst($this->oldStatus);
        $newStatusLabel = $statusLabels[$this->newStatus] ?? ucfirst($this->newStatus);

        $message = (new MailMessage)
            ->subject("Kemaskini Tiket: {$this->ticket->ticket_number}")
            ->greeting('Salam,')
            ->line('Status tiket anda telah dikemaskini:')
            ->line("**Nombor Tiket:** {$this->ticket->ticket_number}")
            ->line("**Subjek:** {$this->ticket->subject}")
            ->line("**Status Lama:** {$oldStatusLabel}")
            ->line("**Status Baru:** {$newStatusLabel}");

        // Add tracking link for guest submissions
        if ($this->ticket->isGuestSubmission()) {
            $message->action('Jejak Tiket', url("/helpdesk/track?ticket={$this->ticket->ticket_number}"));
        } else {
            $message->action('Lihat Tiket', url("/helpdesk/tickets/{$this->ticket->id}"));
        }

        // Add resolution notes if resolved
        if ($this->newStatus === 'resolved' && $this->ticket->resolution_notes) {
            $message->line('**Nota Penyelesaian:**')
                ->line($this->ticket->resolution_notes);
        }

        return $message->line('Terima kasih kerana menggunakan perkhidmatan ICTServe.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }
}

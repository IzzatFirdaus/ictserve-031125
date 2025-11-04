<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\HelpdeskComment;
use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Ticket Comment Added Notification
 *
 * Sent when a new comment is added to a ticket.
 *
 * @trace Requirements 10.1, 22.3
 */
class TicketCommentAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public HelpdeskTicket $ticket,
        public HelpdeskComment $comment
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $commenterName = $this->comment->user?->name ?? $this->comment->commenter_name ?? 'Sistem';

        $message = (new MailMessage)
            ->subject("Komen Baru: {$this->ticket->ticket_number}")
            ->greeting('Salam,')
            ->line('Komen baru telah ditambah pada tiket anda:')
            ->line("**Nombor Tiket:** {$this->ticket->ticket_number}")
            ->line("**Subjek:** {$this->ticket->subject}")
            ->line("**Daripada:** {$commenterName}")
            ->line('**Komen:**')
            ->line($this->comment->comment);

        // Add tracking link for guest submissions
        if ($this->ticket->isGuestSubmission()) {
            $message->action('Jejak Tiket', url("/helpdesk/track?ticket={$this->ticket->ticket_number}"));
        } else {
            $message->action('Lihat Tiket', url("/helpdesk/tickets/{$this->ticket->id}"));
        }

        return $message->line('Terima kasih kerana menggunakan perkhidmatan ICTServe.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'comment_id' => $this->comment->id,
            'commenter' => $this->comment->user?->name ?? $this->comment->commenter_name,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Mail\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Ticket Assigned Mail
 *
 * Email notification sent when a ticket is assigned to a user.
 *
 * @trace Requirements D03-FR-001.3, D03-FR-008.1, Requirement 1.3, 10.2
 */
class TicketAssignedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public HelpdeskTicket $ticket,
        public User $assignedUser
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Tiket Ditugaskan: {$this->ticket->ticket_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.helpdesk.ticket-assigned',
            with: [
                'ticket' => $this->ticket,
                'assignedUser' => $this->assignedUser,
                'ticketUrl' => route('filament.admin.resources.helpdesk.helpdesk-tickets.view', $this->ticket),
            ],
        );
    }
}

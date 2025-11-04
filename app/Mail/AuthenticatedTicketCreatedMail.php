<?php

declare(strict_types=1);

namespace App\Mail;

use App\Mail\Concerns\LogsEmailDispatch;
use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Authenticated Ticket Created Email
 *
 * Sent to authenticated users when they create a helpdesk ticket.
 * Includes portal link and enhanced features with bilingual support.
 *
 * @component Email Template
 *
 * @description WCAG 2.2 AA compliant email confirmation for authenticated ticket creation
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-001.3 Authenticated ticket submission
 * @trace D03-FR-008.1 Enhanced email workflows
 * @trace Requirements 1.3, 8.1, 10.2
 *
 * @wcag_level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-04
 */
class AuthenticatedTicketCreatedMail extends Mailable implements ShouldQueue
{
    use LogsEmailDispatch, Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public HelpdeskTicket $ticket
    ) {
        // Set queue for 60-second SLA compliance (Requirement 8.1)
        $this->onQueue('emails');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('helpdesk.email.authenticated_ticket_created_subject', [
                'ticket_number' => $this->ticket->ticket_number,
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.helpdesk.authenticated-ticket-created',
            with: [
                'ticket' => $this->ticket,
                'user' => $this->ticket->user,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

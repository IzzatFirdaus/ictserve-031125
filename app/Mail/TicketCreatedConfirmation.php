<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\HelpdeskTicket;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Ticket Created Confirmation Email
 *
 * Sent to authenticated users when a helpdesk ticket is created.
 * Provides ticket details and next steps with bilingual support.
 * PKS 5.2.1 Compliant - SSO-Only Architecture (no guest access).
 *
 * @component Email Template
 *
 * @description WCAG 2.2 AA compliant email confirmation for helpdesk ticket creation
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-001.1 PKS-Compliant SSO-Only architecture
 * @trace D03-FR-008.1 Enhanced email workflows
 * @trace Requirements 1.1, 1.2, 10.1, 18.1, 18.2, 25.1
 *
 * @wcag_level AA
 *
 * @version 2.0.0
 *
 * @created 2025-11-04
 *
 * @updated 2025-12-25 PKS 5.2.1 compliance - removed guest mode
 */
class TicketCreatedConfirmation extends BaseMailable
{
    /**
     * Create a new message instance.
     */
    public function __construct(
        public HelpdeskTicket $ticket
    ) {
        parent::__construct();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: (string) __('helpdesk.email.ticket_created_subject', [
                'ticket_number' => $this->ticket->ticket_number,
            ]),
        );
    }

    /**
     * Get the message content definition.
     *
     * PKS 5.2.1 Compliant - All tickets require authenticated user
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.helpdesk.authenticated-ticket-created',
            with: [
                'ticket' => $this->ticket,
                'submitterName' => $this->ticket->user->name,
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

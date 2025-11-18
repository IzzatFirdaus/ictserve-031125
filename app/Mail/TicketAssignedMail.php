<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Ticket Assigned Email
 *
 * Sent to agents when a helpdesk ticket is assigned to them.
 * Provides ticket details and action links with bilingual support.
 *
 * @component Email Template
 *
 * @description WCAG 2.2 AA compliant email notification for ticket assignment
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-003.2 Ticket assignment
 * @trace D03-FR-008.1 Enhanced email workflows
 * @trace Requirements 3.2, 8.1, 10.2
 *
 * @wcag_level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-04
 */
class TicketAssignedMail extends BaseMailable
{
    /**
     * Create a new message instance.
     */
    public function __construct(
        public HelpdeskTicket $ticket,
        public User $assignedTo,
        public ?User $assignedBy = null
    ) {
        parent::__construct(); // Load retry config, queue setup, timeout from BaseMailable
        // Inherits: 3 attempts, [60,300,900]s backoff, 'emails' queue, 120s timeout
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('helpdesk.email.ticket_assigned_subject', [
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
            markdown: 'emails.helpdesk.ticket-assigned',
            with: [
                'ticket' => $this->ticket,
                'assignedTo' => $this->assignedTo,
                'assignedBy' => $this->assignedBy,
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

<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Ticket Claimed Email
 *
 * Sent to guest users when their ticket is claimed by an authenticated user.
 * Provides information about the claim and portal access with bilingual support.
 *
 * @component Email Template
 *
 * @description WCAG 2.2 AA compliant email notification for ticket claiming
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-001.4 Ticket claiming
 * @trace D03-FR-008.1 Enhanced email workflows
 * @trace Requirements 1.4, 8.1, 10.1
 *
 * @wcag_level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-04
 */
class TicketClaimedMail extends BaseMailable
{
    /**
     * Create a new message instance.
     */
    public function __construct(
        public HelpdeskTicket $ticket,
        public User $claimedBy
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
            subject: __('helpdesk.email.ticket_claimed_subject', [
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
            markdown: 'emails.helpdesk.ticket-claimed',
            with: [
                'ticket' => $this->ticket,
                'claimedBy' => $this->claimedBy,
                'submitterName' => $this->ticket->guest_name,
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

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
 * Ticket Status Updated Email
 *
 * Sent to users (guest or authenticated) when a helpdesk ticket status changes.
 * Provides updated ticket details and next steps with bilingual support.
 *
 * @component Email Template
 *
 * @description WCAG 2.2 AA compliant email notification for helpdesk ticket status updates
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-001.2 Guest ticket submission
 * @trace D03-FR-008.1 Enhanced email workflows
 * @trace Requirements 1.2, 8.1, 10.1
 *
 * @wcag_level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-04
 */
class TicketStatusUpdatedMail extends Mailable implements ShouldQueue
{
    use LogsEmailDispatch, Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public HelpdeskTicket $ticket,
        public string $previousStatus,
        public ?string $comment = null
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
            subject: __('helpdesk.email.ticket_status_updated_subject', [
                'ticket_number' => $this->ticket->ticket_number,
                'status' => ucfirst($this->ticket->status),
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.helpdesk.ticket-status-updated',
            with: [
                'ticket' => $this->ticket,
                'previousStatus' => $this->previousStatus,
                'comment' => $this->comment,
                'submitterName' => $this->ticket->user
                    ? $this->ticket->user->name
                    : $this->ticket->guest_name,
                'isGuest' => is_null($this->ticket->user_id),
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

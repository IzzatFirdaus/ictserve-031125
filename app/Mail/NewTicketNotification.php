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
 * New Ticket Notification Email
 *
 * Sent to admin users when a new helpdesk ticket is created.
 * Provides ticket details for assignment and processing.
 *
 * @component Email Template
 *
 * @description WCAG 2.2 AA compliant email notification for admin users
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-001.2 Guest ticket submission
 * @trace D03-FR-008.1 Enhanced email workflows
 * @trace Requirements 1.2, 10.1, 13.1, 18.1, 18.2
 *
 * @wcag_level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-04
 */
class NewTicketNotification extends Mailable implements ShouldQueue
{
    use LogsEmailDispatch, Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public HelpdeskTicket $ticket
    ) {
        // Set queue for 60-second SLA compliance
        $this->onQueue('notifications');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('helpdesk.email.new_ticket_admin_subject', [
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
            markdown: 'emails.helpdesk.new-ticket-admin',
            with: [
                'ticket' => $this->ticket,
                'submitterName' => $this->ticket->user
                    ? $this->ticket->user->name
                    : $this->ticket->guest_name,
                'submitterEmail' => $this->ticket->user
                    ? $this->ticket->user->email
                    : $this->ticket->guest_email,
                'isGuest' => is_null($this->ticket->user_id),
                'adminUrl' => route('filament.admin.resources.helpdesk.helpdesk-tickets.view', $this->ticket),
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

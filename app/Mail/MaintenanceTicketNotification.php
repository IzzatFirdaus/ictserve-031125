<?php

declare(strict_types=1);

namespace App\Mail;

use App\Mail\Concerns\LogsEmailDispatch;
use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Maintenance Ticket Created Email
 *
 * Sent to maintenance team when an asset is returned damaged and a maintenance ticket is auto-created.
 * Provides cross-module integration details with bilingual support.
 *
 * @component Email Template
 *
 * @description WCAG 2.2 AA compliant email notification for automatic maintenance ticket creation
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-002.3 Asset damage reporting
 * @trace D03-FR-008.4 Cross-module notifications
 * @trace Requirements 2.3, 8.4, 10.3
 *
 * @wcag_level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-04
 */
class MaintenanceTicketNotification extends Mailable implements ShouldQueue
{
    use LogsEmailDispatch, Queueable, SerializesModels;

    public function __construct(
        public HelpdeskTicket $ticket,
        public Asset $asset,
        public LoanApplication $application
    ) {
        // Set queue for 60-second SLA compliance (Requirement 8.4)
        $this->onQueue('emails');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: (string) __('helpdesk.email.maintenance_ticket_subject', [
                'ticket_number' => $this->ticket->ticket_number,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.helpdesk.maintenance-ticket',
            with: [
                'ticket' => $this->ticket,
                'asset' => $this->asset,
                'application' => $this->application,
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

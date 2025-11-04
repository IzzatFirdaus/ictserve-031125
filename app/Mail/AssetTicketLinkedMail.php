<?php

declare(strict_types=1);

namespace App\Mail;

use App\Mail\Concerns\LogsEmailDispatch;
use App\Models\Asset;
use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Asset Ticket Linked Email
 *
 * Sent when a helpdesk ticket is linked to an asset.
 * Provides cross-module integration details with bilingual support.
 *
 * @component Email Template
 *
 * @description WCAG 2.2 AA compliant email notification for asset-ticket linkage
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-002.2 Asset-ticket linking
 * @trace D03-FR-008.4 Cross-module notifications
 * @trace Requirements 2.2, 8.4, 10.3
 *
 * @wcag_level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-04
 */
class AssetTicketLinkedMail extends Mailable implements ShouldQueue
{
    use LogsEmailDispatch, Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public HelpdeskTicket $ticket,
        public Asset $asset
    ) {
        // Set queue for 60-second SLA compliance (Requirement 8.4)
        $this->onQueue('emails');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('helpdesk.email.asset_ticket_linked_subject', [
                'ticket_number' => $this->ticket->ticket_number,
                'asset_name' => $this->asset->name,
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.helpdesk.asset-ticket-linked',
            with: [
                'ticket' => $this->ticket,
                'asset' => $this->asset,
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

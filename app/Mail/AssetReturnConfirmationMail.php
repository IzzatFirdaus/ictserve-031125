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
 * Asset Return Confirmation Email
 *
 * Sent when an asset is returned with reference to related helpdesk ticket.
 * Provides cross-module integration details with bilingual support.
 *
 * @component Email Template
 *
 * @description WCAG 2.2 AA compliant email confirmation for asset returns with ticket reference
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-002.3 Asset return with ticket creation
 * @trace D03-FR-008.4 Cross-module notifications
 * @trace Requirements 2.3, 8.4, 10.3
 *
 * @wcag_level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-04
 */
class AssetReturnConfirmationMail extends Mailable implements ShouldQueue
{
    use LogsEmailDispatch, Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public LoanApplication $loanApplication,
        public Asset $asset,
        public ?HelpdeskTicket $maintenanceTicket = null
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
            subject: __('loans.email.asset_return_confirmation_subject', [
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
            markdown: 'emails.helpdesk.asset-return-confirmation',
            with: [
                'loanApplication' => $this->loanApplication,
                'asset' => $this->asset,
                'maintenanceTicket' => $this->maintenanceTicket,
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

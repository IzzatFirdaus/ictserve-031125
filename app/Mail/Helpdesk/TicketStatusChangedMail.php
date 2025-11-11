<?php

declare(strict_types=1);

namespace App\Mail\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Services\TicketStatusTransitionService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Ticket Status Changed Mail
 *
 * Email notification sent when ticket status changes.
 *
 * @trace Requirements D03-FR-001.4, D03-FR-008.1, Requirement 1.4
 */
class TicketStatusChangedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public HelpdeskTicket $ticket,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Status Tiket Dikemaskini: {$this->ticket->ticket_number}",
        );
    }

    public function content(): Content
    {
        $transitionService = app(TicketStatusTransitionService::class);

        return new Content(
            markdown: 'emails.helpdesk.ticket-status-changed',
            with: [
                'ticket' => $this->ticket,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
                'description' => $transitionService->getTransitionDescription($this->oldStatus, $this->newStatus),
                'ticketUrl' => route('filament.admin.resources.helpdesk.helpdesk-tickets.view', $this->ticket),
            ],
        );
    }
}

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

class MaintenanceTicketNotification extends Mailable implements ShouldQueue
{
    use LogsEmailDispatch, Queueable, SerializesModels;

    public function __construct(
        public HelpdeskTicket $ticket,
        public Asset $asset,
        public LoanApplication $application
    ) {
        $this->onQueue('notifications');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('helpdesk.email.maintenance_ticket_subject', [
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

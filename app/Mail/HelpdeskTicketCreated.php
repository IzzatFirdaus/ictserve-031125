<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Helpdesk Ticket Created Notification
 *
 * Sent to guest email after successful ticket submission
 * Must be delivered within 60 seconds (queued for performance)
 *
 * @see Requirements 1.7 - Email confirmation within 60 seconds
 * @see D03 SRS-HELP-004 - Status token for guest tracking
 */
class HelpdeskTicketCreated extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public HelpdeskTicket $ticket,
        public ?string $statusToken = null
    ) {}

    public function build(): self
    {
        return $this
            ->subject(__('helpdesk.email.ticket_created_subject', ['number' => $this->ticket->ticket_number]))
            ->markdown('emails.helpdesk.ticket-created', [
                'ticket' => $this->ticket,
                'statusToken' => $this->statusToken,
                'trackingUrl' => $this->statusToken
                    ? route('helpdesk.track', ['token' => $this->statusToken])
                    : null,
            ]);
    }
}

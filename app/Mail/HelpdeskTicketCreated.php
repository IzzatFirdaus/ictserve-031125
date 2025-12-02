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
 */
class HelpdeskTicketCreated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public HelpdeskTicket $ticket
    ) {}

    public function build(): self
    {
        return $this
            ->subject(__('Helpdesk Ticket Created: :number', ['number' => $this->ticket->ticket_number]))
            ->markdown('emails.helpdesk.ticket-created');
    }
}

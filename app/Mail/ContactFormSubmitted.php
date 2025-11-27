<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\HelpdeskTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Contact Form Submitted Notification
 *
 * Sent to user after contact form submission is converted to a helpdesk ticket.
 * Must be delivered within 60 seconds (queued for performance).
 *
 * @trace D03-FR-021, R21 (Contact Form Integration)
 */
class ContactFormSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public HelpdeskTicket $ticket
    ) {}

    public function build(): self
    {
        return $this
            ->subject(__('Your Message Has Been Received - Ticket :number', ['number' => $this->ticket->ticket_number]))
            ->markdown('emails.contact-form-submitted');
    }
}

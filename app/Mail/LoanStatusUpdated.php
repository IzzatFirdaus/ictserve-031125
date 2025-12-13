<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public LoanApplication $application,
        public ?string $previousStatus = null,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Loan Application Status Update: '.$this->application->application_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.loans.status-updated',
        );
    }
}

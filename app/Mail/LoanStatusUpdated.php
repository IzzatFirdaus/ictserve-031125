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
        public ?string $previousStatus = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('loan.email.status_updated_subject', [
                'number' => $this->application->application_number,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loan.status-updated',
            with: [
                'application' => $this->application,
                'applicantName' => $this->application->applicant_name,
                'applicationNumber' => $this->application->application_number,
                'currentStatus' => $this->application->status->label(),
                'previousStatus' => $this->previousStatus,
            ],
        );
    }
}

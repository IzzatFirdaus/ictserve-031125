<?php

declare(strict_types=1);

namespace App\Mail\Loan;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanApplicationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public LoanApplication $application
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('loan.email.application_submitted_subject', [
                'number' => $this->application->application_number,
            ])
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loan.application-submitted',
            with: [
                'application' => $this->application,
                'applicantName' => $this->application->applicant_name,
                'applicationNumber' => $this->application->application_number,
                'submittedDate' => $this->application->created_at->format('d/m/Y'),
            ]
        );
    }
}

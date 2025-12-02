<?php

declare(strict_types=1);

namespace App\Mail\Loan;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanApplicationRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public LoanApplication $application,
        public string $reason
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('loan.email.application_rejected_subject', [
                'number' => $this->application->application_number,
            ])
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loan.application-rejected',
            with: [
                'application' => $this->application,
                'applicantName' => $this->application->applicant_name,
                'applicationNumber' => $this->application->application_number,
                'rejectionReason' => $this->reason,
                'rejectedDate' => now()->format('d/m/Y'),
            ]
        );
    }
}

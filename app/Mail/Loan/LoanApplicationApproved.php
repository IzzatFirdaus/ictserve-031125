<?php

declare(strict_types=1);

namespace App\Mail\Loan;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanApplicationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public LoanApplication $application
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('loan.email.application_approved_subject', [
                'number' => $this->application->application_number,
            ])
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loan.application-approved',
            with: [
                'application' => $this->application,
                'applicantName' => $this->application->applicant_name,
                'applicationNumber' => $this->application->application_number,
                'approverName' => $this->application->approved_by_name,
                'approvalDate' => $this->application->approval_date?->format('d/m/Y'),
            ]
        );
    }
}

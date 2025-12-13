<?php

declare(strict_types=1);

namespace App\Mail\Loan;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanApprovalRequest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public LoanApplication $application,
        public string $approvalToken
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: (string) __('loan.email.approval_request_subject', [
                'number' => $this->application->application_number,
            ])
        );
    }

    public function content(): Content
    {
        $approvalUrl = route('loan.approval.approve', ['token' => $this->approvalToken]);
        $declineUrl = route('loan.approval.decline', ['token' => $this->approvalToken]);

        return new Content(
            markdown: 'emails.loan.approval-request',
            with: [
                'application' => $this->application,
                'approverName' => $this->application->approver->name,
                'applicantName' => $this->application->applicant_name,
                'applicationNumber' => $this->application->application_number,
                'approvalUrl' => $approvalUrl,
                'declineUrl' => $declineUrl,
                'expiryDate' => now()->addDays(7)->format('d/m/Y'),
            ]
        );
    }
}

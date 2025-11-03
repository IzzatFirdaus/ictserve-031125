<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Loan Application Submitted Email
 *
 * Sent to applicants (guest or authenticated) when a loan application is submitted.
 * Provides application details and approval timeline.
 *
 * @component Email Template
 *
 * @description WCAG 2.2 AA compliant email confirmation for loan application submission
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-002.1 Email approval workflow
 * @trace D03-FR-009.1 Automated email notifications
 * @trace Requirements 1.4, 10.1, 18.1, 18.2
 *
 * @wcag_level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-04
 */
class LoanApplicationSubmitted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public LoanApplication $application
    ) {
        // Set queue for 60-second SLA compliance (Requirement 1.4)
        $this->onQueue('emails');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('asset_loan.email.application_submitted_subject', [
                'application_number' => $this->application->application_number,
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loans.application-submitted',
            with: [
                'application' => $this->application,
                'applicantName' => $this->application->user
                    ? $this->application->user->name
                    : $this->application->applicant_name,
                'isGuest' => is_null($this->application->user_id),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

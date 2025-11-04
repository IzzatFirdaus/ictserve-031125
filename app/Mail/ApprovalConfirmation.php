<?php

declare(strict_types=1);

namespace App\Mail;

use App\Mail\Concerns\LogsEmailDispatch;
use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Approval Confirmation Email
 *
 * Sent to approvers after they approve or decline a loan application.
 * Confirms their decision and provides application details.
 *
 * @component Email Template
 *
 * @description WCAG 2.2 AA compliant email confirmation for approvers
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-002.1 Email approval workflow
 * @trace D03-FR-009.1 Automated email notifications
 * @trace Requirements 1.4, 10.1, 12.4, 18.1, 18.2
 *
 * @wcag_level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-04
 */
class ApprovalConfirmation extends Mailable implements ShouldQueue
{
    use LogsEmailDispatch, Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public LoanApplication $application,
        public bool $approved
    ) {
        // Set queue for 60-second SLA compliance
        $this->onQueue('emails');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjectKey = $this->approved
            ? 'asset_loan.email.approval_confirmed_subject'
            : 'asset_loan.email.decline_confirmed_subject';

        return new Envelope(
            subject: __($subjectKey, [
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
            markdown: 'emails.loans.approval-confirmation',
            with: [
                'application' => $this->application,
                'applicantName' => $this->application->user
                    ? $this->application->user->name
                    : $this->application->applicant_name,
                'approved' => $this->approved,
                'approverName' => $this->application->approver_name,
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

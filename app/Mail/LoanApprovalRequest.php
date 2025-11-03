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
 * Loan Approval Request Email
 *
 * Sent to Grade 41+ officers for loan application approval.
 * Provides DUAL approval options: email-based (no login) AND portal-based (with login).
 *
 * @component Email Template
 *
 * @description WCAG 2.2 AA compliant email with secure token-based approval links
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-002.1 Email approval workflow
 * @trace D03-FR-002.3 Token-based approval processing
 * @trace Requirements 1.4, 1.5, 1.6, 10.1, 12.1, 12.2, 18.1, 18.2
 *
 * @wcag_level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-04
 */
class LoanApprovalRequest extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public LoanApplication $application,
        public string $token
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
            subject: __('asset_loan.email.approval_request_subject', [
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
            markdown: 'emails.loans.approval-request',
            with: [
                'application' => $this->application,
                'applicantName' => $this->application->user
                    ? $this->application->user->name
                    : $this->application->applicant_name,
                'approveUrl' => route('loans.approve', ['token' => $this->token]),
                'declineUrl' => route('loans.decline', ['token' => $this->token]),
                'portalUrl' => route('staff.approvals.index'),
                'tokenExpiresAt' => $this->application->token_expires_at,
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

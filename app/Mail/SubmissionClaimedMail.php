<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Submission Claimed Mail
 *
 * Confirmation email sent when user claims a guest submission.
 * Provides submission details and portal access information.
 *
 * @see .kiro/specs/staff-dashboard-profile/design.md - Guest Submission Claim Service Design
 * @see .kiro/specs/staff-dashboard-profile/requirements.md - Requirement 2.5
 */
class SubmissionClaimedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance
     */
    public function __construct(
        public User $user,
        public HelpdeskTicket|LoanApplication $submission
    ) {}

    /**
     * Get the message envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Submission Claimed - ICTServe Portal',
        );
    }

    /**
     * Get the message content definition
     */
    public function content(): Content
    {
        $submissionType = $this->submission instanceof HelpdeskTicket ? 'ticket' : 'loan';
        $submissionNumber = $this->submission instanceof HelpdeskTicket
            ? $this->submission->ticket_number
            : $this->submission->application_number;

        return new Content(
            markdown: 'emails.submission-claimed',
            with: [
                'userName' => $this->user->name,
                'submissionType' => $submissionType,
                'submissionNumber' => $submissionNumber,
                'submissionUrl' => route('portal.submissions.show', [
                    'type' => $submissionType,
                    'id' => $this->submission->id,
                ]),
                'dashboardUrl' => route('portal.dashboard'),
            ],
        );
    }

    /**
     * Get the attachments for the message
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

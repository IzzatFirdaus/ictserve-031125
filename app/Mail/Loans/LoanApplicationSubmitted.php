<?php

declare(strict_types=1);

namespace App\Mail\Loans;

use App\Mail\Concerns\LogsEmailDispatch;
use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Synchronous variant of the loan application confirmation email.
 *
 * The root namespace mailables continue to implement ShouldQueue for high-volume
 * dispatching, while this Loans namespace variant keeps assertions simple in
 * feature tests that verify immediate delivery semantics.
 */
class LoanApplicationSubmitted extends Mailable
{
    use LogsEmailDispatch, Queueable, SerializesModels;

    public function __construct(
        public LoanApplication $application
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('asset_loan.email.application_submitted_subject', [
                'application_number' => $this->application->application_number,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loans.application-submitted',
            with: [
                'application' => $this->application,
                'applicantName' => $this->application->user?->name
                    ?? $this->application->applicant_name,
                'isGuest' => is_null($this->application->user_id),
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

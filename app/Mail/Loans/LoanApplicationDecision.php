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
use Illuminate\Support\Str;

class LoanApplicationDecision extends Mailable
{
    use LogsEmailDispatch, Queueable, SerializesModels;

    public bool $approved;

    public function __construct(
        public LoanApplication $application,
        bool|string $decision
    ) {
        $this->approved = is_bool($decision)
            ? $decision
            : Str::of((string) $decision)->lower()->exactly('approved');
    }

    public function envelope(): Envelope
    {
        $subjectKey = $this->approved
            ? 'asset_loan.email.application_approved_subject'
            : 'asset_loan.email.application_declined_subject';

        return new Envelope(
            subject: __($subjectKey, [
                'application_number' => $this->application->application_number,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loans.application-decision',
            with: [
                'application' => $this->application,
                'applicantName' => $this->application->user?->name
                    ?? $this->application->applicant_name,
                'approved' => $this->approved,
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

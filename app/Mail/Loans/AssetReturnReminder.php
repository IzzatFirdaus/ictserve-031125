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

class AssetReturnReminder extends Mailable
{
    use LogsEmailDispatch, Queueable, SerializesModels;

    public function __construct(
        public LoanApplication $application,
        public int $daysBeforeDue = 2
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('asset_loan.email.return_reminder_subject', [
                'application_number' => $this->application->application_number,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loans.return-reminder',
            with: [
                'application' => $this->application,
                'borrowerName' => $this->application->user?->name
                    ?? $this->application->applicant_name,
                'dueDate' => $this->application->loan_end_date,
                'daysBeforeDue' => $this->daysBeforeDue,
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

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

class AssetPreparationNotification extends Mailable implements ShouldQueue
{
    use LogsEmailDispatch, Queueable, SerializesModels;

    public function __construct(
        public LoanApplication $application
    ) {
        $this->onQueue('notifications');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('asset_loan.email.asset_preparation_subject', [
                'application_number' => $this->application->application_number,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loans.asset-preparation',
            with: [
                'application' => $this->application,
                'borrowerName' => $this->application->user
                    ? $this->application->user->name
                    : $this->application->applicant_name,
                'loanStart' => $this->application->loan_start_date,
                'loanEnd' => $this->application->loan_end_date,
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

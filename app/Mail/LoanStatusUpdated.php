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

class LoanStatusUpdated extends Mailable implements ShouldQueue
{
    use LogsEmailDispatch, Queueable, SerializesModels;

    public function __construct(
        public LoanApplication $application,
        public ?string $previousStatus = null
    ) {
        $this->onQueue('emails');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('asset_loan.email.status_update_subject', [
                'application_number' => $this->application->application_number,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loans.status-updated',
            with: [
                'application' => $this->application,
                'applicantName' => $this->application->user
                    ? $this->application->user->name
                    : $this->application->applicant_name,
                'previousStatus' => $this->previousStatus,
                'currentStatus' => $this->application->status,
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

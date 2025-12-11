<?php

declare(strict_types=1);

namespace App\Mail\Loan;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssetReturnReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public LoanApplication $application,
        public int $daysUntilDue
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: (string) __('loan.email.return_reminder_subject', [
                'number' => $this->application->application_number,
                'days' => $this->daysUntilDue,
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loan.return-reminder',
            with: [
                'application' => $this->application,
                'applicantName' => $this->application->applicant_name,
                'applicationNumber' => $this->application->application_number,
                'dueDate' => $this->application->end_date->format('d/m/Y'),
                'daysUntilDue' => $this->daysUntilDue,
                'returnLocation' => __('loan.email.bpm_office_location'),
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

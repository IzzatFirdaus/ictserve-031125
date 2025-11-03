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
 * Asset Return Reminder Email
 *
 * Sent to borrowers 48 hours before asset return due date.
 * Provides return instructions and contact information.
 *
 * @component Email Template
 *
 * @description WCAG 2.2 AA compliant email reminder for asset returns
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-009.1 Automated email notifications
 * @trace Requirements 10.1, 10.4, 18.1, 18.2
 *
 * @wcag_level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-04
 */
class AssetReturnReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public LoanApplication $application
    ) {
        // Set queue for automated reminders
        $this->onQueue('notifications');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('asset_loan.email.return_reminder_subject', [
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
            markdown: 'emails.loans.return-reminder',
            with: [
                'application' => $this->application,
                'borrowerName' => $this->application->user
                    ? $this->application->user->name
                    : $this->application->applicant_name,
                'dueDate' => $this->application->end_date,
                'hoursRemaining' => now()->diffInHours($this->application->end_date),
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

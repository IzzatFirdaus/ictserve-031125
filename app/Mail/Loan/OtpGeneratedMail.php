<?php

declare(strict_types=1);

namespace App\Mail\Loan;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpGeneratedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public LoanApplication $application,
        public string $otp
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('loan.email.otp_generated_subject', [
                'number' => $this->application->application_number,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loan.otp-generated',
            with: [
                'application' => $this->application,
                'applicantName' => $this->application->applicant_name,
                'applicationNumber' => $this->application->application_number,
                'otp' => $this->otp,
                'expiryDate' => $this->application->pickup_otp_expires_at?->format('d/m/Y H:i'),
                'collectionLocation' => __('loan.email.bpm_office_location'),
            ],
        );
    }
}

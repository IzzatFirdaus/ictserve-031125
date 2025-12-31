<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestGmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $testMessage = 'This is a test email from ICTServe Gmail integration'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ICTServe Gmail Integration Test',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.test-gmail',
            with: [
                'message' => $this->testMessage,
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'app_name' => config('app.name'),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

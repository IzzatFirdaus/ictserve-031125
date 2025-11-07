<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Export Ready Mail
 *
 * Notification email sent when large export is ready for download.
 * Includes download link and expiration notice (7 days).
 *
 * @see .kiro/specs/staff-dashboard-profile/design.md - Export Service Design
 * @see .kiro/specs/staff-dashboard-profile/requirements.md - Requirements 9.4, 9.5
 */
class ExportReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance
     */
    public function __construct(
        public User $user,
        public string $filename,
        public string $jobId
    ) {}

    /**
     * Get the message envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Export is Ready - ICTServe Portal',
        );
    }

    /**
     * Get the message content definition
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.export-ready',
            with: [
                'userName' => $this->user->name,
                'filename' => $this->filename,
                'downloadUrl' => route('portal.exports.download', ['filename' => $this->filename]),
                'expiresAt' => now()->addDays(7)->format('d M Y'),
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

<?php

declare(strict_types=1);

namespace App\Mail\Users;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Component name: User Welcome Mail
 * Description: Welcome email sent to new users with temporary password and login instructions
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-004.3 (User Management - Welcome Email)
 * @trace D04 §3.3 (User Creation Workflow)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Email Compliance)
 *
 * @version 1.0.0
 *
 * @created 2025-11-07
 */
class UserWelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public string $temporaryPassword,
        public string $loginUrl
    ) {
        // Queue with 5-second delay for 60-second SLA compliance
        $this->delay(now()->addSeconds(5));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Welcome to ICTServe - Your Account Has Been Created'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.users.welcome',
            with: [
                'user' => $this->user,
                'temporaryPassword' => $this->temporaryPassword,
                'loginUrl' => $this->loginUrl,
                'supportEmail' => config('mail.support_email', 'support@motac.gov.my'),
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

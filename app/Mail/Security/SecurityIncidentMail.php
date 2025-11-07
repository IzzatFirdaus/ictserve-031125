<?php

declare(strict_types=1);

namespace App\Mail\Security;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Security Incident Alert Email
 *
 * Sends immediate security incident notifications to superusers.
 * Queued for 60-second SLA compliance with high priority.
 *
 * Requirements: 9.4, 9.5
 *
 * @see D03-FR-007.4 Security incident alerts
 * @see D11 §8 Security implementation
 */
class SecurityIncidentMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public int $maxExceptions = 3;

    public int $timeout = 120;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public array $incidentData,
        public User $recipient
    ) {
        $this->queue = 'high-priority';
        $this->delay = now()->addSeconds(5); // 5-second delay for 60-second SLA
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $severity = strtoupper($this->incidentData['severity']);
        $type = ucfirst(str_replace('_', ' ', $this->incidentData['type']));

        return new Envelope(
            subject: "[{$severity}] Security Incident Alert: {$type} - ICTServe",
            tags: ['security', 'incident', 'alert'],
            metadata: [
                'incident_id' => $this->incidentData['incident_id'],
                'severity' => $this->incidentData['severity'],
                'type' => $this->incidentData['type'],
                'recipient_id' => $this->recipient->id,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.security.security-incident',
            with: [
                'incidentData' => $this->incidentData,
                'recipient' => $this->recipient,
                'dashboardUrl' => route('filament.admin.pages.security-monitoring'),
                'auditTrailUrl' => route('filament.admin.resources.system.audits.index'),
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

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Security incident email failed to send', [
            'incident_id' => $this->incidentData['incident_id'],
            'recipient' => $this->recipient->email,
            'error' => $exception->getMessage(),
            'severity' => $this->incidentData['severity'],
            'type' => $this->incidentData['type'],
        ]);
    }
}

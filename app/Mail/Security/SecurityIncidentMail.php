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
     * Notification type (superuser, csirt, nacsa, mycert)
     */
    public string $notificationType;

    /**
     * Create a new message instance.
     *
     * @param  array<string, mixed>  $incidentData
     */
    public function __construct(
        public array $incidentData,
        public ?User $recipient = null,
        string $notificationType = 'superuser'
    ) {
        $this->notificationType = $notificationType;
        $this->queue = 'high-priority';
        $this->delay = now()->addSeconds(5); // 5-second delay for 60-second SLA
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $severity = strtoupper($this->incidentData['severity'] ?? 'UNKNOWN');
        $type = ucfirst(str_replace('_', ' ', $this->incidentData['type'] ?? 'unknown'));
        $incidentNumber = $this->incidentData['incident_number'] ?? 'N/A';

        $subject = match ($this->notificationType) {
            'csirt' => "[CSIRT] [{$severity}] Insiden Keselamatan: {$type} - {$incidentNumber}",
            'nacsa' => "[NACSA] Laporan Insiden Keselamatan: {$incidentNumber}",
            'mycert' => "[MyCERT] Laporan Insiden Keselamatan: {$incidentNumber}",
            default => "[{$severity}] Amaran Insiden Keselamatan: {$type} - ICTServe",
        };

        return new Envelope(
            subject: $subject,
            tags: ['security', 'incident', 'alert', $this->notificationType],
            metadata: [
                'incident_number' => $incidentNumber,
                'severity' => $this->incidentData['severity'] ?? 'unknown',
                'type' => $this->incidentData['type'] ?? 'unknown',
                'recipient_id' => $this->recipient?->id,
                'notification_type' => $this->notificationType,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $view = match ($this->notificationType) {
            'csirt' => 'emails.security.csirt-notification',
            'nacsa' => 'emails.security.nacsa-report',
            'mycert' => 'emails.security.mycert-report',
            default => 'emails.security.security-incident',
        };

        // Use fallback view if specific view doesn't exist
        if (! view()->exists($view)) {
            $view = 'emails.security.security-incident';
        }

        return new Content(
            view: $view,
            with: [
                'incidentData' => $this->incidentData,
                'recipient' => $this->recipient,
                'notificationType' => $this->notificationType,
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
            'incident_number' => $this->incidentData['incident_number'] ?? 'N/A',
            'recipient' => $this->recipient?->email ?? 'N/A',
            'notification_type' => $this->notificationType,
            'error' => $exception->getMessage(),
            'severity' => $this->incidentData['severity'] ?? 'unknown',
            'type' => $this->incidentData['type'] ?? 'unknown',
        ]);
    }
}

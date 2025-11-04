<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * System Alert Mail
 *
 * Email template for system alerts and notifications.
 * Supports multiple alert types with appropriate styling and urgency indicators.
 *
 * Requirements: 13.4, 9.3, 9.4, 2.5
 */
class SystemAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $alertData
    ) {}

    public function envelope(): Envelope
    {
        $urgencyPrefix = match ($this->alertData['severity']) {
            'critical' => '[KRITIKAL] ',
            'high' => '[TINGGI] ',
            'medium' => '[SEDERHANA] ',
            default => '',
        };

        $subject = $urgencyPrefix.$this->getAlertSubject();

        return new Envelope(
            subject: $subject,
            tags: ['system-alert', $this->alertData['type'], $this->alertData['severity']],
            metadata: [
                'alert_type' => $this->alertData['type'],
                'severity' => $this->alertData['severity'],
                'generated_at' => now()->toISOString(),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.system-alert',
            with: [
                'alertData' => $this->alertData,
                'alertTitle' => $this->getAlertTitle(),
                'alertIcon' => $this->getAlertIcon(),
                'severityColor' => $this->getSeverityColor(),
                'actionUrl' => $this->getActionUrl(),
            ],
        );
    }

    private function getAlertSubject(): string
    {
        return match ($this->alertData['type']) {
            'overdue_tickets' => "Amaran Tiket Tertunggak - {$this->alertData['count']} tiket",
            'overdue_loans' => "Amaran Pinjaman Tertunggak - {$this->alertData['count']} pinjaman",
            'approval_delays' => "Amaran Kelewatan Kelulusan - {$this->alertData['count']} permohonan",
            'asset_shortages' => "Amaran Kekurangan Aset - {$this->alertData['availability_rate']}% ketersediaan",
            'system_health' => "Amaran Kesihatan Sistem - {$this->alertData['health_score']}%",
            'system_test' => 'Ujian Sistem Amaran ICTServe',
            default => 'Amaran Sistem ICTServe',
        };
    }

    private function getAlertTitle(): string
    {
        return match ($this->alertData['type']) {
            'overdue_tickets' => 'Tiket Helpdesk Tertunggak',
            'overdue_loans' => 'Pinjaman Aset Tertunggak',
            'approval_delays' => 'Kelewatan Proses Kelulusan',
            'asset_shortages' => 'Kekurangan Aset Kritikal',
            'system_health' => 'Kesihatan Sistem Rendah',
            'system_test' => 'Ujian Sistem Amaran',
            default => 'Amaran Sistem',
        };
    }

    private function getAlertIcon(): string
    {
        return match ($this->alertData['type']) {
            'overdue_tickets' => '🎫',
            'overdue_loans' => '⏰',
            'approval_delays' => '⏸️',
            'asset_shortages' => '📦',
            'system_health' => '💓',
            'system_test' => '🧪',
            default => '🔔',
        };
    }

    private function getSeverityColor(): string
    {
        return match ($this->alertData['severity']) {
            'critical' => '#b50c0c',
            'high' => '#ff8c00',
            'medium' => '#0056b3',
            'low' => '#198754',
            default => '#6c757d',
        };
    }

    private function getActionUrl(): string
    {
        $baseUrl = config('app.url');

        return match ($this->alertData['type']) {
            'overdue_tickets' => "{$baseUrl}/admin/helpdesk-tickets?tableFilters[status][value]=overdue",
            'overdue_loans' => "{$baseUrl}/admin/loan-applications?tableFilters[status][value]=overdue",
            'approval_delays' => "{$baseUrl}/admin/loan-applications?tableFilters[status][value]=under_review",
            'asset_shortages' => "{$baseUrl}/admin/assets?tableFilters[status][value]=available",
            'system_health' => "{$baseUrl}/admin/unified-analytics-dashboard",
            'system_test' => "{$baseUrl}/admin/alert-configuration",
            default => "{$baseUrl}/admin",
        };
    }
}

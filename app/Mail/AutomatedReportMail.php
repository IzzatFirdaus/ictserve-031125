<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

/**
 * Automated Report Mail
 *
 * Email template for delivering automated reports with attachments.
 * Supports bilingual content and WCAG compliant formatting.
 *
 * Requirements: 13.2, 13.5, 9.1, 4.5
 */
class AutomatedReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $reportData,
        public array $attachmentFiles,
        public string $frequency
    ) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->frequency) {
            'daily' => 'Laporan Harian ICTServe - '.now()->format('d/m/Y'),
            'weekly' => 'Laporan Mingguan ICTServe - Minggu '.now()->weekOfYear.'/'.now()->year,
            'monthly' => 'Laporan Bulanan ICTServe - '.now()->format('F Y'),
            default => 'Laporan ICTServe - '.now()->format('d/m/Y'),
        };

        return new Envelope(
            subject: $subject,
            tags: ['automated-report', $this->frequency],
            metadata: [
                'report_type' => $this->frequency,
                'generated_at' => $this->reportData['report_info']['generated_at'],
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.automated-report',
            with: [
                'reportData' => $this->reportData,
                'frequency' => $this->frequency,
                'attachmentCount' => count($this->attachmentFiles),
            ],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->attachmentFiles as $format => $filepath) {
            if (Storage::exists($filepath)) {
                $attachments[] = Attachment::fromStorage($filepath)
                    ->as($this->getAttachmentName($format))
                    ->withMime($this->getMimeType($format));
            }
        }

        return $attachments;
    }

    private function getAttachmentName(string $format): string
    {
        $timestamp = now()->format('Ymd');
        $frequency = ucfirst($this->frequency);

        return match ($format) {
            'pdf' => "ICTServe_Laporan_{$frequency}_{$timestamp}.pdf",
            'excel' => "ICTServe_Data_{$frequency}_{$timestamp}.xlsx",
            'csv' => "ICTServe_Data_{$frequency}_{$timestamp}.csv",
            default => "ICTServe_Report_{$timestamp}.txt",
        };
    }

    private function getMimeType(string $format): string
    {
        return match ($format) {
            'pdf' => 'application/pdf',
            'excel' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'csv' => 'text/csv',
            default => 'text/plain',
        };
    }
}

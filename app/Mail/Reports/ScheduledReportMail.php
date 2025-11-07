<?php

declare(strict_types=1);

namespace App\Mail\Reports;

use App\Models\ReportSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScheduledReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ReportSchedule $schedule,
        public string $filePath,
        public array $metadata
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Laporan Terjadual: {$this->schedule->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reports.scheduled-report',
            with: [
                'schedule' => $this->schedule,
                'metadata' => $this->metadata,
                'generatedAt' => now(),
            ],
        );
    }

    public function attachments(): array
    {
        $filename = basename($this->filePath);

        return [
            Attachment::fromStorage($this->filePath)
                ->as($filename)
                ->withMime($this->getMimeType()),
        ];
    }

    private function getMimeType(): string
    {
        return match ($this->schedule->format) {
            'pdf' => 'application/pdf',
            'csv' => 'text/csv',
            'excel' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            default => 'application/octet-stream',
        };
    }
}

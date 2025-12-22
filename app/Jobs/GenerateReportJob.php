<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Services\ReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Generate Report Job
 *
 * Handles automated report generation and delivery for ICTServe system.
 * Supports daily, weekly, and monthly reports with email delivery.
 *
 * @see Requirements 13.4, 14.1, 14.2, 14.4, 23.1, 23.6, 23.7
 */
class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out
     */
    public int $timeout = 600;

    /**
     * The backoff delays between retry attempts (exponential backoff)
     *
     * @var array<int>
     */
    public array $backoff = [10, 30, 60];

    /**
     * Create a new job instance
     *
     * @param  array<string, mixed>  $parameters
     * @param  array<string>  $recipients
     */
    public function __construct(
        public string $reportType,
        public array $parameters = [],
        public array $recipients = [],
        public ?User $requestedBy = null
    ) {
        $this->onQueue('reports');
    }

    /**
     * Execute the job
     */
    public function handle(ReportService $reportService): void
    {
        $startTime = microtime(true);

        Log::info('GenerateReportJob started', [
            'report_type' => $this->reportType,
            'parameters' => $this->parameters,
            'recipients_count' => count($this->recipients),
            'requested_by' => $this->requestedBy?->id,
            'attempt' => $this->attempts(),
        ]);

        try {
            // Generate the report
            $reportData = $this->generateReport($reportService);

            // Save report file
            $filename = $this->saveReportFile($reportData);

            // Send to recipients
            $this->deliverReport($filename, $reportData);

            $processingTime = microtime(true) - $startTime;

            Log::info('GenerateReportJob completed successfully', [
                'report_type' => $this->reportType,
                'filename' => $filename,
                'recipients_count' => count($this->recipients),
                'processing_time' => $processingTime,
                'file_size' => file_exists(storage_path('app/reports/'.$filename)) ?
                    filesize(storage_path('app/reports/'.$filename)) : 0,
            ]);
        } catch (\Exception $e) {
            $this->handleFailure($e, microtime(true) - $startTime);
            throw $e;
        }
    }

    /**
     * Generate report based on type
     */
    

/**
 * @return array<string, mixed>
 */
private function generateReport(ReportService $reportService): array
    {
        return match ($this->reportType) {
            'daily' => $reportService->generateDailyReport($this->parameters),
            'weekly' => $reportService->generateWeeklyReport($this->parameters),
            'monthly' => $reportService->generateMonthlyReport($this->parameters),
            'helpdesk_summary' => $reportService->generateHelpdeskSummary($this->parameters),
            'asset_utilization' => $reportService->generateAssetUtilizationReport($this->parameters),
            'sla_compliance' => $reportService->generateSLAComplianceReport($this->parameters),
            'user_activity' => $reportService->generateUserActivityReport($this->parameters),
            'system_performance' => $reportService->generateSystemPerformanceReport($this->parameters),
            default => throw new \InvalidArgumentException("Unknown report type: {$this->reportType}"),
        };
    }

    /**
     * Save report to file
     */
    private function saveReportFile(array $reportData): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "{$this->reportType}_report_{$timestamp}";

        // Determine format based on parameters
        $format = $this->parameters['format'] ?? 'pdf';

        switch ($format) {
            case 'csv':
                $filename .= '.csv';
                $this->saveCsvReport($filename, $reportData);
                break;

            case 'excel':
                $filename .= '.xlsx';
                $this->saveExcelReport($filename, $reportData);
                break;

            case 'json':
                $filename .= '.json';
                $this->saveJsonReport($filename, $reportData);
                break;

            default:
                $filename .= '.pdf';
                $this->savePdfReport($filename, $reportData);
                break;
        }

        return $filename;
    }

    /**
     * Save CSV report
     */
    private function saveCsvReport(string $filename, array $reportData): void
    {
        $path = storage_path('app/reports/'.$filename);

        // Ensure directory exists
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        // Write headers
        if (! empty($reportData['data'])) {
            fputcsv($file, array_keys($reportData['data'][0]));

            // Write data rows
            foreach ($reportData['data'] as $row) {
                fputcsv($file, $row);
            }
        }

        fclose($file);
    }

    /**
     * Save JSON report
     */
    private function saveJsonReport(string $filename, array $reportData): void
    {
        $path = storage_path('app/reports/'.$filename);

        // Ensure directory exists
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, json_encode($reportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Save Excel report (placeholder - would need PhpSpreadsheet)
     */
    private function saveExcelReport(string $filename, array $reportData): void
    {
        // For now, save as CSV with .xlsx extension
        // In production, implement with PhpSpreadsheet
        $this->saveCsvReport($filename, $reportData);
    }

    /**
     * Save PDF report (placeholder - would need DOMPDF or similar)
     */
    private function savePdfReport(string $filename, array $reportData): void
    {
        // For now, save as JSON with .pdf extension
        // In production, implement with DOMPDF or similar
        $this->saveJsonReport($filename, $reportData);
    }

    /**
     * Deliver report to recipients
     */
    private function deliverReport(string $filename, array $reportData): void
    {
        $filePath = storage_path('app/reports/'.$filename);

        if (empty($this->recipients)) {
            // Default recipients for automated reports
            $this->recipients = $this->getDefaultRecipients();
        }

        foreach ($this->recipients as $recipient) {
            try {
                $this->sendReportEmail($recipient, $filename, $filePath, $reportData);
            } catch (\Exception $e) {
                Log::warning('Failed to send report to recipient', [
                    'recipient' => $recipient,
                    'filename' => $filename,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send report via email
     */
    private function sendReportEmail(string $recipient, string $filename, string $filePath, array $reportData): void
    {
        $subject = $this->getReportSubject();
        $summary = $this->generateReportSummary($reportData);

        Mail::raw($summary, function ($message) use ($recipient, $subject, $filePath, $filename) {
            $message->to($recipient)
                ->subject($subject);

            if (file_exists($filePath)) {
                $message->attach($filePath, [
                    'as' => $filename,
                    'mime' => $this->getMimeType($filename),
                ]);
            }
        });
    }

    /**
     * Get report subject line
     */
    private function getReportSubject(): string
    {
        $date = now()->format('d/m/Y');

        return match ($this->reportType) {
            'daily' => "Laporan Harian ICTServe - {$date}",
            'weekly' => "Laporan Mingguan ICTServe - {$date}",
            'monthly' => "Laporan Bulanan ICTServe - {$date}",
            'helpdesk_summary' => "Ringkasan Helpdesk ICTServe - {$date}",
            'asset_utilization' => "Laporan Penggunaan Aset ICTServe - {$date}",
            'sla_compliance' => "Laporan Pematuhan SLA ICTServe - {$date}",
            default => "Laporan ICTServe - {$date}",
        };
    }

    /**
     * Generate report summary for email body
     */
    private function generateReportSummary(array $reportData): string
    {
        $summary = "Laporan {$this->reportType} ICTServe telah dijana.\n\n";

        if (isset($reportData['summary'])) {
            $summary .= "Ringkasan:\n";
            foreach ($reportData['summary'] as $key => $value) {
                $summary .= "- {$key}: {$value}\n";
            }
        }

        $summary .= "\nLaporan penuh dilampirkan dalam fail.\n\n";
        $summary .= 'Sistem ICTServe BPM MOTAC';

        return $summary;
    }

    /**
     * Get default recipients for automated reports
     */
    

/**
 * @return array<string, mixed>
 */
private function getDefaultRecipients(): array
    {
        $admins = User::whereIn('role', ['admin', 'superuser'])
            ->pluck('email')
            ->toArray();

        return $admins ?: ['admin@motac.gov.my'];
    }

    /**
     * Get MIME type for file
     */
    private function getMimeType(string $filename): string
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        return match ($extension) {
            'csv' => 'text/csv',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'pdf' => 'application/pdf',
            'json' => 'application/json',
            default => 'application/octet-stream',
        };
    }

    /**
     * Handle job failure
     */
    private function handleFailure(\Exception $e, float $processingTime): void
    {
        Log::error('GenerateReportJob failed', [
            'report_type' => $this->reportType,
            'parameters' => $this->parameters,
            'recipients_count' => count($this->recipients),
            'requested_by' => $this->requestedBy?->id,
            'attempt' => $this->attempts(),
            'max_tries' => $this->tries,
            'processing_time' => $processingTime,
            'error' => $e->getMessage(),
        ]);
    }

    /**
     * Handle permanent job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('GenerateReportJob permanently failed', [
            'report_type' => $this->reportType,
            'parameters' => $this->parameters,
            'error' => $exception->getMessage(),
        ]);

        // Notify administrators about report generation failure
        try {
            $admins = $this->getDefaultRecipients();
            foreach ($admins as $admin) {
                Mail::raw(
                    "Penjanaan laporan {$this->reportType} gagal: {$exception->getMessage()}",
                    function ($message) use ($admin) {
                        $message->to($admin)
                            ->subject('Kegagalan Penjanaan Laporan ICTServe');
                    }
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to send report failure notification', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * Requirement 23.7: Job tagging for ICTServe operations
     *
     * @return array<string>
     */
    

/**
 * @return array<string, mixed>
 */
public function tags(): array
    {
        $tags = [
            'reports',
            'type:'.$this->reportType,
        ];

        if ($this->requestedBy) {
            $tags[] = 'requested_by:'.$this->requestedBy->id;
        }

        if (isset($this->parameters['format'])) {
            $tags[] = 'format:'.$this->parameters['format'];
        }

        return $tags;
    }

    /**
     * Static factory methods for common report types
     */
    public static function daily(array $parameters = [], array $recipients = []): self
    {
        return new self('daily', $parameters, $recipients);
    }

    public static function weekly(array $parameters = [], array $recipients = []): self
    {
        return new self('weekly', $parameters, $recipients);
    }

    public static function monthly(array $parameters = [], array $recipients = []): self
    {
        return new self('monthly', $parameters, $recipients);
    }
}

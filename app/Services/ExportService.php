<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\ExportSubmissionsJob;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Export Service
 *
 * Handles CSV and PDF export generation for submission history.
 * Implements queue processing for large exports (>1000 records).
 *
 * @see .kiro/specs/staff-dashboard-profile/design.md - Export Service Design
 * @see .kiro/specs/staff-dashboard-profile/requirements.md - Requirements 9.1, 9.2, 9.3, 9.4
 */
class ExportService
{
    /**
     * Threshold for queueing large exports
     */
    private const LARGE_EXPORT_THRESHOLD = 1000;

    /**
     * Export submissions for user
     *
     * @param  string  $format  'csv' or 'pdf'
     * @param  array<string, mixed>  $filters
     * @return string Filename or job ID
     */
    public function exportSubmissions(User $user, string $format, array $filters = []): string
    {
        $submissions = $this->getSubmissionsForExport($user, $filters);

        if ($submissions->count() > self::LARGE_EXPORT_THRESHOLD) {
            return $this->queueLargeExport($user, $format, $filters);
        }

        return $format === 'csv'
            ? $this->generateCSV($submissions)
            : $this->generatePDF($submissions, $user);
    }

    /**
     * Stream a CSV export directly to the browser.
     *
     * @param  string  $type  helpdesk|loan|all
     * @param  array<string, mixed>  $filters
     */
    public function exportToCsv(User $user, string $type, array $filters = []): StreamedResponse
    {
        $filters['type'] = $type;

        $submissions = $this->getSubmissionsForExport($user, $filters);
        $filename = $this->generateFilename($user, $type, 'csv');

        return $this->streamSubmissions($submissions, $filename, 'text/csv');
    }

    /**
     * Stream an Excel-compatible export directly to the browser.
     *
     * @param  string  $type  helpdesk|loan|all
     * @param  array<string, mixed>  $filters
     */
    public function exportToExcel(User $user, string $type, array $filters = []): StreamedResponse
    {
        $filters['type'] = $type;

        $submissions = $this->getSubmissionsForExport($user, $filters);
        $filename = $this->generateFilename($user, $type, 'xlsx');

        return $this->streamSubmissions($submissions, $filename, 'application/vnd.ms-excel', true);
    }

    /**
     * Get submissions for export
     *
     * @param  array<string, mixed>  $filters
     */
    private function getSubmissionsForExport(User $user, array $filters): Collection
    {
        $typeFilter = $filters['type'] ?? 'all';
        $statuses = array_filter((array) ($filters['statuses'] ?? []));
        $dateFrom = $this->normalizeDate($filters['date_from'] ?? null);
        $dateTo = $this->normalizeDate($filters['date_to'] ?? null);

        $tickets = collect();
        if ($typeFilter !== 'loan') {
            $ticketQuery = HelpdeskTicket::query()
                ->where('user_id', $user->id)
                ->with(['division:id,name', 'category:id,name']);

            if ($statuses) {
                $ticketQuery->whereIn('status', $statuses);
            }

            if ($dateFrom) {
                $ticketQuery->where('created_at', '>=', $dateFrom);
            }

            if ($dateTo) {
                $ticketQuery->where('created_at', '<=', $dateTo);
            }

            $tickets = $ticketQuery->get()->map(fn ($ticket) => [
                'type' => 'Helpdesk Ticket',
                'number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'date_submitted' => $ticket->created_at->format('Y-m-d H:i:s'),
                'last_updated' => $ticket->updated_at->format('Y-m-d H:i:s'),
            ]);
        }

        $loans = collect();
        if ($typeFilter !== 'helpdesk') {
            $loanQuery = LoanApplication::query()
                ->where('user_id', $user->id)
                ->with(['loanItems.asset:id,name']);

            if ($statuses) {
                $loanQuery->whereIn('status', $statuses);
            }

            if ($dateFrom) {
                $loanQuery->where('created_at', '>=', $dateFrom);
            }

            if ($dateTo) {
                $loanQuery->where('created_at', '<=', $dateTo);
            }

            $loans = $loanQuery->get()->map(fn ($loan) => [
                'type' => 'Asset Loan',
                'number' => $loan->application_number,
                'subject' => $loan->loanItems->pluck('asset.name')->join(', ') ?: 'N/A',
                'status' => $loan->status,
                'date_submitted' => $loan->created_at->format('Y-m-d H:i:s'),
                'last_updated' => $loan->updated_at->format('Y-m-d H:i:s'),
            ]);
        }

        return $tickets->merge($loans)->values();
    }

    /**
     * Stream submissions as a downloadable CSV-style response.
     *
     * @param  array<int, array<string, string>>  $submissions
     */
    private function streamSubmissions(Collection $submissions, string $filename, string $contentType, bool $withBom = false): StreamedResponse
    {
        return Response::streamDownload(function () use ($submissions, $withBom) {
            $handle = fopen('php://output', 'w');

            if ($withBom) {
                fwrite($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            }

            fputcsv($handle, [
                'Submission Type',
                'Number',
                'Subject/Asset',
                'Status',
                'Date Submitted',
                'Last Updated',
            ]);

            foreach ($submissions as $submission) {
                fputcsv($handle, [
                    $submission['type'],
                    $submission['number'],
                    $submission['subject'],
                    $submission['status'],
                    $submission['date_submitted'],
                    $submission['last_updated'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    private function generateFilename(User $user, string $type, string $extension): string
    {
        $safeName = Str::slug($user->name) ?: 'user';
        $timestamp = now()->format('Y-m-d_His');
        $suffix = $type === 'all' ? 'submissions' : "{$type}_submissions";

        return "{$suffix}_{$safeName}_{$timestamp}.{$extension}";
    }

    private function normalizeDate(null|Carbon|string $value): ?Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            return Carbon::parse($value);
        }

        return null;
    }

    /**
     * Generate CSV export
     *
     * @return string Filename
     */
    private function generateCSV(Collection $submissions): string
    {
        $filename = 'submissions_'.now()->format('Y-m-d_His').'.csv';
        $path = "exports/{$filename}";

        $csv = fopen('php://temp', 'r+');

        // Write headers
        fputcsv($csv, [
            'Submission Type',
            'Number',
            'Subject/Asset',
            'Status',
            'Date Submitted',
            'Last Updated',
        ]);

        // Write data
        foreach ($submissions as $submission) {
            fputcsv($csv, [
                $submission['type'],
                $submission['number'],
                $submission['subject'],
                $submission['status'],
                $submission['date_submitted'],
                $submission['last_updated'],
            ]);
        }

        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        Storage::disk('local')->put($path, $content);

        return $filename;
    }

    /**
     * Generate PDF export
     *
     * @return string Filename
     */
    private function generatePDF(Collection $submissions, User $user): string
    {
        $filename = 'submissions_'.now()->format('Y-m-d_His').'.pdf';
        $path = "exports/{$filename}";

        // For now, create a simple text-based PDF placeholder
        // In production, use a proper PDF library like DomPDF or Snappy
        $content = "Submission History Report\n";
        $content .= "Generated for: {$user->name}\n";
        $content .= 'Generated at: '.now()->format('Y-m-d H:i:s')."\n\n";
        $content .= 'Total Submissions: '.$submissions->count()."\n\n";

        foreach ($submissions as $submission) {
            $content .= "Type: {$submission['type']}\n";
            $content .= "Number: {$submission['number']}\n";
            $content .= "Subject: {$submission['subject']}\n";
            $content .= "Status: {$submission['status']}\n";
            $content .= "Date: {$submission['date_submitted']}\n";
            $content .= "---\n\n";
        }

        Storage::disk('local')->put($path, $content);

        return $filename;
    }

    /**
     * Queue large export for background processing
     *
     * @param  array<string, mixed>  $filters
     * @return string Job ID
     */
    private function queueLargeExport(User $user, string $format, array $filters): string
    {
        $jobId = Str::uuid()->toString();

        ExportSubmissionsJob::dispatch($user, $format, $filters, $jobId);

        return $jobId;
    }

    /**
     * Get export file path
     */
    public function getExportPath(string $filename): string
    {
        return Storage::disk('local')->path("exports/{$filename}");
    }

    /**
     * Delete export file
     */
    public function deleteExport(string $filename): bool
    {
        return Storage::disk('local')->delete("exports/{$filename}");
    }

    /**
     * Clean up old exports (older than 7 days)
     *
     * @return int Number of files deleted
     */
    public function cleanupOldExports(): int
    {
        $files = Storage::disk('local')->files('exports');
        $deleted = 0;
        $cutoffDate = now()->subDays(7);

        foreach ($files as $file) {
            $lastModified = Storage::disk('local')->lastModified($file);

            if ($lastModified < $cutoffDate->timestamp) {
                Storage::disk('local')->delete($file);
                $deleted++;
            }
        }

        return $deleted;
    }
}

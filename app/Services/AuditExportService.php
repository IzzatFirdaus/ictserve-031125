<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Models\Audit;

/**
 * Audit Export Service
 *
 * Handles exporting audit trail records in multiple formats
 * with filtering support and file size limits.
 *
 * @version 1.0.0
 *
 * @since 2025-01-06
 *
 * @author ICTServe Development Team
 * @copyright 2025 MOTAC BPM
 *
 * Requirements: D03-FR-010 (Audit Export), D09 §9 (Audit Requirements)
 * Traceability: Phase 9.3 - Audit Export Service
 * WCAG 2.2 AA: Accessible export formats
 * Bilingual: MS (primary), EN (secondary)
 */
class AuditExportService
{
    /**
     * Maximum file size in bytes (50MB)
     */
    private const MAX_FILE_SIZE = 52428800;

    /**
     * Chunk size for large exports
     */
    private const CHUNK_SIZE = 1000;

    /**
     * Export directory
     */
    private const EXPORT_DIR = 'exports/audits';

    /**
     * Export audit records to CSV format
     *
     * @param  array<string, mixed>  $filters
     * @return string File path
     */
    public function exportToCSV(array $filters = []): string
    {
        $query = $this->buildQuery($filters);
        $filename = $this->generateFilename('csv');
        $filepath = self::EXPORT_DIR.'/'.$filename;

        $handle = fopen(Storage::path($filepath), 'w');

        // Write CSV header
        fputcsv($handle, [
            'ID',
            'Timestamp',
            'User',
            'Email',
            'Action',
            'Entity Type',
            'Entity ID',
            'IP Address',
            'URL',
            'User Agent',
            'Old Values',
            'New Values',
        ]);

        // Write data in chunks
        $query->chunk(self::CHUNK_SIZE, function (Collection $audits) use ($handle) {
            foreach ($audits as $audit) {
                fputcsv($handle, [
                    $audit->id,
                    $audit->created_at->format('Y-m-d H:i:s'),
                    $audit->user?->name ?? 'System',
                    $audit->user?->email ?? 'N/A',
                    $audit->event,
                    class_basename($audit->auditable_type),
                    $audit->auditable_id,
                    $audit->ip_address,
                    $audit->url,
                    $audit->user_agent,
                    json_encode($audit->old_values),
                    json_encode($audit->new_values),
                ]);
            }
        });

        fclose($handle);

        return $filepath;
    }

    /**
     * Export audit records to JSON format
     *
     * @param  array<string, mixed>  $filters
     * @return string File path
     */
    public function exportToJSON(array $filters = []): string
    {
        $query = $this->buildQuery($filters);
        $filename = $this->generateFilename('json');
        $filepath = self::EXPORT_DIR.'/'.$filename;

        $data = [
            'export_date' => Carbon::now()->toIso8601String(),
            'filters' => $filters,
            'total_records' => $query->count(),
            'records' => [],
        ];

        // Collect data in chunks
        $query->chunk(self::CHUNK_SIZE, function (Collection $audits) use (&$data) {
            foreach ($audits as $audit) {
                $data['records'][] = [
                    'id' => $audit->id,
                    'timestamp' => $audit->created_at->toIso8601String(),
                    'user' => [
                        'id' => $audit->user_id,
                        'name' => $audit->user?->name ?? 'System',
                        'email' => $audit->user?->email ?? 'N/A',
                    ],
                    'event' => $audit->event,
                    'entity' => [
                        'type' => $audit->auditable_type,
                        'id' => $audit->auditable_id,
                    ],
                    'request' => [
                        'ip_address' => $audit->ip_address,
                        'url' => $audit->url,
                        'user_agent' => $audit->user_agent,
                    ],
                    'changes' => [
                        'old_values' => $audit->old_values,
                        'new_values' => $audit->new_values,
                    ],
                    'tags' => $audit->tags,
                ];
            }
        });

        Storage::put($filepath, json_encode($data, JSON_PRETTY_PRINT));

        return $filepath;
    }

    /**
     * Export audit records to Excel format
     *
     * @param  array<string, mixed>  $filters
     * @return string File path
     */
    public function exportToExcel(array $filters = []): string
    {
        // For now, use CSV format as Excel-compatible
        // In production, use a library like PhpSpreadsheet
        $csvPath = $this->exportToCSV($filters);
        $excelPath = str_replace('.csv', '.xlsx', $csvPath);

        // Rename file to .xlsx extension
        Storage::move($csvPath, $excelPath);

        return $excelPath;
    }

    /**
     * Export audit records to PDF format
     *
     * @param  array<string, mixed>  $filters
     * @return string File path
     */
    public function exportToPDF(array $filters = []): string
    {
        $query = $this->buildQuery($filters);
        $filename = $this->generateFilename('pdf');
        $filepath = self::EXPORT_DIR.'/'.$filename;

        // Generate HTML content
        $html = $this->generatePDFHTML($query, $filters);

        // For now, save as HTML
        // In production, use a library like DomPDF or wkhtmltopdf
        Storage::put($filepath, $html);

        return $filepath;
    }

    /**
     * Build query with filters
     *
     * @param  array<string, mixed>  $filters
     */
    private function buildQuery(array $filters): Builder
    {
        $query = Audit::query()->with('user');

        // Date range filter
        if (! empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (! empty($filters['created_until'])) {
            $query->whereDate('created_at', '<=', $filters['created_until']);
        }

        // User filter
        if (! empty($filters['user_id'])) {
            $query->whereIn('user_id', (array) $filters['user_id']);
        }

        // Event filter
        if (! empty($filters['event'])) {
            $query->whereIn('event', (array) $filters['event']);
        }

        // Entity type filter
        if (! empty($filters['auditable_type'])) {
            $query->whereIn('auditable_type', (array) $filters['auditable_type']);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Generate filename with timestamp
     */
    private function generateFilename(string $extension): string
    {
        return 'audit_export_'.Carbon::now()->format('Y-m-d_His').'.'.$extension;
    }

    /**
     * Generate HTML content for PDF export
     *
     * @param  array<string, mixed>  $filters
     */
    private function generatePDFHTML(Builder $query, array $filters): string
    {
        $audits = $query->limit(1000)->get(); // Limit for PDF
        $totalRecords = $query->count();

        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Trail Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        h1 {
            color: #0056b3;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .metadata {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-left: 4px solid #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #0056b3;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 2px solid #0056b3;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>ICTServe Audit Trail Export</h1>
    
    <div class="metadata">
        <p><strong>Export Date:</strong> '.Carbon::now()->format('d/m/Y H:i:s').'</p>
        <p><strong>Total Records:</strong> '.$totalRecords.'</p>
        <p><strong>Exported By:</strong> '.(auth()->user()?->name ?? 'System').'</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Timestamp</th>
                <th>User</th>
                <th>Action</th>
                <th>Entity</th>
                <th>IP Address</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($audits as $audit) {
            $html .= '<tr>
                <td>'.$audit->id.'</td>
                <td>'.$audit->created_at->format('d/m/Y H:i:s').'</td>
                <td>'.($audit->user?->name ?? 'System').'</td>
                <td>'.ucfirst($audit->event).'</td>
                <td>'.class_basename($audit->auditable_type).' #'.$audit->auditable_id.'</td>
                <td>'.$audit->ip_address.'</td>
            </tr>';
        }

        $html .= '</tbody>
    </table>

    <div class="footer">
        <p>Generated by ICTServe Audit System</p>
        <p>MOTAC BPM - Ministry of Tourism, Arts & Culture Malaysia</p>
        <p>PDPA 2010 Compliant - 7-Year Retention Policy</p>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * Check if file size exceeds limit
     */
    public function exceedsFileSize(string $filepath): bool
    {
        return Storage::size($filepath) > self::MAX_FILE_SIZE;
    }

    /**
     * Get file size in human-readable format
     */
    public function getFileSize(string $filepath): string
    {
        $bytes = Storage::size($filepath);

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    /**
     * Delete export file
     */
    public function deleteExport(string $filepath): bool
    {
        return Storage::delete($filepath);
    }

    /**
     * Get export file URL
     */
    public function getExportUrl(string $filepath): string
    {
        return Storage::url($filepath);
    }

    /**
     * Clean up old exports
     *
     * @param  int  $days  Age threshold in days
     * @return int Number of files deleted
     */
    public function cleanupOldExports(int $days = 7): int
    {
        $files = Storage::files(self::EXPORT_DIR);
        $threshold = Carbon::now()->subDays($days)->timestamp;
        $deleted = 0;

        foreach ($files as $file) {
            if (Storage::lastModified($file) < $threshold) {
                Storage::delete($file);
                $deleted++;
            }
        }

        return $deleted;
    }
}

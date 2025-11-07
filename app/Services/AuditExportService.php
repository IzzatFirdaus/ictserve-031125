<?php

declare(strict_types=1);

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Models\Audit;

/**
 * Audit Export Service
 *
 * Handles export of audit logs in various formats with filtering capabilities.
 * Supports CSV, PDF, Excel, and JSON formats with WCAG 2.2 AA compliance.
 *
 * Requirements: 9.3, 9.4
 *
 * @see D03-FR-007.2 Audit log export
 * @see D09 §9 Audit requirements
 */
class AuditExportService
{
    private const MAX_EXPORT_SIZE = 50 * 1024 * 1024; // 50MB limit

    private const EXPORT_DIRECTORY = 'exports';

    /**
     * Export audit logs with filtering
     */
    public function exportAuditLogs(
        string $format,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        array $eventTypes = [],
        ?int $userId = null,
        ?string $entityType = null
    ): string {
        $query = Audit::query()->with(['user']);

        // Apply filters
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if (! empty($eventTypes)) {
            $query->whereIn('event', $eventTypes);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($entityType) {
            $query->where('auditable_type', $entityType);
        }

        $audits = $query->orderBy('created_at', 'desc')->get();

        // Check size limit
        $estimatedSize = $this->estimateExportSize($audits, $format);
        if ($estimatedSize > self::MAX_EXPORT_SIZE) {
            throw new \Exception('Export size exceeds 50MB limit. Please narrow your date range or add more filters.');
        }

        $filename = $this->generateFilename($format, $dateFrom, $dateTo);

        return match ($format) {
            'csv' => $this->exportToCsv($audits, $filename),
            'pdf' => $this->exportToPdf($audits, $filename, $dateFrom, $dateTo),
            'excel' => $this->exportToExcel($audits, $filename),
            'json' => $this->exportToJson($audits, $filename),
            default => throw new \InvalidArgumentException("Unsupported format: {$format}"),
        };
    }

    /**
     * Export single audit record
     */
    public function exportSingleAuditRecord(Audit $audit, string $format): string
    {
        $filename = "audit_record_{$audit->id}_".now()->format('Y-m-d_H-i-s').".{$format}";

        return match ($format) {
            'pdf' => $this->exportSingleRecordToPdf($audit, $filename),
            'json' => $this->exportSingleRecordToJson($audit, $filename),
            default => throw new \InvalidArgumentException("Unsupported format for single record: {$format}"),
        };
    }

    /**
     * Export to CSV format
     */
    private function exportToCsv(Collection $audits, string $filename): string
    {
        $csvContent = $this->generateCsvContent($audits);

        Storage::put(self::EXPORT_DIRECTORY.'/'.$filename, $csvContent);

        return $filename;
    }

    /**
     * Export to PDF format
     */
    private function exportToPdf(Collection $audits, string $filename, ?string $dateFrom, ?string $dateTo): string
    {
        $data = [
            'audits' => $audits,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'generatedAt' => now()->format('d/m/Y H:i:s'),
            'totalRecords' => $audits->count(),
        ];

        $pdf = Pdf::loadView('exports.audit-logs-pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial',
            ]);

        Storage::put(self::EXPORT_DIRECTORY.'/'.$filename, $pdf->output());

        return $filename;
    }

    /**
     * Export to Excel format
     */
    private function exportToExcel(Collection $audits, string $filename): string
    {
        // For now, export as CSV with .xlsx extension
        // In production, use Laravel Excel package
        $csvContent = $this->generateCsvContent($audits);

        Storage::put(self::EXPORT_DIRECTORY.'/'.$filename, $csvContent);

        return $filename;
    }

    /**
     * Export to JSON format
     */
    private function exportToJson(Collection $audits, string $filename): string
    {
        $data = [
            'metadata' => [
                'generated_at' => now()->toISOString(),
                'total_records' => $audits->count(),
                'export_format' => 'json',
                'compliance' => 'PDPA 2010 - 7 year retention',
            ],
            'audits' => $audits->map(function ($audit) {
                return [
                    'id' => $audit->id,
                    'timestamp' => $audit->created_at->toISOString(),
                    'user_id' => $audit->user_id,
                    'user_name' => $audit->user?->name,
                    'action' => $audit->event,
                    'entity_type' => class_basename($audit->auditable_type),
                    'entity_id' => $audit->auditable_id,
                    'ip_address' => $audit->ip_address,
                    'user_agent' => $audit->user_agent,
                    'old_values' => $audit->old_values,
                    'new_values' => $audit->new_values,
                ];
            }),
        ];

        Storage::put(self::EXPORT_DIRECTORY.'/'.$filename, json_encode($data, JSON_PRETTY_PRINT));

        return $filename;
    }

    /**
     * Export single record to PDF
     */
    private function exportSingleRecordToPdf(Audit $audit, string $filename): string
    {
        $data = [
            'audit' => $audit,
            'generatedAt' => now()->format('d/m/Y H:i:s'),
        ];

        $pdf = Pdf::loadView('exports.single-audit-record-pdf', $data)
            ->setPaper('a4', 'portrait');

        Storage::put(self::EXPORT_DIRECTORY.'/'.$filename, $pdf->output());

        return $filename;
    }

    /**
     * Export single record to JSON
     */
    private function exportSingleRecordToJson(Audit $audit, string $filename): string
    {
        $data = [
            'metadata' => [
                'generated_at' => now()->toISOString(),
                'record_id' => $audit->id,
                'export_format' => 'json',
            ],
            'audit' => [
                'id' => $audit->id,
                'timestamp' => $audit->created_at->toISOString(),
                'user_id' => $audit->user_id,
                'user_name' => $audit->user?->name,
                'user_email' => $audit->user?->email,
                'action' => $audit->event,
                'entity_type' => $audit->auditable_type,
                'entity_id' => $audit->auditable_id,
                'ip_address' => $audit->ip_address,
                'user_agent' => $audit->user_agent,
                'old_values' => $audit->old_values,
                'new_values' => $audit->new_values,
                'url' => $audit->url,
                'tags' => $audit->tags,
            ],
        ];

        Storage::put(self::EXPORT_DIRECTORY.'/'.$filename, json_encode($data, JSON_PRETTY_PRINT));

        return $filename;
    }

    /**
     * Generate CSV content
     */
    private function generateCsvContent(Collection $audits): string
    {
        $headers = [
            'ID',
            'Timestamp',
            'User ID',
            'User Name',
            'Action',
            'Entity Type',
            'Entity ID',
            'IP Address',
            'User Agent',
            'Old Values',
            'New Values',
        ];

        $csv = implode(',', array_map(fn ($header) => '"'.$header.'"', $headers))."\n";

        foreach ($audits as $audit) {
            $row = [
                $audit->id,
                $audit->created_at->format('d/m/Y H:i:s'),
                $audit->user_id ?? '',
                $audit->user?->name ?? 'System',
                $audit->event,
                class_basename($audit->auditable_type),
                $audit->auditable_id ?? '',
                $audit->ip_address ?? '',
                $audit->user_agent ?? '',
                json_encode($audit->old_values ?? []),
                json_encode($audit->new_values ?? []),
            ];

            $csv .= implode(',', array_map(fn ($field) => '"'.str_replace('"', '""', (string) $field).'"', $row))."\n";
        }

        return $csv;
    }

    /**
     * Estimate export file size
     */
    private function estimateExportSize(Collection $audits, string $format): int
    {
        if ($audits->isEmpty()) {
            return 0;
        }

        // Estimate based on average record size
        $sampleRecord = $audits->first();
        $estimatedRecordSize = strlen(json_encode($sampleRecord->toArray()));

        $multiplier = match ($format) {
            'csv' => 0.7,
            'pdf' => 2.0,
            'excel' => 1.2,
            'json' => 1.0,
            default => 1.0,
        };

        return (int) ($estimatedRecordSize * $audits->count() * $multiplier);
    }

    /**
     * Generate filename for export
     */
    private function generateFilename(string $format, ?string $dateFrom, ?string $dateTo): string
    {
        $dateRange = '';
        if ($dateFrom && $dateTo) {
            $dateRange = '_'.Carbon::parse($dateFrom)->format('Y-m-d').'_to_'.Carbon::parse($dateTo)->format('Y-m-d');
        } elseif ($dateFrom) {
            $dateRange = '_from_'.Carbon::parse($dateFrom)->format('Y-m-d');
        } elseif ($dateTo) {
            $dateRange = '_until_'.Carbon::parse($dateTo)->format('Y-m-d');
        }

        return 'audit_logs'.$dateRange.'_'.now()->format('Y-m-d_H-i-s').'.'.$format;
    }
}

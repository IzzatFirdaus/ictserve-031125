<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Report Export Service
 *
 * Handles export of reports in multiple formats (CSV, PDF, Excel).
 * Provides proper formatting, metadata, and file size management.
 *
 * Requirements: 13.5, 4.5, 6.1, 7.2
 */
class ReportExportService
{
    private const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50MB

    /**
     * Generate report files in multiple formats
     *
     * @param  array<string, mixed>  $reportData
     * @param  array<string, mixed>  $options
     * @return array<string, string>
     */
    public function generateReportFiles(array $reportData, array $options = []): array
    {
        $formats = is_array($options['formats'] ?? null) ? $options['formats'] : ['pdf'];
        $recipient = is_object($options['recipient'] ?? null) ? $options['recipient'] : null;
        $files = [];

        foreach ($formats as $format) {
            if (!is_string($format)) {
                continue;
            }
            $filename = $this->generateFilename($reportData, $format, $recipient);

            try {
                $content = match ($format) {
                    'csv' => $this->generateCSV($reportData),
                    'pdf' => $this->generatePDF($reportData),
                    'excel' => $this->generateExcel($reportData),
                    default => throw new \InvalidArgumentException("Unsupported format: {$format}"),
                };

                // Check file size limit
                if (strlen($content) > self::MAX_FILE_SIZE) {
                    $content = $this->compressContent($content, $format);
                }

                $filepath = "reports/temp/{$filename}";
                Storage::put($filepath, $content);

                $files[$format] = $filepath;

            } catch (\Exception $e) {
                Log::error("Failed to generate {$format} report", [
                    'error' => $e->getMessage(),
                    'report_data' => array_keys($reportData),
                ]);
            }
        }

        return $files;
    }

    /**
     * Export unified analytics data to CSV
     *
     * @param  array<string, mixed>  $data
     */
    public function exportUnifiedAnalyticsCSV(array $data): string
    {
        $csv = $this->createCSVHeader();

        // Executive Summary
        $csv .= "\n\"RINGKASAN EKSEKUTIF\"\n";

        $execSummary = is_array($data['executive_summary'] ?? null) ? $data['executive_summary'] : [];
        $systemHealth = is_array($execSummary['system_health'] ?? null) ? $execSummary['system_health'] : [];
        $keyMetrics = is_array($execSummary['key_metrics'] ?? null) ? $execSummary['key_metrics'] : [];

        $csv .= $this->arrayToCSV([
            'Skor Kesihatan Sistem' => ($systemHealth['score'] ?? '0').'%',
            'Status' => (string) ($systemHealth['status'] ?? 'Unknown'),
            'Jumlah Tiket' => (string) ($keyMetrics['total_tickets'] ?? 0),
            'Kadar Penyelesaian Tiket' => ($keyMetrics['ticket_resolution_rate'] ?? '0').'%',
            'Jumlah Permohonan Pinjaman' => (string) ($keyMetrics['total_loan_applications'] ?? 0),
            'Kadar Kelulusan Pinjaman' => ($keyMetrics['loan_approval_rate'] ?? '0').'%',
            'Kadar Penggunaan Aset' => ($keyMetrics['asset_utilization_rate'] ?? '0').'%',
        ]);

        // Helpdesk Metrics
        $csv .= "\n\n\"METRIK HELPDESK\"\n";
        $unifiedMetrics = is_array($data['unified_metrics'] ?? null) ? $data['unified_metrics'] : [];
        $helpdesk = is_array($unifiedMetrics['helpdesk'] ?? null) ? $unifiedMetrics['helpdesk'] : [];
        $csv .= $this->arrayToCSV([
            'Jumlah Tiket' => (string) ($helpdesk['total_tickets'] ?? 0),
            'Tiket Diselesaikan' => (string) ($helpdesk['resolved_tickets'] ?? 0),
            'Tiket Tertunda' => (string) ($helpdesk['pending_tickets'] ?? 0),
            'Tiket Tertunggak' => (string) ($helpdesk['overdue_tickets'] ?? 0),
            'Kadar Penyelesaian' => ($helpdesk['resolution_rate'] ?? '0').'%',
            'Purata Masa Penyelesaian (Jam)' => (string) ($helpdesk['avg_resolution_hours'] ?? 0),
        ]);

        // Loan Metrics
        $csv .= "\n\n\"METRIK PINJAMAN\"\n";
        $loans = is_array($unifiedMetrics['loans'] ?? null) ? $unifiedMetrics['loans'] : [];
        $csv .= $this->arrayToCSV([
            'Jumlah Permohonan' => (string) ($loans['total_applications'] ?? 0),
            'Permohonan Diluluskan' => (string) ($loans['approved_applications'] ?? 0),
            'Pinjaman Aktif' => (string) ($loans['active_loans'] ?? 0),
            'Pinjaman Tertunggak' => (string) ($loans['overdue_loans'] ?? 0),
            'Menunggu Kelulusan' => (string) ($loans['pending_approval'] ?? 0),
            'Kadar Kelulusan' => ($loans['approval_rate'] ?? '0').'%',
            'Jumlah Nilai Pinjaman (RM)' => number_format((float) ($loans['total_loan_value'] ?? 0), 2),
        ]);

        // Asset Metrics
        $csv .= "\n\n\"METRIK ASET\"\n";
        $assets = is_array($unifiedMetrics['assets'] ?? null) ? $unifiedMetrics['assets'] : [];
        $csv .= $this->arrayToCSV([
            'Jumlah Aset' => (string) ($assets['total_assets'] ?? 0),
            'Aset Tersedia' => (string) ($assets['available_assets'] ?? 0),
            'Aset Dipinjam' => (string) ($assets['loaned_assets'] ?? 0),
            'Aset Penyelenggaraan' => (string) ($assets['maintenance_assets'] ?? 0),
            'Aset Bersara' => (string) ($assets['retired_assets'] ?? 0),
            'Kadar Penggunaan' => ($assets['utilization_rate'] ?? '0').'%',
            'Kadar Ketersediaan' => ($assets['availability_rate'] ?? '0').'%',
        ]);

        return $csv;
    }

    /**
     * Generate CSV format
     *
     * @param  array<string, mixed>  $reportData
     */
    private function generateCSV(array $reportData): string
    {
        return $this->exportUnifiedAnalyticsCSV($reportData);
    }

    /**
     * Generate PDF format
     *
     * @param  array<string, mixed>  $reportData
     */
    private function generatePDF(array $reportData): string
    {
        // For now, return a simple text-based PDF content
        // In a real implementation, you would use a PDF library like DomPDF or TCPDF

        $content = $this->generateTextReport($reportData);

        // Placeholder for PDF generation
        // This would typically use a PDF library
        return "PDF Content:\n\n".$content;
    }

    /**
     * Generate Excel format
     *
     * @param  array<string, mixed>  $reportData
     */
    private function generateExcel(array $reportData): string
    {
        // For now, return CSV format which Excel can open
        // In a real implementation, you would use PhpSpreadsheet

        return $this->generateCSV($reportData);
    }

    /**
     * Generate text-based report
     *
     * @param  array<string, mixed>  $reportData
     */
    private function generateTextReport(array $reportData): string
    {
        $report = [];

        // Header
        $reportInfo = is_array($reportData['report_info'] ?? null) ? $reportData['report_info'] : [];
        $period = is_array($reportInfo['period'] ?? null) ? $reportInfo['period'] : [];

        $report[] = "=== ".($reportInfo['title'] ?? 'Report')." ===";
        $report[] = "Tempoh: ".($period['start'] ?? 'N/A')." hingga ".($period['end'] ?? 'N/A');
        $report[] = "Dijana pada: ".($reportInfo['generated_at'] ?? now()->format('Y-m-d H:i:s'));
        $report[] = "Dijana oleh: ".($reportInfo['generated_by'] ?? 'System');
        $report[] = '';

        // Executive Summary
        $summary = is_array($reportData['executive_summary'] ?? null) ? $reportData['executive_summary'] : [];
        $systemHealth = is_array($summary['system_health'] ?? null) ? $summary['system_health'] : [];

        $report[] = 'RINGKASAN EKSEKUTIF';
        $report[] = '==================';
        $report[] = "Skor Kesihatan Sistem: ".($systemHealth['score'] ?? '0')."% (".($systemHealth['status'] ?? 'Unknown').")";
        $report[] = (string) ($systemHealth['description'] ?? '');
        $report[] = '';

        // Key Metrics
        $report[] = 'METRIK UTAMA';
        $report[] = '============';
        $metrics = is_array($summary['key_metrics'] ?? null) ? $summary['key_metrics'] : [];
        $report[] = "• Jumlah Tiket: ".($metrics['total_tickets'] ?? 0);
        $report[] = "• Kadar Penyelesaian Tiket: ".($metrics['ticket_resolution_rate'] ?? 0)."%";
        $report[] = "• Jumlah Permohonan Pinjaman: ".($metrics['total_loan_applications'] ?? 0);
        $report[] = "• Kadar Kelulusan Pinjaman: ".($metrics['loan_approval_rate'] ?? 0)."%";
        $report[] = "• Kadar Penggunaan Aset: ".($metrics['asset_utilization_rate'] ?? 0)."%";
        $report[] = '';

        // Critical Issues
        $issues = is_array($summary['critical_issues'] ?? null) ? $summary['critical_issues'] : [];
        $overdueTickets = (int) ($issues['overdue_tickets'] ?? 0);
        $overdueLoans = (int) ($issues['overdue_loans'] ?? 0);
        $maintenanceAssets = (int) ($issues['maintenance_assets'] ?? 0);

        if ($overdueTickets > 0 || $overdueLoans > 0 || $maintenanceAssets > 0) {
            $report[] = 'ISU KRITIKAL';
            $report[] = '=============';
            if ($overdueTickets > 0) {
                $report[] = "• Tiket Tertunggak: {$overdueTickets}";
            }
            if ($overdueLoans > 0) {
                $report[] = "• Pinjaman Tertunggak: {$overdueLoans}";
            }
            if ($maintenanceAssets > 0) {
                $report[] = "• Aset Perlu Penyelenggaraan: {$maintenanceAssets}";
            }
            $report[] = '';
        }

        // Recommendations
        $recommendations = is_array($reportData['recommendations'] ?? null) ? $reportData['recommendations'] : [];
        if (! empty($recommendations)) {
            $report[] = 'CADANGAN';
            $report[] = '=========';
            foreach ($recommendations as $rec) {
                if (!is_array($rec)) {
                    continue;
                }
                $report[] = "• ".($rec['title'] ?? 'Recommendation')." (Keutamaan: ".($rec['priority'] ?? 'Medium').")";
                $report[] = "  ".($rec['description'] ?? '');
                $actions = is_array($rec['actions'] ?? null) ? $rec['actions'] : [];
                foreach ($actions as $action) {
                    $report[] = "  - ".(string) $action;
                }
                $report[] = '';
            }
        }

        return implode("\n", $report);
    }

    /**
     * Create CSV header with metadata
     */
    private function createCSVHeader(): string
    {
        return '"Laporan ICTServe - Dijana pada '.now()->format('Y-m-d H:i:s')."\"\n".
               "\"Sistem Pengurusan Perkhidmatan ICT MOTAC\"\n".
               "\"Mematuhi WCAG 2.2 AA dan Standard MyGOV\"\n";
    }

    /**
     * Convert array to CSV format
     *
     * @param  array<string, mixed>  $data
     */
    private function arrayToCSV(array $data): string
    {
        $csv = '';
        foreach ($data as $key => $value) {
            $csv .= '"'.str_replace('"', '""', $key).'","'.str_replace('"', '""', $value)."\"\n";
        }

        return $csv;
    }

    /**
     * Generate filename with proper metadata
     *
     * @param  array<string, mixed>  $reportData
     * @param  object|null  $recipient
     */
    private function generateFilename(array $reportData, string $format, ?object $recipient = null): string
    {
        $info = is_array($reportData['report_info'] ?? null) ? $reportData['report_info'] : [];
        $timestamp = now()->format('Ymd_His');

        $filename = "ICTServe_Report_".($info['frequency'] ?? 'adhoc')."_{$timestamp}";

        if ($recipient && property_exists($recipient, 'name')) {
            $filename .= '_'.Str::slug((string) $recipient->name);
        }

        $extension = match ($format) {
            'csv' => 'csv',
            'pdf' => 'pdf',
            'excel' => 'xlsx',
            default => 'txt',
        };

        return "{$filename}.{$extension}";
    }

    /**
     * Compress content if it exceeds size limit
     */
    private function compressContent(string $content, string $format): string
    {
        if ($format === 'csv') {
            // For CSV, we can remove some detailed data
            $lines = explode("\n", $content);
            $compressed = array_slice($lines, 0, min(1000, count($lines))); // Limit to 1000 lines
            $compressed[] = "\n\"... Data dipotong kerana had saiz fail 50MB ...\"";

            return implode("\n", $compressed);
        }

        // For other formats, use gzip compression if available
        if (function_exists('gzencode')) {
            $result = gzencode($content, 9);
            if ($result === false) {
                return substr($content, 0, self::MAX_FILE_SIZE - 1000)."\n\n... Content truncated due to size limit ...";
            }
            return $result;
        }

        return substr($content, 0, self::MAX_FILE_SIZE - 1000)."\n\n... Content truncated due to size limit ...";
    }
}

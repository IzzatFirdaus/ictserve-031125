<?php

declare(strict_types=1);

namespace App\Services;

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
     */
    public function generateReportFiles(array $reportData, array $options = []): array
    {
        $formats = $options['formats'] ?? ['pdf'];
        $recipient = $options['recipient'] ?? null;
        $files = [];

        foreach ($formats as $format) {
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
                \Log::error("Failed to generate {$format} report", [
                    'error' => $e->getMessage(),
                    'report_data' => array_keys($reportData),
                ]);
            }
        }

        return $files;
    }

    /**
     * Export unified analytics data to CSV
     */
    public function exportUnifiedAnalyticsCSV(array $data): string
    {
        $csv = $this->createCSVHeader();

        // Executive Summary
        $csv .= "\n\"RINGKASAN EKSEKUTIF\"\n";
        $csv .= $this->arrayToCSV([
            'Skor Kesihatan Sistem' => $data['executive_summary']['system_health']['score'].'%',
            'Status' => $data['executive_summary']['system_health']['status'],
            'Jumlah Tiket' => $data['executive_summary']['key_metrics']['total_tickets'],
            'Kadar Penyelesaian Tiket' => $data['executive_summary']['key_metrics']['ticket_resolution_rate'].'%',
            'Jumlah Permohonan Pinjaman' => $data['executive_summary']['key_metrics']['total_loan_applications'],
            'Kadar Kelulusan Pinjaman' => $data['executive_summary']['key_metrics']['loan_approval_rate'].'%',
            'Kadar Penggunaan Aset' => $data['executive_summary']['key_metrics']['asset_utilization_rate'].'%',
        ]);

        // Helpdesk Metrics
        $csv .= "\n\n\"METRIK HELPDESK\"\n";
        $helpdesk = $data['unified_metrics']['helpdesk'];
        $csv .= $this->arrayToCSV([
            'Jumlah Tiket' => $helpdesk['total_tickets'],
            'Tiket Diselesaikan' => $helpdesk['resolved_tickets'],
            'Tiket Tertunda' => $helpdesk['pending_tickets'],
            'Tiket Tertunggak' => $helpdesk['overdue_tickets'],
            'Kadar Penyelesaian' => $helpdesk['resolution_rate'].'%',
            'Purata Masa Penyelesaian (Jam)' => $helpdesk['avg_resolution_hours'],
        ]);

        // Loan Metrics
        $csv .= "\n\n\"METRIK PINJAMAN\"\n";
        $loans = $data['unified_metrics']['loans'];
        $csv .= $this->arrayToCSV([
            'Jumlah Permohonan' => $loans['total_applications'],
            'Permohonan Diluluskan' => $loans['approved_applications'],
            'Pinjaman Aktif' => $loans['active_loans'],
            'Pinjaman Tertunggak' => $loans['overdue_loans'],
            'Menunggu Kelulusan' => $loans['pending_approval'],
            'Kadar Kelulusan' => $loans['approval_rate'].'%',
            'Jumlah Nilai Pinjaman (RM)' => number_format($loans['total_loan_value'], 2),
        ]);

        // Asset Metrics
        $csv .= "\n\n\"METRIK ASET\"\n";
        $assets = $data['unified_metrics']['assets'];
        $csv .= $this->arrayToCSV([
            'Jumlah Aset' => $assets['total_assets'],
            'Aset Tersedia' => $assets['available_assets'],
            'Aset Dipinjam' => $assets['loaned_assets'],
            'Aset Penyelenggaraan' => $assets['maintenance_assets'],
            'Aset Bersara' => $assets['retired_assets'],
            'Kadar Penggunaan' => $assets['utilization_rate'].'%',
            'Kadar Ketersediaan' => $assets['availability_rate'].'%',
        ]);

        return $csv;
    }

    /**
     * Generate CSV format
     */
    private function generateCSV(array $reportData): string
    {
        return $this->exportUnifiedAnalyticsCSV($reportData);
    }

    /**
     * Generate PDF format
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
     */
    private function generateExcel(array $reportData): string
    {
        // For now, return CSV format which Excel can open
        // In a real implementation, you would use PhpSpreadsheet

        return $this->generateCSV($reportData);
    }

    /**
     * Generate text-based report
     */
    private function generateTextReport(array $reportData): string
    {
        $report = [];

        // Header
        $report[] = "=== {$reportData['report_info']['title']} ===";
        $report[] = "Tempoh: {$reportData['report_info']['period']['start']} hingga {$reportData['report_info']['period']['end']}";
        $report[] = "Dijana pada: {$reportData['report_info']['generated_at']}";
        $report[] = "Dijana oleh: {$reportData['report_info']['generated_by']}";
        $report[] = '';

        // Executive Summary
        $summary = $reportData['executive_summary'];
        $report[] = 'RINGKASAN EKSEKUTIF';
        $report[] = '==================';
        $report[] = "Skor Kesihatan Sistem: {$summary['system_health']['score']}% ({$summary['system_health']['status']})";
        $report[] = $summary['system_health']['description'];
        $report[] = '';

        // Key Metrics
        $report[] = 'METRIK UTAMA';
        $report[] = '============';
        $metrics = $summary['key_metrics'];
        $report[] = "• Jumlah Tiket: {$metrics['total_tickets']}";
        $report[] = "• Kadar Penyelesaian Tiket: {$metrics['ticket_resolution_rate']}%";
        $report[] = "• Jumlah Permohonan Pinjaman: {$metrics['total_loan_applications']}";
        $report[] = "• Kadar Kelulusan Pinjaman: {$metrics['loan_approval_rate']}%";
        $report[] = "• Kadar Penggunaan Aset: {$metrics['asset_utilization_rate']}%";
        $report[] = '';

        // Critical Issues
        $issues = $summary['critical_issues'];
        if ($issues['overdue_tickets'] > 0 || $issues['overdue_loans'] > 0 || $issues['maintenance_assets'] > 0) {
            $report[] = 'ISU KRITIKAL';
            $report[] = '=============';
            if ($issues['overdue_tickets'] > 0) {
                $report[] = "• Tiket Tertunggak: {$issues['overdue_tickets']}";
            }
            if ($issues['overdue_loans'] > 0) {
                $report[] = "• Pinjaman Tertunggak: {$issues['overdue_loans']}";
            }
            if ($issues['maintenance_assets'] > 0) {
                $report[] = "• Aset Perlu Penyelenggaraan: {$issues['maintenance_assets']}";
            }
            $report[] = '';
        }

        // Recommendations
        if (! empty($reportData['recommendations'])) {
            $report[] = 'CADANGAN';
            $report[] = '=========';
            foreach ($reportData['recommendations'] as $rec) {
                $report[] = "• {$rec['title']} (Keutamaan: {$rec['priority']})";
                $report[] = "  {$rec['description']}";
                foreach ($rec['actions'] as $action) {
                    $report[] = "  - {$action}";
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
     */
    private function generateFilename(array $reportData, string $format, $recipient = null): string
    {
        $info = $reportData['report_info'];
        $timestamp = now()->format('Ymd_His');

        $filename = "ICTServe_Report_{$info['frequency']}_{$timestamp}";

        if ($recipient) {
            $filename .= '_'.Str::slug($recipient->name ?? 'user');
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
            return gzencode($content, 9);
        }

        return substr($content, 0, self::MAX_FILE_SIZE - 1000)."\n\n... Content truncated due to size limit ...";
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * Data Export Service
 *
 * Handles data export functionality with proper column headers,
 * accessible table structure, metadata inclusion, and 50MB file size limit.
 *
 * @trace Requirements 8.3
 */
class DataExportService
{
    private const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50MB

    /**
     * Export data to specified format with proper headers and metadata
     */
    public function exportData(Collection $data, string $format, array $metadata = []): array
    {
        if ($data->isEmpty()) {
            throw new \Exception('No data available for export');
        }

        $content = match ($format) {
            'csv' => $this->exportToCsv($data, $metadata),
            'excel' => $this->exportToExcel($data, $metadata),
            'pdf' => $this->exportToPdf($data, $metadata),
            default => throw new \InvalidArgumentException("Unsupported format: {$format}"),
        };

        // Check file size limit
        $sizeInBytes = strlen($content);
        if ($sizeInBytes > self::MAX_FILE_SIZE) {
            throw new \Exception('Export file size exceeds 50MB limit. Please apply more specific filters.');
        }

        return [
            'content' => $content,
            'size' => $sizeInBytes,
            'formatted_size' => $this->formatFileSize($sizeInBytes),
            'metadata' => array_merge($metadata, [
                'export_format' => $format,
                'total_records' => $data->count(),
                'generated_at' => now()->toISOString(),
            ]),
        ];
    }

    /**
     * Export data to CSV format with proper column headers
     */
    private function exportToCsv(Collection $data, array $metadata): string
    {
        $output = fopen('php://temp', 'r+');

        // Add metadata header
        fputcsv($output, ['# ICTServe Data Export']);
        fputcsv($output, ['# Generated at: '.now()->format('d/m/Y H:i:s')]);
        fputcsv($output, ['# Total records: '.number_format($data->count())]);

        if (isset($metadata['date_range'])) {
            fputcsv($output, ['# Date range: '.$metadata['date_range']]);
        }

        if (isset($metadata['filters_applied']) && ! empty($metadata['filters_applied'])) {
            fputcsv($output, ['# Filters applied: '.implode(', ', $metadata['filters_applied'])]);
        }

        fputcsv($output, []); // Empty line separator

        // Add column headers with proper formatting
        $headers = array_keys($data->first());
        $formattedHeaders = array_map(function ($header) {
            return ucfirst(str_replace('_', ' ', $header));
        }, $headers);
        fputcsv($output, $formattedHeaders);

        // Add data rows
        foreach ($data as $row) {
            $formattedRow = array_map(function ($value) {
                // Handle null values and format data appropriately
                if ($value === null) {
                    return '';
                }
                if (is_bool($value)) {
                    return $value ? 'Yes' : 'No';
                }

                return (string) $value;
            }, array_values($row));

            fputcsv($output, $formattedRow);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Export data to Excel format (placeholder - would use PhpSpreadsheet in production)
     */
    private function exportToExcel(Collection $data, array $metadata): string
    {
        // For now, return enhanced CSV format
        // In production, this would use PhpSpreadsheet library
        return $this->exportToCsv($data, $metadata);
    }

    /**
     * Export data to PDF format with accessible table structure
     */
    private function exportToPdf(Collection $data, array $metadata): string
    {
        $html = $this->generateAccessiblePdfHtml($data, $metadata);

        // For now, return HTML. In production, this would use TCPDF, DomPDF, or similar
        return $html;
    }

    /**
     * Generate WCAG 2.2 AA compliant HTML for PDF export
     */
    private function generateAccessiblePdfHtml(Collection $data, array $metadata): string
    {
        $headers = array_keys($data->first());
        $formattedHeaders = array_map(function ($header) {
            return ucfirst(str_replace('_', ' ', $header));
        }, $headers);

        $generatedAt = now()->format('d/m/Y H:i:s');
        $totalRecords = number_format($data->count());

        $html = '<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data ICTServe</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 20px;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #0056b3;
        }
        .header h1 {
            color: #0056b3;
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header .subtitle {
            color: #666;
            font-size: 16px;
            margin: 0;
        }
        .metadata {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .metadata h2 {
            color: #0056b3;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .metadata-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .metadata-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .metadata-label {
            font-weight: 600;
            color: #495057;
        }
        .metadata-value {
            color: #212529;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px;
        }
        .data-table th,
        .data-table td {
            border: 1px solid #dee2e6;
            padding: 8px 6px;
            text-align: left;
            vertical-align: top;
        }
        .data-table th {
            background-color: #0056b3;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
        }
        .data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .data-table tbody tr:hover {
            background-color: #e3f2fd;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .footer p {
            margin: 5px 0;
        }
        @media print {
            body { margin: 0; }
            .header { page-break-after: avoid; }
            .data-table { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Data ICTServe</h1>
        <p class="subtitle">Sistem Pengurusan ICT BPM MOTAC</p>
    </div>
    
    <div class="metadata">
        <h2>Maklumat Laporan</h2>
        <div class="metadata-grid">
            <div class="metadata-item">
                <span class="metadata-label">Dijana pada:</span>
                <span class="metadata-value">'.$generatedAt.'</span>
            </div>
            <div class="metadata-item">
                <span class="metadata-label">Jumlah rekod:</span>
                <span class="metadata-value">'.$totalRecords.'</span>
            </div>';

        if (isset($metadata['date_range'])) {
            $html .= '
            <div class="metadata-item">
                <span class="metadata-label">Julat tarikh:</span>
                <span class="metadata-value">'.htmlspecialchars($metadata['date_range']).'</span>
            </div>';
        }

        if (isset($metadata['filters_applied']) && ! empty($metadata['filters_applied'])) {
            $html .= '
            <div class="metadata-item">
                <span class="metadata-label">Penapis digunakan:</span>
                <span class="metadata-value">'.htmlspecialchars(implode(', ', $metadata['filters_applied'])).'</span>
            </div>';
        }

        $html .= '
        </div>
    </div>
    
    <table class="data-table" role="table" aria-label="Data export table">
        <thead>
            <tr>';

        foreach ($formattedHeaders as $header) {
            $html .= '<th scope="col">'.htmlspecialchars($header).'</th>';
        }

        $html .= '</tr>
        </thead>
        <tbody>';

        foreach ($data as $rowIndex => $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $formattedCell = $cell;
                if ($cell === null) {
                    $formattedCell = '-';
                } elseif (is_bool($cell)) {
                    $formattedCell = $cell ? 'Ya' : 'Tidak';
                }
                $html .= '<td>'.htmlspecialchars((string) $formattedCell).'</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody>
    </table>
    
    <div class="footer">
        <p><strong>Sistem ICTServe - BPM MOTAC</strong></p>
        <p>Bahagian Pengurusan Maklumat</p>
        <p>Laporan ini dijana secara automatik pada '.$generatedAt.'</p>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * Format file size in human readable format
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2).' '.$units[$pow];
    }

    /**
     * Validate export parameters
     */
    public function validateExportParams(array $params): array
    {
        $errors = [];

        if (empty($params['format']) || ! in_array($params['format'], ['csv', 'excel', 'pdf'])) {
            $errors[] = 'Invalid or missing export format';
        }

        if (empty($params['data']) || ! ($params['data'] instanceof Collection)) {
            $errors[] = 'Invalid or missing data for export';
        }

        return $errors;
    }

    /**
     * Get supported export formats
     */
    public function getSupportedFormats(): array
    {
        return [
            'csv' => [
                'name' => 'CSV (Comma Separated Values)',
                'extension' => 'csv',
                'mime_type' => 'text/csv',
                'description' => 'Suitable for spreadsheet applications',
            ],
            'excel' => [
                'name' => 'Excel Spreadsheet',
                'extension' => 'xlsx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'description' => 'Microsoft Excel format with formatting',
            ],
            'pdf' => [
                'name' => 'PDF Document',
                'extension' => 'pdf',
                'mime_type' => 'application/pdf',
                'description' => 'Portable document format for viewing and printing',
            ],
        ];
    }
}

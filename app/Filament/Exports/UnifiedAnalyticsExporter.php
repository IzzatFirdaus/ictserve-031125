<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Services\UnifiedAnalyticsService;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

/**
 * Unified Analytics Exporter
 *
 * Exports comprehensive analytics data in multiple formats.
 * Provides proper column headers and accessible table structure.
 *
 * Requirements: 13.5, 4.5, 6.1, 7.2
 */
class UnifiedAnalyticsExporter extends Exporter
{
    protected static ?string $model = Export::class;

    public static function getColumns(): array
    {
        return [
            // Report Metadata
            ExportColumn::make('report_type')
                ->label('Jenis Laporan'),
            ExportColumn::make('generated_at')
                ->label('Dijana Pada'),
            ExportColumn::make('period_start')
                ->label('Tarikh Mula'),
            ExportColumn::make('period_end')
                ->label('Tarikh Tamat'),

            // System Health
            ExportColumn::make('system_health_score')
                ->label('Skor Kesihatan Sistem (%)'),
            ExportColumn::make('system_health_status')
                ->label('Status Kesihatan'),

            // Helpdesk Metrics
            ExportColumn::make('total_tickets')
                ->label('Jumlah Tiket'),
            ExportColumn::make('resolved_tickets')
                ->label('Tiket Diselesaikan'),
            ExportColumn::make('pending_tickets')
                ->label('Tiket Tertunda'),
            ExportColumn::make('overdue_tickets')
                ->label('Tiket Tertunggak'),
            ExportColumn::make('ticket_resolution_rate')
                ->label('Kadar Penyelesaian Tiket (%)'),
            ExportColumn::make('avg_resolution_hours')
                ->label('Purata Masa Penyelesaian (Jam)'),

            // Loan Metrics
            ExportColumn::make('total_loan_applications')
                ->label('Jumlah Permohonan Pinjaman'),
            ExportColumn::make('approved_applications')
                ->label('Permohonan Diluluskan'),
            ExportColumn::make('active_loans')
                ->label('Pinjaman Aktif'),
            ExportColumn::make('overdue_loans')
                ->label('Pinjaman Tertunggak'),
            ExportColumn::make('pending_approval')
                ->label('Menunggu Kelulusan'),
            ExportColumn::make('loan_approval_rate')
                ->label('Kadar Kelulusan Pinjaman (%)'),
            ExportColumn::make('total_loan_value')
                ->label('Jumlah Nilai Pinjaman (RM)'),

            // Asset Metrics
            ExportColumn::make('total_assets')
                ->label('Jumlah Aset'),
            ExportColumn::make('available_assets')
                ->label('Aset Tersedia'),
            ExportColumn::make('loaned_assets')
                ->label('Aset Dipinjam'),
            ExportColumn::make('maintenance_assets')
                ->label('Aset Penyelenggaraan'),
            ExportColumn::make('retired_assets')
                ->label('Aset Bersara'),
            ExportColumn::make('asset_utilization_rate')
                ->label('Kadar Penggunaan Aset (%)'),
            ExportColumn::make('asset_availability_rate')
                ->label('Kadar Ketersediaan Aset (%)'),

            // Integration Metrics
            ExportColumn::make('total_integrations')
                ->label('Jumlah Integrasi'),
            ExportColumn::make('asset_damage_reports')
                ->label('Laporan Kerosakan Aset'),
            ExportColumn::make('maintenance_requests')
                ->label('Permintaan Penyelenggaraan'),
            ExportColumn::make('asset_ticket_links')
                ->label('Pautan Aset-Tiket'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Eksport analitik terpadu telah selesai dan '.number_format($export->successful_rows).' baris telah dieksport.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' baris gagal dieksport.';
        }

        return $body;
    }

    public function getJobQueue(): ?string
    {
        return 'exports';
    }

    public function getJobConnection(): ?string
    {
        return 'redis';
    }

    public function getJobBatchName(): ?string
    {
        return 'unified-analytics-export';
    }

    public function getJobRetryUntil(): ?\DateTime
    {
        return now()->addMinutes(30);
    }

    public function getJobTries(): ?int
    {
        return 3;
    }

    public function getJobTimeout(): ?int
    {
        return 300; // 5 minutes
    }

    public function getMaxFileSize(): ?int
    {
        return 50 * 1024 * 1024; // 50MB
    }

    protected function getRecords(): \Illuminate\Support\Collection
    {
        $service = app(UnifiedAnalyticsService::class);

        // Get date range from export options or use default
        $startDate = $this->getOption('start_date') ? new \DateTime($this->getOption('start_date')) : now()->subMonth();
        $endDate = $this->getOption('end_date') ? new \DateTime($this->getOption('end_date')) : now();

        $metrics = $service->getDashboardMetrics($startDate, $endDate);

        // Convert metrics to exportable format
        return collect([
            [
                'report_type' => 'Analitik Terpadu',
                'generated_at' => $metrics['generated_at'],
                'period_start' => $startDate->format('Y-m-d'),
                'period_end' => $endDate->format('Y-m-d'),

                // System Health
                'system_health_score' => $metrics['summary']['overall_system_health'],
                'system_health_status' => $this->getHealthStatus($metrics['summary']['overall_system_health']),

                // Helpdesk Metrics
                'total_tickets' => $metrics['helpdesk']['total_tickets'],
                'resolved_tickets' => $metrics['helpdesk']['resolved_tickets'],
                'pending_tickets' => $metrics['helpdesk']['pending_tickets'],
                'overdue_tickets' => $metrics['helpdesk']['overdue_tickets'],
                'ticket_resolution_rate' => $metrics['helpdesk']['resolution_rate'],
                'avg_resolution_hours' => $metrics['helpdesk']['avg_resolution_hours'],

                // Loan Metrics
                'total_loan_applications' => $metrics['loans']['total_applications'],
                'approved_applications' => $metrics['loans']['approved_applications'],
                'active_loans' => $metrics['loans']['active_loans'],
                'overdue_loans' => $metrics['loans']['overdue_loans'],
                'pending_approval' => $metrics['loans']['pending_approval'],
                'loan_approval_rate' => $metrics['loans']['approval_rate'],
                'total_loan_value' => $metrics['loans']['total_loan_value'],

                // Asset Metrics
                'total_assets' => $metrics['assets']['total_assets'],
                'available_assets' => $metrics['assets']['available_assets'],
                'loaned_assets' => $metrics['assets']['loaned_assets'],
                'maintenance_assets' => $metrics['assets']['maintenance_assets'],
                'retired_assets' => $metrics['assets']['retired_assets'],
                'asset_utilization_rate' => $metrics['assets']['utilization_rate'],
                'asset_availability_rate' => $metrics['assets']['availability_rate'],

                // Integration Metrics
                'total_integrations' => $metrics['integration']['total_integrations'],
                'asset_damage_reports' => $metrics['integration']['asset_damage_reports'],
                'maintenance_requests' => $metrics['integration']['maintenance_requests'],
                'asset_ticket_links' => $metrics['integration']['asset_ticket_links'],
            ],
        ]);
    }

    private function getHealthStatus(float $score): string
    {
        return match (true) {
            $score >= 90 => 'Cemerlang',
            $score >= 75 => 'Baik',
            $score >= 60 => 'Sederhana',
            default => 'Perlu Perhatian',
        };
    }
}

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
                ->label(__('unified_analytics.report_type')),
            ExportColumn::make('generated_at')
                ->label(__('unified_analytics.generated_at')),
            ExportColumn::make('period_start')
                ->label(__('unified_analytics.period_start')),
            ExportColumn::make('period_end')
                ->label(__('unified_analytics.period_end')),

            // System Health
            ExportColumn::make('system_health_score')
                ->label(__('unified_analytics.system_health_score')),
            ExportColumn::make('system_health_status')
                ->label(__('unified_analytics.system_health_status')),

            // Helpdesk Metrics
            ExportColumn::make('total_tickets')
                ->label(__('unified_analytics.total_tickets')),
            ExportColumn::make('resolved_tickets')
                ->label(__('unified_analytics.resolved_tickets')),
            ExportColumn::make('pending_tickets')
                ->label(__('unified_analytics.pending_tickets')),
            ExportColumn::make('overdue_tickets')
                ->label(__('unified_analytics.overdue_tickets')),
            ExportColumn::make('ticket_resolution_rate')
                ->label(__('unified_analytics.ticket_resolution_rate')),
            ExportColumn::make('avg_resolution_hours')
                ->label(__('unified_analytics.avg_resolution_hours')),

            // Loan Metrics
            ExportColumn::make('total_loan_applications')
                ->label(__('unified_analytics.total_loan_applications')),
            ExportColumn::make('approved_applications')
                ->label(__('unified_analytics.approved_applications')),
            ExportColumn::make('active_loans')
                ->label(__('unified_analytics.active_loans')),
            ExportColumn::make('overdue_loans')
                ->label(__('unified_analytics.overdue_loans')),
            ExportColumn::make('pending_approval')
                ->label(__('unified_analytics.pending_approval')),
            ExportColumn::make('loan_approval_rate')
                ->label(__('unified_analytics.loan_approval_rate')),
            ExportColumn::make('total_loan_value')
                ->label(__('unified_analytics.total_loan_value')),

            // Asset Metrics
            ExportColumn::make('total_assets')
                ->label(__('unified_analytics.total_assets')),
            ExportColumn::make('available_assets')
                ->label(__('unified_analytics.available_assets')),
            ExportColumn::make('loaned_assets')
                ->label(__('unified_analytics.loaned_assets')),
            ExportColumn::make('maintenance_assets')
                ->label(__('unified_analytics.maintenance_assets')),
            ExportColumn::make('retired_assets')
                ->label(__('unified_analytics.retired_assets')),
            ExportColumn::make('asset_utilization_rate')
                ->label(__('unified_analytics.asset_utilization_rate')),
            ExportColumn::make('asset_availability_rate')
                ->label(__('unified_analytics.asset_availability_rate')),

            // Integration Metrics
            ExportColumn::make('total_integrations')
                ->label(__('unified_analytics.total_integrations')),
            ExportColumn::make('asset_damage_reports')
                ->label(__('unified_analytics.asset_damage_reports')),
            ExportColumn::make('maintenance_requests')
                ->label(__('unified_analytics.maintenance_requests')),
            ExportColumn::make('asset_ticket_links')
                ->label(__('unified_analytics.asset_ticket_links')),
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

    public function getJobRetryUntil(): ?\Carbon\CarbonInterface
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

        // Use default date range (last month)
        $startDate = now()->subMonth();
        $endDate = now();

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
            $score >= 90 => __('unified_analytics.health_status_excellent'),
            $score >= 75 => __('unified_analytics.health_status_good'),
            $score >= 60 => __('unified_analytics.health_status_average'),
            default => __('unified_analytics.health_status_needs_attention'),
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Illuminate\Support\Collection;

/**
 * Report Builder Service
 *
 * Data extraction and formatting for custom reports.
 *
 * @see D03-FR-006.1 Reporting requirements
 * @see D03-FR-006.2 Export functionality
 * @see D04 §7.1 Reporting architecture
 */
class ReportBuilderService
{
    /**
     * Generate report data based on parameters
     *
     * @param  string  $module  Module to report on (helpdesk, loans, assets)
     * @param  array  $filters  Filters to apply
     * @return array Report data with metadata
     */
    public function generateReport(string $module, array $filters = []): array
    {
        $data = match ($module) {
            'helpdesk' => $this->generateHelpdeskReport($filters),
            'loans' => $this->generateLoansReport($filters),
            'assets' => $this->generateAssetsReport($filters),
            default => throw new \InvalidArgumentException("Invalid module: {$module}"),
        };

        return [
            'module' => $module,
            'filters' => $filters,
            'generated_at' => now()->toIso8601String(),
            'total_records' => count($data),
            'data' => $data,
        ];
    }

    /**
     * Generate helpdesk tickets report
     */
    private function generateHelpdeskReport(array $filters): Collection
    {
        $query = HelpdeskTicket::query()
            ->with(['user', 'assignedTo', 'division', 'asset']);

        // Apply date range filter
        if (! empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Apply status filter
        if (! empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }

        // Apply priority filter
        if (! empty($filters['priority'])) {
            $query->whereIn('priority', (array) $filters['priority']);
        }

        return $query->get()->map(function ($ticket) {
            return [
                'ticket_number' => $ticket->ticket_number,
                'title' => $ticket->title,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'category' => $ticket->category,
                'submitter' => $ticket->user?->name ?? $ticket->guest_name,
                'assigned_to' => $ticket->assignedTo?->name ?? 'Unassigned',
                'created_at' => $ticket->created_at->format('Y-m-d H:i:s'),
                'resolved_at' => $ticket->resolved_at?->format('Y-m-d H:i:s'),
                'sla_breached' => $ticket->sla_resolution_due_at && $ticket->sla_resolution_due_at->isPast() && ! $ticket->resolved_at,
            ];
        });
    }

    /**
     * Generate loan applications report
     */
    private function generateLoansReport(array $filters): Collection
    {
        $query = LoanApplication::query()
            ->with(['user', 'division', 'loanItems.asset']);

        // Apply date range filter
        if (! empty($filters['date_from'])) {
            $query->where('loan_start_date', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->where('loan_start_date', '<=', $filters['date_to']);
        }

        // Apply status filter
        if (! empty($filters['status'])) {
            $statusValues = collect($filters['status'])->map(fn ($s) => is_string($s) ? $s : $s->value)->toArray();
            $query->whereIn('status', $statusValues);
        }

        return $query->get()->map(function ($loan) {
            $statusValue = $loan->status instanceof \BackedEnum ? $loan->status->value : (string) $loan->status;

            return [
                'application_number' => $loan->application_number,
                'applicant_name' => $loan->applicant_name,
                'status' => $statusValue,
                'loan_start_date' => $loan->loan_start_date?->format('Y-m-d'),
                'loan_end_date' => $loan->loan_end_date?->format('Y-m-d'),
                'assets_count' => $loan->loanItems->count(),
                'assets' => $loan->loanItems->pluck('asset.name')->join(', '),
                'division' => $loan->division?->name_en,
                'created_at' => $loan->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    /**
     * Generate assets report
     */
    private function generateAssetsReport(array $filters): Collection
    {
        $query = Asset::query()
            ->with(['category', 'loanItems']);

        // Apply status filter
        if (! empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }

        // Apply category filter
        if (! empty($filters['category'])) {
            $query->whereIn('category_id', (array) $filters['category']);
        }

        return $query->get()->map(function ($asset) {
            return [
                'asset_code' => $asset->asset_code,
                'name' => $asset->name,
                'category' => $asset->category?->name_en,
                'status' => $asset->status,
                'condition' => $asset->condition,
                'location' => $asset->location,
                'total_loans' => $asset->loanItems->count(),
                'current_value' => $asset->purchase_price,
                'created_at' => $asset->created_at->format('Y-m-d'),
            ];
        });
    }

    /**
     * Format report data for export
     *
     * @param  array  $reportData  Report data from generateReport()
     * @param  string  $format  Export format (csv, excel, pdf)
     * @return array Formatted data ready for export
     */
    public function formatForExport(array $reportData, string $format): array
    {
        return [
            'format' => $format,
            'filename' => $this->generateFilename($reportData['module'], $format),
            'headers' => $this->getHeaders($reportData['module']),
            'data' => $reportData['data'],
            'metadata' => [
                'module' => $reportData['module'],
                'generated_at' => $reportData['generated_at'],
                'total_records' => $reportData['total_records'],
                'filters_applied' => $reportData['filters'],
            ],
        ];
    }

    /**
     * Generate filename for export
     */
    private function generateFilename(string $module, string $format): string
    {
        $timestamp = now()->format('Y-m-d_His');

        return "{$module}_report_{$timestamp}.{$format}";
    }

    /**
     * Get column headers for module
     */
    private function getHeaders(string $module): array
    {
        return match ($module) {
            'helpdesk' => [
                'Ticket Number',
                'Title',
                'Status',
                'Priority',
                'Category',
                'Submitter',
                'Assigned To',
                'Created At',
                'Resolved At',
                'SLA Breached',
            ],
            'loans' => [
                'Application Number',
                'Applicant Name',
                'Status',
                'Loan Start Date',
                'Loan End Date',
                'Assets Count',
                'Assets',
                'Division',
                'Created At',
            ],
            'assets' => [
                'Asset Code',
                'Name',
                'Category',
                'Status',
                'Condition',
                'Location',
                'Total Loans',
                'Current Value',
                'Created At',
            ],
            default => [],
        };
    }
}

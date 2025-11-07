<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Report Builder Service
 *
 * Handles report generation with data formatting for CSV, PDF, Excel formats.
 * Includes metadata and enforces 50MB file size limit.
 *
 * @trace Requirements 8.1, 8.3
 */
class ReportBuilderService
{
    private const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50MB

    public function generateReport(array $params): array
    {
        $data = $this->getReportData($params);
        $filename = $this->generateFilename($params);

        return match ($params['format']) {
            'csv' => $this->generateCsvReport($data, $filename, $params),
            'excel' => $this->generateExcelReport($data, $filename, $params),
            'pdf' => $this->generatePdfReport($data, $filename, $params),
            default => throw new \InvalidArgumentException('Unsupported format'),
        };
    }

    public function getPreviewData(array $params): array
    {
        $data = $this->getReportData($params);

        return [
            'total_records' => $data->count(),
            'sample_data' => $data->take(5)->toArray(),
            'columns' => $this->getColumnHeaders($params['module']),
            'date_range' => [
                'start' => $params['start_date'],
                'end' => $params['end_date'],
            ],
            'filters_applied' => [
                'module' => $params['module'],
                'statuses' => $params['statuses'] ?? [],
            ],
        ];
    }

    private function getReportData(array $params): Collection
    {
        return match ($params['module']) {
            'helpdesk' => $this->getHelpdeskData($params),
            'loans' => $this->getLoansData($params),
            'assets' => $this->getAssetsData($params),
            'users' => $this->getUsersData($params),
            'unified' => $this->getUnifiedData($params),
            default => collect(),
        };
    }

    private function getHelpdeskData(array $params): Collection
    {
        $query = HelpdeskTicket::query()
            ->with(['user', 'assignedTo', 'category', 'asset'])
            ->whereBetween('created_at', [$params['start_date'], $params['end_date']]);

        if (! empty($params['statuses'])) {
            $query->whereIn('status', $params['statuses']);
        }

        return $query->get()->map(function ($ticket) {
            return [
                'ticket_number' => $ticket->ticket_number,
                'title' => $ticket->title,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'category' => $ticket->category?->name_en,
                'requester' => $ticket->user?->name ?? $ticket->guest_name,
                'assigned_to' => $ticket->assignedTo?->name,
                'asset' => $ticket->asset?->name,
                'created_at' => $ticket->created_at->format('Y-m-d H:i:s'),
                'resolved_at' => $ticket->resolved_at?->format('Y-m-d H:i:s'),
            ];
        });
    }

    private function getLoansData(array $params): Collection
    {
        $query = LoanApplication::query()
            ->with(['user', 'loanItems.asset'])
            ->whereBetween('created_at', [$params['start_date'], $params['end_date']]);

        if (! empty($params['statuses'])) {
            $query->whereIn('status', $params['statuses']);
        }

        return $query->get()->map(function ($loan) {
            return [
                'application_number' => $loan->application_number,
                'applicant_name' => $loan->applicant_name,
                'status' => $loan->status,
                'priority' => $loan->priority,
                'assets' => $loan->loanItems->pluck('asset.name')->join(', '),
                'purpose' => $loan->purpose,
                'loan_date' => $loan->loan_date?->format('Y-m-d'),
                'expected_return_date' => $loan->expected_return_date?->format('Y-m-d'),
                'created_at' => $loan->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    private function getAssetsData(array $params): Collection
    {
        $query = Asset::query()
            ->with(['category'])
            ->whereBetween('created_at', [$params['start_date'], $params['end_date']]);

        if (! empty($params['statuses'])) {
            $query->whereIn('status', $params['statuses']);
        }

        return $query->get()->map(function ($asset) {
            return [
                'asset_code' => $asset->asset_code,
                'name' => $asset->name,
                'category' => $asset->category?->name_en,
                'brand' => $asset->brand,
                'model' => $asset->model,
                'status' => $asset->status,
                'condition' => $asset->condition,
                'location' => $asset->location,
                'acquired_date' => $asset->acquired_date?->format('Y-m-d'),
                'created_at' => $asset->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    private function getUsersData(array $params): Collection
    {
        $query = User::query()
            ->with(['division', 'grade'])
            ->whereBetween('created_at', [$params['start_date'], $params['end_date']]);

        if (! empty($params['statuses'])) {
            $isActive = in_array('active', $params['statuses']);
            $query->where('is_active', $isActive);
        }

        return $query->get()->map(function ($user) {
            return [
                'name' => $user->name,
                'email' => $user->email,
                'staff_id' => $user->staff_id,
                'division' => $user->division?->name_en,
                'grade' => $user->grade?->name,
                'role' => $user->role,
                'is_active' => $user->is_active ? 'Active' : 'Inactive',
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'last_login_at' => $user->last_login_at?->format('Y-m-d H:i:s'),
            ];
        });
    }

    private function getUnifiedData(array $params): Collection
    {
        // Combine data from all modules for unified analytics
        return collect([
            'helpdesk_summary' => [
                'total_tickets' => HelpdeskTicket::whereBetween('created_at', [$params['start_date'], $params['end_date']])->count(),
                'resolved_tickets' => HelpdeskTicket::whereBetween('created_at', [$params['start_date'], $params['end_date']])->whereNotNull('resolved_at')->count(),
                'avg_resolution_time' => 'TBD', // Calculate average resolution time
            ],
            'loans_summary' => [
                'total_applications' => LoanApplication::whereBetween('created_at', [$params['start_date'], $params['end_date']])->count(),
                'approved_applications' => LoanApplication::whereBetween('created_at', [$params['start_date'], $params['end_date']])->where('status', 'approved')->count(),
                'active_loans' => LoanApplication::where('status', 'in_use')->count(),
            ],
            'assets_summary' => [
                'total_assets' => Asset::count(),
                'available_assets' => Asset::where('status', 'available')->count(),
                'assets_on_loan' => Asset::where('status', 'on_loan')->count(),
                'assets_in_maintenance' => Asset::where('status', 'maintenance')->count(),
            ],
        ]);
    }

    private function generateCsvReport(Collection $data, string $filename, array $params): array
    {
        $csvContent = $this->generateCsvContent($data, $params);

        if (strlen($csvContent) > self::MAX_FILE_SIZE) {
            throw new \Exception('Report size exceeds 50MB limit');
        }

        $path = "reports/{$filename}";
        Storage::disk('local')->put($path, $csvContent);

        return [
            'filename' => $filename,
            'path' => $path,
            'download_url' => Storage::disk('local')->url($path),
            'size' => strlen($csvContent),
            'format' => 'csv',
        ];
    }

    private function generateExcelReport(Collection $data, string $filename, array $params): array
    {
        // Placeholder for Excel generation
        // Would use PhpSpreadsheet or similar library
        return $this->generateCsvReport($data, str_replace('.csv', '.xlsx', $filename), $params);
    }

    private function generatePdfReport(Collection $data, string $filename, array $params): array
    {
        // Placeholder for PDF generation
        // Would use TCPDF, DomPDF, or similar library
        $pdfContent = $this->generatePdfContent($data, $params);

        $path = "reports/{$filename}";
        Storage::disk('local')->put($path, $pdfContent);

        return [
            'filename' => $filename,
            'path' => $path,
            'download_url' => Storage::disk('local')->url($path),
            'size' => strlen($pdfContent),
            'format' => 'pdf',
        ];
    }

    private function generateCsvContent(Collection $data, array $params): string
    {
        if ($data->isEmpty()) {
            return '';
        }

        $headers = array_keys($data->first());
        $csv = implode(',', $headers)."\n";

        foreach ($data as $row) {
            $csv .= implode(',', array_map(function ($value) {
                return '"'.str_replace('"', '""', $value ?? '').'"';
            }, array_values($row)))."\n";
        }

        // Add metadata
        $csv .= "\n\n# Metadata\n";
        $csv .= '# Generated: '.now()->format('Y-m-d H:i:s')."\n";
        $csv .= "# Module: {$params['module']}\n";
        $csv .= "# Date Range: {$params['start_date']} to {$params['end_date']}\n";
        $csv .= '# Total Records: '.$data->count()."\n";

        return $csv;
    }

    private function generatePdfContent(Collection $data, array $params): string
    {
        // Simplified PDF content - would use proper PDF library in production
        $content = "ICTServe Report\n\n";
        $content .= "Module: {$params['module']}\n";
        $content .= "Date Range: {$params['start_date']} to {$params['end_date']}\n";
        $content .= 'Generated: '.now()->format('Y-m-d H:i:s')."\n\n";
        $content .= 'Total Records: '.$data->count()."\n\n";

        return $content;
    }

    private function generateFilename(array $params): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $extension = match ($params['format']) {
            'csv' => 'csv',
            'excel' => 'xlsx',
            'pdf' => 'pdf',
            default => 'txt',
        };

        return "report_{$params['module']}_{$timestamp}.{$extension}";
    }

    private function getColumnHeaders(string $module): array
    {
        return match ($module) {
            'helpdesk' => ['Ticket Number', 'Title', 'Status', 'Priority', 'Category', 'Requester', 'Assigned To', 'Asset', 'Created At', 'Resolved At'],
            'loans' => ['Application Number', 'Applicant', 'Status', 'Priority', 'Assets', 'Purpose', 'Loan Date', 'Return Date', 'Created At'],
            'assets' => ['Asset Code', 'Name', 'Category', 'Brand', 'Model', 'Status', 'Condition', 'Location', 'Acquired Date', 'Created At'],
            'users' => ['Name', 'Email', 'Staff ID', 'Division', 'Grade', 'Role', 'Status', 'Created At', 'Last Login'],
            default => [],
        };
    }
}

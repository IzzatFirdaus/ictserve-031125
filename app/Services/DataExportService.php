<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LoanApplication;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Data Export Service
 *
 * Handles data export in multiple formats (CSV, Excel, PDF).
 *
 * @trace D03-FR-013.5 (Data Export Functionality)
 */
class DataExportService
{
    public function exportLoanApplications(array $filters = []): string
    {
        $applications = $this->getLoanApplicationsData($filters);

        return $this->generateCSV($applications, 'loan_applications');
    }

    public function exportAssets(array $filters = []): string
    {
        $assets = $this->getAssetsData($filters);

        return $this->generateCSV($assets, 'assets');
    }

    protected function getLoanApplicationsData(array $filters): Collection
    {
        $query = LoanApplication::query()->with(['division', 'user', 'loanItems.asset']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }

        return $query->get()->map(fn($app) => [
            'Application Number' => $app->application_number,
            'Applicant Name' => $app->applicant_name,
            'Email' => $app->applicant_email,
            'Division' => $app->division?->name ?? 'N/A',
            'Purpose' => $app->purpose,
            'Status' => $app->status->value, // Use enum value instead of localized label
            'Loan Start Date' => $app->loan_start_date?->format('Y-m-d'),
            'Loan End Date' => $app->loan_end_date?->format('Y-m-d'),
            'Total Items' => $app->loanItems->count(),
            'Created At' => $app->created_at->format('Y-m-d H:i:s'),
        ]);
    }

    protected function getAssetsData(array $filters): Collection
    {
        $query = \App\Models\Asset::query()->with('category');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get()->map(fn($asset) => [
            'Asset Tag' => $asset->asset_tag,
            'Name' => $asset->name,
            'Category' => $asset->category?->name ?? 'N/A',
            'Status' => $asset->status->label(),
            'Condition' => $asset->condition?->label() ?? 'N/A',
            'Serial Number' => $asset->serial_number ?? 'N/A',
            'Purchase Date' => $asset->purchase_date?->format('Y-m-d') ?? 'N/A',
        ]);
    }

    protected function generateCSV(Collection $data, string $filename): string
    {
        if ($data->isEmpty()) {
            throw new \RuntimeException('No data to export');
        }

        $filename = $filename . '_' . now()->format('Y-m-d_His') . '.csv';
        $path = 'exports/' . $filename;

        // Ensure exports directory exists
        $disk = Storage::disk('local');
        if (!$disk->exists('exports')) {
            $disk->makeDirectory('exports');
        }

        $handle = fopen($disk->path($path), 'w');

        // Write headers
        fputcsv($handle, array_keys($data->first()));

        // Write data rows
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return $path;
    }
}

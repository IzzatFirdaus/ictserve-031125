<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\ExportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ExportSubmissions extends Component
{
    // Export Configuration
    public string $exportType = 'helpdesk'; // 'helpdesk' or 'loan'

    public string $exportFormat = 'csv'; // 'csv' or 'pdf'

    public string $dateRange = 'last_30_days';

    public ?string $customStartDate = null;

    public ?string $customEndDate = null;

    public array $selectedStatuses = [];

    public bool $includeComments = false;

    // Component State
    public bool $isExporting = false;

    public ?string $downloadUrl = null;

    // Available Options
    public array $dateRangeOptions = [
        'last_7_days' => 'Last 7 Days',
        'last_30_days' => 'Last 30 Days',
        'last_90_days' => 'Last 90 Days',
        'current_month' => 'Current Month',
        'last_month' => 'Last Month',
        'custom' => 'Custom Range',
    ];

    public array $helpdeskStatuses = [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'pending' => 'Pending',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ];

    public array $loanStatuses = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'declined' => 'Declined',
        'active' => 'Active',
        'returned' => 'Returned',
        'overdue' => 'Overdue',
    ];

    // Validation Rules
    protected array $rules = [
        'exportType' => ['required', 'in:helpdesk,loan'],
        'exportFormat' => ['required', 'in:csv,pdf'],
        'dateRange' => ['required', 'string'],
        'customStartDate' => ['nullable', 'date', 'before_or_equal:customEndDate'],
        'customEndDate' => ['nullable', 'date', 'after_or_equal:customStartDate'],
        'selectedStatuses' => ['array'],
        'includeComments' => ['boolean'],
    ];

    /**
     * Generate and download export
     */
    public function generateExport(): ?StreamedResponse
    {
        $this->validate();

        if ($this->dateRange === 'custom' && (empty($this->customStartDate) || empty($this->customEndDate))) {
            session()->flash('export-error', __('export.custom_date_required'));

            return null;
        }

        $this->isExporting = true;
        $response = null;

        try {
            $exportService = app(ExportService::class);
            $user = Auth::user();

            if (! $user) {
                session()->flash('export-error', __('export.generation_failed'));

                return null;
            }

            $filters = [];

            if (! empty($this->selectedStatuses)) {
                $filters['statuses'] = $this->selectedStatuses;
            }

            [$startDate, $endDate] = $this->getDateRangeFilters();

            if ($startDate) {
                $filters['date_from'] = $startDate;
            }

            if ($endDate) {
                $filters['date_to'] = $endDate;
            }

            $type = $this->exportType;

            $response = $this->exportFormat === 'csv'
                ? $exportService->exportToCsv($user, $type, $filters)
                : $exportService->exportToExcel($user, $type, $filters);
        } catch (Throwable $e) {
            Log::error('Export generation failed', [
                'message' => $e->getMessage(),
                'user_id' => Auth::id(),
                'export_type' => $this->exportType,
                'export_format' => $this->exportFormat,
            ]);

            session()->flash('export-error', __('export.generation_failed'));

            return null;
        } finally {
            $this->isExporting = false;
        }

        return $response;
    }

    /**
     * Get date range filters based on selection
     */
    private function getDateRangeFilters(): array
    {
        $startDate = null;
        $endDate = null;

        switch ($this->dateRange) {
            case 'last_7_days':
                $startDate = now()->subDays(7)->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'last_30_days':
                $startDate = now()->subDays(30)->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'last_90_days':
                $startDate = now()->subDays(90)->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'current_month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'last_month':
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();
                break;
            case 'custom':
                $startDate = $this->customStartDate ? \Carbon\Carbon::parse($this->customStartDate)->startOfDay() : null;
                $endDate = $this->customEndDate ? \Carbon\Carbon::parse($this->customEndDate)->endOfDay() : null;
                break;
        }

        return [$startDate, $endDate];
    }

    /**
     * Reset export form
     */
    public function resetForm(): void
    {
        $this->exportType = 'helpdesk';
        $this->exportFormat = 'csv';
        $this->dateRange = 'last_30_days';
        $this->customStartDate = null;
        $this->customEndDate = null;
        $this->selectedStatuses = [];
        $this->includeComments = false;
        $this->downloadUrl = null;

        session()->forget(['export-success', 'export-error']);
    }

    /**
     * Render component
     */
    public function render(): View
    {
        $user = Auth::user();

        return view('livewire.export-submissions', [
            'canExportAll' => $user?->hasAnyRole(['Admin', 'Superuser']) ?? false,
            'currentStatuses' => $this->exportType === 'helpdesk' ? $this->helpdeskStatuses : $this->loanStatuses,
        ]);
    }
}

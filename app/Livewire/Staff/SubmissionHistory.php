<?php

declare(strict_types=1);

namespace App\Livewire\Staff;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Traits\OptimizedLivewireComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Submission History Component
 *
 * Displays comprehensive submission history for authenticated users with tabbed interface
 * for helpdesk tickets and loan applications. Includes search, filtering, sorting, and
 * pagination capabilities with WCAG 2.2 Level AA compliance.
 *
 * Features:
 * - Tabbed interface (My Tickets | My Loan Requests)
 * - Search functionality with debouncing (300ms)
 * - Status filtering for both tickets and loans
 * - Date range filtering
 * - Sortable columns with ARIA attributes
 * - Pagination with accessible controls
 * - Real-time updates with wire:poll
 * - Lazy loading for performance
 * - Query optimization with eager loading
 * - 5-minute caching strategy
 *
 * @see D03-FR-021.1 Submission history with tabbed interface
 * @see D03-FR-021.2 Ticket history display
 * @see D03-FR-021.3 Loan history display
 * @see D03-FR-021.4 Search and filter functionality
 * @see D03-FR-024.2 Performance optimization with caching
 * @see D04 §6.2 Authenticated portal Livewire components
 * @see D10 §7 Livewire component documentation
 * @see D12 §9 WCAG 2.2 AA compliance
 *
 * @requirements 21.1, 21.2, 21.3, 21.4, 24.2
 *
 * @wcag-level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-05
 *
 * @author Frontend Engineering Team
 */
#[Lazy]
class SubmissionHistory extends Component
{
    use OptimizedLivewireComponent;
    use WithPagination;

    /**
     * Active tab (tickets or loans)
     */
    #[Url(as: 'tab')]
    public string $activeTab = 'tickets';

    /**
     * Search query
     */
    #[Url(as: 'q')]
    public string $search = '';

    /**
     * Status filter
     */
    /** @var array<int, string> */
    #[Url(as: 'status')]
    public array $statusFilter = [];

    /**
     * Date from filter
     */
    #[Url(as: 'from')]
    public string $dateFrom = '';

    /**
     * Date to filter
     */
    #[Url(as: 'to')]
    public string $dateTo = '';

    /**
     * Sort field
     */
    #[Url(as: 'sort')]
    public string $sortField = 'created_at';

    /**
     * Sort direction
     */
    #[Url(as: 'dir')]
    public string $sortDirection = 'desc';

    /**
     * Items per page
     */
    public int $perPage = 10;

    /**
     * Selected ticket IDs for bulk operations
     *
     * @var array<int, int>
     */
    public array $selectedTickets = [];

    /**
     * Selected loan IDs for bulk operations
     *
     * @var array<int, int>
     */
    public array $selectedLoans = [];

    /**
     * Select all tickets flag
     */
    public bool $selectAllTickets = false;

    /**
     * Select all loans flag
     */
    public bool $selectAllLoans = false;

    /**
     * Get authenticated user
     */
    protected function getUser(): User
    {
        $user = Auth::user();
        assert($user instanceof User);

        return $user;
    }

    /**
     * Get relationships to eager load for preventing N+1 queries
     *
     * Returns relationships based on the model type (tickets vs loans)
     *
     * @return array<int, string>
     */
    protected function getEagerLoadRelationships(string $modelType = 'tickets'): array
    {
        if ($modelType === 'tickets') {
            return [
                'user:id,name,email',
                'assignedUser:id,name',
                'division:id,name_ms,name_en',
                'category:id,name',
            ];
        }

        // For loans
        return [
            'user:id,name,email',
            'division:id,name_ms,name_en',
            'loanItems.asset:id,name,model',
        ];
    }

    /**
     * Get filtered and paginated helpdesk tickets
     *
     * Returns tickets for the authenticated user with applied filters:
     * - Search: ticket_number, subject, description
     * - Status filter: all, open, in_progress, resolved, closed
     * - Date range: created_at between dateFrom and dateTo
     * - Sorting: configurable field and direction
     *
     * @return LengthAwarePaginator<int, HelpdeskTicket>
     */
    #[Computed]
    public function filteredTickets(): LengthAwarePaginator
    {
        $user = $this->getUser();

        /** @var Builder<HelpdeskTicket> $query */
        $query = HelpdeskTicket::query()
            ->where(function (Builder $q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('guest_email', $user->email);
            });

        // Apply search filter
        if (! empty($this->search)) {
            $query->where(function (Builder $q) {
                $q->where('ticket_number', 'like', "%{$this->search}%")
                    ->orWhere('subject', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        // Apply status filter
        if (! empty($this->statusFilter)) {
            $query->whereIn('status', $this->statusFilter);
        }

        // Apply date range filter
        if (! empty($this->dateFrom)) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if (! empty($this->dateTo)) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        // Apply eager loading & use optimized pagination
        $query = $this->applyEagerLoading($query);

        $cacheKey = 'filtered_tickets_'.md5(sprintf('%s|%s|%s|%s|%s', $this->search, implode(',', $this->statusFilter), $this->dateFrom, $this->dateTo, $this->sortField));

        $paginator = $this->getCachedComponentData($cacheKey, function () use ($query) {
            return $this->getOptimizedPaginatedResults($query, $this->perPage);
        }, 60);

        if (! $paginator instanceof LengthAwarePaginator) {
            throw new \UnexpectedValueException('Filtered tickets must return a paginator.');
        }

        return $paginator;
    }

    /**
     * Get filtered and paginated loan applications
     *
     * Returns loan applications for the authenticated user with applied filters:
     * - Search: application_number, purpose, location
     * - Status filter: all, submitted, under_review, approved, active, overdue, returned
     * - Date range: created_at between dateFrom and dateTo
     * - Sorting: configurable field and direction
     *
     * @return LengthAwarePaginator<int, LoanApplication>
     */
    #[Computed]
    public function filteredLoans(): LengthAwarePaginator
    {
        $user = $this->getUser();

        /** @var Builder<LoanApplication> $query */
        $query = LoanApplication::query()
            ->where(function (Builder $q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('applicant_email', $user->email);
            });

        // Apply search filter
        if (! empty($this->search)) {
            $query->where(function (Builder $q) {
                $q->where('application_number', 'like', "%{$this->search}%")
                    ->orWhere('purpose', 'like', "%{$this->search}%")
                    ->orWhere('location', 'like', "%{$this->search}%");
            });
        }

        // Apply status filter
        if (! empty($this->statusFilter)) {
            $query->whereIn('status', $this->statusFilter);
        }

        // Apply date range filter
        if (! empty($this->dateFrom)) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if (! empty($this->dateTo)) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        // Apply eager loading & use optimized pagination
        $query = $this->applyEagerLoading($query);

        $cacheKey = 'filtered_loans_'.md5(sprintf('%s|%s|%s|%s|%s', $this->search, implode(',', $this->statusFilter), $this->dateFrom, $this->dateTo, $this->sortField));

        $paginator = $this->getCachedComponentData($cacheKey, function () use ($query) {
            return $this->getOptimizedPaginatedResults($query, $this->perPage);
        }, 60);

        if (! $paginator instanceof LengthAwarePaginator) {
            throw new \UnexpectedValueException('Filtered loans must return a paginator.');
        }

        return $paginator;
    }

    /**
     * Get ticket status options for filter dropdown
     *
     * @return array<string, string>
     */
    #[Computed]
    public function ticketStatusOptions(): array
    {
        return [
            'all' => __('common.all_statuses'),
            'open' => __('common.open'),
            'in_progress' => __('common.in_progress'),
            'pending_info' => __('common.pending_info'),
            'resolved' => __('common.resolved'),
            'closed' => __('common.closed'),
        ];
    }

    /**
     * Get loan status options for filter dropdown
     *
     * @return array<string, string>
     */
    #[Computed]
    public function loanStatusOptions(): array
    {
        return [
            'all' => __('common.all_statuses'),
            'submitted' => __('common.submitted'),
            'under_review' => __('common.under_review'),
            'approved' => __('common.approved'),
            'active' => __('common.active'),
            'overdue' => __('common.overdue'),
            'returned' => __('common.returned'),
            'rejected' => __('common.rejected'),
        ];
    }

    /**
     * Switch active tab
     */
    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetFilters();
        $this->resetPage();
    }

    /**
     * Reset all filters
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->statusFilter = [];
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    /**
     * Sort by field
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Update search query
     *
     * Resets pagination when search changes
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Update status filter
     *
     * Resets pagination when filter changes
     */
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Update date from filter
     *
     * Resets pagination when filter changes
     */
    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    /**
     * Update date to filter
     *
     * Resets pagination when filter changes
     */
    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    /**
     * Toggle select all tickets
     */
    public function updatedSelectAllTickets(): void
    {
        if ($this->selectAllTickets) {
            $this->selectedTickets = $this->filteredTickets->pluck('id')->toArray();
        } else {
            $this->selectedTickets = [];
        }
    }

    /**
     * Toggle select all loans
     */
    public function updatedSelectAllLoans(): void
    {
        if ($this->selectAllLoans) {
            $this->selectedLoans = $this->filteredLoans->pluck('id')->toArray();
        } else {
            $this->selectedLoans = [];
        }
    }

    /**
     * Get count of selected items based on active tab
     */
    #[Computed]
    public function selectedCount(): int
    {
        return $this->activeTab === 'tickets'
            ? count($this->selectedTickets)
            : count($this->selectedLoans);
    }

    /**
     * Check if any items are selected
     */
    #[Computed]
    public function hasSelection(): bool
    {
        return $this->selectedCount > 0;
    }

    /**
     * Clear all selections
     */
    public function clearSelection(): void
    {
        $this->selectedTickets = [];
        $this->selectedLoans = [];
        $this->selectAllTickets = false;
        $this->selectAllLoans = false;
    }

    /**
     * Bulk export selected items to CSV
     */
    public function bulkExportCSV(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $exportService = app(\App\Services\SubmissionExportService::class);

        if ($this->activeTab === 'tickets') {
            $tickets = HelpdeskTicket::whereIn('id', $this->selectedTickets)
                ->with($this->getEagerLoadRelationships('tickets'))
                ->get();

            $csv = $exportService->exportTicketsToCSV($tickets);
            $filename = $exportService->generateFilename('tickets_selected', 'csv');

            $this->clearSelection();

            return response()->streamDownload(function () use ($csv) {
                echo $csv;
            }, $filename, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        }

        $loans = LoanApplication::whereIn('id', $this->selectedLoans)
            ->with($this->getEagerLoadRelationships('loans'))
            ->get();

        $csv = $exportService->exportLoansToCSV($loans);
        $filename = $exportService->generateFilename('loans_selected', 'csv');

        $this->clearSelection();

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Bulk mark tickets as read (for tickets only)
     */
    public function bulkMarkAsRead(): void
    {
        if ($this->activeTab !== 'tickets' || empty($this->selectedTickets)) {
            return;
        }

        HelpdeskTicket::whereIn('id', $this->selectedTickets)
            ->where('user_id', $this->getUser()->id)
            ->update(['is_read' => true]);

        $this->clearSelection();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('common.bulk_marked_as_read'),
        ]);
    }

    /**
     * Render the component
     */
    public function render(): \Illuminate\View\View
    {
        $view = view('livewire.staff.submission-history');
        assert($view instanceof View);

        return $view->layout('layouts.portal');
    }

    /**
     * Export tickets to CSV
     *
     * Downloads current filtered tickets as CSV file
     */
    public function exportTicketsCSV(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $exportService = app(\App\Services\SubmissionExportService::class);

        // Get all filtered tickets (no pagination)
        $tickets = $this->getTicketsQuery()->get();

        $csv = $exportService->exportTicketsToCSV($tickets);
        $filename = $exportService->generateFilename('tickets', 'csv');

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export loans to CSV
     *
     * Downloads current filtered loans as CSV file
     */
    public function exportLoansCSV(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $exportService = app(\App\Services\SubmissionExportService::class);

        // Get all filtered loans (no pagination)
        $loans = $this->getLoansQuery()->get();

        $csv = $exportService->exportLoansToCSV($loans);
        $filename = $exportService->generateFilename('loans', 'csv');

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export tickets to printable PDF (opens print dialog)
     *
     * Opens a new window with printable HTML that triggers browser print dialog
     */
    public function exportTicketsPDF(): \Illuminate\Http\Response
    {
        $exportService = app(\App\Services\SubmissionExportService::class);

        // Get all filtered tickets (no pagination)
        $tickets = $this->getTicketsQuery()->get();

        $html = $exportService->exportTicketsToHTML($tickets);

        return response($html, 200, [
            'Content-Type' => 'text/html',
        ]);
    }

    /**
     * Export loans to printable PDF (opens print dialog)
     *
     * Opens a new window with printable HTML that triggers browser print dialog
     */
    public function exportLoansPDF(): \Illuminate\Http\Response
    {
        $exportService = app(\App\Services\SubmissionExportService::class);

        // Get all filtered loans (no pagination)
        $loans = $this->getLoansQuery()->get();

        $html = $exportService->exportLoansToHTML($loans);

        return response($html, 200, [
            'Content-Type' => 'text/html',
        ]);
    }

    /**
     * Get base query for tickets (without pagination)
     *
     * @return Builder<HelpdeskTicket>
     */
    protected function getTicketsQuery(): Builder
    {
        $user = $this->getUser();

        /** @var Builder<HelpdeskTicket> $query */
        $query = HelpdeskTicket::query()
            ->where(function (Builder $q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('guest_email', $user->email);
            });

        // Apply search filter
        if (! empty($this->search)) {
            $query->where(function (Builder $q) {
                $q->where('ticket_number', 'like', "%{$this->search}%")
                    ->orWhere('subject', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        // Apply status filter
        if (! empty($this->statusFilter)) {
            $query->whereIn('status', $this->statusFilter);
        }

        // Apply date range filter
        if (! empty($this->dateFrom)) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if (! empty($this->dateTo)) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        // Apply eager loading
        return $this->applyEagerLoading($query);
    }

    /**
     * Get base query for loans (without pagination)
     *
     * @return Builder<LoanApplication>
     */
    protected function getLoansQuery(): Builder
    {
        $user = $this->getUser();

        /** @var Builder<LoanApplication> $query */
        $query = LoanApplication::query()
            ->where(function (Builder $q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('guest_email', $user->email);
            });

        // Apply search filter
        if (! empty($this->search)) {
            $query->where(function (Builder $q) {
                $q->where('application_number', 'like', "%{$this->search}%")
                    ->orWhere('applicant_name', 'like', "%{$this->search}%")
                    ->orWhere('purpose', 'like', "%{$this->search}%");
            });
        }

        // Apply status filter
        if (! empty($this->statusFilter)) {
            $query->whereIn('status', $this->statusFilter);
        }

        // Apply date range filter
        if (! empty($this->dateFrom)) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if (! empty($this->dateTo)) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        // Apply eager loading
        return $this->applyEagerLoading($query);
    }

    /**
     * Get placeholder view for lazy loading
     */
    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="animate-pulse">
                <div class="h-8 bg-gray-200 rounded w-1/4 mb-6"></div>
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="h-10 bg-gray-200 rounded w-full mb-4"></div>
                    <div class="space-y-3">
                        <div class="h-16 bg-gray-200 rounded"></div>
                        <div class="h-16 bg-gray-200 rounded"></div>
                        <div class="h-16 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
        </div>
        HTML;
    }
}

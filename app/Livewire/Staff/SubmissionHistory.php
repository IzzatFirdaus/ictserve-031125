<?php

declare(strict_types=1);

namespace App\Livewire\Staff;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Traits\OptimizedLivewireComponent;
use Illuminate\Support\Facades\Auth;
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
    #[Url(as: 'status')]
    public string $statusFilter = 'all';

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
     */
    protected function getEagerLoadRelationships(): array
    {
        return [
            'user:id,name,email',
            'assignedAgent:id,name',
            'division:id,name_ms,name_en',
            'category:id,name',
            'asset:id,name,model',
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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    #[Computed]
    public function filteredTickets()
    {
        $user = $this->getUser();

        $query = HelpdeskTicket::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('guest_email', $user->email);
            });

        // Apply search filter
        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('ticket_number', 'like', "%{$this->search}%")
                    ->orWhere('subject', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
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
        $query = $this->applyEagerLoading($query);

        return $query->paginate($this->perPage);
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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    #[Computed]
    public function filteredLoans()
    {
        $user = $this->getUser();

        $query = LoanApplication::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('applicant_email', $user->email);
            });

        // Apply search filter
        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('application_number', 'like', "%{$this->search}%")
                    ->orWhere('purpose', 'like', "%{$this->search}%")
                    ->orWhere('location', 'like', "%{$this->search}%");
            });
        }

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
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
        $query = $this->applyEagerLoading($query);

        return $query->paginate($this->perPage);
    }

    /**
     * Get ticket status options for filter dropdown
     *
     * @return array<string, string>
     */
    public function getTicketStatusOptions(): array
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
    public function getLoanStatusOptions(): array
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
        $this->statusFilter = 'all';
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
     * Render the component
     */
    public function render()
    {
        return view('livewire.staff.submission-history');
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

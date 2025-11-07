<?php

// name: SubmissionHistory
// description: Unified submission history with tabbed interface for helpdesk tickets and asset loans
// author: dev-team@motac.gov.my
// trace: D03 SRS-FR-001; D04 §4.1; D11 §6
// last-updated: 2025-11-06

declare(strict_types=1);

namespace App\Livewire;

use App\Models\SavedSearch;
use App\Services\SubmissionService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * SubmissionHistory Livewire Component
 *
 * Provides unified interface for viewing user's helpdesk tickets and asset loan applications.
 * Features tabbed navigation, advanced filtering, sorting, pagination, and saved search functionality.
 * Supports claiming guest submissions via email matching.
 *
 * Requirements: D03 SRS-FR-001 §2.1-2.5 (Submission History Management)
 * UI Compliance: D12 §3 (Component Library), D14 §9 (WCAG 2.2 AA)
 */
class SubmissionHistory extends Component
{
    use WithPagination;

    /**
     * Active tab (helpdesk|loans)
     */
    #[Url(as: 'tab')]
    public string $activeTab = 'helpdesk';

    /**
     * Filter: Status
     */
    #[Url(as: 'status')]
    public string $statusFilter = 'all';

    /**
     * Filter: Category (for helpdesk) or Asset Type (for loans)
     */
    #[Url(as: 'category')]
    public string $categoryFilter = 'all';

    /**
     * Filter: Priority (for helpdesk only)
     */
    #[Url(as: 'priority')]
    public string $priorityFilter = 'all';

    /**
     * Filter: Date range start
     */
    #[Url(as: 'from')]
    public ?string $dateFrom = null;

    /**
     * Filter: Date range end
     */
    #[Url(as: 'to')]
    public ?string $dateTo = null;

    /**
     * Filter: Search term
     */
    #[Url(as: 'search')]
    public string $searchTerm = '';

    /**
     * Sort column
     */
    #[Url(as: 'sort')]
    public string $sortBy = 'created_at';

    /**
     * Sort direction
     */
    #[Url(as: 'dir')]
    public string $sortDirection = 'desc';

    /**
     * Items per page
     */
    public int $perPage = 20;

    /**
     * Show save search modal
     */
    public bool $showSaveSearchModal = false;

    /**
     * Saved search name
     */
    public string $savedSearchName = '';

    protected SubmissionService $submissionService;

    /**
     * Initialize component
     */
    public function boot(SubmissionService $submissionService): void
    {
        $this->submissionService = $submissionService;
    }

    /**
     * Reset pagination when filters change
     */
    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    /**
     * Switch tab
     */
    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->clearFilters();
    }

    /**
     * Clear all filters
     */
    public function clearFilters(): void
    {
        $this->statusFilter = 'all';
        $this->categoryFilter = 'all';
        $this->priorityFilter = 'all';
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->searchTerm = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    /**
     * Sort by column
     */
    public function sortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    /**
     * Open save search modal
     */
    public function openSaveSearchModal(): void
    {
        $this->showSaveSearchModal = true;
        $this->savedSearchName = '';
    }

    /**
     * Close save search modal
     */
    public function closeSaveSearchModal(): void
    {
        $this->showSaveSearchModal = false;
        $this->savedSearchName = '';
    }

    /**
     * Save current search
     */
    public function saveSearch(): void
    {
        $this->validate([
            'savedSearchName' => 'required|max:50',
        ], [
            'savedSearchName.required' => __('Nama carian diperlukan'),
            'savedSearchName.max' => __('Nama carian maksimum :max aksara', ['max' => 50]),
        ]);

        $filters = [
            'status' => $this->statusFilter,
            'category' => $this->categoryFilter,
            'priority' => $this->priorityFilter,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'search' => $this->searchTerm,
            'sort_by' => $this->sortBy,
            'sort_direction' => $this->sortDirection,
        ];

        $this->submissionService->saveSearch(
            Auth::user(),
            $this->savedSearchName,
            $this->activeTab,
            $filters
        );

        $this->closeSaveSearchModal();

        session()->flash('success', __('Carian berjaya disimpan'));
    }

    /**
     * Apply saved search
     */
    public function applySavedSearch(int $searchId): void
    {
        $search = SavedSearch::query()
            ->where('id', $searchId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $filters = $search->filters ?? [];

        $this->statusFilter = $filters['status'] ?? 'all';
        $this->categoryFilter = $filters['category'] ?? 'all';
        $this->priorityFilter = $filters['priority'] ?? 'all';
        $this->dateFrom = $filters['date_from'] ?? null;
        $this->dateTo = $filters['date_to'] ?? null;
        $this->searchTerm = $filters['search'] ?? '';
        $this->sortBy = $filters['sort_by'] ?? 'created_at';
        $this->sortDirection = $filters['sort_direction'] ?? 'desc';

        $this->resetPage();

        session()->flash('success', __('Carian berjaya digunakan'));
    }

    /**
     * Delete saved search
     */
    public function deleteSavedSearch(int $searchId): void
    {
        SavedSearch::query()
            ->where('id', $searchId)
            ->where('user_id', Auth::id())
            ->delete();

        session()->flash('success', __('Carian berjaya dipadam'));
    }

    /**
     * Get submissions based on active tab
     */
    #[Computed]
    public function submissions(): LengthAwarePaginator
    {
        $filters = [
            'type' => $this->activeTab,
            'status' => $this->statusFilter !== 'all' ? $this->statusFilter : null,
            'category' => $this->categoryFilter !== 'all' ? $this->categoryFilter : null,
            'priority' => $this->priorityFilter !== 'all' ? $this->priorityFilter : null,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'search' => $this->searchTerm,
        ];

        return $this->submissionService->getSubmissionHistory(
            Auth::user(),
            $filters,
            $this->perPage
        );
    }

    /**
     * Get available statuses for filter dropdown
     */
    #[Computed]
    public function availableStatuses(): array
    {
        if ($this->activeTab === 'helpdesk') {
            return [
                'all' => __('portal.all_statuses'),
                'pending' => __('portal.status_pending'),
                'assigned' => __('portal.status_assigned'),
                'in_progress' => __('portal.status_in_progress'),
                'resolved' => __('portal.status_resolved'),
                'closed' => __('portal.status_closed'),
                'cancelled' => __('portal.status_cancelled'),
            ];
        }

        return [
            'all' => __('portal.all_statuses'),
            'pending' => __('portal.status_pending'),
            'approved' => __('portal.status_approved'),
            'rejected' => __('portal.status_rejected'),
            'active' => __('portal.status_active'),
            'returned' => __('portal.status_returned'),
            'overdue' => __('portal.status_overdue'),
            'cancelled' => __('portal.status_cancelled'),
        ];
    }

    /**
     * Get available categories for filter dropdown
     */
    #[Computed]
    public function availableCategories(): array
    {
        if ($this->activeTab === 'helpdesk') {
            return [
                'all' => __('portal.all_categories'),
                'hardware' => __('helpdesk.categories.hardware'),
                'software' => __('helpdesk.categories.software'),
                'network' => __('helpdesk.categories.network'),
                'email' => __('helpdesk.categories.email'),
                'access' => __('helpdesk.categories.access'),
                'other' => __('helpdesk.categories.other'),
            ];
        }

        return [
            'all' => __('portal.all_asset_types'),
            'computer' => __('asset_loan.categories.computer'),
            'laptop' => __('asset_loan.categories.laptop'),
            'projector' => __('asset_loan.categories.projector'),
            'camera' => __('asset_loan.categories.camera'),
            'printer' => __('asset_loan.categories.printer'),
            'other' => __('asset_loan.categories.other'),
        ];
    }

    /**
     * Get available priorities for filter dropdown
     */
    #[Computed]
    public function availablePriorities(): array
    {
        return [
            'all' => __('portal.all_priorities'),
            'low' => __('portal.priority_low'),
            'medium' => __('portal.priority_medium'),
            'high' => __('portal.priority_high'),
            'urgent' => __('portal.priority_urgent'),
        ];
    }

    /**
     * Get saved searches
     */
    #[Computed]
    public function savedSearches(): array
    {
        return $this->submissionService->getSavedSearches(
            Auth::user(),
            $this->activeTab
        )->toArray();
    }

    /**
     * Check if filters are active
     */
    #[Computed]
    public function hasActiveFilters(): bool
    {
        return $this->statusFilter !== 'all'
            || $this->categoryFilter !== 'all'
            || $this->priorityFilter !== 'all'
            || $this->dateFrom !== null
            || $this->dateTo !== null
            || $this->searchTerm !== '';
    }

    /**
     * Render the submission history component
     */
    public function render()
    {
        return view('livewire.submission-history');
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire\Staff;

use App\Models\SavedSearch;
use App\Services\CrossModuleSearchService;
use App\Services\SearchHistoryService;
use App\Traits\OptimizedLivewireComponent;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Cross-Module Search Component
 *
 * Provides unified search interface across helpdesk tickets and loan applications
 * with real-time search, filtering, pagination, search history, and saved searches.
 *
 * @see D03-FR-011.2 (Cross-module search functionality)
 * @see D04 §5.2 (Cross-Module Search System)
 */
class CrossModuleSearch extends Component
{
    use OptimizedLivewireComponent;
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: 'all')]
    public string $module = 'all';

    #[Url(except: 'all')]
    public string $status = 'all';

    #[Url(except: '')]
    public string $dateFrom = '';

    #[Url(except: '')]
    public string $dateTo = '';

    public int $perPage = 15;

    public bool $showAdvancedFilters = false;

    /** @var array<string> */
    public array $suggestions = [];

    public bool $showSuggestions = false;

    // Search history and saved searches
    public bool $showHistoryPanel = false;

    public bool $showSaveModal = false;

    public string $saveSearchName = '';

    private CrossModuleSearchService $searchService;

    private SearchHistoryService $historyService;

    public function boot(CrossModuleSearchService $searchService, SearchHistoryService $historyService): void
    {
        $this->searchService = $searchService;
        $this->historyService = $historyService;
    }

    #[Computed]
    public function searchResults(): ?\Illuminate\Pagination\LengthAwarePaginator
    {
        if (empty(trim($this->search))) {
            return null;
        }

        $filters = [
            'module' => $this->module,
            'status' => $this->status,
            'date_from' => $this->dateFrom ?: null,
            'date_to' => $this->dateTo ?: null,
        ];

        return $this->searchService->search(
            $this->search,
            $filters,
            $this->perPage,
            $this->getPage()
        );
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        if (strlen($this->search) >= 2) {
            $this->getSuggestions();
        } else {
            $this->suggestions = [];
            $this->showSuggestions = false;
        }
    }

    public function updatedModule(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    public function getSuggestions(): void
    {
        if (strlen($this->search) >= 2) {
            $this->suggestions = $this->searchService->getSuggestions($this->search);
            $this->showSuggestions = ! empty($this->suggestions);
        }
    }

    public function selectSuggestion(string $suggestion): void
    {
        $this->search = $suggestion;
        $this->showSuggestions = false;
        $this->resetPage();
    }

    public function toggleAdvancedFilters(): void
    {
        $this->showAdvancedFilters = ! $this->showAdvancedFilters;
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->module = 'all';
        $this->status = 'all';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->showSuggestions = false;
        $this->resetPage();
    }

    public function exportResults(): void
    {
        if (empty(trim($this->search))) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('staff.search.no_query_to_export'),
            ]);

            return;
        }

        $filters = [
            'module' => $this->module,
            'status' => $this->status,
            'date_from' => $this->dateFrom ?: null,
            'date_to' => $this->dateTo ?: null,
        ];

        // Get all results for export (no pagination)
        $allResults = $this->searchService->search($this->search, $filters, 1000, 1);

        $this->dispatch('export-search-results', [
            'results' => $allResults->items(),
            'query' => $this->search,
            'filters' => $filters,
        ]);
    }

    /**
     * Execute search and record in history.
     */
    public function executeSearch(): void
    {
        if (empty(trim($this->search))) {
            return;
        }

        $this->showSuggestions = false;
        $this->showHistoryPanel = false;

        // Record search in history
        $results = $this->searchResults();
        $resultCount = $results ? $results->total() : 0;

        $this->historyService->recordSearch(
            $this->search,
            $this->getCurrentFilters(),
            $resultCount
        );
    }

    /**
     * Get current filters as array.
     *
     * @return array<string, mixed>
     */
    private function getCurrentFilters(): array
    {
        return [
            'module' => $this->module,
            'status' => $this->status,
            'date_from' => $this->dateFrom ?: null,
            'date_to' => $this->dateTo ?: null,
        ];
    }

    /**
     * Toggle search history panel.
     */
    public function toggleHistoryPanel(): void
    {
        $this->showHistoryPanel = ! $this->showHistoryPanel;
        $this->showSuggestions = false;
    }

    /**
     * Get search history for current user.
     *
     * @return \Illuminate\Support\Collection<int, SavedSearch>
     */
    #[Computed]
    public function searchHistory(): \Illuminate\Support\Collection
    {
        return $this->historyService->getHistory(10);
    }

    /**
     * Get saved searches for current user.
     *
     * @return \Illuminate\Support\Collection<int, SavedSearch>
     */
    #[Computed]
    public function savedSearches(): \Illuminate\Support\Collection
    {
        return $this->historyService->getSavedSearches();
    }

    /**
     * Apply a search from history or saved searches.
     */
    public function applySearch(int $searchId): void
    {
        $savedSearch = SavedSearch::find($searchId);
        $userId = \Illuminate\Support\Facades\Auth::id();
        if (! $savedSearch || ! $userId || $savedSearch->user_id !== $userId) {
            return;
        }

        // Get query from filters
        $this->search = $savedSearch->filters['query'] ?? '';

        // Get search filters from nested structure
        $searchFilters = $savedSearch->filters['search_filters'] ?? [];
        $this->module = $searchFilters['module'] ?? 'all';
        $this->status = $searchFilters['status'] ?? 'all';
        $this->dateFrom = $searchFilters['date_from'] ?? '';
        $this->dateTo = $searchFilters['date_to'] ?? '';

        $this->showHistoryPanel = false;
        $this->historyService->markAsUsed($searchId);
        $this->resetPage();
    }

    /**
     * Open save search modal.
     */
    public function openSaveModal(): void
    {
        if (empty(trim($this->search))) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('staff.search.no_query_to_save'),
            ]);

            return;
        }

        $this->saveSearchName = '';
        $this->showSaveModal = true;
    }

    /**
     * Save current search.
     */
    public function saveCurrentSearch(): void
    {
        if (empty(trim($this->saveSearchName))) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('staff.search.name_required'),
            ]);

            return;
        }

        $saved = $this->historyService->saveSearch(
            $this->saveSearchName,
            $this->search,
            $this->getCurrentFilters()
        );

        if ($saved) {
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('staff.search.search_saved'),
            ]);
            $this->showSaveModal = false;
            $this->saveSearchName = '';
            unset($this->savedSearches);
        } else {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('staff.search.save_failed'),
            ]);
        }
    }

    /**
     * Delete a saved search.
     */
    public function deleteSavedSearch(int $searchId): void
    {
        if ($this->historyService->deleteSavedSearch($searchId)) {
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('staff.search.search_deleted'),
            ]);
            unset($this->savedSearches);
        }
    }

    /**
     * Clear search history.
     */
    public function clearSearchHistory(): void
    {
        $deleted = $this->historyService->clearHistory();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('staff.search.history_cleared', ['count' => $deleted]),
        ]);
        unset($this->searchHistory);
    }

    public function render(): \Illuminate\View\View: \Illuminate\Contracts\View\View
    {
        return view('livewire.staff.cross-module-search', [
            'results' => $this->searchResults(),
        ]);
    }
}

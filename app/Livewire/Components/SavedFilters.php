<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Saved Filters Component
 *
 * Allows users to save and quickly apply filter combinations for tables.
 *
 * @component SavedFilters
 *
 * @description Save and manage filter combinations for tables
 *
 * @author ICTServe Development Team
 *
 * @version 1.0.0
 *
 * @trace D12 §6.14
 *
 * @requirements 23.1, 23.2, 23.5
 */
class SavedFilters extends Component
{
    /**
     * Context identifier for the filter set (e.g., 'helpdesk_tickets', 'loan_applications')
     */
    public string $context = '';

    /**
     * Current active filters
     *
     * @var array<string, mixed>
     */
    public array $currentFilters = [];

    /**
     * List of saved filters for this context
     *
     * @var array<int, array{id: string, name: string, description: string|null, filters: array<string, mixed>, created_at: string}>
     */
    public array $savedFilters = [];

    /**
     * Currently applied saved filter ID
     */
    public ?string $appliedFilterId = null;

    /**
     * Modal state for saving new filter
     */
    public bool $showSaveModal = false;

    /**
     * New filter name
     */
    public string $newFilterName = '';

    /**
     * New filter description
     */
    public string $newFilterDescription = '';

    /**
     * Validation rules
     *
     * @var array<string, string>
     */
    protected array $rules = [
        'newFilterName' => 'required|string|min:2|max:50',
        'newFilterDescription' => 'nullable|string|max:200',
    ];

    /**
     * Initialize component
     */
    

/**
 * @param array<string, mixed> $currentFilters
 */
public function mount(string $context = '', array $currentFilters = []): void
    {
        $this->context = $context;
        $this->currentFilters = $currentFilters;
        $this->loadSavedFilters();
    }

    /**
     * Load saved filters from user preferences
     */
    public function loadSavedFilters(): void
    {
        if (! Auth::check()) {
            $this->savedFilters = [];

            return;
        }

        $user = Auth::user();
        $allFilters = $user->saved_filters ?? [];

        // Get filters for this context
        $this->savedFilters = $allFilters[$this->context] ?? [];
    }

    /**
     * Open save filter modal
     */
    public function openSaveModal(): void
    {
        $this->newFilterName = '';
        $this->newFilterDescription = '';
        $this->showSaveModal = true;
    }

    /**
     * Close save filter modal
     */
    public function closeSaveModal(): void
    {
        $this->showSaveModal = false;
        $this->resetValidation();
    }

    /**
     * Save current filters as a new saved filter
     */
    public function saveFilter(): void
    {
        if (! Auth::check()) {
            $this->dispatch('toast', message: __('Please log in to save filters'), type: 'warning');

            return;
        }

        $this->validate();

        if (empty($this->currentFilters)) {
            $this->dispatch('toast', message: __('No filters to save'), type: 'warning');

            return;
        }

        $user = Auth::user();
        $allFilters = $user->saved_filters ?? [];

        // Initialize context array if not exists
        if (! isset($allFilters[$this->context])) {
            $allFilters[$this->context] = [];
        }

        // Check for duplicate names
        foreach ($allFilters[$this->context] as $filter) {
            if (strtolower($filter['name']) === strtolower($this->newFilterName)) {
                $this->addError('newFilterName', __('A filter with this name already exists'));

                return;
            }
        }

        // Create new filter entry
        $newFilter = [
            'id' => uniqid('filter_'),
            'name' => $this->newFilterName,
            'description' => $this->newFilterDescription ?: null,
            'filters' => $this->currentFilters,
            'created_at' => now()->toIso8601String(),
        ];

        // Add to context filters
        $allFilters[$this->context][] = $newFilter;

        // Save to user
        $user->update(['saved_filters' => $allFilters]);

        // Reload saved filters
        $this->loadSavedFilters();

        // Close modal and notify
        $this->closeSaveModal();
        $this->dispatch('toast', message: __('Filter saved successfully'), type: 'success');
    }

    /**
     * Apply a saved filter
     */
    public function applyFilter(string $filterId): void
    {
        $filter = collect($this->savedFilters)->firstWhere('id', $filterId);

        if (! $filter) {
            $this->dispatch('toast', message: __('Filter not found'), type: 'error');

            return;
        }

        $this->appliedFilterId = $filterId;
        $this->currentFilters = $filter['filters'];

        // Dispatch event to parent component to apply filters
        $this->dispatch('filters-applied', filters: $filter['filters'], filterId: $filterId);
        $this->dispatch('toast', message: __('Filter applied: :name', ['name' => $filter['name']]), type: 'info');
    }

    /**
     * Clear applied filter
     */
    public function clearFilter(): void
    {
        $this->appliedFilterId = null;
        $this->currentFilters = [];

        // Dispatch event to parent component to clear filters
        $this->dispatch('filters-cleared');
    }

    /**
     * Delete a saved filter
     */
    public function deleteFilter(string $filterId): void
    {
        if (! Auth::check()) {
            return;
        }

        $user = Auth::user();
        $allFilters = $user->saved_filters ?? [];

        if (! isset($allFilters[$this->context])) {
            return;
        }

        // Remove filter from context
        $allFilters[$this->context] = array_values(
            array_filter(
                $allFilters[$this->context],
                fn ($filter) => $filter['id'] !== $filterId
            )
        );

        // Save to user
        $user->update(['saved_filters' => $allFilters]);

        // Clear applied filter if it was the deleted one
        if ($this->appliedFilterId === $filterId) {
            $this->appliedFilterId = null;
        }

        // Reload saved filters
        $this->loadSavedFilters();

        $this->dispatch('toast', message: __('Filter deleted'), type: 'success');
    }

    /**
     * Update current filters from parent component
     */
    #[On('update-current-filters')]
    

/**
 * @param array<string, mixed> $filters
 */
public function updateCurrentFilters(array $filters): void
    {
        $this->currentFilters = $filters;

        // Check if current filters match any saved filter
        $this->appliedFilterId = null;
        foreach ($this->savedFilters as $savedFilter) {
            if ($savedFilter['filters'] === $filters) {
                $this->appliedFilterId = $savedFilter['id'];
                break;
            }
        }
    }

    /**
     * Check if there are any active filters
     */
    public function hasActiveFilters(): bool
    {
        return ! empty(array_filter($this->currentFilters));
    }

    /**
     * Get count of saved filters
     */
    public function getSavedFiltersCount(): int
    {
        return count($this->savedFilters);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.components.saved-filters');
    }
}

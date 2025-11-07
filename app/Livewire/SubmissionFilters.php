<?php

declare(strict_types=1);

// name: SubmissionFilters
// description: Reusable filtering component for submission history with multi-select status, date range, category, and priority filters
// author: dev-team@motac.gov.my
// trace: SRS-FR-002; D04 §3.2; D11 §6; Requirements 8.2, 8.3
// last-updated: 2025-11-07

namespace App\Livewire;

use App\Models\AssetCategory;
use App\Models\TicketCategory;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class SubmissionFilters extends Component
{
    /**
     * Filter type: 'helpdesk' or 'loans'
     */
    public string $filterType = 'helpdesk';

    /**
     * Selected statuses (multi-select array)
     */
    #[Modelable]
    public array $selectedStatuses = [];

    /**
     * Date range filters
     */
    #[Modelable]
    public ?string $dateFrom = null;

    #[Modelable]
    public ?string $dateTo = null;

    /**
     * Category filter (helpdesk categories or asset categories)
     */
    #[Modelable]
    public ?int $selectedCategory = null;

    /**
     * Priority filter (helpdesk only)
     */
    #[Modelable]
    public ?string $selectedPriority = null;

    /**
     * Available status options based on filter type
     */
    #[Computed]
    public function availableStatuses(): array
    {
        if ($this->filterType === 'helpdesk') {
            return [
                'open' => __('portal.status_open'),
                'assigned' => __('portal.status_assigned'),
                'in_progress' => __('portal.status_in_progress'),
                'pending' => __('portal.status_pending'),
                'resolved' => __('portal.status_resolved'),
                'closed' => __('portal.status_closed'),
                'cancelled' => __('portal.status_cancelled'),
            ];
        }

        // Loans
        return [
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
     * Available categories based on filter type
     */
    #[Computed]
    public function availableCategories(): Collection
    {
        if ($this->filterType === 'helpdesk') {
            return TicketCategory::query()
                ->where('is_active', true)
                ->orderBy('name_en')
                ->get(['id', 'name_en', 'name_ms']);
        }

        // Asset categories for loans
        return AssetCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * Available priority options (helpdesk only)
     */
    #[Computed]
    public function availablePriorities(): array
    {
        return [
            'low' => __('portal.priority_low'),
            'medium' => __('portal.priority_medium'),
            'high' => __('portal.priority_high'),
            'urgent' => __('portal.priority_urgent'),
        ];
    }

    /**
     * Toggle status selection
     */
    public function toggleStatus(string $status): void
    {
        if (in_array($status, $this->selectedStatuses, true)) {
            $this->selectedStatuses = array_values(
                array_filter(
                    $this->selectedStatuses,
                    fn (string $s): bool => $s !== $status
                )
            );
        } else {
            $this->selectedStatuses[] = $status;
        }
    }

    /**
     * Select all statuses
     */
    public function selectAllStatuses(): void
    {
        $this->selectedStatuses = array_keys($this->availableStatuses);
    }

    /**
     * Deselect all statuses
     */
    public function deselectAllStatuses(): void
    {
        $this->selectedStatuses = [];
    }

    /**
     * Check if any filters are active
     */
    public function getHasActiveFiltersProperty(): bool
    {
        return ! empty($this->selectedStatuses)
            || $this->dateFrom !== null
            || $this->dateTo !== null
            || $this->selectedCategory !== null
            || $this->selectedPriority !== null;
    }

    /**
     * Clear all filters
     */
    public function clearFilters(): void
    {
        $this->selectedStatuses = [];
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->selectedCategory = null;
        $this->selectedPriority = null;

        // Dispatch event to notify parent component
        $this->dispatch('filters-cleared');
    }

    /**
     * Apply filters
     */
    public function applyFilters(): void
    {
        // Dispatch event to notify parent component
        $this->dispatch('filters-applied', [
            'statuses' => $this->selectedStatuses,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'category' => $this->selectedCategory,
            'priority' => $this->selectedPriority,
        ]);
    }

    /**
     * Get active filter count
     */
    public function getActiveFilterCountProperty(): int
    {
        $count = 0;

        if (! empty($this->selectedStatuses)) {
            $count++;
        }
        if ($this->dateFrom !== null || $this->dateTo !== null) {
            $count++;
        }
        if ($this->selectedCategory !== null) {
            $count++;
        }
        if ($this->selectedPriority !== null) {
            $count++;
        }

        return $count;
    }

    /**
     * Render the component
     */
    public function render(): mixed
    {
        return view('livewire.submission-filters');
    }
}

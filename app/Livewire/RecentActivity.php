<?php

// name: RecentActivity
// description: Dedicated activity feed with filtering capabilities
// author: dev-team@motac.gov.my
// trace: D03 SRS-FR-001; D04 §4.1; D11 §6
// last-updated: 2025-11-06

declare(strict_types=1);

namespace App\Livewire;

use App\Models\PortalActivity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * RecentActivity Livewire Component
 *
 * Provides a detailed, filterable view of user's portal activities.
 * Supports filtering by activity type, date range, and search.
 * Includes pagination for large activity logs.
 *
 * Requirements: D03 SRS-FR-001 §8.1-8.5 (Activity Tracking)
 * UI Compliance: D12 §3 (Component Library), D14 §9 (WCAG 2.2 AA)
 */
class RecentActivity extends Component
{
    use WithPagination;

    /**
     * Filter: Activity type (submission, login, update, export, claim)
     */
    public string $activityType = 'all';

    /**
     * Filter: Date range start
     */
    public ?string $dateFrom = null;

    /**
     * Filter: Date range end
     */
    public ?string $dateTo = null;

    /**
     * Filter: Search term for metadata/description
     */
    public string $search = '';

    /**
     * Number of items per page
     */
    public int $perPage = 20;

    /**
     * Reset pagination when filters change
     */
    public function updatingActivityType(): void
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

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Clear all filters
     */
    public function clearFilters(): void
    {
        $this->activityType = 'all';
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->search = '';
        $this->resetPage();
    }

    /**
     * Get filtered activities
     */
    public function getActivitiesProperty(): LengthAwarePaginator
    {
        $query = PortalActivity::query()
            ->with('user', 'subject')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        // Filter by activity type
        if ($this->activityType !== 'all') {
            $query->where('activity_type', $this->activityType);
        }

        // Filter by date range
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // Search in metadata (JSON search)
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('activity_type', 'like', "%{$this->search}%")
                    ->orWhereRaw("JSON_SEARCH(metadata, 'one', ?) IS NOT NULL", ["%{$this->search}%"]);
            });
        }

        return $query->paginate($this->perPage);
    }

    /**
     * Get available activity types for filter dropdown
     */
    public function getAvailableActivityTypesProperty(): array
    {
        return [
            'all' => __('Semua Aktiviti'),
            'submission' => __('Permohonan'),
            'login' => __('Log Masuk'),
            'update' => __('Kemaskini'),
            'export' => __('Eksport'),
            'claim' => __('Tuntutan'),
            'approval' => __('Kelulusan'),
            'comment' => __('Komen'),
        ];
    }

    /**
     * Render the recent activity component
     */
    public function render()
    {
        return view('livewire.recent-activity', [
            'activities' => $this->activities,
            'availableActivityTypes' => $this->availableActivityTypes,
        ]);
    }
}

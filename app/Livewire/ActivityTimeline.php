<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\PortalActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityTimeline extends Component
{
    use WithPagination;

    // Filter Properties
    public array $selectedTypes = [];

    public string $dateFilter = 'all'; // 'today', 'week', 'month', 'all'

    public ?string $searchQuery = null;

    // Available Activity Types
    public array $activityTypes = [
        'login' => 'Login',
        'logout' => 'Logout',
        'ticket_created' => 'Ticket Created',
        'ticket_updated' => 'Ticket Updated',
        'ticket_claimed' => 'Ticket Claimed',
        'ticket_resolved' => 'Ticket Resolved',
        'loan_created' => 'Loan Created',
        'loan_approved' => 'Loan Approved',
        'loan_declined' => 'Loan Declined',
        'loan_returned' => 'Loan Returned',
        'comment_added' => 'Comment Added',
        'export_generated' => 'Export Generated',
    ];

    // Date Filter Options
    public array $dateFilterOptions = [
        'today' => 'Today',
        'week' => 'This Week',
        'month' => 'This Month',
        'all' => 'All Time',
    ];

    /**
     * Toggle activity type filter
     */
    public function toggleType(string $type): void
    {
        if (in_array($type, $this->selectedTypes, true)) {
            $this->selectedTypes = array_values(array_diff($this->selectedTypes, [$type]));
        } else {
            $this->selectedTypes[] = $type;
        }

        $this->resetPage();
    }

    /**
     * Clear all filters
     */
    public function clearFilters(): void
    {
        $this->selectedTypes = [];
        $this->dateFilter = 'all';
        $this->searchQuery = null;
        $this->resetPage();
    }

    /**
     * Updated search query - reset pagination
     */
    public function updatedSearchQuery(): void
    {
        $this->resetPage();
    }

    /**
     * Updated date filter - reset pagination
     */
    public function updatedDateFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Render component
     */
    public function render(): View
    {
        // Build query for portal activities
        $query = PortalActivity::with('user')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        // Apply activity type filters
        if (! empty($this->selectedTypes)) {
            $query->whereIn('activity_type', $this->selectedTypes);
        }

        // Apply date filters
        match ($this->dateFilter) {
            'today' => $query->whereDate('created_at', today()),
            'week' => $query->where('created_at', '>=', now()->startOfWeek()),
            'month' => $query->where('created_at', '>=', now()->startOfMonth()),
            default => null,
        };

        // Apply search query
        if ($this->searchQuery) {
            $query->where(function ($q) {
                $q->where('activity_description', 'like', '%'.$this->searchQuery.'%')
                    ->orWhere('activity_type', 'like', '%'.$this->searchQuery.'%')
                    ->orWhere('ip_address', 'like', '%'.$this->searchQuery.'%');
            });
        }

        // Paginate results
        $activities = $query->paginate(20);

        // Transform activities for timeline display
        $activities->getCollection()->transform(function ($activity) {
            return [
                'id' => $activity->id,
                'type' => $activity->activity_type,
                'description' => $activity->activity_description ?? $this->activityTypes[$activity->activity_type] ?? 'Activity',
                'timestamp' => $activity->created_at,
                'ip' => $activity->ip_address,
                'user_agent' => $activity->user_agent,
                'metadata' => $activity->metadata,
                'color' => $this->getActivityColor($activity->activity_type),
                'icon' => $this->getActivityIcon($activity->activity_type),
            ];
        });

        return view('livewire.activity-timeline', [
            'activities' => $activities,
            'hasActiveFilters' => ! empty($this->selectedTypes) || $this->dateFilter !== 'all' || $this->searchQuery !== null,
        ]);
    }

    /**
     * Get color class for activity type
     */
    private function getActivityColor(string $type): string
    {
        return match ($type) {
            'login', 'ticket_claimed', 'loan_approved' => 'green',
            'logout', 'ticket_resolved', 'loan_returned' => 'blue',
            'ticket_created', 'loan_created' => 'indigo',
            'ticket_updated', 'comment_added' => 'gray',
            'loan_declined' => 'red',
            'export_generated' => 'purple',
            default => 'gray',
        };
    }

    /**
     * Get icon path for activity type
     */
    private function getActivityIcon(string $type): string
    {
        return match ($type) {
            'login' => 'login',
            'logout' => 'logout',
            'ticket_created', 'ticket_updated', 'ticket_claimed' => 'ticket',
            'ticket_resolved' => 'check-circle',
            'loan_created', 'loan_approved', 'loan_returned' => 'document',
            'loan_declined' => 'x-circle',
            'comment_added' => 'chat',
            'export_generated' => 'download',
            default => 'information-circle',
        };
    }
}

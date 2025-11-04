<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use App\Services\HybridHelpdeskService;
use App\Traits\OptimizedLivewireComponent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * My Tickets Component
 *
 * Displays both claimed guest and authenticated submissions with filtering,
 * sorting, and search capabilities. Includes ticket claiming functionality.
 *
 * @trace Requirements 7.2, 1.4
 *
 * @wcag WCAG 2.2 AA compliant with proper ARIA labels
 */
class MyTickets extends Component
{
    use OptimizedLivewireComponent;
    use WithPagination;

    /**
     * Define relationships to eager load for N+1 prevention
     */
    protected function getEagerLoadRelationships(): array
    {
        return ['category', 'assignedUser', 'user'];
    }

    #[Validate('nullable|string|max:255')]
    public ?string $search = null;

    #[Validate('nullable|in:all,open,resolved,closed,pending')]
    public string $statusFilter = 'all';

    #[Validate('nullable|in:all,guest,authenticated')]
    public string $submissionTypeFilter = 'all';

    #[Validate('nullable|integer|exists:ticket_categories,id')]
    public ?int $categoryFilter = null;

    #[Validate('nullable|in:asc,desc')]
    public string $sortDirection = 'desc';

    #[Validate('nullable|in:created_at,updated_at,status')]
    public string $sortBy = 'created_at';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSubmissionTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSortBy(): void
    {
        $this->resetPage();
    }

    public function claim(int $ticketId): void
    {
        $ticket = HelpdeskTicket::findOrFail($ticketId);
        $user = Auth::user();

        $success = app(HybridHelpdeskService::class)->claimGuestTicket($ticket, $user);

        if ($success) {
            $this->dispatch('ticket-claimed');
            session()->flash('success', __('Tiket berjaya dituntut.'));
        } else {
            session()->flash('error', __('Tiket tidak dapat dituntut. Sila cuba lagi.'));
        }
    }

    #[Computed]
    public function categories(): Collection
    {
        return TicketCategory::query()
            ->orderBy('name')
            ->get(['id', 'name'])->layout('layouts.portal');
    }

    #[Computed]
    public function tickets(): LengthAwarePaginator
    {
        $user = Auth::user();
        $service = app(HybridHelpdeskService::class);

        $query = $service->getUserAccessibleTickets($user)
            ->when($this->statusFilter !== 'all', function ($query) {
                if ($this->statusFilter === 'pending') {
                    $query->where('status', 'pending_user');
                } else {
                    $query->where('status', $this->statusFilter);
                }
            })
            ->when($this->submissionTypeFilter !== 'all', function ($query) use ($user) {
                if ($this->submissionTypeFilter === 'guest') {
                    $query->whereNull('user_id')
                        ->where('guest_email', $user->email);
                } elseif ($this->submissionTypeFilter === 'authenticated') {
                    $query->where('user_id', $user->id);
                }
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('ticket_number', 'like', '%'.$this->search.'%')
                        ->orWhere('subject', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        // Apply eager loading and return paginated results
        return $this->getOptimizedPaginatedResults($query, 15);
    }

    #[Computed]
    public function ticketStats(): array
    {
        $user = Auth::user();
        $service = app(HybridHelpdeskService::class);
        $query = $service->getUserAccessibleTickets($user);

        return $this->getCachedComponentData('ticket_stats', function () use ($query, $user) {
            return [
                'total' => $this->getOptimizedCount(clone $query, 'total_count'),
                'open' => $this->getOptimizedCount((clone $query)->whereNotIn('status', ['resolved', 'closed']), 'open_count'),
                'resolved' => $this->getOptimizedCount((clone $query)->where('status', 'resolved'), 'resolved_count'),
                'guest' => $this->getOptimizedCount((clone $query)->whereNull('user_id')->where('guest_email', $user->email), 'guest_count'),
                'authenticated' => $this->getOptimizedCount((clone $query)->where('user_id', $user->id), 'authenticated_count'),
            ];
        }, 60); // Cache stats for 1 minute
    }

    public function render()
    {
        return view('livewire.helpdesk.my-tickets')->layout('layouts.portal');
    }
}

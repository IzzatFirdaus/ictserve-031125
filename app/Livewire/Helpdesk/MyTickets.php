<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\HybridHelpdeskService;
use App\Traits\OptimizedLivewireComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
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
     *
     * @return array<int, string>
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
        assert($user instanceof User);

        $success = app(HybridHelpdeskService::class)->claimGuestTicket($ticket, $user);

        if ($success) {
            $this->dispatch('ticket-claimed');
            session()->flash('success', __('Tiket berjaya dituntut.'));
        } else {
            session()->flash('error', __('Tiket tidak dapat dituntut. Sila cuba lagi.'));
        }
    }

    /**
     * @return \Illuminate\Support\Collection<int, TicketCategory>
     */
    #[Computed]
    public function categories(): Collection
    {
        /** @var Collection<int, TicketCategory> $categories */
        $categories = TicketCategory::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return $categories;
    }

    /**
     * @return LengthAwarePaginator<int, HelpdeskTicket>
     */
    #[Computed]
    public function tickets(): LengthAwarePaginator
    {
        $user = Auth::user();
        assert($user instanceof User);
        $service = app(HybridHelpdeskService::class);

        /** @var Builder<HelpdeskTicket> $query */
        $query = $service->getUserAccessibleTickets($user)
            ->when($this->statusFilter !== 'all', function (Builder $query): void {
                if ($this->statusFilter === 'pending') {
                    $query->where('status', 'pending_user');
                } else {
                    $query->where('status', $this->statusFilter);
                }
            })
            ->when($this->submissionTypeFilter !== 'all', function (Builder $query) use ($user): void {
                if ($this->submissionTypeFilter === 'guest') {
                    $query->whereNull('user_id')
                        ->where('guest_email', $user->email);
                } elseif ($this->submissionTypeFilter === 'authenticated') {
                    $query->where('user_id', $user->id);
                }
            })
            ->when($this->categoryFilter, function (Builder $query): void {
                $query->where('category_id', $this->categoryFilter);
            })
            ->when($this->search, function (Builder $query): void {
                $query->where(function (Builder $subQuery): void {
                    $subQuery->where('ticket_number', 'like', '%'.$this->search.'%')
                        ->orWhere('subject', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        // Apply eager loading and return paginated results
        return $this->getOptimizedPaginatedResults($query, 15);
    }

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function ticketStats(): array
    {
        $user = Auth::user();
        assert($user instanceof User);
        $service = app(HybridHelpdeskService::class);
        /** @var Builder<HelpdeskTicket> $query */
        $query = $service->getUserAccessibleTickets($user);

        /** @var array<string, int> $stats */
        $stats = $this->getCachedComponentData('ticket_stats', function () use ($query, $user) {
            return [
                'total' => $this->getOptimizedCount(clone $query, 'total_count'),
                'open' => $this->getOptimizedCount((clone $query)->whereNotIn('status', ['resolved', 'closed']), 'open_count'),
                'resolved' => $this->getOptimizedCount((clone $query)->where('status', 'resolved'), 'resolved_count'),
                'guest' => $this->getOptimizedCount((clone $query)->whereNull('user_id')->where('guest_email', $user->email), 'guest_count'),
                'authenticated' => $this->getOptimizedCount((clone $query)->where('user_id', $user->id), 'authenticated_count'),
            ];
        }, 60); // Cache stats for 1 minute

        return is_array($stats) ? $stats : [];
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.helpdesk.my-tickets')->layout('layouts.portal');
    }
}

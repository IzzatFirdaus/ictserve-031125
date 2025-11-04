<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Services\HybridHelpdeskService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class MyTickets extends Component
{
    use WithPagination;

    #[Validate('nullable|string|max:255')]
    public ?string $search = null;

    #[Validate('nullable|in:all,open,resolved,closed,pending')]
    public string $statusFilter = 'all';

    public string $sortDirection = 'desc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function claim(int $ticketId): void
    {
        $ticket = HelpdeskTicket::findOrFail($ticketId);
        $user = Auth::user();

        app(HybridHelpdeskService::class)->claimGuestTicket($ticket, $user);

        $this->dispatch('ticket-claimed');
    }

    #[Computed]
    public function tickets(): LengthAwarePaginator
    {
        $user = Auth::user();

        return HelpdeskTicket::query()
            ->with(['category', 'assignedUser'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere(function ($guest) use ($user) {
                        $guest->whereNull('user_id')
                            ->where('guest_email', $user->email);
                    });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                if ($this->statusFilter === 'pending') {
                    $query->where('status', 'pending_user');
                } else {
                    $query->where('status', $this->statusFilter);
                }
            })
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('ticket_number', 'like', '%'.$this->search.'%')
                        ->orWhere('subject', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('created_at', $this->sortDirection)
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.helpdesk.my-tickets');
    }
}

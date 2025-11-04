<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component
{
    /**
     * @var array<string, int>
     */
    public array $stats = [];

    public Collection $recentTickets;

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);

        $this->loadData();
    }

    #[On('ticket-refresh-requested')]
    public function loadData(): void
    {
        $user = Auth::user();

        $query = HelpdeskTicket::query()
            ->where(function ($builder) use ($user) {
                $builder->where('user_id', $user->id)
                    ->orWhere(function ($sub) use ($user) {
                        $sub->whereNull('user_id')
                            ->where('guest_email', $user->email);
                    });
            });

        $this->stats = [
            'open' => (clone $query)->whereNotIn('status', ['resolved', 'closed'])->count(),
            'pending' => (clone $query)->where('status', 'pending_user')->count(),
            'resolved' => (clone $query)->where('status', 'resolved')->count(),
            'claimable' => HelpdeskTicket::query()
                ->whereNull('user_id')
                ->where('guest_email', $user->email)
                ->count(),
        ];

        $this->recentTickets = (clone $query)
            ->with(['category', 'assignedUser'])
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.helpdesk.dashboard');
    }
}

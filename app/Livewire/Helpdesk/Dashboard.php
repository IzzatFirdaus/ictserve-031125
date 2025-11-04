<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Services\HybridHelpdeskService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Authenticated Portal Dashboard Component
 *
 * Displays personalized statistics, recent activity feed, and quick actions
 * for authenticated staff members accessing the helpdesk portal.
 *
 * @trace Requirements 7.1, 7.2
 *
 * @wcag WCAG 2.2 AA compliant with proper ARIA labels
 */
class Dashboard extends Component
{
    /**
     * @var array<string, int>
     */
    public array $stats = [];

    public Collection $recentTickets;

    public Collection $recentActivity;

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);

        $this->loadData();
    }

    #[On('ticket-refresh-requested')]
    #[On('ticket-claimed')]
    public function loadData(): void
    {
        $user = Auth::user();
        $service = app(HybridHelpdeskService::class);

        $query = $service->getUserAccessibleTickets($user);

        // Personalized statistics
        $this->stats = [
            'my_open' => (clone $query)->whereNotIn('status', ['resolved', 'closed'])->count(),
            'my_resolved' => (clone $query)->where('status', 'resolved')->count(),
            'claimed' => (clone $query)->where('user_id', $user->id)->whereNotNull('guest_email')->count(),
            'claimable' => HelpdeskTicket::query()
                ->whereNull('user_id')
                ->where('guest_email', $user->email)
                ->count(),
        ];

        // Recent tickets (last 5)
        $this->recentTickets = (clone $query)
            ->with(['category', 'assignedUser'])
            ->latest()
            ->limit(5)
            ->get();

        // Recent activity feed (last 10 updates)
        $this->recentActivity = (clone $query)
            ->with(['category', 'assignedUser'])
            ->where('updated_at', '>=', now()->subDays(7))
            ->latest('updated_at')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function quickActions(): array
    {
        return [
            [
                'label' => __('Create Ticket'),
                'route' => 'helpdesk.authenticated.create',
                'icon' => 'heroicon-o-plus-circle',
                'color' => 'primary',
            ],
            [
                'label' => __('View All Tickets'),
                'route' => 'helpdesk.authenticated.tickets',
                'icon' => 'heroicon-o-ticket',
                'color' => 'secondary',
            ],
            [
                'label' => __('Claim Tickets'),
                'route' => 'helpdesk.authenticated.claim',
                'icon' => 'heroicon-o-hand-raised',
                'color' => 'secondary',
                'badge' => $this->stats['claimable'] ?? 0,
            ],
        ];
    }

    public function render()
    {
        return view('livewire.helpdesk.dashboard');
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Services\HybridHelpdeskService;
use App\Traits\OptimizedLivewireComponent;
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
    use OptimizedLivewireComponent;

    /**
     * @var array<string, int>
     */
    public array $stats = [];

    public Collection $recentTickets;

    public Collection $recentActivity;

    /**
     * Define relationships to eager load for N+1 prevention
     */
    protected function getEagerLoadRelationships(): array
    {
        return ['category', 'assignedUser', 'user'];
    }

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

        // Personalized statistics with caching
        $this->stats = $this->getCachedComponentData('stats', function () use ($query, $user) {
            return [
                'my_open' => $this->getOptimizedCount((clone $query)->whereNotIn('status', ['resolved', 'closed']), 'my_open_count'),
                'my_resolved' => $this->getOptimizedCount((clone $query)->where('status', 'resolved'), 'my_resolved_count'),
                'claimed' => $this->getOptimizedCount((clone $query)->where('user_id', $user->id)->whereNotNull('guest_email'), 'claimed_count'),
                'claimable' => $this->getOptimizedCount(
                    HelpdeskTicket::query()
                        ->whereNull('user_id')
                        ->where('guest_email', $user->email),
                    'claimable_count'
                ),
            ];
        }, 60); // Cache stats for 1 minute

        // Recent tickets (last 5) with eager loading
        $this->recentTickets = $this->getCachedComponentData('recent_tickets', function () use ($query) {
            return $this->applyEagerLoading(clone $query)
                ->latest()
                ->limit(5)
                ->get();
        }, 60);

        // Recent activity feed (last 10 updates) with eager loading
        $this->recentActivity = $this->getCachedComponentData('recent_activity', function () use ($query) {
            return $this->applyEagerLoading(clone $query)
                ->where('updated_at', '>=', now()->subDays(7))
                ->latest('updated_at')
                ->limit(10)
                ->get();
        }, 60);
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
        return view('livewire.helpdesk.dashboard')->layout('layouts.portal');
    }
}

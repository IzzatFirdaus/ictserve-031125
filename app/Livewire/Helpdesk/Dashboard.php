<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Services\HybridHelpdeskService;
use App\Traits\OptimizedLivewireComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
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

    /**
     * @var Collection<int, HelpdeskTicket>
     */
    public Collection $recentTickets;

    /**
     * @var Collection<int, HelpdeskTicket>
     */
    public Collection $recentActivity;

    /**
     * Define relationships to eager load for N+1 prevention
     */
    /**
     * @return array<int, string>
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
        assert($user instanceof User);

        $service = app(HybridHelpdeskService::class);
        /** @var Builder<HelpdeskTicket> $query */
        $query = $service->getUserAccessibleTickets($user);

        // Personalized statistics with caching
        /** @var array<string, int> $stats */
        $stats = $this->getCachedComponentData('stats', function () use ($query, $user) {
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
        $this->stats = $stats;

        // Recent tickets (last 5) with eager loading
        /** @var Collection<int, HelpdeskTicket> $recentTickets */
        $recentTickets = $this->getCachedComponentData('recent_tickets', function () use ($query) {
            return $this->applyEagerLoading(clone $query)
                ->latest()
                ->limit(5)
                ->get();
        }, 60);
        $this->recentTickets = $recentTickets;

        // Recent activity feed (last 10 updates) with eager loading
        /** @var Collection<int, HelpdeskTicket> $recentActivity */
        $recentActivity = $this->getCachedComponentData('recent_activity', function () use ($query) {
            return $this->applyEagerLoading(clone $query)
                ->where('updated_at', '>=', now()->subDays(7))
                ->latest('updated_at')
                ->limit(10)
                ->get();
        }, 60);
        $this->recentActivity = $recentActivity;
    }

    /**
     * @return array<int, array{label: string, route: string, icon: string, color: string, badge?: int}>
     */
    #[Computed]
    public function quickActions(): array
    {
        return [
            [
                'label' => __('common.new_ticket'),
                'route' => 'helpdesk.create',
                'icon' => 'heroicon-o-plus-circle',
                'color' => 'primary',
            ],
            [
                'label' => __('common.view_all_tickets'),
                'route' => 'helpdesk.authenticated.tickets',
                'icon' => 'heroicon-o-ticket',
                'color' => 'secondary',
            ],
            [
                'label' => __('common.claim_submissions'),
                'route' => 'helpdesk.authenticated.tickets',
                'icon' => 'heroicon-o-hand-raised',
                'color' => 'secondary',
                'badge' => $this->stats['claimable'] ?? 0,
            ],
        ];
    }

    #[Layout('layouts.portal')]
    public function render(): View
    {
        return view('livewire.helpdesk.dashboard');
    }
}

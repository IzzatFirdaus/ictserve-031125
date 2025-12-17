<?php

declare(strict_types=1);

namespace App\Livewire\Staff;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Traits\OptimizedLivewireComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Authenticated Staff Dashboard Component
 *
 * Unified dashboard for authenticated MOTAC staff showing personalized statistics,
 * recent activity from both helpdesk and asset loan modules, and quick action buttons.
 *
 * Features:
 * - Personalized statistics (My Open Tickets, My Pending Loans, My Approvals, Overdue Items)
 * - Real-time updates via Laravel Reverb WebSocket per D12 §2
 * - Recent activity feed (tickets and loans)
 * - Quick action buttons for common tasks
 * - Role-based content (Grade 41+ approval statistics)
 * - WCAG 2.2 Level AA compliant
 * - OptimizedLivewireComponent trait for performance
 * - shadow-card styling per D14 §7.5
 *
 * @see D03-FR-019.1 Staff dashboard with personalized statistics
 * @see D03-FR-019.2 Recent activity display
 * @see D03-FR-019.3 Quick action buttons
 * @see D03-FR-024.2 Performance optimization with caching
 * @see D04 §6.2 Authenticated portal Livewire components
 * @see D10 §7 Livewire component documentation
 * @see D12 §2 Real-time features with Laravel Reverb
 * @see D12 §6.4 Dashboard statistics cards
 * @see D12 §9 WCAG 2.2 AA dashboard compliance
 * @see D14 §7.5 Shadow tokens
 *
 * @requirements 4.1, 19.1, 19.2, 19.3, 19.4, 19.5, 24.2, 24.3
 *
 * @wcag-level AA
 *
 * @version 2.0.0
 *
 * @updated 2025-12-05
 *
 * @author Frontend Engineering Team
 */
#[Layout('layouts.portal')]
class AuthenticatedDashboard extends Component
{
    use OptimizedLivewireComponent;

    /**
     * Activity filter type (all, tickets, loans)
     * Task 4.2.3: Create recent activity feed with filtering options
     */
    #[Url(as: 'filter')]
    public string $activityFilter = 'all';

    /**
     * Get Echo listeners for real-time updates via Laravel Reverb per D12 §2
     *
     * @return array<string, string>
     */
    public function getListeners(): array
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return ['dashboard-refresh' => 'refreshData'];
        }

        return [
            // Private channel for user-specific updates
            "echo-private:App.Models.User.{$user->id},TicketStatusUpdated" => 'handleTicketUpdate',
            "echo-private:App.Models.User.{$user->id},LoanStatusUpdated" => 'handleLoanUpdate',
            "echo-private:App.Models.User.{$user->id},notification" => 'handleNotification',
            // Dashboard refresh event
            'dashboard-refresh' => 'refreshData',
        ];
    }

    /**
     * Handle ticket status update from Laravel Reverb broadcast
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo-private:TicketStatusUpdated')]
    public function handleTicketUpdate(array $event): void
    {
        // Clear ticket-related caches
        $this->invalidateComponentCache();
        unset($this->statistics);
        unset($this->recentTickets);

        // Dispatch toast notification
        $ticketNumber = $event['ticket_number'] ?? '';
        $newStatus = $event['status'] ?? '';
        $this->dispatch('toast', message: __('dashboard.ticket_updated', ['number' => $ticketNumber, 'status' => $newStatus]), type: 'info');
    }

    /**
     * Handle loan status update from Laravel Reverb broadcast
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo-private:LoanStatusUpdated')]
    public function handleLoanUpdate(array $event): void
    {
        // Clear loan-related caches
        $this->invalidateComponentCache();
        unset($this->statistics);
        unset($this->recentLoans);

        // Dispatch toast notification
        $applicationNumber = $event['application_number'] ?? '';
        $newStatus = $event['status'] ?? '';
        $this->dispatch('toast', message: __('dashboard.loan_updated', ['number' => $applicationNumber, 'status' => $newStatus]), type: 'info');
    }

    /**
     * Handle general notification from Laravel Reverb broadcast
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo-private:notification')]
    public function handleNotification(array $event): void
    {
        // Refresh all data on notification
        $this->refreshData();
    }

    /**
     * Available activity filter options
     *
     * @var array<string, string>
     */
    public array $filterOptions = [
        'all' => 'All Activity',
        'tickets' => 'Tickets Only',
        'loans' => 'Loans Only',
    ];

    /**
     * Set activity filter
     */
    public function setActivityFilter(string $filter): void
    {
        if (\array_key_exists($filter, $this->filterOptions)) {
            $this->activityFilter = $filter;
            // Clear cached data to refresh with new filter
            unset($this->recentTickets);
            unset($this->recentLoans);
        }
    }

    /**
     * Get authenticated user
     */
    protected function getUser(): User
    {
        $user = Auth::user();
        assert($user instanceof User);

        return $user;
    }

    /**
     * Get relationships to eager load for preventing N+1 queries
     *
     * @return array<int, string>
     */
    protected function getEagerLoadRelationships(): array
    {
        return [
            'user:id,name,email',
            'assignedUser:id,name',
            'division:id,name_ms,name_en',
            'asset:id,name,model',
            'loanItems.asset:id,name,model',
        ];
    }

    /**
     * Get dashboard statistics with caching
     *
     * Returns personalized statistics for the authenticated user:
     * - My Open Tickets: Count of open helpdesk tickets
     * - My Pending Loans: Count of pending loan applications
     * - My Approvals: Count of pending approvals (Grade 41+ only)
     * - Overdue Items: Count of overdue loan returns
     *
     * @return array<string, int>
     */
    #[Computed]
    public function statistics(): array
    {
        /** @var array<string, int> $stats */
        $stats = $this->getCachedComponentData('statistics', function () {
            $user = $this->getUser();

            $stats = [
                'open_tickets' => $this->getOpenTicketsCount($user),
                'pending_loans' => $this->getPendingLoansCount($user),
                'overdue_items' => $this->getOverdueItemsCount($user),
            ];

            // Add approval count for Grade 41+ users
            if ($this->isApprover($user)) {
                $stats['pending_approvals'] = $this->getPendingApprovalsCount();
            }

            return $stats;
        }, 300); // Cache for 5 minutes

        return $stats;
    }

    /**
     * Get recent helpdesk tickets (max 5)
     *
     * Returns the 5 most recent helpdesk tickets for the authenticated user,
     * including both tickets created by the user and tickets assigned to them.
     *
     * @return EloquentCollection<int, HelpdeskTicket>
     */
    #[Computed]
    public function recentTickets(): EloquentCollection
    {
        /** @var EloquentCollection<int, HelpdeskTicket> $tickets */
        $tickets = $this->getCachedComponentData('recent_tickets', function () {
            $user = $this->getUser();

            return HelpdeskTicket::query()
                ->where(function (Builder $query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhere('assigned_to_user', $user->id);
                })
                ->with(['user:id,name', 'assignedUser:id,name', 'division:id,name_ms,name_en'])
                ->latest()
                ->limit(5)
                ->get();
        }, 300); // Cache for 5 minutes

        return $tickets;
    }

    /**
     * Get recent loan applications (max 5)
     *
     * Returns the 5 most recent loan applications for the authenticated user.
     *
     * @return EloquentCollection<int, LoanApplication>
     */
    #[Computed]
    public function recentLoans(): EloquentCollection
    {
        /** @var EloquentCollection<int, LoanApplication> $loans */
        $loans = $this->getCachedComponentData('recent_loans', function () {
            $user = $this->getUser();

            return LoanApplication::query()
                ->where('user_id', $user->id)
                ->with(['loanItems.asset:id,name,model', 'division:id,name_ms,name_en'])
                ->latest()
                ->limit(5)
                ->get();
        }, 300); // Cache for 5 minutes

        return $loans;
    }

    /**
     * Get recent portal activities (max 10)
     *
     * Returns the 10 most recent portal activities for the authenticated user,
     * including ticket submissions, status changes, loan activities, etc.
     *
     * @return EloquentCollection<int, \App\Models\PortalActivity>
     */
    #[Computed]
    public function recentActivities(): EloquentCollection
    {
        /** @var EloquentCollection<int, \App\Models\PortalActivity> $activities */
        $activities = $this->getCachedComponentData('recent_activities', function () {
            $user = $this->getUser();

            return \App\Models\PortalActivity::query()
                ->where('user_id', $user->id)
                ->with(['user:id,name', 'subject'])
                ->latest()
                ->limit(10)
                ->get();
        }, 300); // Cache for 5 minutes

        return $activities;
    }

    /**
     * Get count of open tickets for user
     */
    protected function getOpenTicketsCount(User $user): int
    {
        /** @var Builder<HelpdeskTicket> $query */
        $query = HelpdeskTicket::query()
            ->where(function (Builder $query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('assigned_to_user', $user->id);
            })
            ->whereIn('status', ['open', 'assigned', 'in_progress', 'pending_user']);

        return $query->count();
    }

    /**
     * Get count of pending loan applications for user
     */
    protected function getPendingLoansCount(User $user): int
    {
        /** @var Builder<LoanApplication> $query */
        $query = LoanApplication::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'under_review', 'pending_info', 'approved', 'ready_issuance']);

        return $query->count();
    }

    /**
     * Get count of overdue loan items for user
     */
    protected function getOverdueItemsCount(User $user): int
    {
        /** @var Builder<LoanApplication> $query */
        $query = LoanApplication::query()
            ->where('user_id', $user->id)
            ->where('status', 'overdue');

        return $query->count();
    }

    /**
     * Get count of pending approvals (Grade 41+ only)
     */
    protected function getPendingApprovalsCount(): int
    {
        /** @var Builder<LoanApplication> $query */
        $query = LoanApplication::query()
            ->whereIn('status', ['submitted', 'under_review'])
            ->whereNull('approved_at');

        return $query->count();
    }

    /**
     * Check if user is an approver (Grade 41+)
     */
    protected function isApprover(User $user): bool
    {
        $gradeLevel = $user->grade?->level;
        $gradeLevel ??= 0;

        return $gradeLevel >= 41 || $user->hasRole('approver') || $user->hasRole('admin') || $user->hasRole('superuser');
    }

    /**
     * Refresh dashboard data
     *
     * Clears cached data and forces refresh of all computed properties.
     * Triggered by wire:poll.30s or manual refresh.
     * Optimized for FID (First Input Delay) with minimal blocking operations.
     */
    #[On('dashboard-refresh')]
    public function refreshData(): void
    {
        // Defer cache invalidation to prevent blocking
        $this->dispatch('$refresh');

        // Clear cache asynchronously
        $this->invalidateComponentCache();

        // Unset computed properties to force refresh
        unset($this->statistics);
        unset($this->recentTickets);
        unset($this->recentLoans);
    }

    /**
     * Placeholder method for lazy loading
     * Prevents initial render blocking
     */
    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <x-ui.skeleton-card />
                    <x-ui.skeleton-card />
                    <x-ui.skeleton-card />
                    <x-ui.skeleton-card />
                </div>
            </div>
        </div>
        HTML;
    }

    /**
     * Render the component
     */
    public function render(): View
    {
        return view('livewire.staff.authenticated-dashboard');
    }
}

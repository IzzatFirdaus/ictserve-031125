<?php

declare(strict_types=1);

namespace App\Livewire\Staff;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Traits\OptimizedLivewireComponent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Authenticated Staff Dashboard Component
 *
 * Unified dashboard for authenticated MOTAC staff showing personalized statistics,
 * recent activity from both helpdesk and asset loan modules, and quick action buttons.
 *
 * Features:
 * - Personalized statistics (tickets, loans, approvals, overdue items)
 * - Real-time updates with wire:poll.30s
 * - Recent activity feed (tickets and loans)
 * - Quick action buttons for common tasks
 * - Role-based content (Grade 41+ approval statistics)
 * - WCAG 2.2 Level AA compliant
 * - OptimizedLivewireComponent trait for performance
 *
 * @see D03-FR-019.1 Staff dashboard with personalized statistics
 * @see D03-FR-019.2 Recent activity display
 * @see D03-FR-019.3 Quick action buttons
 * @see D03-FR-024.2 Performance optimization with caching
 * @see D04 §6.2 Authenticated portal Livewire components
 * @see D10 §7 Livewire component documentation
 * @see D12 §9 WCAG 2.2 AA dashboard compliance
 *
 * @requirements 19.1, 19.2, 19.3, 19.4, 19.5, 24.2, 24.3
 *
 * @wcag-level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-05
 *
 * @author Frontend Engineering Team
 */
class AuthenticatedDashboard extends Component
{
    use OptimizedLivewireComponent;

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
        return $this->getCachedComponentData('statistics', function () {
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
    }

    /**
     * Get recent helpdesk tickets (max 5)
     *
     * Returns the 5 most recent helpdesk tickets for the authenticated user,
     * including both tickets created by the user and tickets assigned to them.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    #[Computed]
    public function recentTickets()
    {
        return $this->getCachedComponentData('recent_tickets', function () {
            $user = $this->getUser();

            return HelpdeskTicket::query()
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhere('assigned_to_user', $user->id);
                })
                ->with(['user:id,name', 'assignedUser:id,name', 'division:id,name_ms,name_en'])
                ->latest()
                ->limit(5)
                ->get();
        }, 300); // Cache for 5 minutes
    }

    /**
     * Get recent loan applications (max 5)
     *
     * Returns the 5 most recent loan applications for the authenticated user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    #[Computed]
    public function recentLoans()
    {
        return $this->getCachedComponentData('recent_loans', function () {
            $user = $this->getUser();

            return LoanApplication::query()
                ->where('user_id', $user->id)
                ->with(['loanItems.asset:id,name,model', 'division:id,name_ms,name_en'])
                ->latest()
                ->limit(5)
                ->get();
        }, 300); // Cache for 5 minutes
    }

    /**
     * Get count of open tickets for user
     */
    protected function getOpenTicketsCount(User $user): int
    {
        return HelpdeskTicket::query()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('assigned_to_user', $user->id);
            })
            ->whereIn('status', ['open', 'assigned', 'in_progress', 'pending_user'])
            ->count();
    }

    /**
     * Get count of pending loan applications for user
     */
    protected function getPendingLoansCount(User $user): int
    {
        return LoanApplication::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'under_review', 'pending_info', 'approved', 'ready_issuance'])
            ->count();
    }

    /**
     * Get count of overdue loan items for user
     */
    protected function getOverdueItemsCount(User $user): int
    {
        return LoanApplication::query()
            ->where('user_id', $user->id)
            ->where('status', 'overdue')
            ->count();
    }

    /**
     * Get count of pending approvals (Grade 41+ only)
     */
    protected function getPendingApprovalsCount(): int
    {
        return LoanApplication::query()
            ->whereIn('status', ['submitted', 'under_review'])
            ->whereNull('approved_by')
            ->count();
    }

    /**
     * Check if user is an approver (Grade 41+)
     */
    protected function isApprover(User $user): bool
    {
        return $user->hasRole('approver') || $user->hasRole('admin') || $user->hasRole('superuser');
    }

    /**
     * Refresh dashboard data
     *
     * Clears cached data and forces refresh of all computed properties.
     * Triggered by wire:poll.30s or manual refresh.
     */
    #[On('dashboard-refresh')]
    public function refreshData(): void
    {
        $this->invalidateComponentCache();

        // Unset computed properties to force refresh
        unset($this->statistics);
        unset($this->recentTickets);
        unset($this->recentLoans);
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.staff.authenticated-dashboard')->layout('layouts.app');
    }
}

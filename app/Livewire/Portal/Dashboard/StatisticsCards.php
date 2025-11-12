<?php

declare(strict_types=1);

namespace App\Livewire\Portal\Dashboard;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Dashboard Statistics Cards Component
 *
 * Displays real-time statistics for authenticated users including:
 * - My Open Tickets: Count of open helpdesk tickets
 * - My Pending Loans: Count of pending loan applications
 * - Overdue Items: Count of overdue loan returns
 * - Pending Approvals: Count of pending approvals (Grade 41+ only)
 *
 * @traceability Requirements 1.1, 1.5
 */
class StatisticsCards extends Component
{
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
     * Get count of open tickets for user
     */
    #[Computed]
    public function openTicketsCount(): int
    {
        $user = $this->getUser();

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
    #[Computed]
    public function pendingLoansCount(): int
    {
        $user = $this->getUser();

        return LoanApplication::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'under_review', 'pending_info', 'approved', 'ready_issuance'])
            ->count();
    }

    /**
     * Get count of overdue loan items for user
     */
    #[Computed]
    public function overdueItemsCount(): int
    {
        $user = $this->getUser();

        return LoanApplication::query()
            ->where('user_id', $user->id)
            ->where('status', 'overdue')
            ->count();
    }

    /**
     * Determine if the current user should see approvals (Grade 41+ or approver/admin/superuser)
     */
    #[Computed]
    public function isApproverUser(): bool
    {
        $user = $this->getUser();

        // Honor grade requirement (>= 41) and role-based access
        $meetsGrade = method_exists($user, 'meetsApproverGradeRequirement')
            ? $user->meetsApproverGradeRequirement()
            : false;

        return $meetsGrade
            || $user->hasRole('approver')
            || $user->hasRole('admin')
            || $user->hasRole('superuser');
    }

    /**
     * Get count of pending approvals for approvers
     */
    #[Computed]
    public function pendingApprovalsCount(): int
    {
        if (! $this->isApproverUser()) {
            return 0;
        }

        return LoanApplication::query()
            ->whereIn('status', ['submitted', 'under_review'])
            ->whereNull('approved_at')
            ->count();
    }

    /**
     * Check if user is an approver (Grade 41+)
     */
    protected function isApprover(): bool
    {
        $user = $this->getUser();

        return $user->hasRole('approver') || $user->hasRole('admin') || $user->hasRole('superuser');
    }

    /**
     * Refresh statistics
     *
     * Forces re-computation of all statistics by dispatching refresh event.
     */
    public function refreshStatistics(): void
    {
        $this->dispatch('$refresh');

        // Unset computed properties to force refresh
        unset($this->openTicketsCount);
        unset($this->pendingLoansCount);
        unset($this->overdueItemsCount);
        unset($this->pendingApprovalsCount);
        unset($this->isApproverUser);
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.portal.dashboard.statistics-cards');
    }
}

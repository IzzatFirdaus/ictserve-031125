<?php

declare(strict_types=1);

namespace App\Livewire\Portal\Widgets;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Personal Statistics Widget Component
 *
 * Reusable widget for displaying personalized user statistics across the portal.
 * Can be embedded in dashboard, profile, and other authenticated pages.
 *
 * Features:
 * - Configurable stat types (tickets, loans, approvals, overdue)
 * - Real-time updates via Livewire polling
 * - Responsive design with loading states
 * - WCAG 2.2 Level AA compliant
 * - Optional WebSocket integration for live updates
 *
 * @author Frontend Engineering Team
 *
 * @version 1.0.0
 *
 * @created 2025-11-28
 */
class PersonalStatsWidget extends Component
{
    /**
     * Widget configuration
     *
     * @var array<string, bool>
     */
    public array $config = [
        'show_tickets' => true,
        'show_loans' => true,
        'show_approvals' => true,
        'show_overdue' => true,
    ];

    /**
     * Enable/disable real-time updates
     */
    public bool $liveUpdates = true;

    /**
     * Polling interval in seconds (0 = no polling)
     */
    public int $pollInterval = 30;

    /**
     * Show/hide widget
     */
    public bool $visible = true;

    /**
     * Mount component with configuration
     *
     * @param  array<string, bool>  $config
     */
    public function mount(array $config = [], bool $liveUpdates = true, int $pollInterval = 30): void
    {
        $this->config = array_merge($this->config, $config);
        $this->liveUpdates = $liveUpdates;
        $this->pollInterval = $pollInterval;
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
     * Get open tickets count
     */
    #[Computed]
    public function openTicketsCount(): int
    {
        if (! $this->config['show_tickets']) {
            return 0;
        }

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
     * Get pending loans count
     */
    #[Computed]
    public function pendingLoansCount(): int
    {
        if (! $this->config['show_loans']) {
            return 0;
        }

        $user = $this->getUser();

        return LoanApplication::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'under_review', 'pending_info', 'approved', 'ready_issuance'])
            ->count();
    }

    /**
     * Get pending approvals count (Grade 41+ only)
     */
    #[Computed]
    public function pendingApprovalsCount(): int
    {
        if (! $this->config['show_approvals']) {
            return 0;
        }

        $user = $this->getUser();

        // Check if user is an approver
        if (! $this->isApprover($user)) {
            return 0;
        }

        return LoanApplication::query()
            ->whereIn('status', ['submitted', 'under_review'])
            ->whereNull('approved_at')
            ->count();
    }

    /**
     * Get overdue items count
     */
    #[Computed]
    public function overdueItemsCount(): int
    {
        if (! $this->config['show_overdue']) {
            return 0;
        }

        $user = $this->getUser();

        return LoanApplication::query()
            ->where('user_id', $user->id)
            ->where('status', 'overdue')
            ->count();
    }

    /**
     * Check if user is an approver
     */
    protected function isApprover(User $user): bool
    {
        $gradeLevel = $user->grade?->level ?? 0;

        return $gradeLevel >= 41
            || $user->hasRole('approver')
            || $user->hasRole('admin')
            || $user->hasRole('superuser');
    }

    /**
     * Refresh widget data
     */
    #[On('stats-refresh')]
    public function refresh(): void
    {
        // Clear computed properties to force refresh
        unset($this->openTicketsCount);
        unset($this->pendingLoansCount);
        unset($this->pendingApprovalsCount);
        unset($this->overdueItemsCount);

        $this->dispatch('$refresh');
    }

    /**
     * Toggle widget visibility
     */
    public function toggleVisibility(): void
    {
        $this->visible = ! $this->visible;
    }

    /**
     * Render the widget
     */
    public function render(): \Illuminate\View\View: View
    {
        return view('livewire.portal.widgets.personal-stats-widget');
    }
}

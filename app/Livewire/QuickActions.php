<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Reactive;
use Livewire\Component;

/**
 * QuickActions Livewire Component
 *
 * Enhanced quick action shortcuts with role-based visibility for common staff tasks.
 * Livewire 3 optimized with #[Reactive], #[Computed], and #[Lazy] attributes.
 *
 * @trace D03-FR-001.1, D04-§4.1, D12-§3
 *
 * @requirements 1.3, 5.5, 3.2
 *
 * @wcag-level AA
 *
 * @version 1.1.0
 */
#[Lazy]
class QuickActions extends Component
{
    #[Reactive]
    public array $actions = [];

    /**
     * Mount component and determine available actions.
     */
    public function mount(): void
    {
        $user = Auth::user();

        // Base actions for all staff
        $this->actions = [
            [
                'label' => __('portal.actions.submit_ticket'),
                'route' => 'portal.tickets.create',
                'icon' => 'heroicon-o-ticket',
                'color' => 'primary',
                'visible' => true,
            ],
            [
                'label' => __('portal.actions.request_loan'),
                'route' => 'portal.loans.create',
                'icon' => 'heroicon-o-cube',
                'color' => 'success',
                'visible' => true,
            ],
            [
                'label' => __('portal.actions.view_submissions'),
                'route' => 'portal.submissions.index',
                'icon' => 'heroicon-o-document-text',
                'color' => 'info',
                'visible' => true,
            ],
            [
                'label' => __('portal.actions.manage_profile'),
                'route' => 'portal.profile.edit',
                'icon' => 'heroicon-o-user-circle',
                'color' => 'secondary',
                'visible' => true,
            ],
        ];

        // Add approver-specific actions (Grade 41+)
        if ($user && method_exists($user, 'isApprover') && $user->isApprover()) {
            $this->actions[] = [
                'label' => __('portal.actions.view_approvals'),
                'route' => 'portal.approvals.index',
                'icon' => 'heroicon-o-check-circle',
                'color' => 'warning',
                'visible' => true,
            ];
        }

        // Add admin-specific actions
        if ($user && $user->hasRole('admin')) {
            $this->actions[] = [
                'label' => __('portal.actions.admin_panel'),
                    'route' => 'filament.admin.pages.admin-dashboard',
                'icon' => 'heroicon-o-cog-6-tooth',
                'color' => 'danger',
                'visible' => true,
                'external' => true,
            ];
        }

        // Add superuser-specific actions
        if ($user && $user->hasRole('superuser')) {
            $this->actions[] = [
                'label' => __('portal.actions.system_config'),
                'route' => 'filament.admin.pages.settings',
                'icon' => 'heroicon-o-wrench-screwdriver',
                'color' => 'danger',
                'visible' => true,
                'external' => true,
            ];
        }
    }

    /**
     * Check if user has pending notifications (cached for performance).
     */
    #[Computed(persist: true, cache: true)]
    public function pendingNotificationsCount(): int
    {
        return Auth::user()?->unreadNotifications()->count() ?? 0;
    }

    /**
     * Check if user has claimable guest submissions (cached for performance).
     */
    #[Computed(persist: true, cache: true)]
    public function hasClaimableSubmissions(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        $email = $user->email;

        $hasTickets = \App\Models\HelpdeskTicket::query()
            ->where('guest_email', $email)
            ->whereNull('user_id')
            ->exists();

        $hasLoans = \App\Models\LoanApplication::query()
            ->where('guest_email', $email)
            ->whereNull('user_id')
            ->exists();

        return $hasTickets || $hasLoans;
    }

    /**
     * Get visible actions for current user (cached computed property).
     */
    #[Computed(persist: true)]
    public function visibleActions(): array
    {
        return array_filter($this->actions, fn ($action) => $action['visible'] ?? true);
    }

    /**
     * Optimized placeholder for lazy loading with WCAG compliance.
     */
    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6" 
             role="status" aria-label="Loading quick actions">
            <div class="animate-pulse space-y-4">
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div class="h-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    <div class="h-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    <div class="h-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    <div class="h-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    <div class="h-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
                </div>
            </div>
            <span class="sr-only">Loading quick actions...</span>
        </div>
        HTML;
    }

    /**
     * Render the quick actions component with optimized data passing.
     */
    public function render()
    {
        return view('livewire.quick-actions');
    }
}

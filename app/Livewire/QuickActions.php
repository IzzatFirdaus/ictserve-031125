<?php

// name: QuickActions
// description: Enhanced quick action shortcuts with role-based visibility for common staff tasks
// author: dev-team@motac.gov.my
// trace: D03 SRS-FR-001; D04 §4.1; D12 §3 (Requirements 1.3, 5.5)
// last-updated: 2025-11-07

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * QuickActions Livewire Component
 *
 * Provides quick access buttons for common staff tasks on the authenticated dashboard.
 * Includes shortcuts for: new ticket submission, new loan application, profile management,
 * notifications, and data export. Features role-based action visibility for Approver, Admin,
 * and Superuser roles with WCAG 2.2 AA compliant 44×44px touch targets.
 *
 * Requirements: D03 SRS-FR-001 §2 (Quick Actions), Requirements 1.3, 5.5
 * UI Compliance: D12 §3 (Component Library), D14 §9 (WCAG 2.2 AA)
 */
class QuickActions extends Component
{
    /**
     * Available quick actions based on user role.
     */
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
                'route' => 'filament.admin.pages.dashboard',
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
     * Check if user has pending notifications.
     */
    #[Computed]
    public function pendingNotificationsCount(): int
    {
        return Auth::user()->unreadNotifications()->count();
    }

    /**
     * Check if user has claimable guest submissions.
     */
    #[Computed]
    public function hasClaimableSubmissions(): bool
    {
        $email = Auth::user()->email;

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
     * Get visible actions for current user.
     */
    #[Computed]
    public function visibleActions(): array
    {
        return array_filter($this->actions, fn ($action) => $action['visible'] ?? true);
    }

    /**
     * Render the quick actions component.
     */
    public function render()
    {
        return view('livewire.quick-actions', [
            'actions' => $this->visibleActions,
            'pendingNotificationsCount' => $this->pendingNotificationsCount,
            'hasClaimableSubmissions' => $this->hasClaimableSubmissions,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire\Navigation;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Portal Navigation Component
 *
 * Main navigation bar for authenticated staff portal with role-based menu items,
 * responsive mobile menu, language switcher, and WCAG 2.2 AA compliance.
 *
 * @trace D03-FR-001.1, D04 §6.1, D12 §9, Requirements 5.5, 11.2, 14.2
 *
 * @wcag-level AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.5)
 */
class PortalNavigation extends Component
{
    /**
     * Get role-based navigation links
     *
     * @return array<int, array<string, mixed>>
     */
    #[Computed]
    public function navigationLinks(): array
    {
        $user = Auth::user();

        $links = [
            // Staff role (all authenticated users)
            [
                'label' => __('staff.nav.dashboard'),
                'route' => 'staff.dashboard',
                'icon' => 'home',
                'roles' => ['staff', 'approver', 'admin', 'superuser'],
            ],
            [
                'label' => __('staff.nav.helpdesk'),
                'route' => 'helpdesk.authenticated.dashboard',
                'icon' => 'ticket',
                'roles' => ['staff', 'approver', 'admin', 'superuser'],
            ],
            [
                'label' => __('staff.nav.loans'),
                'route' => 'loan.authenticated.dashboard',
                'icon' => 'cube',
                'roles' => ['staff', 'approver', 'admin', 'superuser'],
            ],

            // Approver role (Grade 41+)
            [
                'label' => __('staff.nav.approvals'),
                'route' => 'loan.approvals.index',
                'icon' => 'check-circle',
                'roles' => ['approver', 'admin', 'superuser'],
            ],

            // Admin role
            [
                'label' => __('staff.nav.admin_panel'),
                'route' => 'filament.admin.pages.dashboard',
                'icon' => 'cog',
                'roles' => ['admin', 'superuser'],
                'external' => true,
            ],
        ];

        // Filter links based on user role and available routes
        return array_filter($links, function ($link) use ($user) {
            // Check if route exists
            if (! isset($link['route']) || ! \Illuminate\Support\Facades\Route::has($link['route'])) {
                return false;
            }

            // Check if user has required role
            if (isset($link['roles']) && $user instanceof User) {
                $userRole = $this->getUserRole($user);

                return in_array($userRole, $link['roles'], true);
            }

            return true;
        });
    }

    /**
     * Determine user's highest role
     */
    private function getUserRole(User $user): string
    {
        if ($user->hasRole('superuser')) {
            return 'superuser';
        }

        if ($user->hasRole('admin')) {
            return 'admin';
        }

        if ($user->isApprover()) {
            return 'approver';
        }

        return 'staff';
    }

    /**
     * Check if current route matches the given route
     */
    public function isCurrentRoute(string $route): bool
    {
        return request()->routeIs($route);
    }

    public function render()
    {
        return view('livewire.navigation.portal-navigation');
    }
}

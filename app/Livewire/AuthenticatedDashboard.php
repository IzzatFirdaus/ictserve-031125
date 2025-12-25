<?php

declare(strict_types=1);

// name: AuthenticatedDashboard
// description: PKS 5.2.1 Compliant authenticated staff dashboard with real-time statistics and role-specific widgets
// author: dev-team@motac.gov.my
// trace: D03 SRS-FR-006, D04 §5.1, D12 §3 (Requirements 1.1-1.5, 5.5, 8.1-8.5, 25.1)
// last-updated: 2025-12-25
// pks-compliance: PKS 5.2.1 - SSO-only architecture, no guest access

namespace App\Livewire;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * PKS 5.2.1 Compliant Authenticated Dashboard
 *
 * SSO-only architecture - all users must be authenticated via LDAP/AD.
 * Guest submission claiming functionality has been removed per PKS 5.2.1.
 */
class AuthenticatedDashboard extends Component
{
    /**
     * Dashboard statistics cache.
     *
     * @var array<string, int>|null
     */
    public ?array $statistics = null;

    /**
     * Role-specific widgets data.
     *
     * @var array<string, mixed>|null
     */
    public ?array $roleWidgets = null;

    /**
     * Last refresh timestamp.
     */
    public ?string $lastRefresh = null;

    /**
     * Mount component and load initial data.
     * PKS 5.2.1: Requires authenticated user - no guest access.
     */
    public function mount(DashboardService $dashboardService): void
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Unauthorized - PKS 5.2.1 requires authenticated access');
        }

        $this->loadStatistics($dashboardService);
        $this->loadRoleSpecificWidgets($dashboardService);
        $this->lastRefresh = now()->toIso8601String();
    }

    /**
     * Load dashboard statistics with 5-minute cache.
     */
    public function loadStatistics(DashboardService $dashboardService): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403, 'Unauthorized');
        }

        /** @var array<string, int> $stats */
        $stats = $dashboardService->getStatistics($user);
        $this->statistics = $stats;
    }

    /**
     * Load role-specific widgets based on user role.
     */
    public function loadRoleSpecificWidgets(DashboardService $dashboardService): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403, 'Unauthorized');
        }

        /** @var array<string, mixed> $widgets */
        $widgets = $dashboardService->getRoleSpecificWidgets($user);
        $this->roleWidgets = $widgets;
    }

    /**
     * Refresh statistics (called by wire:poll or manual refresh).
     */
    public function refreshStatistics(DashboardService $dashboardService): void
    {
        $this->loadStatistics($dashboardService);
        $this->loadRoleSpecificWidgets($dashboardService);
        $this->lastRefresh = now()->toIso8601String();

        $this->dispatch('statistics-refreshed');
    }

    /**
     * Listen for external refresh requests.
     */
    #[On('refresh-dashboard')]
    public function handleRefreshRequest(DashboardService $dashboardService): void
    {
        $this->refreshStatistics($dashboardService);
    }

    /**
     * Render the component.
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.authenticated-dashboard', [
            'user' => Auth::user(),
            'statistics' => $this->statistics,
            'roleWidgets' => $this->roleWidgets,
            'lastRefresh' => $this->lastRefresh,
        ]);
    }
}

<?php

declare(strict_types=1);

// name: AuthenticatedDashboard
// description: Enhanced authenticated staff dashboard with real-time statistics, role-specific widgets, and activity feed
// author: dev-team@motac.gov.my
// trace: D03 SRS-FR-006, D04 §5.1, D12 §3 (Requirements 1.1-1.5, 5.5, 8.1-8.5)
// last-updated: 2025-11-06

namespace App\Livewire;

use App\Services\DashboardService;
use App\Services\GuestSubmissionClaimService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

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
     * Claimable submissions count.
     *
     * @var array{tickets:int, loans:int, total:int}
     */
    public array $claimableSubmissions = [
        'tickets' => 0,
        'loans' => 0,
        'total' => 0,
    ];

    /**
     * Indicates whether to show claim banner.
     */
    public bool $showClaimBanner = false;

    /**
     * Last refresh timestamp.
     */
    public ?string $lastRefresh = null;

    /**
     * Mount component and load initial data.
     */
    public function mount(
        DashboardService $dashboardService,
        GuestSubmissionClaimService $claimService
    ): void {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Unauthorized');
        }

        $this->loadStatistics($dashboardService);
        $this->loadRoleSpecificWidgets($dashboardService);
        $this->loadClaimableSubmissions($claimService);
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
     * Load claimable guest submissions.
     */
    public function loadClaimableSubmissions(GuestSubmissionClaimService $claimService): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403, 'Unauthorized');
        }
        $claimableTotal = (int) $claimService->getClaimableCount($user);

        $this->claimableSubmissions = [
            'tickets' => 0,
            'loans' => 0,
            'total' => $claimableTotal,
        ];

        $this->showClaimBanner = $this->claimableSubmissions['total'] > 0;
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
     * Dismiss claim banner.
     */
    public function dismissClaimBanner(): void
    {
        $this->showClaimBanner = false;
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
    public function render(): \Illuminate\View\View: View
    {
        return view('livewire.authenticated-dashboard', [
            'user' => Auth::user(),
            'statistics' => $this->statistics,
            'roleWidgets' => $this->roleWidgets,
            'claimableSubmissions' => $this->claimableSubmissions,
            'showClaimBanner' => $this->showClaimBanner,
            'lastRefresh' => $this->lastRefresh,
        ]);
    }
}

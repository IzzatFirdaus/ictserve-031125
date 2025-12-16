<?php

declare(strict_types=1);

namespace App\Livewire\Portal\Dashboard;

use App\Models\User;
use App\Services\EnhancedUnifiedDashboardService;
use App\Traits\OptimizedLivewireComponent;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Enhanced Staff Dashboard Component
 *
 * Unified dashboard for authenticated MOTAC staff with real-time updates
 * via Laravel Reverb WebSocket, personalized metrics, and quick actions.
 *
 * Features:
 * - Real-time updates via Laravel Reverb (60-second polling)
 * - Personalized statistics (tickets, loans, approvals)
 * - Recent activity feed with filtering
 * - Pending actions with priority sorting
 * - WCAG 2.2 Level AA compliant
 * - Bahasa Melayu exclusive interface
 *
 * @see D03-FR-019 Staff dashboard requirements
 * @see D04 §6.2 Authenticated portal components
 * @see D12 §2 Real-time features with Laravel Reverb
 * @see D12 §9 WCAG 2.2 AA compliance
 *
 * @requirements 1.1, 1.3, 4.1, 6.1, 6.2, 6.4, 6.5, 14.1
 *
 * @version 3.6.0
 */
#[Layout('layouts.portal')]
class EnhancedStaffDashboard extends Component
{
    use OptimizedLivewireComponent;

    /**
     * Activity filter type
     */
    public string $activityFilter = 'all';

    /**
     * Available filter options
     *
     * @var array<string, string>
     */
    public array $filterOptions = [
        'all' => 'Semua Aktiviti',
        'tickets' => 'Tiket Sahaja',
        'loans' => 'Pinjaman Sahaja',
    ];

    /**
     * Get Echo listeners for real-time updates
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
            "echo-private:App.Models.User.{$user->id},TicketStatusUpdated" => 'handleTicketUpdate',
            "echo-private:App.Models.User.{$user->id},LoanStatusUpdated" => 'handleLoanUpdate',
            "echo-private:App.Models.User.{$user->id},notification" => 'handleNotification',
            'dashboard-refresh' => 'refreshData',
        ];
    }

    /**
     * Handle ticket status update from WebSocket
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo-private:TicketStatusUpdated')]
    public function handleTicketUpdate(array $event): void
    {
        $this->invalidateComponentCache();
        unset($this->dashboardData);

        $ticketNumber = $event['ticket_number'] ?? '';
        $newStatus = $event['status'] ?? '';
        $this->dispatch('toast', message: "Tiket #{$ticketNumber} dikemas kini: {$newStatus}", type: 'info');
    }

    /**
     * Handle loan status update from WebSocket
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo-private:LoanStatusUpdated')]
    public function handleLoanUpdate(array $event): void
    {
        $this->invalidateComponentCache();
        unset($this->dashboardData);

        $applicationNumber = $event['application_number'] ?? '';
        $newStatus = $event['status'] ?? '';
        $this->dispatch('toast', message: "Pinjaman #{$applicationNumber} dikemas kini: {$newStatus}", type: 'info');
    }

    /**
     * Handle general notification
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo-private:notification')]
    public function handleNotification(array $event): void
    {
        $this->refreshData();
    }

    /**
     * Set activity filter
     */
    public function setActivityFilter(string $filter): void
    {
        if (array_key_exists($filter, $this->filterOptions)) {
            $this->activityFilter = $filter;
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
     * Get dashboard data with caching
     *
     * @return array<string, mixed>
     */
    #[Computed]
    public function dashboardData(): array
    {
        return $this->getCachedComponentData('dashboard_data', function () {
            $service = app(EnhancedUnifiedDashboardService::class);

            return $service->getStaffDashboardMetrics($this->getUser());
        }, 60);
    }

    /**
     * Get filtered recent activity
     *
     * @return array<int, array<string, mixed>>
     */
    #[Computed]
    public function filteredActivity(): array
    {
        $activity = $this->dashboardData['recent_activity'] ?? [];

        if ($this->activityFilter === 'all') {
            return $activity;
        }

        return array_filter(
            $activity,
            fn ($item) => ($this->activityFilter === 'tickets' && $item['type'] === 'ticket') ||
                ($this->activityFilter === 'loans' && $item['type'] === 'loan')
        );
    }

    /**
     * Check if user is an approver
     */
    #[Computed]
    public function isApprover(): bool
    {
        $user = $this->getUser();
        $gradeLevel = $user->grade?->level ?? 0;

        return $gradeLevel >= 41 || $user->hasRole(['approver', 'admin', 'superuser']);
    }

    /**
     * Refresh dashboard data
     */
    #[On('dashboard-refresh')]
    public function refreshData(): void
    {
        $this->invalidateComponentCache();
        unset($this->dashboardData);
        unset($this->filteredActivity);
        $this->dispatch('$refresh');
    }

    /**
     * Placeholder for lazy loading
     */
    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                    <x-ui.skeleton-card />
                    <x-ui.skeleton-card />
                    <x-ui.skeleton-card />
                    <x-ui.skeleton-card />
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <x-ui.skeleton-card class="h-64" />
                    <x-ui.skeleton-card class="h-64" />
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
        return view('livewire.portal.dashboard.enhanced-staff-dashboard');
    }
}

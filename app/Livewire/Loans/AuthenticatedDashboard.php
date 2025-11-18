<?php

declare(strict_types=1);

namespace App\Livewire\Loans;

use App\Models\User;
use App\Traits\OptimizedLivewireComponent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Authenticated Loan Dashboard Component
 *
 * Personalized dashboard for authenticated users showing loan statistics,
 * active loans, pending applications, and overdue items.
 *
 * @see D03-FR-011.1 Authenticated user dashboard
 * @see D03-FR-011.2 Loan history management
 * @see D03-FR-011.5 Real-time data updates
 * @see D04 §6.2 Authenticated portal Livewire components
 * @see D10 §7 Livewire component documentation
 * @see D12 §9 WCAG 2.2 AA dashboard compliance
 *
 * @requirements 11.1, 11.2, 11.5, 15.1
 *
 * @wcag-level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-03
 */
#[Lazy]
class AuthenticatedDashboard extends Component
{
    use OptimizedLivewireComponent;
    use WithPagination;

    /**
     * Current active tab
     */
    public string $activeTab = 'overview';

    /**
     * Search query for filtering
     */
    public string $search = '';

    /**
     * Status filter
     */
    public ?string $statusFilter = null;

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
     * Get active loans count
     */
    #[Computed]
    public function activeLoansCount(): int
    {
        return $this->getCachedComponentData('active_loans_count', function () {
            return $this->getUser()->loanApplications()
                ->whereIn('status', ['approved', 'issued', 'in_use', 'ready_issuance'])
                ->count();
        }, 60);
    }

    /**
     * Get pending applications count
     */
    #[Computed]
    public function pendingCount(): int
    {
        return $this->getCachedComponentData('pending_count', function () {
            return $this->getUser()->loanApplications()
                ->whereIn('status', ['submitted', 'under_review', 'pending_info'])
                ->count();
        }, 60);
    }

    /**
     * Get overdue items count
     */
    #[Computed]
    public function overdueCount(): int
    {
        return $this->getCachedComponentData('overdue_count', function () {
            return $this->getUser()->loanApplications()
                ->where('status', 'overdue')
                ->count();
        }, 60);
    }

    /**
     * Get total applications count
     */
    #[Computed]
    public function totalApplicationsCount(): int
    {
        return $this->getCachedComponentData('total_applications_count', function () {
            return $this->getUser()->loanApplications()->count();
        }, 60);
    }

    /**
     * Get active loans with relationships
     */
    #[Computed]
    public function activeLoans()
    {
        return $this->getCachedComponentData('active_loans', function () {
            return $this->getUser()->loanApplications()
                ->whereIn('status', ['approved', 'issued', 'in_use', 'ready_issuance'])
                ->with(['loanItems.asset', 'division'])
                ->latest()
                ->get();
        }, 60);
    }

    /**
     * Get pending applications with relationships
     */
    #[Computed]
    public function pendingApplications()
    {
        return $this->getCachedComponentData('pending_applications', function () {
            return $this->getUser()->loanApplications()
                ->whereIn('status', ['submitted', 'under_review', 'pending_info'])
                ->with(['loanItems.asset', 'division'])
                ->latest()
                ->get();
        }, 60);
    }

    /**
     * Get overdue items with relationships
     */
    #[Computed]
    public function overdueItems()
    {
        return $this->getCachedComponentData('overdue_items', function () {
            return $this->getUser()->loanApplications()
                ->where('status', 'overdue')
                ->with(['loanItems.asset', 'division'])
                ->latest()
                ->get();
        }, 60);
    }

    /**
     * Get all loan history with search and filter
     */
    #[Computed]
    public function loanHistory()
    {
        $query = $this->getUser()->loanApplications()
            ->with(['loanItems.asset', 'division']);

        // Apply search filter
        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('application_number', 'like', "%{$this->search}%")
                    ->orWhere('purpose', 'like', "%{$this->search}%")
                    ->orWhereHas('loanItems.asset', function ($assetQuery) {
                        $assetQuery->where('name', 'like', "%{$this->search}%");
                    });
            });
        }

        // Apply status filter
        if (! empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        $query = $this->applyEagerLoading($query);

        return $this->getOptimizedPaginatedResults($query, 25);
    }

    /**
     * Switch active tab
     */
    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    /**
     * Clear filters
     */
    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = null;
        $this->resetPage();
    }

    /**
     * Refresh dashboard data
     */
    #[On('loan-updated')]
    public function refreshData(): void
    {
        // Unset computed properties to force refresh
        unset($this->activeLoansCount);
        unset($this->pendingCount);
        unset($this->overdueCount);
        unset($this->totalApplicationsCount);
        unset($this->activeLoans);
        unset($this->pendingApplications);
        unset($this->overdueItems);
        unset($this->loanHistory);
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.loans.authenticated-dashboard')->layout('layouts.portal');
    }
}

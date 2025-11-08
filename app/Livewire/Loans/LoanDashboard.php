<?php

declare(strict_types=1);

namespace App\Livewire\Loans;

use App\Models\LoanApplication;
use App\Traits\OptimizedLivewireComponent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;

/**
 * Loan Dashboard Component
 *
 * Personalized dashboard for authenticated users showing loan statistics and quick actions.
 *
 * @see D03-FR-011.1 Authenticated user dashboard
 * @see D03-FR-011.2 Loan history management
 * @see D04 §3.1 Livewire components
 */
#[Lazy]
class LoanDashboard extends Component
{
    use OptimizedLivewireComponent;

    public string $activeTab = 'overview';

    #[Computed]
    public function activeLoans()
    {
        return LoanApplication::where('user_id', Auth::id())
            ->whereIn('status', ['approved', 'issued', 'in_use'])
            ->with(['loanItems.asset', 'division'])
            ->latest()
            ->get();
    }

    #[Computed]
    public function pendingApplications()
    {
        return LoanApplication::where('user_id', Auth::id())
            ->whereIn('status', ['submitted', 'under_review'])
            ->with(['loanItems.asset', 'division'])
            ->latest()
            ->get();
    }

    #[Computed]
    public function overdueItems()
    {
        return LoanApplication::where('user_id', Auth::id())
            ->where('status', 'in_use')
            ->where('loan_end_date', '<', now())
            ->with(['loanItems.asset', 'division'])
            ->get();
    }

    #[Computed]
    public function statistics()
    {
        $userId = Auth::id();

        return [
            'active_loans' => LoanApplication::where('user_id', $userId)
                ->whereIn('status', ['approved', 'issued', 'in_use'])
                ->count(),
            'pending_applications' => LoanApplication::where('user_id', $userId)
                ->whereIn('status', ['submitted', 'under_review'])
                ->count(),
            'overdue_items' => LoanApplication::where('user_id', $userId)
                ->where('status', 'in_use')
                ->where('loan_end_date', '<', now())
                ->count(),
            'total_applications' => LoanApplication::where('user_id', $userId)->count(),
        ];
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.loans.loan-dashboard');
    }

    public function placeholder()
    {
        return view('livewire.placeholders.loading');
    }
}

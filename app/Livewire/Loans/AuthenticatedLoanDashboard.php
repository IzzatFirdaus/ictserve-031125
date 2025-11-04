<?php

declare(strict_types=1);

namespace App\Livewire\Loans;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Authenticated Loan Dashboard Component
 *
 * Displays loan application statistics and recent applications for authenticated users.
 *
 * @trace Requirements 22.2, 22.3, 22.6, 23.3
 */
class AuthenticatedLoanDashboard extends Component
{
    #[Computed]
    public function stats(): array
    {
        $user = Auth::user();

        $totalApplications = LoanApplication::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere(function ($guest) use ($user) {
                    $guest->whereNull('user_id')
                        ->where('applicant_email', $user->email);
                });
        })->count();

        $pendingApplications = LoanApplication::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere(function ($guest) use ($user) {
                    $guest->whereNull('user_id')
                        ->where('applicant_email', $user->email);
                });
        })->whereIn('status', [
            LoanStatus::SUBMITTED,
            LoanStatus::UNDER_REVIEW,
            LoanStatus::PENDING_INFO,
        ])->count();

        $activeLoans = LoanApplication::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere(function ($guest) use ($user) {
                    $guest->whereNull('user_id')
                        ->where('applicant_email', $user->email);
                });
        })->whereIn('status', [
            LoanStatus::APPROVED,
            LoanStatus::READY_ISSUANCE,
            LoanStatus::ISSUED,
            LoanStatus::IN_USE,
            LoanStatus::RETURN_DUE,
        ])->count();

        $claimableApplications = LoanApplication::whereNull('user_id')
            ->where('applicant_email', $user->email)
            ->count();

        return [
            'total' => $totalApplications,
            'pending' => $pendingApplications,
            'active' => $activeLoans,
            'claimable' => $claimableApplications,
        ];
    }

    #[Computed]
    public function recentApplications()
    {
        $user = Auth::user();

        return LoanApplication::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere(function ($guest) use ($user) {
                    $guest->whereNull('user_id')
                        ->where('applicant_email', $user->email);
                });
        })
            ->with(['division', 'loanItems.asset'])
            ->latest()
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.loans.authenticated-loan-dashboard')->layout('layouts.portal');
    }
}

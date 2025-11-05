<?php

declare(strict_types=1);

namespace App\Livewire\Staff;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Services\HybridHelpdeskService;
use App\Services\LoanApplicationService;
use App\Traits\OptimizedLivewireComponent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Component: ClaimSubmissions
 *
 * Allows authenticated staff to search for and claim guest submissions
 * (helpdesk tickets and loan applications) by email verification.
 *
 * @see D03-FR-022.6 (Guest submission claiming)
 * @see D04 §6.5 (Claim Submissions Component)
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-05
 *
 * WCAG 2.2 Level AA Compliance:
 * - Proper ARIA attributes for search results
 * - Keyboard navigation support
 * - Screen reader announcements for claim actions
 * - 44×44px touch targets on all interactive elements
 */
class ClaimSubmissions extends Component
{
    use OptimizedLivewireComponent;

    #[Validate('required|email')]
    public string $searchEmail = '';

    public array $selectedTickets = [];

    public array $selectedLoans = [];

    public bool $showResults = false;

    public string $activeTab = 'tickets';

    /**
     * Initialize component with user's email
     */
    public function mount(): void
    {
        $this->searchEmail = Auth::user()->email ?? '';
    }

    /**
     * Search for guest submissions by email
     */
    public function searchSubmissions(): void
    {
        $this->validate();

        // Verify email matches authenticated user
        if ($this->searchEmail !== Auth::user()->email) {
            $this->addError('searchEmail', __('staff.claims.email_mismatch'));

            return;
        }

        $this->showResults = true;
        $this->selectedTickets = [];
        $this->selectedLoans = [];

        // Announce results to screen readers
        $this->dispatch('announce', message: __('staff.claims.search_complete'));
    }

    /**
     * Get found tickets for the searched email
     */
    #[Computed]
    public function foundTickets()
    {
        if (! $this->showResults || empty($this->searchEmail)) {
            return collect();
        }

        return $this->cacheData(
            "claim-tickets-{$this->searchEmail}",
            fn () => HelpdeskTicket::query()
                ->whereNotNull('guest_email')
                ->where('guest_email', $this->searchEmail)
                ->whereNull('user_id')
                ->with(['category', 'division'])
                ->latest()
                ->get(),
            minutes: 5
        );
    }

    /**
     * Get found loan applications for the searched email
     */
    #[Computed]
    public function foundLoans()
    {
        if (! $this->showResults || empty($this->searchEmail)) {
            return collect();
        }

        return $this->cacheData(
            "claim-loans-{$this->searchEmail}",
            fn () => LoanApplication::query()
                ->whereNotNull('applicant_email')
                ->where('applicant_email', $this->searchEmail)
                ->whereNull('user_id')
                ->with(['asset'])
                ->latest()
                ->get(),
            minutes: 5
        );
    }

    /**
     * Claim selected tickets
     */
    public function claimTickets(HybridHelpdeskService $helpdeskService): void
    {
        if (empty($this->selectedTickets)) {
            $this->addError('selectedTickets', __('staff.claims.no_tickets_selected'));

            return;
        }

        $user = Auth::user();
        $claimedCount = 0;

        foreach ($this->selectedTickets as $ticketId) {
            try {
                $ticket = HelpdeskTicket::findOrFail($ticketId);

                // Verify it's a guest submission and email matches
                if ($ticket->guest_email === $this->searchEmail && ! $ticket->user_id) {
                    $helpdeskService->claimGuestTicket($ticket, $user);
                    $claimedCount++;
                }
            } catch (\Throwable $e) {
                \Log::error('Failed to claim ticket', [
                    'ticket_id' => $ticketId,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->selectedTickets = [];
        $this->showResults = false;

        session()->flash('success', __('staff.claims.tickets_claimed', ['count' => $claimedCount]));
        $this->dispatch('announce', message: __('staff.claims.tickets_claimed', ['count' => $claimedCount]));
    }

    /**
     * Claim selected loan applications
     */
    public function claimLoans(LoanApplicationService $loanService): void
    {
        if (empty($this->selectedLoans)) {
            $this->addError('selectedLoans', __('staff.claims.no_loans_selected'));

            return;
        }

        $user = Auth::user();
        $claimedCount = 0;

        foreach ($this->selectedLoans as $loanId) {
            try {
                $loan = LoanApplication::findOrFail($loanId);

                // Verify it's a guest submission and email matches
                if ($loan->applicant_email === $this->searchEmail && ! $loan->user_id) {
                    $loanService->claimGuestApplication($loan, $user);
                    $claimedCount++;
                }
            } catch (\Throwable $e) {
                \Log::error('Failed to claim loan application', [
                    'loan_id' => $loanId,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->selectedLoans = [];
        $this->showResults = false;

        session()->flash('success', __('staff.claims.loans_claimed', ['count' => $claimedCount]));
        $this->dispatch('announce', message: __('staff.claims.loans_claimed', ['count' => $claimedCount]));
    }

    /**
     * Reset search and results
     */
    public function resetSearch(): void
    {
        $this->searchEmail = Auth::user()->email ?? '';
        $this->showResults = false;
        $this->selectedTickets = [];
        $this->selectedLoans = [];
        $this->resetErrorBag();
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.staff.claim-submissions')
            ->layout('layouts.app');
    }
}

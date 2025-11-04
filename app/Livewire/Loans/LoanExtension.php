<?php

declare(strict_types=1);

namespace App\Livewire\Loans;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Services\LoanApplicationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class LoanExtension extends Component
{
    public LoanApplication $application;

    #[Validate('required|date|after:today')]
    public string $newEndDate = '';

    #[Validate('required|string|min:10|max:1000')]
    public string $justification = '';

    public function mount(LoanApplication $application): void
    {
        abort_unless($this->canAccess($application), 403);
        abort_unless($this->canExtend($application), 403);

        $this->application = $application;
        $this->newEndDate = $application->loan_end_date?->addDays(7)->format('Y-m-d') ?? now()->addWeek()->format('Y-m-d');
    }

    public function submit(LoanApplicationService $service)
    {
        $this->validate();

        if ($this->newEndDate <= $this->application->loan_end_date?->format('Y-m-d')) {
            $this->addError('newEndDate', __('Tarikh baharu mesti melebihi tarikh tamat sedia ada.'));

            return null;
        }

        $service->requestExtension($this->application, $this->newEndDate, $this->justification);

        session()->flash('message', __('Permohonan lanjutan telah dihantar untuk kelulusan.'));

        return redirect()->route('loan.authenticated.show', $this->application);
    }

    protected function canAccess(LoanApplication $application): bool
    {
        $user = Auth::user();

        if ($application->user_id === $user->id) {
            return true;
        }

        return strtolower($application->applicant_email) === strtolower($user->email);
    }

    protected function canExtend(LoanApplication $application): bool
    {
        $extendable = [
            LoanStatus::APPROVED,
            LoanStatus::READY_ISSUANCE,
            LoanStatus::ISSUED,
            LoanStatus::IN_USE,
            LoanStatus::RETURN_DUE,
        ];

        return in_array($application->status, $extendable, true);
    }

    public function render()
    {
        return view('livewire.loans.loan-extension')->layout('layouts.portal');
    }
}

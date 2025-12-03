<?php

namespace App\Livewire;

use App\Models\LoanApplication;
use Livewire\Attributes\Layout;
use Livewire\Component;

class GuestLoanTracking extends Component
{
    public string $applicationNumber = '';

    public string $email = '';

    public $application = null;

    public bool $searched = false;

    public function mount($ref = null): void
    {
        if ($ref) {
            $this->applicationNumber = $ref;
            $this->track();
        }
    }

    public function track(): void
    {
        $this->validate([
            'applicationNumber' => 'required|string|min:5',
        ]);

        $this->searched = true;

        $this->application = LoanApplication::where('application_number', $this->applicationNumber)
            ->with(['loanItems', 'division'])
            ->first();

        if (! $this->application) {
            $this->addError('applicationNumber', __('loan.messages.application_not_found'));
        }
    }

    public function trackByToken(): void
    {
        $this->validate([
            'applicationNumber' => 'required|string|min:5',
            'email' => 'required|email',
        ]);

        $this->searched = true;

        $this->application = LoanApplication::where('application_number', $this->applicationNumber)
            ->where('applicant_email', $this->email)
            ->with(['loanItems', 'division'])
            ->first();

        if (! $this->application) {
            $this->addError('applicationNumber', __('loan.messages.application_not_found'));
        }
    }

    #[Layout('layouts.front')]
    public function render()
    {
        return view('livewire.guest-loan-tracking');
    }
}

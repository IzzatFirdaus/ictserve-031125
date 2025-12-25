<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

/**
 * Guest Loan Application Component - PKS 5.2.1 Compliant
 *
 * Note: This is a stub class. The actual implementation should be created
 * as part of the loan application feature development.
 *
 * PKS 5.2.1: Guest loan applications will require user registration
 * before submission to ensure user_id linkage.
 */
class GuestLoanApplication extends Component
{
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.guest-loan-application');
    }
}

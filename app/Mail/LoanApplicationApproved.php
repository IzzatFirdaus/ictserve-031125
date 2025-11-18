<?php

declare(strict_types=1);

namespace App\Mail;

class LoanApplicationApproved extends BaseMailable
{
    public function __construct(public LoanApplication $loanApplication) {}

    public function build()
    {
        return $this->subject('Loan Application Approved')
            ->view('emails.loans.application-approved');
    }
}

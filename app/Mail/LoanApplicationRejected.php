<?php

declare(strict_types=1);

namespace App\Mail;

class LoanApplicationRejected extends BaseMailable
{
    public function __construct(public LoanApplication $loanApplication) {}

    public function build()
    {
        return $this->subject('Loan Application Rejected')
            ->view('emails.loans.application-rejected');
    }
}

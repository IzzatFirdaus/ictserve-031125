<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoanApplicationRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public LoanApplication $loanApplication) {}

    public function build()
    {
        return $this->subject('Loan Application Rejected')
            ->view('emails.loans.application-rejected');
    }
}

<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoanApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public LoanApplication $application)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('Loan Application Approved')
            ->view('emails.loans.approved', [
                'application' => $this->application,
            ]);
    }
}

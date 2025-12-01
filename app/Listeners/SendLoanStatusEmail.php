<?php

namespace App\Listeners;

use App\Events\LoanStatusChanged;
use App\Mail\LoanStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendLoanStatusEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LoanStatusChanged $event): void
    {
        if ($event->loanApplication->applicant_email) {
            Mail::to($event->loanApplication->applicant_email)
                ->send(new LoanStatusUpdated($event->loanApplication));
        }
    }
}

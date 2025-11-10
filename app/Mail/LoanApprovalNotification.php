<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * LoanApprovalNotification
 * Simple queued mailable used by portal approval tests.
 * trace: D03-FR-012.4; D04 §6.1; D11 §8
 */
class LoanApprovalNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public LoanApplication $application) {}

    public function build(): self
    {
        return $this->subject(__('Loan Application Approved'))
            ->view('emails.loans.approved-portal', [
                'application' => $this->application,
            ]);
    }
}

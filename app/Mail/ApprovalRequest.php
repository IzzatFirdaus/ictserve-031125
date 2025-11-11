<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovalRequest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public LoanApplication $loanApplication) {}

    public function build()
    {
        return $this->subject('Loan Approval Request')
            ->view('emails.loans.approval-request')
            ->with([
                'application' => $this->loanApplication,
                'applicantName' => $this->loanApplication->applicant_name,
                'approveUrl' => route('loan.approve', [
                    'token' => $this->loanApplication->approval_token,
                    'action' => 'approve',
                ]),
                'declineUrl' => route('loan.approve', [
                    'token' => $this->loanApplication->approval_token,
                    'action' => 'reject',
                ]),
            ]);
    }
}

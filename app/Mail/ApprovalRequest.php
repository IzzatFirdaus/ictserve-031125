<?php

declare(strict_types=1);

namespace App\Mail;

class ApprovalRequest extends BaseMailable
{
    public function __construct(public \App\Models\LoanApplication $loanApplication) {}

    public function build(): \Illuminate\Mail\Mailable
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

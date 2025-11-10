<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LoanStatus;
use App\Mail\AdminAssetPreparationNotification;
use App\Mail\ApprovalRequest;
use App\Mail\LoanApplicationApproved;
use App\Mail\LoanApplicationRejected;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Email Approval Workflow Service
 *
 * @see D03-FR-002.1 Email approval workflow
 * @see D03-FR-002.3 Secure token system
 */
class EmailApprovalWorkflowService
{
    public function __construct(
        private DualApprovalService $dualApprovalService,
        private EmailNotificationService $emailNotificationService
    ) {}

    public function routeForEmailApproval(LoanApplication $loanApplication): void
    {
        $approver = User::where('role', 'approver')->first();

        $loanApplication->update([
            'status' => LoanStatus::UNDER_REVIEW,
            'approver_email' => $approver->email,
            'approval_token' => Str::random(64),
            'approval_token_expires_at' => now()->addDays(7),
        ]);

        Mail::to($approver->email)->send(new ApprovalRequest($loanApplication));
    }

    public function processEmailApproval(string $token, bool $approved, ?string $remarks = null): LoanApplication
    {
        $loanApplication = LoanApplication::where('approval_token', $token)
            ->whereNotNull('approval_token')
            ->where('approval_token_expires_at', '>', now())
            ->firstOrFail();

        if ($approved) {
            $loanApplication->update([
                'status' => LoanStatus::APPROVED,
                'approved_at' => now(),
                'approval_remarks' => $remarks,
                'approval_token' => null,
                'approval_token_expires_at' => null,
            ]);

            Mail::to($loanApplication->applicant_email)->send(new LoanApplicationApproved($loanApplication));
            Mail::to(config('mail.admin_email', 'admin@motac.gov.my'))->send(new AdminAssetPreparationNotification($loanApplication));
        } else {
            $loanApplication->update([
                'status' => LoanStatus::REJECTED,
                'rejected_reason' => $remarks,
                'approval_token' => null,
                'approval_token_expires_at' => null,
            ]);

            Mail::to($loanApplication->applicant_email)->send(new LoanApplicationRejected($loanApplication));
        }

        return $loanApplication->fresh();
    }
}

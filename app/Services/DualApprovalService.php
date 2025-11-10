<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DualApprovalService
{
    public function __construct(
        private ApprovalMatrixService $approvalMatrix,
        private NotificationService $notificationService
    ) {}

    /**
     * Dispatch approval request with dual (email + portal) options.
     */
    public function sendApprovalRequest(LoanApplication $application): void
    {
        $approver = $this->approvalMatrix->determineApprover(
            $application->grade,
            (float) $application->total_value
        );

        $token = $application->generateApprovalToken();

        $application->update([
            'approver_email' => $approver['email'],
            'approved_by_name' => $approver['name'],
            'status' => LoanStatus::UNDER_REVIEW,
            'approval_method' => null,
            'approval_remarks' => null,
        ]);

        $this->notificationService->sendApprovalRequest($application, $approver, $token);

        Log::info('Loan application routed for dual approval', [
            'application_number' => $application->application_number,
            'approver_email' => $approver['email'],
            'token_expires_at' => $application->approval_token_expires_at,
        ]);
    }

    /**
     * Process approval decision submitted via email link (no authentication).
     */
    public function processEmailApproval(string $token, bool $approved, ?string $remarks = null): array
    {
        $application = LoanApplication::where('approval_token', $token)->first();

        if (! $application) {
            return [
                'success' => false,
                'message' => __('loan.approval.invalid_token'),
            ];
        }

        if (! $application->isTokenValid($token)) {
            return [
                'success' => false,
                'message' => __('loan.approval.token_expired'),
            ];
        }

        DB::beginTransaction();

        try {
            $application->update([
                'status' => $approved ? LoanStatus::APPROVED : LoanStatus::REJECTED,
                'approved_at' => $approved ? now() : null,
                'rejected_reason' => $approved ? null : $remarks,
                'approval_token' => null,
                'approval_token_expires_at' => null,
            ]);

            $this->logApprovalDecision($application, $approved, 'email', $remarks, null);
            $this->sendApprovalNotifications($application, $approved, $remarks);

            if ($approved) {
                $this->notificationService->notifyAdminForAssetPreparation($application);
            }

            DB::commit();

            Log::info('Email approval processed', [
                'application_number' => $application->application_number,
                'approved' => $approved,
                'method' => 'email',
            ]);

            return [
                'success' => true,
                'message' => $approved
                    ? __('loan.approval.approved_successfully')
                    : __('loan.approval.declined_successfully'),
                'application' => $application,
            ];
        } catch (\Throwable $exception) {
            DB::rollBack();

            Log::error('Failed to process email approval', [
                'error' => $exception->getMessage(),
                'token' => $token,
            ]);

            return [
                'success' => false,
                'message' => __('loan.approval.processing_failed'),
            ];
        }
    }

    /**
     * Process approval decision submitted from the authenticated portal.
     */
    public function processPortalApproval(
        LoanApplication $application,
        User $approver,
        bool $approved,
        ?string $remarks = null
    ): array {
        if (! $approver->canApprove()) {
            return [
                'success' => false,
                'message' => __('loan.approval.no_permission'),
            ];
        }

        DB::beginTransaction();

        try {
            $application->update([
                'status' => $approved ? LoanStatus::APPROVED : LoanStatus::REJECTED,
                'approved_at' => $approved ? now() : null,
                'approver_email' => $approver->email,
                'approved_by_name' => $approver->name,
                'rejected_reason' => $approved ? null : $remarks,
                'approval_token' => null,
                'approval_token_expires_at' => null,
            ]);

            $this->logApprovalDecision($application, $approved, 'portal', $remarks, $approver);
            $this->sendApprovalNotifications($application, $approved, $remarks);

            if ($approved) {
                $this->notificationService->notifyAdminForAssetPreparation($application);
            }

            DB::commit();

            Log::info('Portal approval processed', [
                'application_number' => $application->application_number,
                'approved' => $approved,
                'approver_id' => $approver->id,
                'method' => 'portal',
            ]);

            return [
                'success' => true,
                'message' => $approved
                    ? __('loan.approval.approved_successfully')
                    : __('loan.approval.declined_successfully'),
                'application' => $application,
            ];
        } catch (\Throwable $exception) {
            DB::rollBack();

            Log::error('Failed to process portal approval', [
                'error' => $exception->getMessage(),
                'application_id' => $application->id,
            ]);

            return [
                'success' => false,
                'message' => __('loan.approval.processing_failed'),
            ];
        }
    }

    /**
     * Persist approval decision metadata.
     */
    public function logApprovalDecision(
        LoanApplication $application,
        bool $approved,
        string $method,
        ?string $remarks,
        ?User $approver
    ): void {
        $application->updateQuietly([
            'approval_method' => $method,
            'approval_remarks' => $remarks,
            'approved_by_name' => $approver?->name ?? $application->approved_by_name,
        ]);

        Log::info('Approval decision logged', [
            'application_number' => $application->application_number,
            'status' => $application->status->value,
            'method' => $method,
        ]);
    }

    private function sendApprovalNotifications(
        LoanApplication $application,
        bool $approved,
        ?string $remarks = null
    ): void {
        $this->notificationService->sendApprovalDecision($application, $approved, $remarks);
        $this->notificationService->sendApprovalConfirmation($application, $approved);
    }

    /**
     * Alias for sendApprovalRequest() - for test compatibility
     */
    public function routeForEmailApproval(LoanApplication $application): void
    {
        $this->sendApprovalRequest($application);
    }
}

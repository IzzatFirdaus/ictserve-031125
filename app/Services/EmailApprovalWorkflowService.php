<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Email Approval Workflow Service
 *
 * Manages email-based approval workflows for Grade 41+ officers with secure token system.
 *
 * @see D03-FR-002.1 Email approval workflow
 * @see D03-FR-002.3 Token-based approval processing
 * @see D04 §2.2 Approval workflow engine
 */
class EmailApprovalWorkflowService
{
    public function __construct(
        private ApprovalMatrixService $approvalMatrix,
        private NotificationService $notificationService
    ) {}

    /**
     * Route loan application to appropriate approver via email
     *
     * @param LoanApplication $application
     * @return void
     */
    public function routeForEmailApproval(LoanApplication $application): void
    {
        // Determine approver based on grade and asset value
        $approver = $this->approvalMatrix->determineApprover(
            $application->grade,
            $application->total_value
        );

        // Generate secure approval token (7-day validity)
        $token = $application->generateApprovalToken();

        // Update application with approver details
        $application->update([
            'approver_email' => $approver['email'],
            'approved_by_name' => $approver['name'],
            'status' => LoanStatus::UNDER_REVIEW,
        ]);

        // Send approval request email with dual options (email + portal)
        $this->notificationService->sendApprovalRequest($application, $approver, $token);

        Log::info('Loan application routed for approval', [
            'application_number' => $application->application_number,
            'approver_email' => $approver['email'],
            'token_expires_at' => $application->approval_token_expires_at,
        ]);
    }

    /**
     * Process email-based approval (no login required)
     *
     * @param string $token Approval token
     * @param bool $approved Approval decision
     * @param string|null $remarks Optional approval remarks
     * @return array Result with success status and message
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
                'approval_token' => null, // Invalidate token
                'approval_token_expires_at' => null,
            ]);

            // Send confirmation emails
            $this->sendApprovalNotifications($application, $approved, $remarks);

            if ($approved) {
                // Notify admin for asset preparation
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
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process email approval', [
                'error' => $e->getMessage(),
                'token' => $token,
            ]);

            return [
                'success' => false,
                'message' => __('loan.approval.processing_failed'),
            ];
        }
    }

    /**
     * Process portal-based approval (login required)
     *
     * @param LoanApplication $application
     * @param User $approver
     * @param bool $approved
     * @param string|null $remarks
     * @return array
     */
    public function processPortalApproval(
        LoanApplication $application,
        User $approver,
        bool $approved,
        ?string $remarks = null
    ): array {
        // Verify approver has permission
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
                'rejected_reason' => $approved ? null : $remarks,
                'approver_email' => $approver->email,
                'approved_by_name' => $approver->name,
                'approval_token' => null, // Invalidate email token
                'approval_token_expires_at' => null,
            ]);

            // Send confirmation emails
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
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process portal approval', [
                'error' => $e->getMessage(),
                'application_id' => $application->id,
            ]);

            return [
                'success' => false,
                'message' => __('loan.approval.processing_failed'),
            ];
        }
    }

    /**
     * Send approval notification emails
     *
     * @param LoanApplication $application
     * @param bool $approved
     * @param string|null $remarks
     * @return void
     */
    private function sendApprovalNotifications(
        LoanApplication $application,
        bool $approved,
        ?string $remarks
    ): void {
        // Send confirmation to applicant
        $this->notificationService->sendApprovalDecision($application, $approved, $remarks);

        // Send confirmation to approver
        $this->notificationService->sendApprovalConfirmation($application, $approved);
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ApprovalServiceInterface;
use App\Contracts\TokenServiceInterface;
use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\LoanApproval;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

/**
 * Approval Service Implementation for ICTServe v3.5.0
 *
 * Implements email-based approval workflow for loan applications.
 * Supports Grade 41+ officers approving/rejecting via signed URLs without login.
 *
 * Security Features:
 * - SHA-512 token hashing per D03 §8.1
 * - IP address hashing for privacy compliance
 * - Signed URLs with 72-hour expiry
 * - Audit trail via dual audit system
 *
 * Workflow:
 * 1. Application submitted → initiateApproval()
 * 2. Find Grade 41+ approver → findApprover()
 * 3. Generate signed URL → TokenService
 * 4. Send email → sendApprovalEmail()
 * 5. Approver clicks link → Guest-accessible approval page
 * 6. Decision submitted → recordDecision()
 * 7. Update application status → LoanService
 *
 * @see D03 SRS-LOAN-004 Approval token requirements
 * @see D03 SRS-LOAN-005 Approval page requirements
 * @see D03 SRS-LOAN-006 Decision recording requirements
 * @see Requirements 4.1, 4.3, 4.5
 */
class ApprovalService implements ApprovalServiceInterface
{
    /**
     * Minimum grade required for loan approval
     * Per D02 §6.2 - Grade 41+ officers authorized
     */
    private const MINIMUM_APPROVER_GRADE = 41;

    /**
     * Approval token expiry in hours
     * Per D03 SRS-LOAN-004 - 72 hours validity
     */
    private const TOKEN_EXPIRY_HOURS = 72;

    public function __construct(
        private TokenServiceInterface $tokenService,
        private NotificationService $notificationService
    ) {}

    /**
     * Initiate approval workflow for loan application
     *
     * Generates approval token, finds designated approver, and sends approval email.
     * This is the main entry point for starting the approval process.
     *
     * @param  LoanApplication  $app  The loan application requiring approval
     * @return array{approver_email: string, token: string, expires_at: \Carbon\Carbon}
     *
     * @throws \Exception If no eligible approver found or email sending fails
     */
    public function initiateApproval(LoanApplication $app): array
    {
        try {
            // Find designated approver
            $approverEmail = $this->findApprover($app);

            // Generate approval token (SHA-512 hashed)
            $tokenData = $this->tokenService->generateApprovalToken($app, self::TOKEN_EXPIRY_HOURS);

            // Generate signed URL for approval page
            $signedUrl = $this->generateSignedApprovalUrl($app, $tokenData['token']);

            // Send approval email
            $this->sendApprovalEmail($app, $approverEmail, $signedUrl);

            // Update application status
            $app->update([
                'status' => LoanStatus::UNDER_REVIEW,
                'approver_email' => $approverEmail,
            ]);

            Log::info('Approval workflow initiated', [
                'application_number' => $app->application_number,
                'approver_email' => $approverEmail,
                'token_expires_at' => $tokenData['expires_at'],
            ]);

            return [
                'approver_email' => $approverEmail,
                'token' => $tokenData['token'],
                'expires_at' => $tokenData['expires_at'],
            ];
        } catch (\Exception $e) {
            Log::error('Failed to initiate approval workflow', [
                'application_number' => $app->application_number,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Find designated approver for loan application
     *
     * Searches for Grade 41+ officer in applicant's division.
     * Falls back to BPM admin if no division approver found.
     *
     * Search Priority:
     * 1. Grade 41+ officer in applicant's division
     * 2. Grade 41+ admin in BPM division
     * 3. Any superuser
     *
     * @param  LoanApplication  $app  The loan application
     * @return string The approver's email address
     *
     * @throws \Exception If no eligible approver found
     */
    public function findApprover(LoanApplication $app): string
    {
        // Priority 1: Find Grade 41+ officer in applicant's division
        if ($app->division_id) {
            $divisionApprover = User::where('division_id', $app->division_id)
                ->where('grade', '>=', self::MINIMUM_APPROVER_GRADE)
                ->whereIn('role', ['admin', 'superuser'])
                ->where('email_verified_at', '!=', null)
                ->first();

            if ($divisionApprover) {
                Log::info('Found division approver', [
                    'application_number' => $app->application_number,
                    'approver_email' => $divisionApprover->email,
                    'division_id' => $app->division_id,
                ]);

                return $divisionApprover->email;
            }
        }

        // Priority 2: Find Grade 41+ admin in BPM division
        $bpmApprover = User::where('grade', '>=', self::MINIMUM_APPROVER_GRADE)
            ->whereIn('role', ['admin', 'superuser'])
            ->where('email_verified_at', '!=', null)
            ->first();

        if ($bpmApprover) {
            Log::info('Found BPM approver', [
                'application_number' => $app->application_number,
                'approver_email' => $bpmApprover->email,
            ]);

            return $bpmApprover->email;
        }

        // Priority 3: Fallback to any superuser
        $superuser = User::where('role', 'superuser')
            ->where('email_verified_at', '!=', null)
            ->first();

        if ($superuser) {
            Log::warning('No Grade 41+ approver found, using superuser fallback', [
                'application_number' => $app->application_number,
                'approver_email' => $superuser->email,
            ]);

            return $superuser->email;
        }

        // No eligible approver found
        Log::error('No eligible approver found', [
            'application_number' => $app->application_number,
            'division_id' => $app->division_id,
        ]);

        throw new \Exception('No eligible approver found for loan application. Please contact BPM administrator.');
    }

    /**
     * Record approval decision with audit trail
     *
     * Records the approval/rejection decision in loan_approvals table.
     * Logs IP hash for security audit per D03 §8.1.
     * Updates loan application status accordingly.
     *
     * @param  LoanApplication  $app  The loan application
     * @param  string  $decision  'APPROVED' or 'REJECTED'
     * @param  string|null  $remarks  Optional approval/rejection remarks
     * @param  string  $ipHash  SHA-512 hash of approver's IP address
     *
     * @throws \Exception If decision recording fails
     */
    public function recordDecision(
        LoanApplication $app,
        string $decision,
        ?string $remarks,
        string $ipHash
    ): void {
        // Validate decision
        if (! in_array($decision, ['APPROVED', 'REJECTED'], true)) {
            throw new \InvalidArgumentException("Invalid decision: {$decision}. Must be 'APPROVED' or 'REJECTED'.");
        }

        try {
            DB::beginTransaction();

            // Record approval decision
            $approval = LoanApproval::create([
                'loan_application_id' => $app->id,
                'approver_email' => $app->approver_email,
                'approver_grade' => $this->getApproverGrade($app->approver_email),
                'decision' => $decision,
                'remarks' => $remarks,
                'decision_at' => now(),
                'decision_ip_hash' => $ipHash,
                'token_hash' => $app->approval_token_hash,
                'metadata' => [
                    'application_number' => $app->application_number,
                    'applicant_name' => $app->applicant_name,
                    'applicant_email' => $app->applicant_email,
                    'total_value' => $app->total_value,
                    'loan_duration_days' => $app->getLoanDurationDays(),
                ],
            ]);

            // Update application status
            if ($decision === 'APPROVED') {
                $app->update([
                    'status' => LoanStatus::APPROVED,
                    'approved_at' => now(),
                    'approved_by_name' => $app->approver_email,
                    'approval_remarks' => $remarks,
                    'approval_method' => 'email',
                ]);

                // Update asset status from reserved to allocated
                foreach ($app->loanItems as $item) {
                    $item->asset->update(['status' => 'allocated']);
                }
            } else {
                $app->update([
                    'status' => LoanStatus::REJECTED,
                    'rejected_at' => now(),
                    'rejected_by' => $app->approver_email,
                    'rejection_reason' => $remarks,
                ]);

                // Release reserved assets
                foreach ($app->loanItems as $item) {
                    $item->asset->update(['status' => 'available']);
                }
            }

            DB::commit();

            // Send notification to applicant
            $this->notificationService->sendLoanStatusUpdate(
                $app,
                LoanStatus::UNDER_REVIEW->value
            );

            Log::info('Approval decision recorded', [
                'application_number' => $app->application_number,
                'decision' => $decision,
                'approver_email' => $app->approver_email,
                'approval_id' => $approval->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to record approval decision', [
                'application_number' => $app->application_number,
                'decision' => $decision,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Send approval request email with signed URL
     *
     * Sends email to designated approver with:
     * - Application summary
     * - Signed URL valid for 72 hours
     * - Approve/Reject action buttons
     *
     * @param  LoanApplication  $app  The loan application
     * @param  string  $approverEmail  Approver's email address
     * @param  string  $signedUrl  Signed URL for approval page
     *
     * @throws \Exception If email sending fails
     */
    public function sendApprovalEmail(
        LoanApplication $app,
        string $approverEmail,
        string $signedUrl
    ): void {
        try {
            // Use NotificationService to send approval email
            // This ensures consistent email formatting and bilingual support
            $this->notificationService->sendApprovalRequest(
                $app,
                ['email' => $approverEmail, 'name' => $this->getApproverName($approverEmail)],
                $signedUrl
            );

            Log::info('Approval email sent', [
                'application_number' => $app->application_number,
                'approver_email' => $approverEmail,
                'signed_url_expires' => now()->addHours(self::TOKEN_EXPIRY_HOURS),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send approval email', [
                'application_number' => $app->application_number,
                'approver_email' => $approverEmail,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate signed URL for approval page
     *
     * Creates a temporary signed URL that expires after 72 hours.
     * URL includes approval token for validation.
     *
     * @param  LoanApplication  $app  The loan application
     * @param  string  $token  The approval token (plain, not hashed)
     * @return string The signed URL
     */
    private function generateSignedApprovalUrl(LoanApplication $app, string $token): string
    {
        return URL::temporarySignedRoute(
            'loan.approval.show',
            now()->addHours(self::TOKEN_EXPIRY_HOURS),
            [
                'application' => $app->id,
                'token' => $token,
            ]
        );
    }

    /**
     * Get approver's grade from email
     *
     * @param  string  $email  Approver's email address
     * @return string|null Approver's grade or null if not found
     */
    private function getApproverGrade(string $email): ?string
    {
        $user = User::where('email', $email)->first();

        return $user?->grade;
    }

    /**
     * Get approver's name from email
     *
     * @param  string  $email  Approver's email address
     * @return string Approver's name or email if not found
     */
    private function getApproverName(string $email): string
    {
        $user = User::where('email', $email)->first();

        return $user?->name ?? $email;
    }
}

<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\LoanApplication;

/**
 * Approval Service Interface for ICTServe v3.5.0
 *
 * Provides email-based approval workflow for loan applications.
 * Supports Grade 41+ officers approving/rejecting applications via signed URLs.
 *
 * Features:
 * - Approval token generation with 72-hour expiry
 * - Approver discovery based on division and grade
 * - Decision recording with IP hash logging
 * - Email notification with signed approval URL
 *
 * @see D03 SRS-LOAN-004 Approval token requirements
 * @see D03 SRS-LOAN-005 Approval page requirements
 * @see D03 SRS-LOAN-006 Decision recording requirements
 * @see Requirements 4.1, 4.3, 4.5
 */
interface ApprovalServiceInterface
{
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
    public function initiateApproval(LoanApplication $app): array;

    /**
     * Find designated approver for loan application
     *
     * Searches for Grade 41+ officer in applicant's division.
     * Falls back to BPM admin if no division approver found.
     *
     * @param  LoanApplication  $app  The loan application
     * @return string The approver's email address
     *
     * @throws \Exception If no eligible approver found
     */
    public function findApprover(LoanApplication $app): string;

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
    ): void;

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
    ): void;
}

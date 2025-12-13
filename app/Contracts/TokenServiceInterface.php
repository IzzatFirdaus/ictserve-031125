<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\LoanApplication;
use Illuminate\Database\Eloquent\Model;

/**
 * Token Service Interface for ICTServe v3.5.0
 *
 * Provides secure token generation and validation for:
 * - Status checking (guest access to ticket/loan status)
 * - Approval workflows (email-based loan approvals)
 *
 * All tokens use SHA-512 hashing per D03 §8.1 security requirements.
 *
 * @see D03 SRS-HELP-004 Status token requirements
 * @see D03 SRS-LOAN-004 Approval token requirements
 * @see Requirements 1.5, 4.1, 14.4
 */
interface TokenServiceInterface
{
    /**
     * Generate a status token for a model (HelpdeskTicket or LoanApplication)
     *
     * Creates a unique token that allows guest users to check status without authentication.
     * Token is hashed with SHA-512 before storage.
     *
     * @param  Model  $model  The model to generate token for
     * @return string The plain token (before hashing) to send to user
     */
    public function generateStatusToken(Model $model): string;

    /**
     * Generate an approval token for loan application
     *
     * Creates a signed URL token for email-based approval workflow.
     * Token is hashed with SHA-512 and has configurable expiry.
     *
     * @param  LoanApplication  $app  The loan application
     * @param  int  $expiryHours  Token validity period (default: 72 hours)
     * @return array{token: string, hash: string, expires_at: \Carbon\Carbon}
     */
    public function generateApprovalToken(LoanApplication $app, int $expiryHours = 72): array;

    /**
     * Validate and retrieve model by status token
     *
     * Looks up a model (HelpdeskTicket or LoanApplication) by hashed status token.
     *
     * @param  string  $token  The plain token from user
     * @param  string  $type  Model type ('ticket' or 'loan')
     * @return Model|null The model if found, null otherwise
     */
    public function validateStatusToken(string $token, string $type): ?Model;

    /**
     * Validate approval token for loan application
     *
     * Checks if the provided token matches the stored hash and is not expired.
     *
     * @param  LoanApplication  $app  The loan application
     * @param  string  $token  The plain token from approval link
     * @return bool True if valid and not expired
     */
    public function validateApprovalToken(LoanApplication $app, string $token): bool;

    /**
     * Regenerate approval token for expired applications
     *
     * Creates a new approval token when the original has expired.
     * Only accessible to superuser role.
     *
     * @param  LoanApplication  $app  The loan application
     * @return array{token: string, hash: string, expires_at: \Carbon\Carbon}
     */
    public function regenerateApprovalToken(LoanApplication $app): array;
}

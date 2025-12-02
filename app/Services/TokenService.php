<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\TokenServiceInterface;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Token Service Implementation for ICTServe v3.5.0
 *
 * Implements secure token generation and validation using SHA-512 hashing.
 * Supports both status checking tokens and approval workflow tokens.
 *
 * Security Features:
 * - SHA-512 hashing for all tokens (per D03 §8.1)
 * - Cryptographically secure random token generation
 * - Constant-time comparison to prevent timing attacks
 * - Configurable token expiry for approval workflows
 *
 * @see D03 SRS-HELP-004 Status token requirements
 * @see D03 SRS-LOAN-004 Approval token requirements
 * @see Requirements 1.5, 4.1, 14.4
 */
class TokenService implements TokenServiceInterface
{
    /**
     * Token length in bytes (before hex encoding)
     * Results in 64-character hex string
     */
    private const TOKEN_LENGTH = 32;

    /**
     * Generate a status token for a model
     *
     * Creates a cryptographically secure random token, hashes it with SHA-512,
     * and stores the hash in the model's status_token_hash field.
     *
     * @param  Model  $model  HelpdeskTicket or LoanApplication
     * @return string The plain token to send to user (NOT the hash)
     *
     * @throws \InvalidArgumentException If model doesn't support status tokens
     */
    public function generateStatusToken(Model $model): string
    {
        // Validate model type
        if (! $this->supportsStatusToken($model)) {
            throw new \InvalidArgumentException(
                'Model '.get_class($model).' does not support status tokens'
            );
        }

        // Generate cryptographically secure random token
        $token = $this->generateSecureToken();

        // Hash with SHA-512 and store
        $hash = $this->hashToken($token);
        $model->status_token_hash = $hash;
        $model->save();

        // Return plain token for user (NOT the hash)
        return $token;
    }

    /**
     * Generate an approval token for loan application
     *
     * Creates a token with expiry for email-based approval workflow.
     * Stores both the hash and expiry timestamp.
     *
     * @param  LoanApplication  $app  The loan application
     * @param  int  $expiryHours  Token validity period (default: 72 hours)
     * @return array ['token' => string, 'hash' => string, 'expires_at' => Carbon]
     */
    public function generateApprovalToken(LoanApplication $app, int $expiryHours = 72): array
    {
        // Generate cryptographically secure random token
        $token = $this->generateSecureToken();

        // Hash with SHA-512
        $hash = $this->hashToken($token);

        // Calculate expiry
        $expiresAt = Carbon::now()->addHours($expiryHours);

        // Store hash and expiry
        $app->approval_token_hash = $hash;
        $app->approval_token_expires_at = $expiresAt;
        $app->save();

        return [
            'token' => $token,
            'hash' => $hash,
            'expires_at' => $expiresAt,
        ];
    }

    /**
     * Validate and retrieve model by status token
     *
     * Hashes the provided token and searches for matching model.
     * Uses constant-time comparison to prevent timing attacks.
     *
     * @param  string  $token  The plain token from user
     * @param  string  $type  Model type ('ticket' or 'loan')
     * @return Model|null The model if found, null otherwise
     */
    public function validateStatusToken(string $token, string $type): ?Model
    {
        // Hash the provided token
        $hash = $this->hashToken($token);

        // Query appropriate model
        $model = match ($type) {
            'ticket' => HelpdeskTicket::where('status_token_hash', $hash)->first(),
            'loan' => LoanApplication::where('status_token_hash', $hash)->first(),
            default => null,
        };

        return $model;
    }

    /**
     * Validate approval token for loan application
     *
     * Checks if token matches stored hash and is not expired.
     * Uses constant-time comparison for security.
     *
     * @param  LoanApplication  $app  The loan application
     * @param  string  $token  The plain token from approval link
     * @return bool True if valid and not expired
     */
    public function validateApprovalToken(LoanApplication $app, string $token): bool
    {
        // Check if token hash exists
        if (empty($app->approval_token_hash)) {
            return false;
        }

        // Check if token is expired
        if ($app->approval_token_expires_at === null || $app->approval_token_expires_at->isPast()) {
            return false;
        }

        // Hash provided token
        $hash = $this->hashToken($token);

        // Constant-time comparison to prevent timing attacks
        return hash_equals($app->approval_token_hash, $hash);
    }

    /**
     * Regenerate approval token for expired applications
     *
     * Creates a new approval token when the original has expired.
     * This method should only be called by superuser role.
     *
     * @param  LoanApplication  $app  The loan application
     * @return array ['token' => string, 'hash' => string, 'expires_at' => Carbon]
     */
    public function regenerateApprovalToken(LoanApplication $app): array
    {
        // Generate new token with default 72-hour expiry
        return $this->generateApprovalToken($app, 72);
    }

    /**
     * Generate cryptographically secure random token
     *
     * Uses random_bytes() for cryptographic security.
     * Converts to hexadecimal for URL-safe transmission.
     *
     * @return string 64-character hexadecimal token
     */
    private function generateSecureToken(): string
    {
        return bin2hex(random_bytes(self::TOKEN_LENGTH));
    }

    /**
     * Hash token using SHA-512
     *
     * Per D03 §8.1 security requirements, all tokens must be hashed
     * with SHA-512 before storage.
     *
     * @param  string  $token  The plain token
     * @return string The SHA-512 hash (128 characters)
     */
    private function hashToken(string $token): string
    {
        return hash('sha512', $token);
    }

    /**
     * Check if model supports status tokens
     *
     * @param  Model  $model  The model to check
     * @return bool True if model has status_token_hash field
     */
    private function supportsStatusToken(Model $model): bool
    {
        return $model instanceof HelpdeskTicket || $model instanceof LoanApplication;
    }
}

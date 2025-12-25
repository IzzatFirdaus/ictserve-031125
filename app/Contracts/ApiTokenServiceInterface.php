<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * API Token Service Interface for ICTServe v3.5.0
 *
 * Provides secure API token management using Laravel Sanctum.
 * Supports token creation, revocation, validation, and usage logging.
 *
 * Token Abilities:
 * - read:tickets - Read helpdesk tickets
 * - write:tickets - Create/update helpdesk tickets
 * - read:loans - Read loan applications
 * - write:loans - Create/update loan applications
 * - admin:all - Full administrative access
 *
 * @see D03 SRS-API-001 API Authentication Requirements
 * @see D09 §4.6 Dual Audit System
 * @see Requirements 37.1, 37.2, 37.3, 37.5
 */
interface ApiTokenServiceInterface
{
    /**
     * Create a new API token for a user
     *
     * Generates a Sanctum personal access token with specified abilities
     * and optional expiration period.
     *
     * @param  User  $user  The user to create token for
     * @param  string  $name  Token name for identification
     * @param  array<string>  $abilities  Token abilities (default: ['*'] for all)
     * @param  int|null  $expirationDays  Days until expiration (default: 30, null for no expiry)
     * @return NewAccessToken The newly created token with plain text value
     *
     * @see Requirements 37.1, 37.2
     */
    

/**
 * @param array<string, mixed> $abilities
 */
public function createToken(User $user, string $name, array $abilities = ['*'], ?int $expirationDays = 30): NewAccessToken;

    /**
     * Revoke a specific token for a user
     *
     * Deletes the specified token, invalidating it immediately.
     * Logs the revocation action in audit trail.
     *
     * @param  User  $user  The user who owns the token
     * @param  int  $tokenId  The token ID to revoke
     * @return bool True if token was revoked, false if not found
     *
     * @see Requirements 37.3
     */
    public function revokeToken(User $user, int $tokenId): bool;

    /**
     * Revoke all tokens for a user
     *
     * Deletes all personal access tokens for the user.
     * Useful for security incidents or account deactivation.
     *
     * @param  User  $user  The user whose tokens to revoke
     * @return int Number of tokens revoked
     *
     * @see Requirements 37.3
     */
    public function revokeAllTokens(User $user): int;

    /**
     * Get all active (non-expired) tokens for a user
     *
     * Returns collection of tokens that are still valid.
     * Excludes expired tokens.
     *
     * @param  User  $user  The user to get tokens for
     * @return Collection<int, PersonalAccessToken> Collection of active tokens
     *
     * @see Requirements 37.2
     */
    public function getActiveTokens(User $user): Collection;

    /**
     * Validate if a token has required abilities
     *
     * Checks if the token has all the specified abilities.
     * Supports wildcard ability ('*') which grants all permissions.
     *
     * @param  PersonalAccessToken  $token  The token to validate
     * @param  array<string>  $requiredAbilities  Abilities to check for
     * @return bool True if token has all required abilities
     *
     * @see Requirements 37.3
     */
    

/**
 * @param array<string, mixed> $requiredAbilities
 */
public function validateTokenAbilities(PersonalAccessToken $token, array $requiredAbilities): bool;

    /**
     * Log API token usage for audit trail
     *
     * Records token usage in api_token_usage_logs table.
     * Includes action, endpoint, IP hash, and response status.
     *
     * @param  PersonalAccessToken  $token  The token being used
     * @param  string  $action  The action performed (e.g., 'api.tickets.index')
     * @param  string|null  $endpoint  The API endpoint accessed
     * @param  string|null  $ipAddress  Client IP address (will be hashed)
     * @param  string|null  $userAgent  Client user agent
     * @param  int|null  $responseStatus  HTTP response status code
     *
     * @see Requirements 37.5
     */
    public function logTokenUsage(
        PersonalAccessToken $token,
        string $action,
        ?string $endpoint = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?int $responseStatus = null
    ): void;
}

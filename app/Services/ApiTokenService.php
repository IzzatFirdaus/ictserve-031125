<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ApiTokenServiceInterface;
use App\Models\ApiTokenUsageLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * API Token Service Implementation for ICTServe v3.5.0
 *
 * Implements secure API token management using Laravel Sanctum.
 * Provides token creation, revocation, validation, and usage logging.
 *
 * Security Features:
 * - Configurable token expiration (default: 30 days)
 * - Fine-grained abilities for permission control
 * - IP address hashing for privacy (SHA-512)
 * - Comprehensive audit logging
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
class ApiTokenService implements ApiTokenServiceInterface
{
    /**
     * Available token abilities
     */
    public const ABILITIES = [
        'read:tickets',
        'write:tickets',
        'read:loans',
        'write:loans',
        'admin:all',
    ];

    /**
     * Default token expiration in days
     */
    private const DEFAULT_EXPIRATION_DAYS = 30;

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
     */
    public function createToken(
        User $user,
        string $name,
        array $abilities = ['*'],
        ?int $expirationDays = self::DEFAULT_EXPIRATION_DAYS
    ): NewAccessToken {
        // Calculate expiration date
        $expiresAt = $expirationDays !== null
            ? Carbon::now()->addDays($expirationDays)
            : null;

        // Create the token using Sanctum
        $token = $user->createToken(
            name: $name,
            abilities: $abilities,
            expiresAt: $expiresAt
        );

        // Log token creation
        $this->logTokenUsage(
            token: $token->accessToken,
            action: 'token.created',
            endpoint: '/api/tokens',
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
            responseStatus: 201
        );

        // Log activity for audit trail
        activity('api_token')
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties([
                'token_name' => $name,
                'abilities' => $abilities,
                'expires_at' => $expiresAt?->toIso8601String(),
            ])
            ->log('API token created');

        return $token;
    }

    /**
     * Revoke a specific token for a user
     *
     * Deletes the specified token, invalidating it immediately.
     * Logs the revocation action in audit trail.
     *
     * @param  User  $user  The user who owns the token
     * @param  int  $tokenId  The token ID to revoke
     * @return bool True if token was revoked, false if not found
     */
    public function revokeToken(User $user, int $tokenId): bool
    {
        /** @var PersonalAccessToken|null $token */
        $token = $user->tokens()->find($tokenId);

        if ($token === null) {
            return false;
        }

        $tokenName = $token->name;

        // Log before deletion
        $this->logTokenUsage(
            token: $token,
            action: 'token.revoked',
            endpoint: "/api/tokens/{$tokenId}",
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
            responseStatus: 200
        );

        // Delete the token
        $token->delete();

        // Log activity for audit trail
        /** @var User|null $currentUser */
        $currentUser = Auth::user();
        activity('api_token')
            ->performedOn($user)
            ->causedBy($currentUser ?? $user)
            ->withProperties([
                'token_id' => $tokenId,
                'token_name' => $tokenName,
            ])
            ->log('API token revoked');

        return true;
    }

    /**
     * Revoke all tokens for a user
     *
     * Deletes all personal access tokens for the user.
     * Useful for security incidents or account deactivation.
     *
     * @param  User  $user  The user whose tokens to revoke
     * @return int Number of tokens revoked
     */
    public function revokeAllTokens(User $user): int
    {
        $tokenCount = $user->tokens()->count();

        if ($tokenCount === 0) {
            return 0;
        }

        // Delete all tokens
        $user->tokens()->delete();

        // Log activity for audit trail
        /** @var User|null $currentUser */
        $currentUser = Auth::user();
        activity('api_token')
            ->performedOn($user)
            ->causedBy($currentUser ?? $user)
            ->withProperties([
                'tokens_revoked' => $tokenCount,
            ])
            ->log('All API tokens revoked');

        return $tokenCount;
    }

    /**
     * Get all active (non-expired) tokens for a user
     *
     * Returns collection of tokens that are still valid.
     * Excludes expired tokens.
     *
     * @param  User  $user  The user to get tokens for
     * @return Collection<int, PersonalAccessToken> Collection of active tokens
     */
    public function getActiveTokens(User $user): Collection
    {
        return $user->tokens()
            ->where(function ($query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Validate if a token has required abilities
     *
     * Checks if the token has all the specified abilities.
     * Supports wildcard ability ('*') which grants all permissions.
     *
     * @param  PersonalAccessToken  $token  The token to validate
     * @param  array<string>  $requiredAbilities  Abilities to check for
     * @return bool True if token has all required abilities
     */
    public function validateTokenAbilities(PersonalAccessToken $token, array $requiredAbilities): bool
    {
        // Wildcard grants all abilities
        if (\in_array('*', $token->abilities, true)) {
            return true;
        }

        // Check if token has all required abilities
        foreach ($requiredAbilities as $ability) {
            if (! \in_array($ability, $token->abilities, true)) {
                // Check for admin:all which grants all abilities
                if (! \in_array('admin:all', $token->abilities, true)) {
                    return false;
                }
            }
        }

        return true;
    }

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
     */
    public function logTokenUsage(
        PersonalAccessToken $token,
        string $action,
        ?string $endpoint = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?int $responseStatus = null
    ): void {
        ApiTokenUsageLog::create([
            'personal_access_token_id' => $token->id,
            'user_id' => $token->tokenable_id,
            'action' => $action,
            'endpoint' => $endpoint ?? '/api/unknown',
            'ip_hash' => $ipAddress !== null ? $this->hashIpAddress($ipAddress) : null,
            'user_agent' => $userAgent !== null ? $this->truncateUserAgent($userAgent) : null,
            'response_status' => $responseStatus ?? 200,
            'created_at' => Carbon::now(),
        ]);
    }

    /**
     * Hash IP address using SHA-512 for privacy
     *
     * Per D03 §8.1 security requirements, IP addresses must be hashed
     * before storage to protect user privacy.
     *
     * @param  string  $ipAddress  The IP address to hash
     * @return string The SHA-512 hash (128 characters)
     */
    private function hashIpAddress(string $ipAddress): string
    {
        return hash('sha512', $ipAddress);
    }

    /**
     * Truncate user agent string to reasonable length
     *
     * @param  string  $userAgent  The user agent string
     * @param  int  $maxLength  Maximum length (default: 500)
     * @return string Truncated user agent
     */
    private function truncateUserAgent(string $userAgent, int $maxLength = 500): string
    {
        if (\strlen($userAgent) <= $maxLength) {
            return $userAgent;
        }

        return \substr($userAgent, 0, $maxLength - 3).'...';
    }

    /**
     * Get available token abilities
     *
     * @return array<string> List of available abilities
     */
    public static function getAvailableAbilities(): array
    {
        return self::ABILITIES;
    }

    /**
     * Check if an ability is valid
     *
     * @param  string  $ability  The ability to check
     * @return bool True if ability is valid
     */
    public static function isValidAbility(string $ability): bool
    {
        return $ability === '*' || \in_array($ability, self::ABILITIES, true);
    }

    /**
     * Get token usage statistics for a user
     *
     * @param  User  $user  The user to get statistics for
     * @param  int  $days  Number of days to look back (default: 30)
     * @return array<string, mixed> Usage statistics
     */
    public function getTokenUsageStats(User $user, int $days = 30): array
    {
        $since = Carbon::now()->subDays($days);

        $logs = ApiTokenUsageLog::where('user_id', $user->id)
            ->where('created_at', '>=', $since)
            ->get();

        return [
            'total_requests' => $logs->count(),
            'successful_requests' => $logs->filter(fn ($log) => $log->isSuccessful())->count(),
            'failed_requests' => $logs->filter(fn ($log) => $log->isFailed())->count(),
            'unique_endpoints' => $logs->pluck('endpoint')->filter()->unique()->count(),
            'actions_breakdown' => $logs->groupBy('action')->map(fn ($group) => $group->count())->toArray(),
            'period_days' => $days,
        ];
    }

    /**
     * Check if a token is expired
     *
     * @param  PersonalAccessToken  $token  The token to check
     * @return bool True if token is expired
     */
    public function isTokenExpired(PersonalAccessToken $token): bool
    {
        if ($token->expires_at === null) {
            return false;
        }

        return Carbon::parse($token->expires_at)->isPast();
    }

    /**
     * Get token expiration status
     *
     * @param  PersonalAccessToken  $token  The token to check
     * @return array<string, mixed> Expiration status details
     */
    public function getTokenExpirationStatus(PersonalAccessToken $token): array
    {
        if ($token->expires_at === null) {
            return [
                'expires' => false,
                'expires_at' => null,
                'is_expired' => false,
                'days_remaining' => null,
            ];
        }

        $expiresAt = Carbon::parse($token->expires_at);
        $isExpired = $expiresAt->isPast();

        return [
            'expires' => true,
            'expires_at' => $expiresAt->toIso8601String(),
            'is_expired' => $isExpired,
            'days_remaining' => $isExpired ? 0 : (int) Carbon::now()->diffInDays($expiresAt),
        ];
    }
}

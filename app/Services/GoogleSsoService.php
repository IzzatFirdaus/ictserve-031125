<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\GoogleSsoServiceInterface;
use App\Contracts\SsoHealthCheckInterface;
use App\Events\GoogleSsoLinked;
use App\Exceptions\InvalidEmailDomainException;
use App\Models\SsoAuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;

/**
 * Google SSO Service Implementation for ICTServe v3.6.0
 *
 * Implements Google Single Sign-On functionality for MOTAC staff with:
 * - Email domain validation (@motac.gov.my only)
 * - User creation and account linking with idempotent behavior
 * - Comprehensive audit logging via dual audit system
 * - Error handling and graceful degradation
 *
 * Security Features:
 * - Domain whitelist validation (case-insensitive)
 * - Password hashing for SSO-created users
 * - Activity logging for compliance (owen-it + spatie)
 * - IP address and user agent tracking
 *
 * @see D00 §4.1 True Hybrid Architecture
 * @see D03-FR-001.3 Google SSO Authentication
 * @see D04 §6.1 Security
 * @see D09 Database Documentation (Dual Audit System)
 * @see Requirements 1.1, 1.2, 2.1, 4.1
 */
class GoogleSsoService implements GoogleSsoServiceInterface
{
    /**
     * Allowed email domains for SSO authentication
     * Per D00 §4.1, only MOTAC staff can authenticate via SSO
     */
    private const ALLOWED_DOMAINS = ['motac.gov.my'];

    /**
     * Default role for new SSO registrations
     */
    private const DEFAULT_ROLE = 'staff';

    /**
     * Cache TTL for user profile data (in seconds)
     * 1 hour default for performance optimization
     */
    private const CACHE_TTL = 900;

    /**
     * Cache key prefix for SSO user profiles
     */
    private const CACHE_PREFIX = 'sso:profile:';

    public function __construct(private readonly SsoHealthCheckInterface $healthCheck)
    {
    }

    /**
     * Validate that email domain is @motac.gov.my
     *
     * Performs case-insensitive domain validation to ensure
     * only MOTAC staff can authenticate via Google SSO.
     *
     * @param  string  $email  The email address to validate
     * @return bool True if domain is @motac.gov.my
     */
    public function validateDomain(string $email): bool
    {
        $email = Str::lower(trim($email));

        // Extract domain from email
        $parts = explode('@', $email);

        if (count($parts) !== 2) {
            return false;
        }

        [$localPart, $domain] = $parts;

        // Reject if local part is empty
        if (empty($localPart)) {
            return false;
        }

        return in_array($domain, self::ALLOWED_DOMAINS, true);
    }

    /**
     * Create or update user from Google OAuth profile
     *
     * Creates a new user if email doesn't exist, or updates existing user
     * with Google SSO credentials. Ensures idempotent behavior - multiple
     * authentications for the same Google user will not create duplicate records.
     *
     * @param  SocialiteUser  $googleUser  The Google OAuth user profile
     * @return User The created or updated user
     *
     * @throws InvalidEmailDomainException If email is not @motac.gov.my
     */
    public function createOrUpdateUser(SocialiteUser $googleUser): User
    {
        $rawEmail = $googleUser->getEmail();

        // Handle null email from Google OAuth
        if ($rawEmail === null) {
            $this->logAuthenticationAttempt('unknown', false, 'No email provided by Google');

            throw new InvalidEmailDomainException('', self::ALLOWED_DOMAINS);
        }

        $email = Str::lower(trim($rawEmail));

        // Validate email domain
        if (! $this->validateDomain($email)) {
            $this->logAuthenticationAttempt($email, false, 'Invalid email domain');

            throw new InvalidEmailDomainException($email, self::ALLOWED_DOMAINS);
        }

        // Find existing user by email
        $user = User::where('email', $email)->first();

        if (! $user) {
            // Create new user from Google profile
            $user = $this->createUserFromGoogle($googleUser, $email);

            $this->logAuthenticationAttempt($email, true, null, $user->id);

            Log::info('New user registered via Google SSO', [
                'user_id' => $user->id,
                'email' => $email,
            ]);
        } else {
            // Link existing account if not already linked
            if (! $user->google_id) {
                $this->linkExistingAccount($user, $googleUser);
            }

            $this->logAuthenticationAttempt($email, true, null, $user->id);
        }

        return $user;
    }

    /**
     * Create a new user from Google OAuth profile
     *
     * @param  SocialiteUser  $googleUser  The Google OAuth user profile
     * @param  string  $email  The normalized email address
     * @return User The newly created user
     */
    private function createUserFromGoogle(SocialiteUser $googleUser, string $email): User
    {
        return DB::transaction(function () use ($googleUser, $email): User {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $email,
                'password' => bcrypt(Str::random(32)), // Random password for SSO users
                'email_verified_at' => now(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'role' => self::DEFAULT_ROLE,
                'locale' => 'ms', // Default to Bahasa Melayu per D15
                'is_active' => true,
            ]);

            // Log activity for audit compliance
            $this->logUserCreationActivity($user);

            return $user;
        });
    }

    /**
     * Link existing user account to Google SSO
     *
     * Updates an existing user's Google SSO credentials without
     * modifying other user data. Dispatches GoogleSsoLinked event
     * for real-time UI updates via Echo/Reverb.
     *
     * @param  User  $user  The existing user to link
     * @param  SocialiteUser  $googleUser  The Google OAuth user profile
     */
    public function linkExistingAccount(User $user, SocialiteUser $googleUser): void
    {
        DB::transaction(function () use ($user, $googleUser): void {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);

            // Dispatch broadcast event for real-time UI update
            GoogleSsoLinked::dispatch($user, $user->email);

            // Log activity for audit compliance
            $this->logAccountLinkingActivity($user);
        });

        Log::info('Existing user linked to Google SSO', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * Log authentication attempt for audit compliance
     *
     * Creates audit log entry for all SSO authentication attempts,
     * both successful and failed, per D09 audit requirements.
     * Uses dual audit system (owen-it + spatie).
     *
     * @param  string  $email  The email address used in attempt
     * @param  bool  $success  Whether authentication succeeded
     * @param  string|null  $error  Error message if authentication failed
     * @param  int|null  $userId  User ID if authentication succeeded
     */
    public function logAuthenticationAttempt(
        string $email,
        bool $success,
        ?string $error = null,
        ?int $userId = null
    ): void {
        $logData = [
            'email' => $email,
            'success' => $success,
            'error' => $error,
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ];

        // Log to activity log (spatie/laravel-activitylog)
        $activity = activity('google_sso_authentication')
            ->withProperties($logData);

        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $activity->performedOn($user)->causedBy($user);
            }
        }

        $activity->log($success ? 'Google SSO authentication successful' : 'Google SSO authentication failed');

        // Also log to Laravel's log system
        $logLevel = $success ? 'info' : 'warning';
        Log::channel('single')->{$logLevel}('Google SSO authentication attempt', $logData);

        $user = $userId ? User::find($userId) : null;
        $auditPayload = [
            'user_id' => $user?->id,
            'email' => $email,
            'google_id' => $user?->google_id,
            'ip_address' => $logData['ip_address'] ?? 'unknown',
            'user_agent' => $logData['user_agent'] ?? null,
        ];

        if ($success) {
            SsoAuditLog::logSuccess($auditPayload);
        } else {
            $auditPayload['error_type'] = 'general_error';
            $auditPayload['error_message'] = $error;
            SsoAuditLog::logFailure($auditPayload);
        }
    }

    /**
     * Get health status of Google SSO service
     *
     * Returns array with service availability and configuration status.
     * Checks if Google OAuth credentials are properly configured.
     *
     * @return array{available: bool, configured: bool, message: string}
     */
    public function getHealthStatus(): array
    {
        $status = $this->healthCheck->getServiceStatus();

        return [
            'available' => $status['status'] === 'healthy' && ($status['available'] ?? false),
            'configured' => (bool) ($status['configured'] ?? false),
            'message' => $status['message'] ?? 'Google SSO status tidak diketahui',
            'details' => $status['details'] ?? [],
        ];
    }

    /**
     * Get allowed email domains for SSO
     *
     * @return array<string> List of allowed domains
     */
    public function getAllowedDomains(): array
    {
        return self::ALLOWED_DOMAINS;
    }

    /**
     * Check if user has Google SSO linked
     *
     * @param  User  $user  The user to check
     * @return bool True if user has Google SSO linked
     */
    public function hasGoogleSsoLinked(User $user): bool
    {
        return ! empty($user->google_id);
    }

    /**
     * Unlink Google SSO from user account
     *
     * Removes Google SSO credentials from user account while
     * preserving other user data. Logs the action for audit compliance.
     *
     * @param  User  $user  The user to unlink
     * @return bool True if unlink was successful
     */
    public function unlinkGoogleSso(User $user): bool
    {
        if (! $this->hasGoogleSsoLinked($user)) {
            return false;
        }

        DB::transaction(function () use ($user): void {
            $user->update([
                'google_id' => null,
                'google_token' => null,
                'google_refresh_token' => null,
            ]);

            // Log activity for audit compliance
            $this->logSsoUnlinkActivity($user);
        });

        Log::info('Google SSO unlinked from user account', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return true;
    }

    /**
     * Log user creation activity for audit compliance
     *
     * @param  User  $user  The newly created user
     */
    private function logUserCreationActivity(User $user): void
    {
        activity('google_sso_registration')
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties([
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toIso8601String(),
            ])
            ->log('User registered via Google SSO');
    }

    /**
     * Log account linking activity for audit compliance
     *
     * @param  User  $user  The user whose account was linked
     */
    private function logAccountLinkingActivity(User $user): void
    {
        activity('google_sso_linking')
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties([
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toIso8601String(),
            ])
            ->log('Existing account linked to Google SSO');
    }

    /**
     * Log SSO unlink activity for audit compliance
     *
     * @param  User  $user  The user whose SSO was unlinked
     */
    private function logSsoUnlinkActivity(User $user): void
    {
        activity('google_sso_unlink')
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties([
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toIso8601String(),
            ])
            ->log('Google SSO unlinked from account');
    }

    // =========================================================================
    // Caching Methods (v3.6.0 Performance Enhancement)
    // =========================================================================

    /**
     * Get cached user profile by Google ID
     *
     * Retrieves user profile from cache if available, otherwise
     * fetches from database and caches the result.
     *
     * @param  string  $googleId  The Google user ID
     * @return User|null The cached user or null if not found
     */
    public function getCachedUserByGoogleId(string $googleId): ?User
    {
        $cacheKey = self::CACHE_PREFIX . $googleId;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($googleId): ?User {
            return User::where('google_id', $googleId)->first();
        });
    }

    /**
     * Cache user profile data after successful authentication
     *
     * @param  User  $user  The user to cache
     */
    public function cacheUserProfile(User $user): void
    {
        if (empty($user->google_id)) {
            return;
        }

        $cacheKey = self::CACHE_PREFIX . $user->google_id;
        Cache::put($cacheKey, $user, self::CACHE_TTL);

        Log::debug('User profile cached for SSO', [
            'user_id' => $user->id,
            'google_id' => $user->google_id,
            'cache_ttl' => self::CACHE_TTL,
        ]);
    }

    /**
     * Invalidate cached user profile
     *
     * Should be called when user data changes or SSO is unlinked.
     *
     * @param  string  $googleId  The Google user ID to invalidate
     */
    public function invalidateUserCache(string $googleId): void
    {
        $cacheKey = self::CACHE_PREFIX . $googleId;
        Cache::forget($cacheKey);

        Log::debug('User profile cache invalidated', [
            'google_id' => $googleId,
        ]);
    }

    /**
     * Invalidate user cache by User model
     *
     * @param  User  $user  The user whose cache should be invalidated
     */
    public function invalidateUserCacheByUser(User $user): void
    {
        if (! empty($user->google_id)) {
            $this->invalidateUserCache($user->google_id);
        }
    }

    /**
     * Get cache statistics for monitoring
     *
     * @return array{cache_driver: string, prefix: string, ttl: int}
     */
    public function getCacheStats(): array
    {
        return [
            'cache_driver' => config('cache.default'),
            'prefix' => self::CACHE_PREFIX,
            'ttl' => self::CACHE_TTL,
        ];
    }

    /**
     * Warm cache for frequently accessed users
     *
     * Pre-loads user profiles into cache for users who have
     * logged in recently via SSO.
     *
     * @param  int  $limit  Maximum number of users to cache
     * @return int Number of users cached
     */
    public function warmCache(int $limit = 100): int
    {
        $users = User::whereNotNull('google_id')
            ->where('is_active', true)
            ->orderBy('last_login_at', 'desc')
            ->limit($limit)
            ->get();

        $count = 0;
        foreach ($users as $user) {
            $this->cacheUserProfile($user);
            $count++;
        }

        Log::info('SSO user cache warmed', [
            'users_cached' => $count,
            'limit' => $limit,
        ]);

        return $count;
    }
}

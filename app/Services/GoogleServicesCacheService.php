<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\GoogleServicesCacheServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Google Services Cache Service for ICTServe v3.6.1
 *
 * Provides centralized caching for Google services including:
 * - User profile caching for SSO
 * - OAuth token caching
 * - Verification status caching
 * - Gmail quota caching
 *
 * @see Requirements 13.2, 13.3
 */
class GoogleServicesCacheService implements GoogleServicesCacheServiceInterface
{
    /**
     * Cache key prefixes
     */
    private const PREFIX_USER_PROFILE = 'google:user:profile:';

    private const PREFIX_OAUTH_TOKEN = 'google:oauth:token:';

    private const PREFIX_VERIFICATION_STATUS = 'google:verification:status';

    private const PREFIX_GMAIL_QUOTA = 'google:gmail:quota';

    private const PREFIX_SSO_HEALTH = 'google:sso:health';

    private const PREFIX_GMAIL_HEALTH = 'google:gmail:health';

    private const PREFIX_PERFORMANCE_METRICS = 'google:performance:metrics';

    /**
     * Cache TTL values (in seconds)
     */
    private const TTL_USER_PROFILE = 900;        // 15 minutes

    private const TTL_OAUTH_TOKEN = 3600;        // 1 hour

    private const TTL_VERIFICATION_STATUS = 300; // 5 minutes

    private const TTL_GMAIL_QUOTA = 60;          // 1 minute

    private const TTL_HEALTH_STATUS = 30;        // 30 seconds

    private const TTL_PERFORMANCE_METRICS = 60;  // 1 minute

    // =========================================================================
    // User Profile Caching
    // =========================================================================

    /**
     * Get cached user profile by Google ID
     */
    public function getUserProfile(string $googleId): ?User
    {
        $cacheKey = self::PREFIX_USER_PROFILE.$googleId;

        return Cache::get($cacheKey);
    }

    /**
     * Cache user profile
     */
    public function cacheUserProfile(User $user): void
    {
        if (empty($user->google_id)) {
            return;
        }

        $cacheKey = self::PREFIX_USER_PROFILE.$user->google_id;
        Cache::put($cacheKey, $user, self::TTL_USER_PROFILE);

        Log::debug('Google Services: User profile cached', [
            'user_id' => $user->id,
            'google_id' => $user->google_id,
            'ttl' => self::TTL_USER_PROFILE,
        ]);
    }

    /**
     * Invalidate user profile cache
     */
    public function invalidateUserProfile(string $googleId): void
    {
        $cacheKey = self::PREFIX_USER_PROFILE.$googleId;
        Cache::forget($cacheKey);

        Log::debug('Google Services: User profile cache invalidated', [
            'google_id' => $googleId,
        ]);
    }

    /**
     * Get or cache user profile
     */
    public function rememberUserProfile(string $googleId, callable $callback): ?User
    {
        $cacheKey = self::PREFIX_USER_PROFILE.$googleId;

        return Cache::remember($cacheKey, self::TTL_USER_PROFILE, $callback);
    }

    // =========================================================================
    // OAuth Token Caching
    // =========================================================================

    /**
     * Get cached OAuth token
     */
    public function getOAuthToken(string $userId): ?array
    {
        $cacheKey = self::PREFIX_OAUTH_TOKEN.$userId;

        return Cache::get($cacheKey);
    }

    /**
     * Cache OAuth token
     */
    public function cacheOAuthToken(string $userId, array $token, ?int $expiresIn = null): void
    {
        $cacheKey = self::PREFIX_OAUTH_TOKEN.$userId;
        $ttl = $expiresIn ?? self::TTL_OAUTH_TOKEN;

        // Don't cache longer than token expiry
        if (isset($token['expires_in']) && $token['expires_in'] < $ttl) {
            $ttl = (int) $token['expires_in'] - 60; // 1 minute buffer
        }

        if ($ttl > 0) {
            Cache::put($cacheKey, $token, $ttl);

            Log::debug('Google Services: OAuth token cached', [
                'user_id' => $userId,
                'ttl' => $ttl,
            ]);
        }
    }

    /**
     * Invalidate OAuth token cache
     */
    public function invalidateOAuthToken(string $userId): void
    {
        $cacheKey = self::PREFIX_OAUTH_TOKEN.$userId;
        Cache::forget($cacheKey);

        Log::debug('Google Services: OAuth token cache invalidated', [
            'user_id' => $userId,
        ]);
    }

    // =========================================================================
    // Verification Status Caching
    // =========================================================================

    /**
     * Get cached verification status
     */
    public function getVerificationStatus(): ?array
    {
        return Cache::get(self::PREFIX_VERIFICATION_STATUS);
    }

    /**
     * Cache verification status
     */
    public function cacheVerificationStatus(array $status): void
    {
        Cache::put(self::PREFIX_VERIFICATION_STATUS, $status, self::TTL_VERIFICATION_STATUS);

        Log::debug('Google Services: Verification status cached', [
            'status' => $status['status'] ?? 'unknown',
            'ttl' => self::TTL_VERIFICATION_STATUS,
        ]);
    }

    /**
     * Invalidate verification status cache
     */
    public function invalidateVerificationStatus(): void
    {
        Cache::forget(self::PREFIX_VERIFICATION_STATUS);
    }

    /**
     * Get or cache verification status
     */
    public function rememberVerificationStatus(callable $callback): array
    {
        return Cache::remember(
            self::PREFIX_VERIFICATION_STATUS,
            self::TTL_VERIFICATION_STATUS,
            $callback
        );
    }

    // =========================================================================
    // Gmail Quota Caching
    // =========================================================================

    /**
     * Get cached Gmail quota
     */
    public function getGmailQuota(): ?array
    {
        return Cache::get(self::PREFIX_GMAIL_QUOTA);
    }

    /**
     * Cache Gmail quota
     */
    public function cacheGmailQuota(array $quota): void
    {
        Cache::put(self::PREFIX_GMAIL_QUOTA, $quota, self::TTL_GMAIL_QUOTA);
    }

    /**
     * Invalidate Gmail quota cache
     */
    public function invalidateGmailQuota(): void
    {
        Cache::forget(self::PREFIX_GMAIL_QUOTA);
    }

    // =========================================================================
    // Health Status Caching
    // =========================================================================

    /**
     * Get cached SSO health status
     */
    public function getSsoHealthStatus(): ?array
    {
        return Cache::get(self::PREFIX_SSO_HEALTH);
    }

    /**
     * Cache SSO health status
     */
    public function cacheSsoHealthStatus(array $status): void
    {
        Cache::put(self::PREFIX_SSO_HEALTH, $status, self::TTL_HEALTH_STATUS);
    }

    /**
     * Get cached Gmail health status
     */
    public function getGmailHealthStatus(): ?array
    {
        return Cache::get(self::PREFIX_GMAIL_HEALTH);
    }

    /**
     * Cache Gmail health status
     */
    public function cacheGmailHealthStatus(array $status): void
    {
        Cache::put(self::PREFIX_GMAIL_HEALTH, $status, self::TTL_HEALTH_STATUS);
    }

    /**
     * Invalidate all health status caches
     */
    public function invalidateHealthStatus(): void
    {
        Cache::forget(self::PREFIX_SSO_HEALTH);
        Cache::forget(self::PREFIX_GMAIL_HEALTH);
    }

    // =========================================================================
    // Performance Metrics Caching
    // =========================================================================

    /**
     * Get cached performance metrics
     */
    public function getPerformanceMetrics(): ?array
    {
        return Cache::get(self::PREFIX_PERFORMANCE_METRICS);
    }

    /**
     * Cache performance metrics
     */
    public function cachePerformanceMetrics(array $metrics): void
    {
        Cache::put(self::PREFIX_PERFORMANCE_METRICS, $metrics, self::TTL_PERFORMANCE_METRICS);
    }

    /**
     * Invalidate performance metrics cache
     */
    public function invalidatePerformanceMetrics(): void
    {
        Cache::forget(self::PREFIX_PERFORMANCE_METRICS);
    }

    // =========================================================================
    // Cache Warming
    // =========================================================================

    /**
     * Warm cache for frequently accessed users
     *
     * @param  int  $limit  Maximum number of users to cache
     * @return int Number of users cached
     */
    public function warmUserProfileCache(int $limit = 100): int
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

        Log::info('Google Services: User profile cache warmed', [
            'users_cached' => $count,
            'limit' => $limit,
        ]);

        return $count;
    }

    // =========================================================================
    // Cache Statistics
    // =========================================================================

    /**
     * Get cache statistics
     */
    public function getCacheStatistics(): array
    {
        return [
            'driver' => config('cache.default'),
            'prefixes' => [
                'user_profile' => self::PREFIX_USER_PROFILE,
                'oauth_token' => self::PREFIX_OAUTH_TOKEN,
                'verification_status' => self::PREFIX_VERIFICATION_STATUS,
                'gmail_quota' => self::PREFIX_GMAIL_QUOTA,
                'sso_health' => self::PREFIX_SSO_HEALTH,
                'gmail_health' => self::PREFIX_GMAIL_HEALTH,
                'performance_metrics' => self::PREFIX_PERFORMANCE_METRICS,
            ],
            'ttl' => [
                'user_profile' => self::TTL_USER_PROFILE,
                'oauth_token' => self::TTL_OAUTH_TOKEN,
                'verification_status' => self::TTL_VERIFICATION_STATUS,
                'gmail_quota' => self::TTL_GMAIL_QUOTA,
                'health_status' => self::TTL_HEALTH_STATUS,
                'performance_metrics' => self::TTL_PERFORMANCE_METRICS,
            ],
            'cached_items' => [
                'verification_status' => Cache::has(self::PREFIX_VERIFICATION_STATUS),
                'gmail_quota' => Cache::has(self::PREFIX_GMAIL_QUOTA),
                'sso_health' => Cache::has(self::PREFIX_SSO_HEALTH),
                'gmail_health' => Cache::has(self::PREFIX_GMAIL_HEALTH),
                'performance_metrics' => Cache::has(self::PREFIX_PERFORMANCE_METRICS),
            ],
        ];
    }

    /**
     * Clear all Google services caches
     */
    public function clearAllCaches(): void
    {
        $this->invalidateVerificationStatus();
        $this->invalidateGmailQuota();
        $this->invalidateHealthStatus();
        $this->invalidatePerformanceMetrics();

        Log::info('Google Services: All caches cleared');
    }
}

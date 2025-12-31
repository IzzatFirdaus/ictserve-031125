<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\User;

/**
 * Google Services Cache Service Interface
 *
 * @see Requirements 13.2, 13.3
 */
interface GoogleServicesCacheServiceInterface
{
    /**
     * Get cached user profile by Google ID
     */
    public function getUserProfile(string $googleId): ?User;

    /**
     * Cache user profile
     */
    public function cacheUserProfile(User $user): void;

    /**
     * Invalidate user profile cache
     */
    public function invalidateUserProfile(string $googleId): void;

    /**
     * Get or cache user profile
     */
    public function rememberUserProfile(string $googleId, callable $callback): ?User;

    /**
     * Get cached OAuth token
     */
    public function getOAuthToken(string $userId): ?array;

    /**
     * Cache OAuth token
     */
    public function cacheOAuthToken(string $userId, array $token, ?int $expiresIn = null): void;

    /**
     * Invalidate OAuth token cache
     */
    public function invalidateOAuthToken(string $userId): void;

    /**
     * Get cached verification status
     */
    public function getVerificationStatus(): ?array;

    /**
     * Cache verification status
     */
    public function cacheVerificationStatus(array $status): void;

    /**
     * Invalidate verification status cache
     */
    public function invalidateVerificationStatus(): void;

    /**
     * Get or cache verification status
     */
    public function rememberVerificationStatus(callable $callback): array;

    /**
     * Get cached Gmail quota
     */
    public function getGmailQuota(): ?array;

    /**
     * Cache Gmail quota
     */
    public function cacheGmailQuota(array $quota): void;

    /**
     * Invalidate Gmail quota cache
     */
    public function invalidateGmailQuota(): void;

    /**
     * Get cached SSO health status
     */
    public function getSsoHealthStatus(): ?array;

    /**
     * Cache SSO health status
     */
    public function cacheSsoHealthStatus(array $status): void;

    /**
     * Get cached Gmail health status
     */
    public function getGmailHealthStatus(): ?array;

    /**
     * Cache Gmail health status
     */
    public function cacheGmailHealthStatus(array $status): void;

    /**
     * Invalidate all health status caches
     */
    public function invalidateHealthStatus(): void;

    /**
     * Get cached performance metrics
     */
    public function getPerformanceMetrics(): ?array;

    /**
     * Cache performance metrics
     */
    public function cachePerformanceMetrics(array $metrics): void;

    /**
     * Invalidate performance metrics cache
     */
    public function invalidatePerformanceMetrics(): void;

    /**
     * Warm cache for frequently accessed users
     */
    public function warmUserProfileCache(int $limit = 100): int;

    /**
     * Get cache statistics
     */
    public function getCacheStatistics(): array;

    /**
     * Clear all Google services caches
     */
    public function clearAllCaches(): void;
}

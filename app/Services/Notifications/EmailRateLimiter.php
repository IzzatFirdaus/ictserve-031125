<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Email Rate Limiter Service
 *
 * Implements rate limiting for email sending to prevent abuse
 * and ensure system stability under high load.
 *
 * @see Requirements 8.6 - Rate limiting for notification dispatch
 *
 * @trace D03 SRS-FR-043 (notification performance)
 */
class EmailRateLimiter
{
    /**
     * Default rate limit: 100 emails per minute per user.
     */
    private const DEFAULT_USER_LIMIT = 100;

    /**
     * Default rate limit: 1000 emails per minute system-wide.
     */
    private const DEFAULT_SYSTEM_LIMIT = 1000;

    /**
     * Rate limit window in seconds (1 minute).
     */
    private const WINDOW_SECONDS = 60;

    /**
     * Cache prefix for rate limiting.
     */
    private const CACHE_PREFIX = 'email_rate_limit:';

    /**
     * Check if user can send email (within rate limit).
     */
    public function canSendForUser(int $userId): bool
    {
        $key = $this->getUserKey($userId);
        $current = (int) Cache::get($key, 0);
        $limit = $this->getUserLimit();

        return $current < $limit;
    }

    /**
     * Check if system can send email (within global rate limit).
     */
    public function canSendSystemWide(): bool
    {
        $key = $this->getSystemKey();
        $current = (int) Cache::get($key, 0);
        $limit = $this->getSystemLimit();

        return $current < $limit;
    }

    /**
     * Record an email send attempt for a user.
     */
    public function recordSendForUser(int $userId): void
    {
        $key = $this->getUserKey($userId);
        $current = (int) Cache::get($key, 0);

        Cache::put($key, $current + 1, self::WINDOW_SECONDS);

        // Also record system-wide
        $this->recordSystemSend();
    }

    /**
     * Record a system-wide email send.
     */
    public function recordSystemSend(): void
    {
        $key = $this->getSystemKey();
        $current = (int) Cache::get($key, 0);

        Cache::put($key, $current + 1, self::WINDOW_SECONDS);
    }

    /**
     * Get remaining sends for a user.
     */
    public function getRemainingForUser(int $userId): int
    {
        $key = $this->getUserKey($userId);
        $current = (int) Cache::get($key, 0);
        $limit = $this->getUserLimit();

        return max(0, $limit - $current);
    }

    /**
     * Get remaining system-wide sends.
     */
    public function getRemainingSystemWide(): int
    {
        $key = $this->getSystemKey();
        $current = (int) Cache::get($key, 0);
        $limit = $this->getSystemLimit();

        return max(0, $limit - $current);
    }

    /**
     * Get time until rate limit resets for a user.
     */
    public function getResetTimeForUser(int $userId): int
    {
        $key = $this->getUserKey($userId);

        return Cache::has($key) ? self::WINDOW_SECONDS : 0;
    }

    /**
     * Check if sending is allowed and record if so.
     *
     * Returns true if email can be sent, false if rate limited.
     */
    public function attemptSend(int $userId): bool
    {
        if (! $this->canSendSystemWide()) {
            Log::channel('notifications')->warning('Email rate limit exceeded (system-wide)', [
                'user_id' => $userId,
                'limit' => $this->getSystemLimit(),
            ]);

            return false;
        }

        if (! $this->canSendForUser($userId)) {
            Log::channel('notifications')->warning('Email rate limit exceeded (user)', [
                'user_id' => $userId,
                'limit' => $this->getUserLimit(),
            ]);

            return false;
        }

        $this->recordSendForUser($userId);

        return true;
    }

    /**
     * Get rate limit statistics.
     *
     * @return array<string, mixed>
     */
    public function getStatistics(int $userId): array
    {
        return [
            'user_remaining' => $this->getRemainingForUser($userId),
            'user_limit' => $this->getUserLimit(),
            'system_remaining' => $this->getRemainingSystemWide(),
            'system_limit' => $this->getSystemLimit(),
            'window_seconds' => self::WINDOW_SECONDS,
        ];
    }

    /**
     * Clear rate limit for a user (admin function).
     */
    public function clearUserLimit(int $userId): void
    {
        Cache::forget($this->getUserKey($userId));

        Log::channel('notifications')->info('Email rate limit cleared for user', [
            'user_id' => $userId,
        ]);
    }

    /**
     * Clear system-wide rate limit (admin function).
     */
    public function clearSystemLimit(): void
    {
        Cache::forget($this->getSystemKey());

        Log::channel('notifications')->info('System-wide email rate limit cleared');
    }

    /**
     * Get user rate limit from config.
     */
    private function getUserLimit(): int
    {
        return (int) config('notifications.rate_limits.user_per_minute', self::DEFAULT_USER_LIMIT);
    }

    /**
     * Get system rate limit from config.
     */
    private function getSystemLimit(): int
    {
        return (int) config('notifications.rate_limits.system_per_minute', self::DEFAULT_SYSTEM_LIMIT);
    }

    /**
     * Generate cache key for user rate limit.
     */
    private function getUserKey(int $userId): string
    {
        return self::CACHE_PREFIX."user:{$userId}";
    }

    /**
     * Generate cache key for system rate limit.
     */
    private function getSystemKey(): string
    {
        return self::CACHE_PREFIX.'system';
    }
}

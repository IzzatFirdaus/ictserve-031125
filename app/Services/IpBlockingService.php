<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BlockedIp;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * IP Blocking Service
 *
 * Manages IP-based blocking for abuse prevention.
 * Supports automatic blocking based on rate limit violations
 * and manual blocking by administrators.
 */
class IpBlockingService
{
    /**
     * Cache key prefix for blocked IPs.
     */
    private const CACHE_PREFIX = 'blocked_ip:';

    /**
     * Cache TTL in seconds (5 minutes).
     */
    private const CACHE_TTL = 300;

    /**
     * Number of rate limit violations before auto-blocking.
     */
    private const AUTO_BLOCK_THRESHOLD = 5;

    /**
     * Default auto-block duration in hours.
     */
    private const AUTO_BLOCK_DURATION_HOURS = 24;

    /**
     * Check if an IP address is blocked.
     */
    public function isBlocked(string $ipAddress): bool
    {
        $cacheKey = self::CACHE_PREFIX.$ipAddress;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($ipAddress): bool {
            return BlockedIp::isBlocked($ipAddress);
        });
    }

    /**
     * Get the active block for an IP address.
     */
    public function getActiveBlock(string $ipAddress): ?BlockedIp
    {
        return BlockedIp::getActiveBlock($ipAddress);
    }

    /**
     * Record a rate limit violation and auto-block if threshold exceeded.
     */
    public function recordViolation(string $ipAddress, string $reason = 'Rate limit exceeded'): void
    {
        $existingBlock = BlockedIp::where('ip_address', $ipAddress)->first();

        if ($existingBlock) {
            // Increment violation count
            $existingBlock->increment('violation_count');

            // If already blocked and active, extend the block
            if ($existingBlock->isActive()) {
                $existingBlock->update([
                    'expires_at' => now()->addHours(self::AUTO_BLOCK_DURATION_HOURS),
                    'reason' => $reason.' (repeated violation)',
                ]);
            } elseif ($existingBlock->violation_count >= self::AUTO_BLOCK_THRESHOLD) {
                // Reactivate block if threshold exceeded again
                $existingBlock->update([
                    'blocked_at' => now(),
                    'expires_at' => now()->addHours(self::AUTO_BLOCK_DURATION_HOURS * 2), // Double duration for repeat offenders
                    'reason' => $reason.' (repeat offender)',
                ]);
            }
        } else {
            // Create new record
            BlockedIp::create([
                'ip_address' => $ipAddress,
                'reason' => $reason,
                'type' => 'auto',
                'violation_count' => 1,
                'blocked_at' => now(),
                'expires_at' => null, // Not blocked yet, just tracking
            ]);
        }

        // Check if we should auto-block
        $this->checkAndAutoBlock($ipAddress, $reason);

        // Clear cache
        $this->clearCache($ipAddress);
    }

    /**
     * Check violation count and auto-block if threshold exceeded.
     */
    private function checkAndAutoBlock(string $ipAddress, string $reason): void
    {
        $record = BlockedIp::where('ip_address', $ipAddress)->first();

        if ($record && $record->violation_count >= self::AUTO_BLOCK_THRESHOLD && ! $record->isActive()) {
            $record->update([
                'blocked_at' => now(),
                'expires_at' => now()->addHours(self::AUTO_BLOCK_DURATION_HOURS),
                'reason' => $reason.' (auto-blocked after '.self::AUTO_BLOCK_THRESHOLD.' violations)',
            ]);

            Log::warning('IP auto-blocked for abuse', [
                'ip_address' => $ipAddress,
                'violation_count' => $record->violation_count,
                'expires_at' => $record->expires_at,
            ]);
        }
    }

    /**
     * Manually block an IP address.
     */
    public function blockIp(
        string $ipAddress,
        string $reason,
        ?int $blockedBy = null,
        ?int $durationHours = null
    ): BlockedIp {
        $expiresAt = $durationHours ? now()->addHours($durationHours) : null;

        $block = BlockedIp::updateOrCreate(
            ['ip_address' => $ipAddress],
            [
                'reason' => $reason,
                'type' => 'manual',
                'blocked_at' => now(),
                'expires_at' => $expiresAt,
                'blocked_by' => $blockedBy,
            ]
        );

        // Increment violation count if existing
        if (! $block->wasRecentlyCreated) {
            $block->increment('violation_count');
        }

        Log::info('IP manually blocked', [
            'ip_address' => $ipAddress,
            'reason' => $reason,
            'blocked_by' => $blockedBy,
            'expires_at' => $expiresAt,
        ]);

        $this->clearCache($ipAddress);

        return $block;
    }

    /**
     * Unblock an IP address.
     */
    public function unblockIp(string $ipAddress): bool
    {
        $block = BlockedIp::where('ip_address', $ipAddress)->first();

        if (! $block) {
            return false;
        }

        // Set expiration to now (soft unblock, keeps history)
        $block->update(['expires_at' => now()]);

        Log::info('IP unblocked', ['ip_address' => $ipAddress]);

        $this->clearCache($ipAddress);

        return true;
    }

    /**
     * Permanently delete an IP block record.
     */
    public function deleteBlock(string $ipAddress): bool
    {
        $deleted = BlockedIp::where('ip_address', $ipAddress)->delete();

        if ($deleted) {
            Log::info('IP block record deleted', ['ip_address' => $ipAddress]);
            $this->clearCache($ipAddress);
        }

        return $deleted > 0;
    }

    /**
     * Get all currently blocked IPs.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, BlockedIp>
     */
    public function getBlockedIps(): \Illuminate\Database\Eloquent\Collection
    {
        return BlockedIp::active()->orderByDesc('blocked_at')->get();
    }

    /**
     * Clean up expired blocks.
     */
    public function cleanupExpiredBlocks(): int
    {
        return BlockedIp::expired()->delete();
    }

    /**
     * Clear the cache for an IP address.
     */
    private function clearCache(string $ipAddress): void
    {
        Cache::forget(self::CACHE_PREFIX.$ipAddress);
    }

    /**
     * Get violation count for an IP address.
     */
    public function getViolationCount(string $ipAddress): int
    {
        return BlockedIp::where('ip_address', $ipAddress)->value('violation_count') ?? 0;
    }

    /**
     * Get the auto-block threshold.
     */
    public function getAutoBlockThreshold(): int
    {
        return self::AUTO_BLOCK_THRESHOLD;
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\WidgetDataUpdated;
use App\Models\User;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Contracts\Broadcasting\Broadcaster;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Widget Real-Time Manager Service
 *
 * Manages WebSocket broadcasting for dashboard widget updates with rate limiting,
 * caching, and fallback polling support. Integrates with Laravel Reverb for
 * real-time widget data synchronization across user sessions.
 *
 * @see D16_BROADCASTING_SETUP.md - WebSocket configuration
 * @see Requirements R8 (Real-time Updates), R19 (Real-Time Widget Updates)
 *
 * @trace D03 SRS-FR-008, D04 §5.3 - Real-time dashboard requirements
 *
 * @version 3.6.1
 *
 * @since 3.6.0
 */
class WidgetRealtimeManager
{
    /**
     * Rate limiting configuration
     */
    private const RATE_LIMIT_MAX_ATTEMPTS = 60; // Max broadcasts per minute

    private const RATE_LIMIT_DECAY_MINUTES = 1;

    private const USER_RATE_LIMIT_MAX_ATTEMPTS = 30; // Max per user per minute

    private const WIDGET_CACHE_TTL = 120; // 2 minutes cache TTL

    /**
     * Fallback polling configuration
     */
    private const FALLBACK_POLLING_INTERVAL = 30; // 30 seconds as per requirements

    private const MAX_RETRY_ATTEMPTS = 3;

    public function __construct(
        private readonly Cache $cache,
        private readonly Broadcaster $broadcaster
    ) {}

    /**
     * Broadcast widget data update to appropriate channels
     *
     * @param  string  $widgetId  Widget identifier
     * @param  string  $widgetType  Widget type (e.g., 'stats', 'chart', 'ai_performance')
     * @param  array  $data  Widget data payload
     * @param  int|null  $userId  Target user ID (null for global broadcast)
     * @param  int|null  $refreshInterval  Custom refresh interval in seconds
     * @return bool Success status
     */
    public function broadcastWidgetUpdate(
        string $widgetId,
        string $widgetType,
        array $data,
        ?int $userId = null,
        ?int $refreshInterval = null
    ): bool {
        try {
            // Rate limiting check
            if (! $this->checkRateLimit($widgetId, $userId)) {
                Log::warning('Widget broadcast rate limit exceeded', [
                    'widget_id' => $widgetId,
                    'user_id' => $userId,
                ]);

                return false;
            }

            // Check if data has actually changed (avoid unnecessary broadcasts)
            if (! $this->hasDataChanged($widgetId, $data)) {
                Log::debug('Widget data unchanged, skipping broadcast', [
                    'widget_id' => $widgetId,
                ]);

                return true; // Not an error, just no change
            }

            // Create and dispatch the broadcast event
            $event = new WidgetDataUpdated(
                widgetId: $widgetId,
                widgetType: $widgetType,
                data: $data,
                userId: $userId,
                refreshInterval: $refreshInterval
            );

            broadcast($event);

            // Cache the data for change detection
            $this->cacheWidgetData($widgetId, $data);

            Log::info('Widget update broadcasted successfully', [
                'widget_id' => $widgetId,
                'widget_type' => $widgetType,
                'user_id' => $userId,
                'data_size' => count($data),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to broadcast widget update', [
                'widget_id' => $widgetId,
                'widget_type' => $widgetType,
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Broadcast update to user-specific widget channel
     *
     * @param  int  $userId  Target user ID
     * @param  string  $widgetId  Widget identifier
     * @param  string  $widgetType  Widget type
     * @param  array  $data  Widget data
     * @param  int|null  $refreshInterval  Custom refresh interval
     * @return bool Success status
     */
    public function broadcastToUser(
        int $userId,
        string $widgetId,
        string $widgetType,
        array $data,
        ?int $refreshInterval = null
    ): bool {
        return $this->broadcastWidgetUpdate(
            widgetId: $widgetId,
            widgetType: $widgetType,
            data: $data,
            userId: $userId,
            refreshInterval: $refreshInterval
        );
    }

    /**
     * Broadcast update to global admin channel
     *
     * @param  string  $widgetId  Widget identifier
     * @param  string  $widgetType  Widget type
     * @param  array  $data  Widget data
     * @param  int|null  $refreshInterval  Custom refresh interval
     * @return bool Success status
     */
    public function broadcastToAdmins(
        string $widgetId,
        string $widgetType,
        array $data,
        ?int $refreshInterval = null
    ): bool {
        return $this->broadcastWidgetUpdate(
            widgetId: $widgetId,
            widgetType: $widgetType,
            data: $data,
            userId: null, // Global broadcast
            refreshInterval: $refreshInterval
        );
    }

    /**
     * Subscribe user to widget updates
     *
     * @param  int  $userId  User ID
     * @param  string  $widgetId  Widget identifier
     * @return bool Success status
     */
    public function subscribeUserToWidget(int $userId, string $widgetId): bool
    {
        try {
            $subscriptionKey = "widget_subscription:{$userId}:{$widgetId}";
            $this->cache->put($subscriptionKey, true, now()->addHours(24));

            Log::info('User subscribed to widget updates', [
                'user_id' => $userId,
                'widget_id' => $widgetId,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to subscribe user to widget', [
                'user_id' => $userId,
                'widget_id' => $widgetId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Unsubscribe user from widget updates
     *
     * @param  int  $userId  User ID
     * @param  string  $widgetId  Widget identifier
     * @return bool Success status
     */
    public function unsubscribeUserFromWidget(int $userId, string $widgetId): bool
    {
        try {
            $subscriptionKey = "widget_subscription:{$userId}:{$widgetId}";
            $this->cache->forget($subscriptionKey);

            Log::info('User unsubscribed from widget updates', [
                'user_id' => $userId,
                'widget_id' => $widgetId,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to unsubscribe user from widget', [
                'user_id' => $userId,
                'widget_id' => $widgetId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get fallback polling data for widgets when WebSocket is unavailable
     *
     * @param  array  $widgetIds  Array of widget IDs to poll
     * @param  int|null  $userId  User ID for authorization
     * @return array Widget data for polling
     */
    public function getFallbackPollingData(array $widgetIds, ?int $userId = null): array
    {
        $pollingData = [];

        foreach ($widgetIds as $widgetId) {
            try {
                // Check if user is authorized to access this widget
                if ($userId && ! $this->isUserAuthorizedForWidget($userId, $widgetId)) {
                    continue;
                }

                // Get cached widget data
                $cacheKey = "widget_data:{$widgetId}";
                $cachedData = $this->cache->get($cacheKey);

                if ($cachedData) {
                    $pollingData[$widgetId] = [
                        'data' => $cachedData,
                        'timestamp' => now()->toISOString(),
                        'cache_hit' => true,
                    ];
                } else {
                    // Widget data not in cache, mark for refresh
                    $pollingData[$widgetId] = [
                        'data' => null,
                        'timestamp' => now()->toISOString(),
                        'cache_hit' => false,
                        'needs_refresh' => true,
                    ];
                }
            } catch (\Exception $e) {
                Log::error('Error getting fallback polling data for widget', [
                    'widget_id' => $widgetId,
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);

                $pollingData[$widgetId] = [
                    'data' => null,
                    'timestamp' => now()->toISOString(),
                    'error' => 'Failed to retrieve widget data',
                ];
            }
        }

        return $pollingData;
    }

    /**
     * Check rate limiting for widget broadcasts
     *
     * @param  string  $widgetId  Widget identifier
     * @param  int|null  $userId  User ID (for user-specific rate limiting)
     * @return bool Whether broadcast is allowed
     */
    private function checkRateLimit(string $widgetId, ?int $userId = null): bool
    {
        // Global widget rate limit
        $globalKey = "widget_broadcast_rate:{$widgetId}";
        if (! RateLimiter::attempt(
            $globalKey,
            self::RATE_LIMIT_MAX_ATTEMPTS,
            function () {},
            self::RATE_LIMIT_DECAY_MINUTES * 60
        )) {
            return false;
        }

        // User-specific rate limit (if user is specified)
        if ($userId) {
            $userKey = "user_widget_broadcast_rate:{$userId}";
            if (! RateLimiter::attempt(
                $userKey,
                self::USER_RATE_LIMIT_MAX_ATTEMPTS,
                function () {},
                self::RATE_LIMIT_DECAY_MINUTES * 60
            )) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if widget data has changed since last broadcast
     *
     * @param  string  $widgetId  Widget identifier
     * @param  array  $newData  New widget data
     * @return bool Whether data has changed
     */
    private function hasDataChanged(string $widgetId, array $newData): bool
    {
        $cacheKey = "widget_data:{$widgetId}";
        $cachedData = $this->cache->get($cacheKey);

        if (! $cachedData) {
            return true; // No cached data, consider it changed
        }

        // Compare data using hash for efficiency
        $newHash = md5(serialize($newData));
        $cachedHash = md5(serialize($cachedData));

        return $newHash !== $cachedHash;
    }

    /**
     * Cache widget data for change detection
     *
     * @param  string  $widgetId  Widget identifier
     * @param  array  $data  Widget data
     */
    private function cacheWidgetData(string $widgetId, array $data): void
    {
        $cacheKey = "widget_data:{$widgetId}";
        $this->cache->put($cacheKey, $data, self::WIDGET_CACHE_TTL);
    }

    /**
     * Check if user is authorized to access widget
     *
     * @param  int  $userId  User ID
     * @param  string  $widgetId  Widget identifier
     * @return bool Authorization status
     */
    private function isUserAuthorizedForWidget(int $userId, string $widgetId): bool
    {
        try {
            $user = User::find($userId);
            if (! $user) {
                return false;
            }

            // Check subscription status
            $subscriptionKey = "widget_subscription:{$userId}:{$widgetId}";
            if (! $this->cache->has($subscriptionKey)) {
                return false;
            }

            // Additional authorization checks can be added here
            // For now, subscription implies authorization
            return true;
        } catch (\Exception $e) {
            Log::error('Error checking widget authorization', [
                'user_id' => $userId,
                'widget_id' => $widgetId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get widget broadcasting statistics
     *
     * @return array Broadcasting statistics
     */
    public function getBroadcastingStats(): array
    {
        return [
            'rate_limit_config' => [
                'max_attempts_per_minute' => self::RATE_LIMIT_MAX_ATTEMPTS,
                'user_max_attempts_per_minute' => self::USER_RATE_LIMIT_MAX_ATTEMPTS,
                'cache_ttl_seconds' => self::WIDGET_CACHE_TTL,
            ],
            'fallback_config' => [
                'polling_interval_seconds' => self::FALLBACK_POLLING_INTERVAL,
                'max_retry_attempts' => self::MAX_RETRY_ATTEMPTS,
            ],
            'timestamp' => now()->toISOString(),
        ];
    }
}

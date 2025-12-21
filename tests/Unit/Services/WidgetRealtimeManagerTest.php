<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Events\WidgetDataUpdated;
use App\Services\WidgetRealtimeManager;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Contracts\Broadcasting\Broadcaster;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Mockery;

/**
 * Widget Real-Time Manager Service Tests
 *
 * Comprehensive test suite for the WidgetRealtimeManager service covering
 * broadcasting, rate limiting, caching, and fallback polling functionality.
 *
 * @see app/Services/WidgetRealtimeManager.php
 * @trace D03 SRS-FR-008, D04 §5.3 - Real-time dashboard requirements
 * @requirements R8 (Real-time Updates), R19 (Real-Time Widget Updates)
 *
 * @package Tests\Unit\Services
 * @version 3.6.1
 * @since 3.6.0
 */
class WidgetRealtimeManagerTest extends TestCase
{
    private WidgetRealtimeManager $realtimeManager;
    private Cache $mockCache;
    private Broadcaster $mockBroadcaster;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock dependencies
        $this->mockCache = Mockery::mock(Cache::class);
        $this->mockBroadcaster = Mockery::mock(Broadcaster::class);

        // Create service instance with mocked dependencies
        $this->realtimeManager = new WidgetRealtimeManager(
            $this->mockCache,
            $this->mockBroadcaster
        );

        // Clear rate limiter state
        RateLimiter::clear('widget_broadcast_rate:test_widget');
        RateLimiter::clear('user_widget_broadcast_rate:1');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_can_broadcast_widget_update_successfully(): void
    {
        // Arrange
        Event::fake();

        $widgetId = 'test_widget';
        $widgetType = 'stats';
        $data = ['count' => 42, 'status' => 'active'];
        $userId = 1;

        // Mock cache operations for change detection
        $this->mockCache->shouldReceive('get')
            ->with("widget_data:{$widgetId}")
            ->once()
            ->andReturn(null); // No cached data, so change detected

        $this->mockCache->shouldReceive('put')
            ->with("widget_data:{$widgetId}", $data, 120)
            ->once();

        // Act
        $result = $this->realtimeManager->broadcastWidgetUpdate(
            $widgetId,
            $widgetType,
            $data,
            $userId
        );

        // Assert
        $this->assertTrue($result);
        Event::assertDispatched(WidgetDataUpdated::class, function ($event) use ($widgetId, $widgetType, $data, $userId) {
            return $event->widgetId === $widgetId &&
                $event->widgetType === $widgetType &&
                $event->data === $data &&
                $event->userId === $userId;
        });
    }

    #[Test]
    public function it_skips_broadcast_when_data_unchanged(): void
    {
        // Arrange
        Event::fake();

        $widgetId = 'test_widget';
        $widgetType = 'stats';
        $data = ['count' => 42, 'status' => 'active'];

        // Mock cache to return same data (no change)
        $this->mockCache->shouldReceive('get')
            ->with("widget_data:{$widgetId}")
            ->once()
            ->andReturn($data);

        // Act
        $result = $this->realtimeManager->broadcastWidgetUpdate(
            $widgetId,
            $widgetType,
            $data
        );

        // Assert
        $this->assertTrue($result); // Still returns true (not an error)
        Event::assertNotDispatched(WidgetDataUpdated::class);
    }

    #[Test]
    public function it_respects_rate_limiting(): void
    {
        // Arrange
        Event::fake();
        Log::shouldReceive('warning')->once();

        $widgetId = 'test_widget';
        $widgetType = 'stats';
        $data = ['count' => 42];

        // Exhaust rate limit
        for ($i = 0; $i < 60; $i++) {
            RateLimiter::hit("widget_broadcast_rate:{$widgetId}");
        }

        // Act
        $result = $this->realtimeManager->broadcastWidgetUpdate(
            $widgetId,
            $widgetType,
            $data
        );

        // Assert
        $this->assertFalse($result);
        Event::assertNotDispatched(WidgetDataUpdated::class);
    }

    #[Test]
    public function it_can_broadcast_to_user_channel(): void
    {
        // Arrange
        Event::fake();

        $userId = 1;
        $widgetId = 'user_stats';
        $widgetType = 'personal';
        $data = ['notifications' => 5];

        // Mock cache operations
        $this->mockCache->shouldReceive('get')->andReturn(null);
        $this->mockCache->shouldReceive('put')->once();

        // Act
        $result = $this->realtimeManager->broadcastToUser(
            $userId,
            $widgetId,
            $widgetType,
            $data
        );

        // Assert
        $this->assertTrue($result);
        Event::assertDispatched(WidgetDataUpdated::class, function ($event) use ($userId) {
            return $event->userId === $userId;
        });
    }

    #[Test]
    public function it_can_broadcast_to_admin_channel(): void
    {
        // Arrange
        Event::fake();

        $widgetId = 'system_stats';
        $widgetType = 'admin';
        $data = ['cpu_usage' => 75.5];

        // Mock cache operations
        $this->mockCache->shouldReceive('get')->andReturn(null);
        $this->mockCache->shouldReceive('put')->once();

        // Act
        $result = $this->realtimeManager->broadcastToAdmins(
            $widgetId,
            $widgetType,
            $data
        );

        // Assert
        $this->assertTrue($result);
        Event::assertDispatched(WidgetDataUpdated::class, function ($event) {
            return $event->userId === null; // Global broadcast
        });
    }

    #[Test]
    public function it_can_subscribe_user_to_widget(): void
    {
        // Arrange
        $userId = 1;
        $widgetId = 'test_widget';

        $this->mockCache->shouldReceive('put')
            ->with("widget_subscription:{$userId}:{$widgetId}", true, Mockery::any())
            ->once();

        // Act
        $result = $this->realtimeManager->subscribeUserToWidget($userId, $widgetId);

        // Assert
        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_unsubscribe_user_from_widget(): void
    {
        // Arrange
        $userId = 1;
        $widgetId = 'test_widget';

        $this->mockCache->shouldReceive('forget')
            ->with("widget_subscription:{$userId}:{$widgetId}")
            ->once();

        // Act
        $result = $this->realtimeManager->unsubscribeUserFromWidget($userId, $widgetId);

        // Assert
        $this->assertTrue($result);
    }

    #[Test]
    public function it_provides_fallback_polling_data(): void
    {
        // Arrange
        $widgetIds = ['widget1', 'widget2'];
        $userId = null; // Use null to skip authorization checks

        // Mock cache responses for widget data
        $this->mockCache->shouldReceive('get')
            ->with('widget_data:widget1')
            ->once()
            ->andReturn(['data' => 'cached_data_1']);

        $this->mockCache->shouldReceive('get')
            ->with('widget_data:widget2')
            ->once()
            ->andReturn(null);

        // Act
        $result = $this->realtimeManager->getFallbackPollingData($widgetIds, $userId);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('widget1', $result);
        $this->assertArrayHasKey('widget2', $result);

        $this->assertEquals(['data' => 'cached_data_1'], $result['widget1']['data']);
        $this->assertTrue($result['widget1']['cache_hit']);

        $this->assertNull($result['widget2']['data']);
        $this->assertFalse($result['widget2']['cache_hit']);
        $this->assertTrue($result['widget2']['needs_refresh']);
    }

    #[Test]
    public function it_handles_exceptions_gracefully(): void
    {
        // Arrange
        Log::shouldReceive('error')->once();

        $widgetId = 'test_widget';
        $widgetType = 'stats';
        $data = ['count' => 42];

        // Mock cache to throw exception
        $this->mockCache->shouldReceive('get')
            ->andThrow(new \Exception('Cache error'));

        // Act
        $result = $this->realtimeManager->broadcastWidgetUpdate(
            $widgetId,
            $widgetType,
            $data
        );

        // Assert
        $this->assertFalse($result);
    }

    #[Test]
    public function it_provides_broadcasting_statistics(): void
    {
        // Act
        $stats = $this->realtimeManager->getBroadcastingStats();

        // Assert
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('rate_limit_config', $stats);
        $this->assertArrayHasKey('fallback_config', $stats);
        $this->assertArrayHasKey('timestamp', $stats);

        $this->assertEquals(60, $stats['rate_limit_config']['max_attempts_per_minute']);
        $this->assertEquals(30, $stats['rate_limit_config']['user_max_attempts_per_minute']);
        $this->assertEquals(120, $stats['rate_limit_config']['cache_ttl_seconds']);

        $this->assertEquals(30, $stats['fallback_config']['polling_interval_seconds']);
        $this->assertEquals(3, $stats['fallback_config']['max_retry_attempts']);
    }

    #[Test]
    public function it_handles_custom_refresh_intervals(): void
    {
        // Arrange
        Event::fake();

        $widgetId = 'test_widget';
        $widgetType = 'stats';
        $data = ['count' => 42];
        $customInterval = 60; // 1 minute

        // Mock cache operations
        $this->mockCache->shouldReceive('get')->andReturn(null);
        $this->mockCache->shouldReceive('put')->once();

        // Act
        $result = $this->realtimeManager->broadcastWidgetUpdate(
            $widgetId,
            $widgetType,
            $data,
            null,
            $customInterval
        );

        // Assert
        $this->assertTrue($result);
        Event::assertDispatched(WidgetDataUpdated::class, function ($event) use ($customInterval) {
            return $event->refreshInterval === $customInterval;
        });
    }
}

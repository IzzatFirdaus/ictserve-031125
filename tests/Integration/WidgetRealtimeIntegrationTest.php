<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Events\WidgetDataUpdated;
use App\Models\User;
use App\Services\WidgetRealtimeManager;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Widget Real-Time Integration Tests
 *
 * Integration tests for the complete widget real-time system including
 * WebSocket broadcasting, API endpoints, and fallback polling.
 *
 * @see app/Services/WidgetRealtimeManager.php
 * @see app/Http/Controllers/Api/WidgetPollingController.php
 *
 * @trace D03 SRS-FR-008, D04 §5.3 - Real-time dashboard requirements
 *
 * @requirements R8 (Real-time Updates), R19 (Real-Time Widget Updates)
 *
 * @version 3.6.1
 *
 * @since 3.6.0
 */
class WidgetRealtimeIntegrationTest extends TestCase
{
    private User $user;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->user = User::factory()->create([
            'email' => 'staff@motac.gov.my',
        ]);
        $this->user->assignRole('staff');

        $this->adminUser = User::factory()->create([
            'email' => 'admin@motac.gov.my',
        ]);
        $this->adminUser->assignRole('admin');
    }

    #[Test]
    public function it_can_broadcast_and_receive_widget_updates(): void
    {
        // Arrange
        Event::fake();
        $realtimeManager = app(WidgetRealtimeManager::class);

        $widgetId = 'test_widget';
        $widgetType = 'stats';
        $data = ['count' => 42, 'status' => 'active'];

        // Act
        $result = $realtimeManager->broadcastWidgetUpdate(
            $widgetId,
            $widgetType,
            $data,
            $this->user->id
        );

        // Assert
        $this->assertTrue($result);
        $user = $this->user;
        Event::assertDispatched(WidgetDataUpdated::class, function ($event) use ($widgetId, $user) {
            return $event->widgetId === $widgetId &&
                $event->userId === $user->id;
        });
    }

    #[Test]
    public function it_provides_polling_data_via_api(): void
    {
        // Arrange
        $realtimeManager = app(WidgetRealtimeManager::class);

        // Subscribe user to widget
        $widgetId = 'test_widget';
        $realtimeManager->subscribeUserToWidget($this->user->id, $widgetId);

        // Broadcast some data to cache it
        $data = ['count' => 42, 'status' => 'active'];
        $realtimeManager->broadcastWidgetUpdate($widgetId, 'stats', $data, $this->user->id);

        // Act
        $response = $this->actingAs($this->user)
            ->postJson('/api/widgets/polling-data', [
                'widget_ids' => [$widgetId],
            ]);

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    $widgetId => [
                        'data',
                        'timestamp',
                        'cache_hit',
                    ],
                ],
                'timestamp',
                'polling_interval',
                'user_id',
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals($this->user->id, $response->json('user_id'));
        $this->assertEquals(30, $response->json('polling_interval'));
    }

    #[Test]
    public function it_handles_single_widget_polling(): void
    {
        // Arrange
        $realtimeManager = app(WidgetRealtimeManager::class);

        $widgetId = 'single_widget';
        $realtimeManager->subscribeUserToWidget($this->user->id, $widgetId);

        $data = ['value' => 123];
        $realtimeManager->broadcastWidgetUpdate($widgetId, 'metric', $data, $this->user->id);

        // Act
        $response = $this->actingAs($this->user)
            ->getJson("/api/widgets/{$widgetId}/data");

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data',
                    'timestamp',
                    'cache_hit',
                ],
                'widget_id',
                'timestamp',
                'user_id',
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals($widgetId, $response->json('widget_id'));
        $this->assertEquals($this->user->id, $response->json('user_id'));
    }

    #[Test]
    public function it_provides_broadcasting_stats_for_admins(): void
    {
        // Act
        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/widgets/broadcasting/stats');

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'rate_limit_config' => [
                        'max_attempts_per_minute',
                        'user_max_attempts_per_minute',
                        'cache_ttl_seconds',
                    ],
                    'fallback_config' => [
                        'polling_interval_seconds',
                        'max_retry_attempts',
                    ],
                    'timestamp',
                ],
                'timestamp',
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals(60, $response->json('data.rate_limit_config.max_attempts_per_minute'));
        $this->assertEquals(30, $response->json('data.fallback_config.polling_interval_seconds'));
    }

    #[Test]
    public function it_denies_broadcasting_stats_for_non_admins(): void
    {
        // Act
        $response = $this->actingAs($this->user)
            ->getJson('/api/widgets/broadcasting/stats');

        // Assert
        $response->assertForbidden()
            ->assertJson([
                'success' => false,
                'error' => 'Unauthorized access',
                'message' => 'Akses tidak dibenarkan.',
            ]);
    }

    #[Test]
    public function it_provides_health_check_endpoint(): void
    {
        // Act
        $response = $this->actingAs($this->user)
            ->getJson('/api/widgets/health');

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'service',
                'status',
                'timestamp',
                'version',
                'checks' => [
                    'realtime_manager',
                    'cache_available',
                    'auth_working',
                ],
            ]);

        $this->assertEquals('widget-polling', $response->json('service'));
        $this->assertEquals('healthy', $response->json('status'));
        $this->assertEquals('3.6.1', $response->json('version'));
        $this->assertTrue($response->json('checks.realtime_manager'));
        $this->assertTrue($response->json('checks.cache_available'));
    }

    #[Test]
    public function it_handles_unauthorized_widget_access(): void
    {
        // Arrange
        $widgetId = 'unauthorized_widget';

        // Act - Try to get data for widget user is not subscribed to
        $response = $this->actingAs($this->user)
            ->getJson("/api/widgets/{$widgetId}/data");

        // Assert
        $response->assertNotFound()
            ->assertJson([
                'success' => false,
                'error' => 'Widget not found or not authorized',
                'message' => 'Widget tidak dijumpai atau tidak dibenarkan.',
            ]);
    }

    #[Test]
    public function it_validates_polling_request_parameters(): void
    {
        // Act - Send invalid request
        $response = $this->actingAs($this->user)
            ->postJson('/api/widgets/polling-data', [
                'widget_ids' => 'invalid', // Should be array
            ]);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonStructure([
                'success',
                'error',
                'details' => [
                    'widget_ids',
                ],
            ]);

        $this->assertFalse($response->json('success'));
        $this->assertEquals('Invalid request parameters', $response->json('error'));
    }

    #[Test]
    public function it_limits_polling_request_size(): void
    {
        // Arrange - Create request with too many widgets
        $widgetIds = array_map(fn ($i) => "widget_{$i}", range(1, 25)); // 25 widgets (max is 20)

        // Act
        $response = $this->actingAs($this->user)
            ->postJson('/api/widgets/polling-data', [
                'widget_ids' => $widgetIds,
            ]);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['widget_ids']);
    }

    #[Test]
    public function it_handles_subscription_management(): void
    {
        // Arrange
        $realtimeManager = app(WidgetRealtimeManager::class);
        $widgetId = 'subscription_test_widget';

        // Act - Subscribe
        $subscribeResult = $realtimeManager->subscribeUserToWidget($this->user->id, $widgetId);
        $this->assertTrue($subscribeResult);

        // Verify subscription works for polling
        $pollingData = $realtimeManager->getFallbackPollingData([$widgetId], $this->user->id);
        $this->assertArrayHasKey($widgetId, $pollingData);

        // Act - Unsubscribe
        $unsubscribeResult = $realtimeManager->unsubscribeUserFromWidget($this->user->id, $widgetId);
        $this->assertTrue($unsubscribeResult);

        // Verify unsubscription
        $pollingDataAfter = $realtimeManager->getFallbackPollingData([$widgetId], $this->user->id);
        $this->assertArrayNotHasKey($widgetId, $pollingDataAfter);
    }

    #[Test]
    public function it_handles_rate_limiting_in_api(): void
    {
        // Arrange - Make many requests to trigger rate limiting
        $widgetId = 'rate_limit_test';

        // Act - Make requests up to the limit
        for ($i = 0; $i < 120; $i++) {
            $response = $this->actingAs($this->user)
                ->postJson('/api/widgets/polling-data', [
                    'widget_ids' => [$widgetId],
                ]);

            if ($response->status() === 429) {
                // Rate limit hit
                break;
            }
        }

        // Assert - Should eventually hit rate limit
        $this->assertEquals(429, $response->status());
    }

    #[Test]
    public function it_integrates_with_broadcasting_channels(): void
    {
        // This test verifies that the broadcasting system is properly configured
        // In a real environment, this would test WebSocket connections

        // Arrange
        Event::fake();
        $realtimeManager = app(WidgetRealtimeManager::class);

        // Act - Broadcast to different channel types
        $userResult = $realtimeManager->broadcastToUser(
            $this->user->id,
            'user_widget',
            'personal',
            ['notifications' => 3]
        );

        $adminResult = $realtimeManager->broadcastToAdmins(
            'admin_widget',
            'system',
            ['cpu_usage' => 75.5]
        );

        // Assert
        $this->assertTrue($userResult);
        $this->assertTrue($adminResult);

        Event::assertDispatched(WidgetDataUpdated::class, 2);
    }

    #[Test]
    public function it_handles_concurrent_widget_updates(): void
    {
        // Arrange
        Event::fake();
        $realtimeManager = app(WidgetRealtimeManager::class);

        $widgets = [
            ['id' => 'widget_1', 'type' => 'stats', 'data' => ['value' => 1]],
            ['id' => 'widget_2', 'type' => 'chart', 'data' => ['value' => 2]],
            ['id' => 'widget_3', 'type' => 'metric', 'data' => ['value' => 3]],
        ];

        // Act - Broadcast multiple widgets concurrently
        $results = [];
        foreach ($widgets as $widget) {
            $results[] = $realtimeManager->broadcastWidgetUpdate(
                $widget['id'],
                $widget['type'],
                $widget['data'],
                $this->user->id
            );
        }

        // Assert
        foreach ($results as $result) {
            $this->assertTrue($result);
        }

        Event::assertDispatched(WidgetDataUpdated::class, 3);
    }

    #[Test]
    public function it_maintains_data_consistency_across_updates(): void
    {
        // Arrange
        $realtimeManager = app(WidgetRealtimeManager::class);
        $widgetId = 'consistency_test_widget';

        $realtimeManager->subscribeUserToWidget($this->user->id, $widgetId);

        // Act - Multiple updates with different data
        $updates = [
            ['count' => 1, 'status' => 'starting'],
            ['count' => 2, 'status' => 'processing'],
            ['count' => 3, 'status' => 'completed'],
        ];

        foreach ($updates as $data) {
            $realtimeManager->broadcastWidgetUpdate(
                $widgetId,
                'progress',
                $data,
                $this->user->id
            );
        }

        // Assert - Get final state via polling
        $pollingData = $realtimeManager->getFallbackPollingData([$widgetId], $this->user->id);

        $this->assertArrayHasKey($widgetId, $pollingData);
        $finalData = $pollingData[$widgetId]['data'];

        $this->assertEquals(3, $finalData['count']);
        $this->assertEquals('completed', $finalData['status']);
    }
}

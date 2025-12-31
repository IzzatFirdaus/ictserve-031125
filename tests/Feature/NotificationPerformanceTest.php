<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Services\Notifications\EmailRateLimiter;
use App\Services\Notifications\NotificationCacheService;
use App\Services\Notifications\NotificationQueryOptimizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Notification Performance Tests
 *
 * Tests for performance optimizations in the notification system.
 *
 * @see Requirements 8.1, 8.3, 8.6, 8.7
 */
class NotificationPerformanceTest extends TestCase
{
    use RefreshDatabase;

    private NotificationCacheService $cacheService;

    private EmailRateLimiter $rateLimiter;

    private NotificationQueryOptimizer $queryOptimizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheService = new NotificationCacheService;
        $this->rateLimiter = new EmailRateLimiter;
        $this->queryOptimizer = new NotificationQueryOptimizer;
    }

    #[Test]
    public function it_caches_unread_notification_count(): void
    {
        $user = User::factory()->create();

        // Create some notifications
        $this->createNotificationsForUser($user, 5);

        // First call should hit database
        $count1 = $this->cacheService->getUnreadCount($user);
        $this->assertEquals(5, $count1);

        // Second call should hit cache (verify by checking cache exists)
        $cacheKey = "notifications:count:{$user->id}";
        $this->assertTrue(Cache::has($cacheKey));

        $count2 = $this->cacheService->getUnreadCount($user);
        $this->assertEquals(5, $count2);
    }

    #[Test]
    public function it_invalidates_cache_when_requested(): void
    {
        $user = User::factory()->create();
        $this->createNotificationsForUser($user, 3);

        // Populate cache
        $this->cacheService->getUnreadCount($user);

        // Invalidate cache
        $this->cacheService->invalidateCountCache($user);

        // Cache should be cleared
        $cacheKey = "notifications:count:{$user->id}";
        $this->assertFalse(Cache::has($cacheKey));
    }

    #[Test]
    public function it_caches_recent_notifications(): void
    {
        $user = User::factory()->create();
        $this->createNotificationsForUser($user, 10);

        // First call should hit database
        $notifications = $this->cacheService->getRecentNotifications($user, 5);
        $this->assertCount(5, $notifications);

        // Verify cache exists
        $cacheKey = "notifications:list:{$user->id}:5";
        $this->assertTrue(Cache::has($cacheKey));
    }

    #[Test]
    public function email_rate_limiter_allows_sends_within_limit(): void
    {
        $userId = 1;

        // Clear any existing rate limit
        $this->rateLimiter->clearUserLimit($userId);

        // Should allow sends within limit
        $this->assertTrue($this->rateLimiter->canSendForUser($userId));
        $this->assertTrue($this->rateLimiter->attemptSend($userId));
    }

    #[Test]
    public function email_rate_limiter_tracks_remaining_sends(): void
    {
        $userId = 2;

        // Clear any existing rate limit
        $this->rateLimiter->clearUserLimit($userId);

        $initialRemaining = $this->rateLimiter->getRemainingForUser($userId);

        // Record a send
        $this->rateLimiter->recordSendForUser($userId);

        $afterRemaining = $this->rateLimiter->getRemainingForUser($userId);

        $this->assertEquals($initialRemaining - 1, $afterRemaining);
    }

    #[Test]
    public function email_rate_limiter_provides_statistics(): void
    {
        $userId = 3;

        $stats = $this->rateLimiter->getStatistics($userId);

        $this->assertArrayHasKey('user_remaining', $stats);
        $this->assertArrayHasKey('user_limit', $stats);
        $this->assertArrayHasKey('system_remaining', $stats);
        $this->assertArrayHasKey('system_limit', $stats);
        $this->assertArrayHasKey('window_seconds', $stats);
    }

    #[Test]
    public function query_optimizer_paginates_notifications(): void
    {
        $user = User::factory()->create();
        $this->createNotificationsForUser($user, 25);

        $paginated = $this->queryOptimizer->getPaginatedNotifications($user, 1, 10);

        $this->assertEquals(25, $paginated->total());
        $this->assertCount(10, $paginated->items());
        $this->assertEquals(3, $paginated->lastPage());
    }

    #[Test]
    public function query_optimizer_filters_unread_only(): void
    {
        $user = User::factory()->create();
        $this->createNotificationsForUser($user, 10);

        // Mark some as read
        DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->limit(5)
            ->update(['read_at' => now()]);

        $paginated = $this->queryOptimizer->getPaginatedNotifications(
            $user,
            1,
            20,
            null,
            unreadOnly: true
        );

        $this->assertEquals(5, $paginated->total());
    }

    #[Test]
    public function query_optimizer_bulk_marks_as_read(): void
    {
        $user = User::factory()->create();
        $this->createNotificationsForUser($user, 5);

        $notificationIds = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->limit(3)
            ->pluck('id')
            ->toArray();

        $updated = $this->queryOptimizer->bulkMarkAsRead($user, $notificationIds);

        $this->assertEquals(3, $updated);

        $unreadCount = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $this->assertEquals(2, $unreadCount);
    }

    #[Test]
    public function query_optimizer_bulk_deletes_notifications(): void
    {
        $user = User::factory()->create();
        $this->createNotificationsForUser($user, 5);

        $notificationIds = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->limit(2)
            ->pluck('id')
            ->toArray();

        $deleted = $this->queryOptimizer->bulkDelete($user, $notificationIds);

        $this->assertEquals(2, $deleted);

        $remainingCount = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->count();

        $this->assertEquals(3, $remainingCount);
    }

    #[Test]
    public function query_optimizer_marks_all_as_read(): void
    {
        $user = User::factory()->create();
        $this->createNotificationsForUser($user, 10);

        $updated = $this->queryOptimizer->markAllAsRead($user);

        $this->assertEquals(10, $updated);

        $unreadCount = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $this->assertEquals(0, $unreadCount);
    }

    #[Test]
    public function query_optimizer_provides_statistics(): void
    {
        $user = User::factory()->create();
        $this->createNotificationsForUser($user, 5);

        $stats = $this->queryOptimizer->getStatistics();

        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('unread', $stats);
        $this->assertArrayHasKey('read', $stats);
        $this->assertEquals(5, $stats['total']);
        $this->assertEquals(5, $stats['unread']);
    }

    #[Test]
    public function cache_service_warms_up_user_cache(): void
    {
        $user = User::factory()->create();
        $this->createNotificationsForUser($user, 5);

        // Warm up cache
        $this->cacheService->warmUpUserCache($user);

        // Verify caches are populated
        $countKey = "notifications:count:{$user->id}";
        $listKey = "notifications:list:{$user->id}:10";

        $this->assertTrue(Cache::has($countKey));
        $this->assertTrue(Cache::has($listKey));
    }

    #[Test]
    public function cache_service_provides_statistics(): void
    {
        $stats = $this->cacheService->getCacheStatistics();

        $this->assertArrayHasKey('count_ttl', $stats);
        $this->assertArrayHasKey('list_ttl', $stats);
        $this->assertArrayHasKey('template_ttl', $stats);
        $this->assertArrayHasKey('cache_prefix', $stats);
    }

    /**
     * Helper to create notifications for a user.
     */
    private function createNotificationsForUser(User $user, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            DB::table('notifications')->insert([
                'id' => fake()->uuid(),
                'type' => 'App\Notifications\TestNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'title' => "Test Notification {$i}",
                    'message' => "This is test notification {$i}",
                    'type' => 'system',
                ]),
                'created_at' => now()->subMinutes($i),
                'updated_at' => now()->subMinutes($i),
            ]);
        }
    }
}

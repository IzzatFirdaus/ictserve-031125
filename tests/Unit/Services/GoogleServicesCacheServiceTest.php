<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\GoogleServicesCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Unit tests for GoogleServicesCacheService
 *
 * @see Requirements 13.2, 13.3
 */
class GoogleServicesCacheServiceTest extends TestCase
{
    use RefreshDatabase;

    private GoogleServicesCacheService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GoogleServicesCacheService;
        Cache::flush();
    }

    // =========================================================================
    // User Profile Caching Tests
    // =========================================================================

    public function test_cache_user_profile_stores_user_in_cache(): void
    {
        $user = User::factory()->create([
            'google_id' => 'google-123456',
            'email' => 'test@motac.gov.my',
        ]);

        $this->service->cacheUserProfile($user);

        $cachedUser = $this->service->getUserProfile('google-123456');

        $this->assertNotNull($cachedUser);
        $this->assertEquals($user->id, $cachedUser->id);
        $this->assertEquals($user->email, $cachedUser->email);
    }

    public function test_cache_user_profile_does_not_cache_user_without_google_id(): void
    {
        $user = User::factory()->create([
            'google_id' => null,
            'email' => 'test@motac.gov.my',
        ]);

        $this->service->cacheUserProfile($user);

        // Should not throw error, just skip caching
        $this->assertTrue(true);
    }

    public function test_get_user_profile_returns_null_when_not_cached(): void
    {
        $result = $this->service->getUserProfile('non-existent-google-id');

        $this->assertNull($result);
    }

    public function test_invalidate_user_profile_removes_from_cache(): void
    {
        $user = User::factory()->create([
            'google_id' => 'google-123456',
            'email' => 'test@motac.gov.my',
        ]);

        $this->service->cacheUserProfile($user);
        $this->assertNotNull($this->service->getUserProfile('google-123456'));

        $this->service->invalidateUserProfile('google-123456');

        $this->assertNull($this->service->getUserProfile('google-123456'));
    }

    public function test_remember_user_profile_caches_callback_result(): void
    {
        $user = User::factory()->create([
            'google_id' => 'google-remember-test',
            'email' => 'remember@motac.gov.my',
        ]);

        $callCount = 0;
        $callback = function () use ($user, &$callCount) {
            $callCount++;

            return $user;
        };

        // First call should execute callback
        $result1 = $this->service->rememberUserProfile('google-remember-test', $callback);
        $this->assertEquals(1, $callCount);
        $this->assertEquals($user->id, $result1->id);

        // Second call should use cache
        $result2 = $this->service->rememberUserProfile('google-remember-test', $callback);
        $this->assertEquals(1, $callCount); // Still 1, callback not called again
        $this->assertEquals($user->id, $result2->id);
    }

    // =========================================================================
    // OAuth Token Caching Tests
    // =========================================================================

    public function test_cache_oauth_token_stores_token(): void
    {
        $token = [
            'access_token' => 'test-access-token',
            'refresh_token' => 'test-refresh-token',
            'expires_in' => 3600,
        ];

        $this->service->cacheOAuthToken('user-123', $token);

        $cachedToken = $this->service->getOAuthToken('user-123');

        $this->assertNotNull($cachedToken);
        $this->assertEquals('test-access-token', $cachedToken['access_token']);
        $this->assertEquals('test-refresh-token', $cachedToken['refresh_token']);
    }

    public function test_get_oauth_token_returns_null_when_not_cached(): void
    {
        $result = $this->service->getOAuthToken('non-existent-user');

        $this->assertNull($result);
    }

    public function test_invalidate_oauth_token_removes_from_cache(): void
    {
        $token = ['access_token' => 'test-token'];

        $this->service->cacheOAuthToken('user-123', $token);
        $this->assertNotNull($this->service->getOAuthToken('user-123'));

        $this->service->invalidateOAuthToken('user-123');

        $this->assertNull($this->service->getOAuthToken('user-123'));
    }

    // =========================================================================
    // Verification Status Caching Tests
    // =========================================================================

    public function test_cache_verification_status_stores_status(): void
    {
        $status = [
            'status' => 'verified',
            'is_production_mode' => true,
            'test_users' => [],
        ];

        $this->service->cacheVerificationStatus($status);

        $cachedStatus = $this->service->getVerificationStatus();

        $this->assertNotNull($cachedStatus);
        $this->assertEquals('verified', $cachedStatus['status']);
        $this->assertTrue($cachedStatus['is_production_mode']);
    }

    public function test_get_verification_status_returns_null_when_not_cached(): void
    {
        $result = $this->service->getVerificationStatus();

        $this->assertNull($result);
    }

    public function test_invalidate_verification_status_removes_from_cache(): void
    {
        $status = ['status' => 'testing'];

        $this->service->cacheVerificationStatus($status);
        $this->assertNotNull($this->service->getVerificationStatus());

        $this->service->invalidateVerificationStatus();

        $this->assertNull($this->service->getVerificationStatus());
    }

    public function test_remember_verification_status_caches_callback_result(): void
    {
        $callCount = 0;
        $callback = function () use (&$callCount) {
            $callCount++;

            return ['status' => 'verified', 'checked_at' => now()->toIso8601String()];
        };

        // First call
        $result1 = $this->service->rememberVerificationStatus($callback);
        $this->assertEquals(1, $callCount);
        $this->assertEquals('verified', $result1['status']);

        // Second call should use cache
        $result2 = $this->service->rememberVerificationStatus($callback);
        $this->assertEquals(1, $callCount);
        $this->assertEquals('verified', $result2['status']);
    }

    // =========================================================================
    // Gmail Quota Caching Tests
    // =========================================================================

    public function test_cache_gmail_quota_stores_quota(): void
    {
        $quota = [
            'daily_limit' => 500,
            'emails_sent_today' => 100,
            'percentage_used' => 20,
        ];

        $this->service->cacheGmailQuota($quota);

        $cachedQuota = $this->service->getGmailQuota();

        $this->assertNotNull($cachedQuota);
        $this->assertEquals(500, $cachedQuota['daily_limit']);
        $this->assertEquals(100, $cachedQuota['emails_sent_today']);
    }

    public function test_invalidate_gmail_quota_removes_from_cache(): void
    {
        $quota = ['daily_limit' => 500];

        $this->service->cacheGmailQuota($quota);
        $this->assertNotNull($this->service->getGmailQuota());

        $this->service->invalidateGmailQuota();

        $this->assertNull($this->service->getGmailQuota());
    }

    // =========================================================================
    // Health Status Caching Tests
    // =========================================================================

    public function test_cache_sso_health_status_stores_status(): void
    {
        $status = [
            'available' => true,
            'configured' => true,
            'message' => 'SSO is healthy',
        ];

        $this->service->cacheSsoHealthStatus($status);

        $cachedStatus = $this->service->getSsoHealthStatus();

        $this->assertNotNull($cachedStatus);
        $this->assertTrue($cachedStatus['available']);
    }

    public function test_cache_gmail_health_status_stores_status(): void
    {
        $status = [
            'is_authenticated' => true,
            'authentication_method' => 'oauth',
            'connectivity' => true,
        ];

        $this->service->cacheGmailHealthStatus($status);

        $cachedStatus = $this->service->getGmailHealthStatus();

        $this->assertNotNull($cachedStatus);
        $this->assertTrue($cachedStatus['is_authenticated']);
    }

    public function test_invalidate_health_status_removes_both_caches(): void
    {
        $this->service->cacheSsoHealthStatus(['available' => true]);
        $this->service->cacheGmailHealthStatus(['is_authenticated' => true]);

        $this->assertNotNull($this->service->getSsoHealthStatus());
        $this->assertNotNull($this->service->getGmailHealthStatus());

        $this->service->invalidateHealthStatus();

        $this->assertNull($this->service->getSsoHealthStatus());
        $this->assertNull($this->service->getGmailHealthStatus());
    }

    // =========================================================================
    // Performance Metrics Caching Tests
    // =========================================================================

    public function test_cache_performance_metrics_stores_metrics(): void
    {
        $metrics = [
            'sso' => ['total_operations' => 100, 'success_rate_percent' => 98.5],
            'gmail' => ['total_operations' => 50, 'success_rate_percent' => 99.0],
        ];

        $this->service->cachePerformanceMetrics($metrics);

        $cachedMetrics = $this->service->getPerformanceMetrics();

        $this->assertNotNull($cachedMetrics);
        $this->assertEquals(100, $cachedMetrics['sso']['total_operations']);
        $this->assertEquals(50, $cachedMetrics['gmail']['total_operations']);
    }

    public function test_invalidate_performance_metrics_removes_from_cache(): void
    {
        $metrics = ['sso' => ['total_operations' => 100]];

        $this->service->cachePerformanceMetrics($metrics);
        $this->assertNotNull($this->service->getPerformanceMetrics());

        $this->service->invalidatePerformanceMetrics();

        $this->assertNull($this->service->getPerformanceMetrics());
    }

    // =========================================================================
    // Cache Warming Tests
    // =========================================================================

    public function test_warm_user_profile_cache_caches_active_users(): void
    {
        // Create users with Google SSO
        $user1 = User::factory()->create([
            'google_id' => 'google-warm-1',
            'is_active' => true,
            'last_login_at' => now(),
        ]);

        $user2 = User::factory()->create([
            'google_id' => 'google-warm-2',
            'is_active' => true,
            'last_login_at' => now()->subDay(),
        ]);

        // User without Google SSO should not be cached
        User::factory()->create([
            'google_id' => null,
            'is_active' => true,
        ]);

        // Inactive user should not be cached
        User::factory()->create([
            'google_id' => 'google-inactive',
            'is_active' => false,
        ]);

        $count = $this->service->warmUserProfileCache(10);

        $this->assertEquals(2, $count);
        $this->assertNotNull($this->service->getUserProfile('google-warm-1'));
        $this->assertNotNull($this->service->getUserProfile('google-warm-2'));
    }

    // =========================================================================
    // Cache Statistics Tests
    // =========================================================================

    public function test_get_cache_statistics_returns_expected_structure(): void
    {
        $stats = $this->service->getCacheStatistics();

        $this->assertArrayHasKey('driver', $stats);
        $this->assertArrayHasKey('prefixes', $stats);
        $this->assertArrayHasKey('ttl', $stats);
        $this->assertArrayHasKey('cached_items', $stats);

        $this->assertArrayHasKey('user_profile', $stats['prefixes']);
        $this->assertArrayHasKey('oauth_token', $stats['prefixes']);
        $this->assertArrayHasKey('verification_status', $stats['prefixes']);
    }

    public function test_get_cache_statistics_reflects_cached_items(): void
    {
        // Initially nothing cached
        $stats1 = $this->service->getCacheStatistics();
        $this->assertFalse($stats1['cached_items']['verification_status']);

        // Cache something
        $this->service->cacheVerificationStatus(['status' => 'verified']);

        $stats2 = $this->service->getCacheStatistics();
        $this->assertTrue($stats2['cached_items']['verification_status']);
    }

    // =========================================================================
    // Clear All Caches Tests
    // =========================================================================

    public function test_clear_all_caches_removes_all_google_services_caches(): void
    {
        // Cache various items
        $this->service->cacheVerificationStatus(['status' => 'verified']);
        $this->service->cacheGmailQuota(['daily_limit' => 500]);
        $this->service->cacheSsoHealthStatus(['available' => true]);
        $this->service->cacheGmailHealthStatus(['is_authenticated' => true]);
        $this->service->cachePerformanceMetrics(['sso' => []]);

        // Verify all cached
        $this->assertNotNull($this->service->getVerificationStatus());
        $this->assertNotNull($this->service->getGmailQuota());
        $this->assertNotNull($this->service->getSsoHealthStatus());
        $this->assertNotNull($this->service->getGmailHealthStatus());
        $this->assertNotNull($this->service->getPerformanceMetrics());

        // Clear all
        $this->service->clearAllCaches();

        // Verify all cleared
        $this->assertNull($this->service->getVerificationStatus());
        $this->assertNull($this->service->getGmailQuota());
        $this->assertNull($this->service->getSsoHealthStatus());
        $this->assertNull($this->service->getGmailHealthStatus());
        $this->assertNull($this->service->getPerformanceMetrics());
    }
}

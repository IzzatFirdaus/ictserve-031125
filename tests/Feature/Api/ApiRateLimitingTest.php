<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * API Rate Limiting Tests
 *
 * Tests the API rate limiting configuration per Requirement 37.4:
 * - Authenticated tokens: 60 requests/minute
 * - Unauthenticated requests: 10 requests/minute
 *
 * @see D03 SRS-API-001
 * @see Requirement 37.4
 */
class ApiRateLimitingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Clear rate limiters before each test
        RateLimiter::clear('api');
        RateLimiter::clear('api-token');
        RateLimiter::clear('api-guest');
    }

    #[Test]
    public function api_rate_limiter_is_configured(): void
    {
        // Verify the 'api' rate limiter exists
        $this->assertTrue(
            RateLimiter::limiter('api') !== null,
            'API rate limiter should be configured'
        );
    }

    #[Test]
    public function api_token_rate_limiter_is_configured(): void
    {
        // Verify the 'api-token' rate limiter exists
        $this->assertTrue(
            RateLimiter::limiter('api-token') !== null,
            'API token rate limiter should be configured'
        );
    }

    #[Test]
    public function api_guest_rate_limiter_is_configured(): void
    {
        // Verify the 'api-guest' rate limiter exists
        $this->assertTrue(
            RateLimiter::limiter('api-guest') !== null,
            'API guest rate limiter should be configured'
        );
    }

    #[Test]
    public function authenticated_user_gets_60_requests_per_minute(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        // Create a mock request with authenticated user
        $request = Request::create('/api/test', 'GET');
        $request->setUserResolver(fn () => $user);

        // Get the rate limiter callback
        $limiter = RateLimiter::limiter('api');
        $limit = $limiter($request);

        // Verify the limit is 60 per minute for authenticated users
        $this->assertInstanceOf(Limit::class, $limit);
        $this->assertEquals(60, $limit->maxAttempts);
    }

    #[Test]
    public function unauthenticated_request_gets_10_requests_per_minute(): void
    {
        // Create a mock request without authenticated user
        $request = Request::create('/api/test', 'GET');
        $request->setUserResolver(fn () => null);
        $request->server->set('REMOTE_ADDR', '192.168.1.100');

        // Get the rate limiter callback
        $limiter = RateLimiter::limiter('api');
        $limit = $limiter($request);

        // Verify the limit is 10 per minute for unauthenticated requests
        $this->assertInstanceOf(Limit::class, $limit);
        $this->assertEquals(10, $limit->maxAttempts);
    }

    #[Test]
    public function api_token_limiter_returns_60_for_authenticated(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        // Create a mock request with authenticated user
        $request = Request::create('/api/test', 'GET');
        $request->setUserResolver(fn () => $user);

        // Get the rate limiter callback
        $limiter = RateLimiter::limiter('api-token');
        $limit = $limiter($request);

        // Verify the limit is 60 per minute for authenticated tokens
        $this->assertInstanceOf(Limit::class, $limit);
        $this->assertEquals(60, $limit->maxAttempts);
    }

    #[Test]
    public function api_token_limiter_returns_10_for_unauthenticated(): void
    {
        // Create a mock request without authenticated user
        $request = Request::create('/api/test', 'GET');
        $request->setUserResolver(fn () => null);
        $request->server->set('REMOTE_ADDR', '192.168.1.100');

        // Get the rate limiter callback
        $limiter = RateLimiter::limiter('api-token');
        $limit = $limiter($request);

        // Verify the limit is 10 per minute for unauthenticated requests
        $this->assertInstanceOf(Limit::class, $limit);
        $this->assertEquals(10, $limit->maxAttempts);
    }

    #[Test]
    public function api_guest_limiter_always_returns_10(): void
    {
        // Create a mock request
        $request = Request::create('/api/test', 'GET');
        $request->server->set('REMOTE_ADDR', '192.168.1.100');

        // Get the rate limiter callback
        $limiter = RateLimiter::limiter('api-guest');
        $limit = $limiter($request);

        // Verify the limit is always 10 per minute for guest limiter
        $this->assertInstanceOf(Limit::class, $limit);
        $this->assertEquals(10, $limit->maxAttempts);
    }

    #[Test]
    public function rate_limit_key_uses_user_id_for_authenticated(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        // Create a mock request with authenticated user
        $request = Request::create('/api/test', 'GET');
        $request->setUserResolver(fn () => $user);

        // Get the rate limiter callback
        $limiter = RateLimiter::limiter('api');
        $limit = $limiter($request);

        // The key should be based on user ID (can be int or string)
        $key = is_string($limit->key) ? $limit->key : (string) $limit->key;
        $this->assertStringContainsString((string) $user->id, $key);
    }

    #[Test]
    public function rate_limit_key_uses_ip_for_unauthenticated(): void
    {
        // Create a mock request without authenticated user
        $request = Request::create('/api/test', 'GET');
        $request->setUserResolver(fn () => null);
        $request->server->set('REMOTE_ADDR', '192.168.1.100');

        // Get the rate limiter callback
        $limiter = RateLimiter::limiter('api');
        $limit = $limiter($request);

        // The key should be based on IP address (can be int or string)
        $key = is_string($limit->key) ? $limit->key : (string) $limit->key;
        $this->assertStringContainsString('192.168.1.100', $key);
    }

    #[Test]
    public function api_endpoint_with_throttle_returns_rate_limit_headers(): void
    {
        // Test the memory import endpoint which has throttle middleware
        // This endpoint uses 'throttle:60,1' middleware
        $response = $this->postJson('/api/v1/memory/import', [
            'entities' => [],
        ], [
            'Authorization' => 'Bearer test-token',
        ]);

        // Check for rate limit headers (Laravel adds these automatically when throttle middleware is applied)
        // Note: The response may be 401/403 due to auth, but headers should still be present
        $this->assertTrue(
            $response->headers->has('X-RateLimit-Limit') ||
                $response->headers->has('X-RateLimit-Remaining') ||
                $response->getStatusCode() === 429, // Too Many Requests also indicates rate limiting is working
            'Rate limiting headers or 429 response should be present'
        );
    }
}

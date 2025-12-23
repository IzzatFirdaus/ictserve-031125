<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\SsoHealthCheckInterface;
use App\Services\SsoHealthCheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit tests for SsoHealthCheck service
 *
 * Tests Google OAuth availability checking, configuration validation,
 * and connectivity testing per Requirements 8.1, 8.2.
 *
 * @see D00 §4.1 True Hybrid Architecture
 * @see D03-FR-001.3 Google SSO Authentication
 * @see Requirements 8.1, 8.2
 */
class SsoHealthCheckTest extends TestCase
{
    use RefreshDatabase;

    private SsoHealthCheck $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SsoHealthCheckInterface::class);

        // Clear cache before each test
        Cache::forget('sso_health_check');
    }

    #[Test]
    public function service_is_bound_in_container(): void
    {
        $service = app(SsoHealthCheckInterface::class);

        $this->assertInstanceOf(SsoHealthCheck::class, $service);
    }

    #[Test]
    public function service_is_singleton(): void
    {
        $service1 = app(SsoHealthCheckInterface::class);
        $service2 = app(SsoHealthCheckInterface::class);

        $this->assertSame($service1, $service2);
    }

    #[Test]
    public function validate_configuration_returns_valid_when_all_config_present(): void
    {
        Config::set('services.google.client_id', 'test-client-id');
        Config::set('services.google.client_secret', 'test-client-secret');
        Config::set('services.google.redirect', 'http://localhost/auth/google/callback');
        Config::set('services.google.allowed_domains', ['motac.gov.my']);

        $result = $this->service->validateConfiguration();

        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
    }

    #[Test]
    public function validate_configuration_returns_error_when_client_id_missing(): void
    {
        Config::set('services.google.client_id', null);
        Config::set('services.google.client_secret', 'test-client-secret');
        Config::set('services.google.redirect', 'http://localhost/auth/google/callback');

        $result = $this->service->validateConfiguration();

        $this->assertFalse($result['valid']);
        $this->assertContains(
            'Google OAuth Client ID is not configured (GOOGLE_CLIENT_ID)',
            $result['errors']
        );
    }

    #[Test]
    public function validate_configuration_returns_error_when_client_secret_missing(): void
    {
        Config::set('services.google.client_id', 'test-client-id');
        Config::set('services.google.client_secret', null);
        Config::set('services.google.redirect', 'http://localhost/auth/google/callback');

        $result = $this->service->validateConfiguration();

        $this->assertFalse($result['valid']);
        $this->assertContains(
            'Google OAuth Client Secret is not configured (GOOGLE_CLIENT_SECRET)',
            $result['errors']
        );
    }

    #[Test]
    public function validate_configuration_returns_error_when_redirect_uri_missing(): void
    {
        Config::set('services.google.client_id', 'test-client-id');
        Config::set('services.google.client_secret', 'test-client-secret');
        Config::set('services.google.redirect', null);

        $result = $this->service->validateConfiguration();

        $this->assertFalse($result['valid']);
        $this->assertContains(
            'Google OAuth Redirect URI is not configured (GOOGLE_REDIRECT_URI)',
            $result['errors']
        );
    }

    #[Test]
    public function validate_configuration_returns_error_for_invalid_redirect_uri(): void
    {
        Config::set('services.google.client_id', 'test-client-id');
        Config::set('services.google.client_secret', 'test-client-secret');
        Config::set('services.google.redirect', 'not-a-valid-url');

        $result = $this->service->validateConfiguration();

        $this->assertFalse($result['valid']);
        $this->assertContains(
            'Google OAuth Redirect URI is not a valid URL',
            $result['errors']
        );
    }

    #[Test]
    public function validate_configuration_returns_warning_when_no_allowed_domains(): void
    {
        Config::set('services.google.client_id', 'test-client-id');
        Config::set('services.google.client_secret', 'test-client-secret');
        Config::set('services.google.redirect', 'http://localhost/auth/google/callback');
        Config::set('services.google.allowed_domains', []);

        $result = $this->service->validateConfiguration();

        $this->assertTrue($result['valid']);
        $this->assertContains(
            'No allowed domains configured for SSO (using default: motac.gov.my)',
            $result['warnings']
        );
    }

    #[Test]
    public function connectivity_returns_true_when_google_responds(): void
    {
        Http::fake([
            'accounts.google.com/*' => Http::response([
                'authorization_endpoint' => 'https://accounts.google.com/o/oauth2/v2/auth',
                'token_endpoint' => 'https://oauth2.googleapis.com/token',
            ], 200),
        ]);

        $result = $this->service->testConnectivity();

        $this->assertTrue($result);
    }

    #[Test]
    public function connectivity_returns_false_when_google_unavailable(): void
    {
        Http::fake([
            'accounts.google.com/*' => Http::response(null, 500),
        ]);

        $result = $this->service->testConnectivity();

        $this->assertFalse($result);
    }

    #[Test]
    public function connectivity_returns_false_when_response_missing_endpoints(): void
    {
        Http::fake([
            'accounts.google.com/*' => Http::response([
                'issuer' => 'https://accounts.google.com',
                // Missing authorization_endpoint and token_endpoint
            ], 200),
        ]);

        $result = $this->service->testConnectivity();

        $this->assertFalse($result);
    }

    #[Test]
    public function check_google_oauth_availability_returns_false_when_not_configured(): void
    {
        Config::set('services.google.client_id', null);
        Config::set('services.google.client_secret', null);
        Config::set('services.google.redirect', null);

        $result = $this->service->checkGoogleOAuthAvailability();

        $this->assertFalse($result);
    }

    #[Test]
    public function check_google_oauth_availability_returns_true_when_configured_and_available(): void
    {
        Config::set('services.google.client_id', 'test-client-id');
        Config::set('services.google.client_secret', 'test-client-secret');
        Config::set('services.google.redirect', 'http://localhost/auth/google/callback');

        Http::fake([
            'accounts.google.com/*' => Http::response([
                'authorization_endpoint' => 'https://accounts.google.com/o/oauth2/v2/auth',
                'token_endpoint' => 'https://oauth2.googleapis.com/token',
            ], 200),
        ]);

        $result = $this->service->checkGoogleOAuthAvailability();

        $this->assertTrue($result);
    }

    #[Test]
    public function get_service_status_returns_unhealthy_when_not_configured(): void
    {
        Config::set('services.google.client_id', null);
        Config::set('services.google.client_secret', null);
        Config::set('services.google.redirect', null);

        $result = $this->service->getServiceStatus();

        $this->assertEquals('unhealthy', $result['status']);
        $this->assertFalse($result['configured']);
        $this->assertFalse($result['available']);
        $this->assertStringContainsString('not properly configured', $result['message']);
    }

    #[Test]
    public function get_service_status_returns_healthy_when_fully_operational(): void
    {
        Config::set('services.google.client_id', 'test-client-id');
        Config::set('services.google.client_secret', 'test-client-secret');
        Config::set('services.google.redirect', 'http://localhost/auth/google/callback');
        Config::set('services.google.allowed_domains', ['motac.gov.my']);

        Http::fake([
            'accounts.google.com/*' => Http::response([
                'authorization_endpoint' => 'https://accounts.google.com/o/oauth2/v2/auth',
                'token_endpoint' => 'https://oauth2.googleapis.com/token',
            ], 200),
        ]);

        $result = $this->service->getServiceStatus();

        $this->assertEquals('healthy', $result['status']);
        $this->assertTrue($result['configured']);
        $this->assertTrue($result['available']);
        $this->assertStringContainsString('fully operational', $result['message']);
    }

    #[Test]
    public function get_service_status_returns_degraded_when_connectivity_fails(): void
    {
        Config::set('services.google.client_id', 'test-client-id');
        Config::set('services.google.client_secret', 'test-client-secret');
        Config::set('services.google.redirect', 'http://localhost/auth/google/callback');

        Http::fake([
            'accounts.google.com/*' => Http::response(null, 500),
        ]);

        $result = $this->service->getServiceStatus();

        $this->assertEquals('degraded', $result['status']);
        $this->assertTrue($result['configured']);
        $this->assertFalse($result['available']);
        $this->assertStringContainsString('connectivity test failed', $result['message']);
    }

    #[Test]
    public function get_service_status_caches_result(): void
    {
        Config::set('services.google.client_id', 'test-client-id');
        Config::set('services.google.client_secret', 'test-client-secret');
        Config::set('services.google.redirect', 'http://localhost/auth/google/callback');

        Http::fake([
            'accounts.google.com/*' => Http::response([
                'authorization_endpoint' => 'https://accounts.google.com/o/oauth2/v2/auth',
                'token_endpoint' => 'https://oauth2.googleapis.com/token',
            ], 200),
        ]);

        // First call should make HTTP request
        $result1 = $this->service->getServiceStatus();

        // Second call should use cache
        $result2 = $this->service->getServiceStatus();

        $this->assertEquals($result1, $result2);
        Http::assertSentCount(1); // Only one HTTP request should be made
    }

    #[Test]
    public function clear_cache_forces_fresh_check(): void
    {
        Config::set('services.google.client_id', 'test-client-id');
        Config::set('services.google.client_secret', 'test-client-secret');
        Config::set('services.google.redirect', 'http://localhost/auth/google/callback');

        Http::fake([
            'accounts.google.com/*' => Http::response([
                'authorization_endpoint' => 'https://accounts.google.com/o/oauth2/v2/auth',
                'token_endpoint' => 'https://oauth2.googleapis.com/token',
            ], 200),
        ]);

        // First call
        $this->service->getServiceStatus();

        // Clear cache
        $this->service->clearCache();

        // Second call should make new HTTP request
        $this->service->getServiceStatus();

        Http::assertSentCount(2); // Two HTTP requests should be made
    }

    #[Test]
    public function get_health_summary_returns_simplified_status(): void
    {
        Config::set('services.google.client_id', 'test-client-id');
        Config::set('services.google.client_secret', 'test-client-secret');
        Config::set('services.google.redirect', 'http://localhost/auth/google/callback');

        Http::fake([
            'accounts.google.com/*' => Http::response([
                'authorization_endpoint' => 'https://accounts.google.com/o/oauth2/v2/auth',
                'token_endpoint' => 'https://oauth2.googleapis.com/token',
            ], 200),
        ]);

        $result = $this->service->getHealthSummary();

        $this->assertArrayHasKey('healthy', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertTrue($result['healthy']);
        $this->assertEquals('healthy', $result['status']);
    }

    #[Test]
    public function is_sso_enabled_returns_true_when_configured(): void
    {
        Config::set('services.google.client_id', 'test-client-id');
        Config::set('services.google.client_secret', 'test-client-secret');
        Config::set('services.google.redirect', 'http://localhost/auth/google/callback');

        $result = $this->service->isSsoEnabled();

        $this->assertTrue($result);
    }

    #[Test]
    public function is_sso_enabled_returns_false_when_not_configured(): void
    {
        Config::set('services.google.client_id', null);
        Config::set('services.google.client_secret', null);
        Config::set('services.google.redirect', null);

        $result = $this->service->isSsoEnabled();

        $this->assertFalse($result);
    }

    #[Test]
    public function get_discovery_url_returns_correct_url(): void
    {
        $url = $this->service->getDiscoveryUrl();

        $this->assertEquals(
            'https://accounts.google.com/.well-known/openid-configuration',
            $url
        );
    }
}

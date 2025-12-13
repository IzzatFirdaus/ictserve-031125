<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\SsoHealthCheckInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SSO Health Check Service Implementation for ICTServe v3.6.0
 *
 * Provides comprehensive health monitoring for Google SSO integration:
 * - Google OAuth service availability checking
 * - Configuration validation for client ID, secret, and redirect URI
 * - Network connectivity testing to Google OAuth endpoints
 * - Service status reporting with caching for performance
 *
 * Security Features:
 * - No sensitive data exposed in status responses
 * - Cached results to prevent excessive external requests
 * - Timeout protection for network operations
 *
 * @see D00 §4.1 True Hybrid Architecture
 * @see D03-FR-001.3 Google SSO Authentication
 * @see D11 Technical Design Documentation
 * @see Requirements 8.1, 8.2
 */
class SsoHealthCheck implements SsoHealthCheckInterface
{
    /**
     * Google OAuth discovery endpoint for connectivity testing
     */
    private const GOOGLE_OAUTH_DISCOVERY_URL = 'https://accounts.google.com/.well-known/openid-configuration';

    /**
     * Cache key for health check results
     */
    private const CACHE_KEY = 'sso_health_check';

    /**
     * Cache TTL in seconds (5 minutes)
     */
    private const CACHE_TTL = 300;

    /**
     * Default connection timeout in seconds
     */
    private const DEFAULT_TIMEOUT = 5;

    /**
     * Service status constants
     */
    private const STATUS_HEALTHY = 'healthy';

    private const STATUS_DEGRADED = 'degraded';

    private const STATUS_UNHEALTHY = 'unhealthy';

    /**
     * Check if Google OAuth service is available
     *
     * Tests connectivity to Google OAuth endpoints and validates
     * that the service can accept authentication requests.
     *
     * @return bool True if Google OAuth is available and responding
     */
    public function checkGoogleOAuthAvailability(): bool
    {
        // First check if configuration is valid
        $configValidation = $this->validateConfiguration();
        if (! $configValidation['valid']) {
            return false;
        }

        // Then test connectivity
        return $this->testConnectivity();
    }

    /**
     * Validate Google OAuth configuration
     *
     * Checks that all required configuration values are present:
     * - Client ID
     * - Client Secret
     * - Redirect URI
     *
     * @return array{valid: bool, errors: array<string>, warnings: array<string>}
     */
    public function validateConfiguration(): array
    {
        $errors = [];
        $warnings = [];

        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUri = config('services.google.redirect');
        $allowedDomains = config('services.google.allowed_domains', []);

        // Check required configuration values
        if (empty($clientId)) {
            $errors[] = 'Google OAuth Client ID is not configured (GOOGLE_CLIENT_ID)';
        }

        if (empty($clientSecret)) {
            $errors[] = 'Google OAuth Client Secret is not configured (GOOGLE_CLIENT_SECRET)';
        }

        if (empty($redirectUri)) {
            $errors[] = 'Google OAuth Redirect URI is not configured (GOOGLE_REDIRECT_URI)';
        }

        // Validate redirect URI format
        if (! empty($redirectUri) && is_string($redirectUri) && ! filter_var($redirectUri, FILTER_VALIDATE_URL)) {
            $errors[] = 'Google OAuth Redirect URI is not a valid URL';
        }

        // Check for HTTPS in production
        if (! empty($redirectUri) && is_string($redirectUri) && app()->environment('production')) {
            if (! str_starts_with($redirectUri, 'https://')) {
                $warnings[] = 'Redirect URI should use HTTPS in production environment';
            }
        }

        // Check allowed domains configuration
        if (empty($allowedDomains)) {
            $warnings[] = 'No allowed domains configured for SSO (using default: motac.gov.my)';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Test network connectivity to Google OAuth endpoints
     *
     * Performs HTTP request to Google's OAuth discovery endpoint
     * to verify network connectivity and service availability.
     *
     * @param  int  $timeout  Connection timeout in seconds (default: 5)
     * @return bool True if connectivity test passes
     */
    public function testConnectivity(int $timeout = self::DEFAULT_TIMEOUT): bool
    {
        try {
            $response = Http::timeout($timeout)
                ->withoutVerifying() // Allow self-signed certs in dev
                ->get(self::GOOGLE_OAUTH_DISCOVERY_URL);

            if ($response->successful()) {
                // Verify response contains expected OAuth configuration
                $data = $response->json();

                return is_array($data)
                    && isset($data['authorization_endpoint'])
                    && isset($data['token_endpoint']);
            }

            Log::warning('Google OAuth connectivity test failed', [
                'status' => $response->status(),
                'reason' => $response->reason(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::warning('Google OAuth connectivity test exception', [
                'error' => $e->getMessage(),
                'timeout' => $timeout,
            ]);

            return false;
        }
    }

    /**
     * Get comprehensive service status
     *
     * Returns detailed status information including:
     * - Overall health status (healthy, degraded, unhealthy)
     * - Configuration validation results
     * - Connectivity test results
     * - Last check timestamp
     *
     * @return array{status: string, configured: bool, available: bool, message: string, details: array<string, mixed>, checked_at: string}
     */
    public function getServiceStatus(): array
    {
        // Try to get cached result first
        /** @var array{status: string, configured: bool, available: bool, message: string, details: array<string, mixed>, checked_at: string}|null $cached */
        $cached = Cache::get(self::CACHE_KEY);
        if ($cached !== null && is_array($cached)) {
            return $cached;
        }

        // Perform fresh health check
        $configValidation = $this->validateConfiguration();
        $isConfigured = $configValidation['valid'];
        $isAvailable = false;
        $status = self::STATUS_UNHEALTHY;
        $message = '';

        if (! $isConfigured) {
            $message = 'Google SSO is not properly configured';
            $status = self::STATUS_UNHEALTHY;
        } else {
            // Test connectivity only if configuration is valid
            $isAvailable = $this->testConnectivity();

            if ($isAvailable) {
                $status = self::STATUS_HEALTHY;
                $message = 'Google SSO service is fully operational';
            } else {
                $status = self::STATUS_DEGRADED;
                $message = 'Google SSO is configured but connectivity test failed';
            }
        }

        // Add warnings to message if present
        if (! empty($configValidation['warnings'])) {
            $message .= ' (Warnings: '.implode('; ', $configValidation['warnings']).')';
        }

        $result = [
            'status' => $status,
            'configured' => $isConfigured,
            'available' => $isAvailable,
            'message' => $message,
            'details' => [
                'configuration_errors' => $configValidation['errors'],
                'configuration_warnings' => $configValidation['warnings'],
                'connectivity_tested' => $isConfigured,
                'connectivity_passed' => $isAvailable,
                'allowed_domains' => config('services.google.allowed_domains', ['motac.gov.my']),
                'redirect_uri_configured' => ! empty(config('services.google.redirect')),
            ],
            'checked_at' => now()->toIso8601String(),
        ];

        // Cache the result
        Cache::put(self::CACHE_KEY, $result, self::CACHE_TTL);

        return $result;
    }

    /**
     * Get health check summary for admin dashboard
     *
     * Returns simplified health status suitable for display
     * in admin dashboards and monitoring systems.
     *
     * @return array{healthy: bool, status: string, message: string}
     */
    public function getHealthSummary(): array
    {
        $fullStatus = $this->getServiceStatus();

        return [
            'healthy' => $fullStatus['status'] === self::STATUS_HEALTHY,
            'status' => $fullStatus['status'],
            'message' => $fullStatus['message'],
        ];
    }

    /**
     * Check if SSO feature is enabled
     *
     * Verifies that SSO is enabled in application configuration
     * and all prerequisites are met.
     *
     * @return bool True if SSO feature is enabled and ready
     */
    public function isSsoEnabled(): bool
    {
        // Check if Google OAuth is configured
        $configValidation = $this->validateConfiguration();

        return $configValidation['valid'];
    }

    /**
     * Clear cached health check results
     *
     * Forces a fresh health check on next status request.
     * Useful after configuration changes.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get the Google OAuth discovery URL
     *
     * Returns the URL used for connectivity testing.
     * Useful for debugging and documentation.
     *
     * @return string The Google OAuth discovery endpoint URL
     */
    public function getDiscoveryUrl(): string
    {
        return self::GOOGLE_OAUTH_DISCOVERY_URL;
    }
}

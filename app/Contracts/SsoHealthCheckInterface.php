<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * SSO Health Check Service Interface for ICTServe v3.6.0
 *
 * Provides health monitoring and configuration validation for Google SSO:
 * - Google OAuth service availability checking
 * - Configuration validation for client ID, secret, and redirect URI
 * - Network connectivity testing to Google OAuth endpoints
 * - Service status reporting for admin dashboards
 *
 * @see D00 §4.1 True Hybrid Architecture
 * @see D03-FR-001.3 Google SSO Authentication
 * @see D11 Technical Design Documentation
 * @see Requirements 8.1, 8.2
 */
interface SsoHealthCheckInterface
{
    /**
     * Check if Google OAuth service is available
     *
     * Tests connectivity to Google OAuth endpoints and validates
     * that the service can accept authentication requests.
     *
     * @return bool True if Google OAuth is available and responding
     */
    public function checkGoogleOAuthAvailability(): bool;

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
    public function validateConfiguration(): array;

    /**
     * Test network connectivity to Google OAuth endpoints
     *
     * Performs HTTP request to Google's OAuth discovery endpoint
     * to verify network connectivity and service availability.
     *
     * @param  int  $timeout  Connection timeout in seconds (default: 5)
     * @return bool True if connectivity test passes
     */
    public function testConnectivity(int $timeout = 5): bool;

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
    public function getServiceStatus(): array;

    /**
     * Get health check summary for admin dashboard
     *
     * Returns simplified health status suitable for display
     * in admin dashboards and monitoring systems.
     *
     * @return array{healthy: bool, status: string, message: string}
     */
    public function getHealthSummary(): array;

    /**
     * Check if SSO feature is enabled
     *
     * Verifies that SSO is enabled in application configuration
     * and all prerequisites are met.
     *
     * @return bool True if SSO feature is enabled and ready
     */
    public function isSsoEnabled(): bool;
}

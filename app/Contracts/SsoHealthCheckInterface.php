<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * SSO Health Check Service Interface for ICTServe v3.6.1
 *
 * Provides comprehensive health monitoring for all Google services:
 * - Google OAuth/SSO service availability checking
 * - Gmail API availability and quota monitoring
 * - OAuth verification status monitoring
 * - Configuration validation for all Google services
 * - Network connectivity testing to Google endpoints
 * - Service status reporting for admin dashboards
 *
 * @see D00 §4.1 True Hybrid Architecture
 * @see D03-FR-001.3 Google SSO Authentication
 * @see D11 Technical Design Documentation
 * @see Requirements 8.1, 8.2, 14.1, 14.2, 14.5
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
     * Check if Gmail API service is available
     *
     * Tests connectivity to Gmail API endpoints and validates
     * that the service can accept API requests.
     *
     * @return bool True if Gmail API is available and responding
     */
    public function checkGmailApiAvailability(): bool;

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
     * Validate Gmail API configuration
     *
     * Checks that all required Gmail API configuration values are present:
     * - Service account credentials or OAuth tokens
     * - Required scopes
     * - Sender email configuration
     *
     * @return array{valid: bool, errors: array<string>, warnings: array<string>}
     */
    public function validateGmailConfiguration(): array;

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

    /**
     * Get OAuth verification status
     *
     * Returns the current OAuth app verification status and details.
     *
     * @return array{status: string, is_production: bool, is_testing: bool, test_users_count: int, message: string}
     */
    public function getVerificationStatus(): array;

    /**
     * Check quota limits for all Google services
     *
     * Returns quota usage and limits for SSO and Gmail API.
     *
     * @return array{sso: array<string, mixed>, gmail: array<string, mixed>, overall_status: string}
     */
    public function checkQuotaLimits(): array;

    /**
     * Get overall status for all Google services
     *
     * Returns comprehensive status for SSO, Gmail API, and verification.
     *
     * @return array{sso: array<string, mixed>, gmail: array<string, mixed>, verification: array<string, mixed>, overall_status: string, checked_at: string}
     */
    public function getOverallServiceStatus(): array;
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\GoogleOAuthVerificationServiceInterface;
use App\Contracts\SsoHealthCheckInterface;
use App\Models\GoogleServicesAuditLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SSO Health Check Service Implementation for ICTServe v3.6.1
 *
 * Provides comprehensive health monitoring for all Google services:
 * - Google OAuth/SSO service availability checking
 * - Gmail API availability and quota monitoring
 * - OAuth verification status monitoring
 * - Configuration validation for all Google services
 *
 * @see Requirements 8.1, 8.2, 14.1, 14.2, 14.5
 */
class SsoHealthCheck implements SsoHealthCheckInterface
{
    private const GOOGLE_OAUTH_DISCOVERY_URL = 'https://accounts.google.com/.well-known/openid-configuration';

    private const GMAIL_API_DISCOVERY_URL = 'https://gmail.googleapis.com/$discovery/rest?version=v1';

    private const CACHE_KEY = 'sso_health_check';

    private const CACHE_KEY_GMAIL = 'gmail_health_check';

    private const CACHE_KEY_OVERALL = 'google_services_overall_status';

    private const CACHE_TTL = 300;

    private const DEFAULT_TIMEOUT = 5;

    private const STATUS_HEALTHY = 'healthy';

    private const STATUS_DEGRADED = 'degraded';

    private const STATUS_UNHEALTHY = 'unhealthy';

    public function __construct(
        private readonly ?GoogleOAuthVerificationServiceInterface $verificationService = null
    ) {}

    public function checkGoogleOAuthAvailability(): bool
    {
        $configValidation = $this->validateConfiguration();
        if (! $configValidation['valid']) {
            return false;
        }

        return $this->testConnectivity();
    }

    public function checkGmailApiAvailability(): bool
    {
        $configValidation = $this->validateGmailConfiguration();
        if (! $configValidation['valid']) {
            return false;
        }

        return $this->testGmailConnectivity();
    }

    /**
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

        if (empty($clientId)) {
            $errors[] = 'Google OAuth Client ID is not configured (GOOGLE_CLIENT_ID)';
        }

        if (empty($clientSecret)) {
            $errors[] = 'Google OAuth Client Secret is not configured (GOOGLE_CLIENT_SECRET)';
        }

        if (empty($redirectUri)) {
            $errors[] = 'Google OAuth Redirect URI is not configured (GOOGLE_REDIRECT_URI)';
        }

        if (! empty($redirectUri) && is_string($redirectUri) && ! filter_var($redirectUri, FILTER_VALIDATE_URL)) {
            $errors[] = 'Google OAuth Redirect URI is not a valid URL';
        }

        if (! empty($redirectUri) && is_string($redirectUri) && app()->environment('production')) {
            if (! str_starts_with($redirectUri, 'https://')) {
                $warnings[] = 'Redirect URI should use HTTPS in production environment';
            }
        }

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
     * @return array{valid: bool, errors: array<string>, warnings: array<string>}
     */
    public function validateGmailConfiguration(): array
    {
        $errors = [];
        $warnings = [];

        $serviceAccountPath = config('services.google.service_account_path');
        $serviceAccountJson = config('services.google.service_account_json');
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $gmailEnabled = config('services.google.gmail_enabled', false);
        $senderEmail = config('mail.from.address');

        if (! $gmailEnabled) {
            $warnings[] = 'Gmail API is not enabled in configuration';
        }

        $hasServiceAccount = ! empty($serviceAccountPath) || ! empty($serviceAccountJson);
        $hasOAuth = ! empty($clientId) && ! empty($clientSecret);

        if (! $hasServiceAccount && ! $hasOAuth) {
            $errors[] = 'No Gmail API authentication method configured (service account or OAuth)';
        }

        if (empty($senderEmail)) {
            $warnings[] = 'Mail sender address not configured';
        }

        $smtpHost = config('mail.mailers.smtp.host');
        if (empty($smtpHost)) {
            $warnings[] = 'SMTP fallback not configured - Gmail API failures will not have fallback';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    public function testConnectivity(int $timeout = self::DEFAULT_TIMEOUT): bool
    {
        try {
            $response = Http::timeout($timeout)->withoutVerifying()->get(self::GOOGLE_OAUTH_DISCOVERY_URL);

            if ($response->successful()) {
                $data = $response->json();

                return is_array($data) && isset($data['authorization_endpoint']) && isset($data['token_endpoint']);
            }

            Log::warning('Google OAuth connectivity test failed', ['status' => $response->status()]);

            return false;
        } catch (\Exception $e) {
            Log::warning('Google OAuth connectivity test exception', ['error' => $e->getMessage()]);

            return false;
        }
    }

    public function testGmailConnectivity(int $timeout = self::DEFAULT_TIMEOUT): bool
    {
        try {
            $response = Http::timeout($timeout)->withoutVerifying()->get(self::GMAIL_API_DISCOVERY_URL);

            if ($response->successful()) {
                $data = $response->json();

                return is_array($data) && isset($data['name']) && $data['name'] === 'gmail';
            }

            Log::warning('Gmail API connectivity test failed', ['status' => $response->status()]);

            return false;
        } catch (\Exception $e) {
            Log::warning('Gmail API connectivity test exception', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * @return array{status: string, configured: bool, available: bool, message: string, details: array<string, mixed>, checked_at: string}
     */
    public function getServiceStatus(): array
    {
        $cached = Cache::get(self::CACHE_KEY);
        if ($cached !== null && is_array($cached)) {
            return $cached;
        }

        $configValidation = $this->validateConfiguration();
        $isConfigured = $configValidation['valid'];
        $isAvailable = false;
        $status = self::STATUS_UNHEALTHY;
        $message = '';

        if (! $isConfigured) {
            $message = 'Google SSO is not properly configured';
        } else {
            $isAvailable = $this->testConnectivity();
            if ($isAvailable) {
                $status = self::STATUS_HEALTHY;
                $message = 'Google SSO service is fully operational';
            } else {
                $status = self::STATUS_DEGRADED;
                $message = 'Google SSO is configured but connectivity test failed';
            }
        }

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

        Cache::put(self::CACHE_KEY, $result, self::CACHE_TTL);

        return $result;
    }

    /**
     * @return array{status: string, configured: bool, available: bool, message: string, details: array<string, mixed>, checked_at: string}
     */
    public function getGmailServiceStatus(): array
    {
        $cached = Cache::get(self::CACHE_KEY_GMAIL);
        if ($cached !== null && is_array($cached)) {
            return $cached;
        }

        $configValidation = $this->validateGmailConfiguration();
        $isConfigured = $configValidation['valid'];
        $isAvailable = false;
        $status = self::STATUS_UNHEALTHY;
        $message = '';

        if (! $isConfigured) {
            $message = 'Gmail API is not properly configured';
        } else {
            $isAvailable = $this->testGmailConnectivity();
            if ($isAvailable) {
                $status = self::STATUS_HEALTHY;
                $message = 'Gmail API service is fully operational';
            } else {
                $status = self::STATUS_DEGRADED;
                $message = 'Gmail API is configured but connectivity test failed';
            }
        }

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
                'sender_email' => config('mail.from.address'),
                'smtp_fallback_configured' => ! empty(config('mail.mailers.smtp.host')),
            ],
            'checked_at' => now()->toIso8601String(),
        ];

        Cache::put(self::CACHE_KEY_GMAIL, $result, self::CACHE_TTL);

        return $result;
    }

    /**
     * @return array{healthy: bool, status: string, message: string}
     */
    public function getHealthSummary(): array
    {
        $serviceStatus = $this->getServiceStatus();

        return [
            'healthy' => $serviceStatus['status'] === self::STATUS_HEALTHY,
            'status' => $serviceStatus['status'],
            'message' => $serviceStatus['message'],
        ];
    }

    public function isSsoEnabled(): bool
    {
        $enabled = config('services.google.sso_enabled', true);
        if (! $enabled) {
            return false;
        }

        $configValidation = $this->validateConfiguration();

        return $configValidation['valid'];
    }

    /**
     * Get OAuth verification status
     *
     * @return array{status: string, is_production: bool, is_testing: bool, test_users_count: int, message: string}
     */
    public function getVerificationStatus(): array
    {
        if ($this->verificationService === null) {
            return [
                'status' => 'unknown',
                'is_production' => false,
                'is_testing' => true,
                'test_users_count' => 0,
                'message' => 'Verification service not available',
            ];
        }

        $status = $this->verificationService->getVerificationStatus();
        $isProduction = $this->verificationService->isInProductionMode();
        $isTesting = $this->verificationService->isInTestingMode();
        $testUsersCount = $this->verificationService->getTestUserCount();

        $message = match ($status) {
            'verified' => 'OAuth app is verified and in production mode',
            'pending' => 'OAuth app verification is pending review',
            'testing' => "OAuth app is in testing mode ({$testUsersCount} test users configured)",
            'rejected' => 'OAuth app verification was rejected',
            default => 'OAuth verification status unknown',
        };

        return [
            'status' => $status,
            'is_production' => $isProduction,
            'is_testing' => $isTesting,
            'test_users_count' => $testUsersCount,
            'message' => $message,
        ];
    }

    /**
     * Check quota limits for all Google services
     *
     * @return array{sso: array<string, mixed>, gmail: array<string, mixed>, overall_status: string}
     */
    public function checkQuotaLimits(): array
    {
        $ssoQuota = $this->getSsoQuotaStatus();
        $gmailQuota = $this->getGmailQuotaStatus();

        $overallStatus = self::STATUS_HEALTHY;
        if ($ssoQuota['status'] === self::STATUS_UNHEALTHY || $gmailQuota['status'] === self::STATUS_UNHEALTHY) {
            $overallStatus = self::STATUS_UNHEALTHY;
        } elseif ($ssoQuota['status'] === self::STATUS_DEGRADED || $gmailQuota['status'] === self::STATUS_DEGRADED) {
            $overallStatus = self::STATUS_DEGRADED;
        }

        return [
            'sso' => $ssoQuota,
            'gmail' => $gmailQuota,
            'overall_status' => $overallStatus,
        ];
    }

    /**
     * Get overall status for all Google services
     *
     * @return array{sso: array<string, mixed>, gmail: array<string, mixed>, verification: array<string, mixed>, overall_status: string, checked_at: string}
     */
    public function getOverallServiceStatus(): array
    {
        $cached = Cache::get(self::CACHE_KEY_OVERALL);
        if ($cached !== null && is_array($cached)) {
            return $cached;
        }

        $ssoStatus = $this->getServiceStatus();
        $gmailStatus = $this->getGmailServiceStatus();
        $verificationStatus = $this->getVerificationStatus();

        $overallStatus = self::STATUS_HEALTHY;

        if ($ssoStatus['status'] === self::STATUS_UNHEALTHY) {
            $overallStatus = self::STATUS_UNHEALTHY;
        } elseif ($ssoStatus['status'] === self::STATUS_DEGRADED || $gmailStatus['status'] === self::STATUS_DEGRADED) {
            $overallStatus = self::STATUS_DEGRADED;
        }

        if ($verificationStatus['status'] === 'rejected') {
            $overallStatus = self::STATUS_DEGRADED;
        }

        $result = [
            'sso' => $ssoStatus,
            'gmail' => $gmailStatus,
            'verification' => $verificationStatus,
            'overall_status' => $overallStatus,
            'checked_at' => now()->toIso8601String(),
        ];

        Cache::put(self::CACHE_KEY_OVERALL, $result, self::CACHE_TTL);

        return $result;
    }

    /**
     * Get SSO quota status based on audit logs
     *
     * @return array{status: string, daily_usage: int, daily_limit: int, usage_percentage: float, message: string}
     */
    private function getSsoQuotaStatus(): array
    {
        $dailyLimit = (int) config('services.google.sso_daily_limit', 10000);

        $dailyUsage = GoogleServicesAuditLog::query()
            ->where('service_type', GoogleServicesAuditLog::SERVICE_SSO)
            ->where('attempted_at', '>=', now()->startOfDay())
            ->count();

        $usagePercentage = $dailyLimit > 0 ? ($dailyUsage / $dailyLimit) * 100 : 0;

        $status = self::STATUS_HEALTHY;
        $message = 'SSO quota usage is within normal limits';

        if ($usagePercentage >= 100) {
            $status = self::STATUS_UNHEALTHY;
            $message = 'SSO daily quota exceeded';
        } elseif ($usagePercentage >= 80) {
            $status = self::STATUS_DEGRADED;
            $message = 'SSO quota usage is high (>80%)';
        }

        return [
            'status' => $status,
            'daily_usage' => $dailyUsage,
            'daily_limit' => $dailyLimit,
            'usage_percentage' => round($usagePercentage, 2),
            'message' => $message,
        ];
    }

    /**
     * Get Gmail API quota status based on audit logs
     *
     * @return array{status: string, daily_usage: int, daily_limit: int, usage_percentage: float, message: string}
     */
    private function getGmailQuotaStatus(): array
    {
        $dailyLimit = (int) config('services.google.gmail_daily_limit', 2000);

        $dailyUsage = GoogleServicesAuditLog::query()
            ->where('service_type', GoogleServicesAuditLog::SERVICE_GMAIL)
            ->where('attempted_at', '>=', now()->startOfDay())
            ->count();

        $usagePercentage = $dailyLimit > 0 ? ($dailyUsage / $dailyLimit) * 100 : 0;

        $status = self::STATUS_HEALTHY;
        $message = 'Gmail API quota usage is within normal limits';

        if ($usagePercentage >= 100) {
            $status = self::STATUS_UNHEALTHY;
            $message = 'Gmail API daily quota exceeded';
        } elseif ($usagePercentage >= 80) {
            $status = self::STATUS_DEGRADED;
            $message = 'Gmail API quota usage is high (>80%)';
        }

        return [
            'status' => $status,
            'daily_usage' => $dailyUsage,
            'daily_limit' => $dailyLimit,
            'usage_percentage' => round($usagePercentage, 2),
            'message' => $message,
        ];
    }

    /**
     * Clear all health check caches
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::CACHE_KEY_GMAIL);
        Cache::forget(self::CACHE_KEY_OVERALL);
    }

    /**
     * Get Google OAuth discovery URL
     */
    public function getDiscoveryUrl(): string
    {
        return self::GOOGLE_OAUTH_DISCOVERY_URL;
    }

    /**
     * Get Gmail API discovery URL
     */
    public function getGmailDiscoveryUrl(): string
    {
        return self::GMAIL_API_DISCOVERY_URL;
    }
}

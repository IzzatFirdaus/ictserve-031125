<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\GoogleOAuthVerificationServiceInterface;
use App\Exceptions\GmailQuotaExceededException;
use App\Exceptions\GmailRateLimitException;
use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Enhanced Gmail Service with OAuth Verification Support for ICTServe v3.6.1
 *
 * Features:
 * - OAuth verification status handling
 * - Multiple authentication methods (OAuth, Service Account, SMTP fallback)
 * - Comprehensive quota monitoring and alerting
 * - Rate limiting for API calls
 * - Automatic fallback mechanisms
 *
 * @see Requirements 3.1, 3.2, 3.3, 3.4, 6.1
 */
class GmailService
{
    public const AUTH_METHOD_OAUTH = 'oauth';

    public const AUTH_METHOD_SERVICE_ACCOUNT = 'service_account';

    public const AUTH_METHOD_SMTP_FALLBACK = 'smtp_fallback';

    private const CACHE_KEY_QUOTA_USAGE = 'gmail_quota_usage';

    private const CACHE_KEY_RATE_LIMIT = 'gmail_rate_limit';

    private const CACHE_KEY_DAILY_SENDS = 'gmail_daily_sends';

    private const CACHE_KEY_QUOTA_ALERTS = 'gmail_quota_alerts';

    private const CACHE_TTL = 300;

    /**
     * Gmail API daily quota limits (per project)
     * Standard quota: 1 billion quota units per day
     */
    private const DAILY_QUOTA_LIMIT = 1000000000;

    /**
     * Per-user rate limit (quota units per 100 seconds)
     */
    private const PER_USER_RATE_LIMIT = 250000000;

    /**
     * Email send quota cost (quota units per email)
     */
    private const SEND_QUOTA_COST = 100;

    /**
     * Maximum emails per day (based on quota)
     */
    private const MAX_DAILY_EMAILS = 500;

    /**
     * Rate limit window in seconds
     */
    private const RATE_LIMIT_WINDOW = 60;

    /**
     * Maximum requests per rate limit window
     */
    private const MAX_REQUESTS_PER_WINDOW = 100;

    /**
     * Quota warning threshold percentage
     */
    private const QUOTA_WARNING_THRESHOLD = 80;

    /**
     * Quota critical threshold percentage
     */
    private const QUOTA_CRITICAL_THRESHOLD = 95;

    private ?Client $client = null;

    private ?Gmail $service = null;

    private string $tokenPath = 'gmail_token.json';

    private string $currentAuthMethod = self::AUTH_METHOD_OAUTH;

    public function __construct(
        private GoogleOAuthVerificationServiceInterface $verificationService
    ) {
        $this->loadEnvironmentVariables();

        $enabled = config('services.google.gmail_enabled');
        if (! $enabled) {
            $enabled = $_ENV['GOOGLE_GMAIL_ENABLED'] ?? env('GOOGLE_GMAIL_ENABLED', false);
        }

        if ($enabled === true || $enabled === 'true' || $enabled === '1') {
            $this->initializeClient();
        } else {
            Log::info('Gmail service disabled');
        }
    }

    private function loadEnvironmentVariables(): void
    {
        try {
            $envFile = base_path('.env');
            if (file_exists($envFile)) {
                $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (str_starts_with(trim($line), '#')) {
                        continue;
                    }
                    if (str_starts_with($line, 'GOOGLE_') && str_contains($line, '=')) {
                        [$key, $value] = explode('=', $line, 2);
                        $value = trim($value, '"\' ');
                        $_ENV[$key] = $value;
                        putenv("{$key}={$value}");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to load environment variables', ['error' => $e->getMessage()]);
        }
    }

    private function initializeClient(): void
    {
        try {
            $verificationStatus = $this->verificationService->getVerificationStatus();
            Log::info('Gmail API: Initializing with verification status', ['status' => $verificationStatus]);

            $this->currentAuthMethod = $this->selectAuthenticationMethod();

            switch ($this->currentAuthMethod) {
                case self::AUTH_METHOD_OAUTH:
                    $this->initializeOAuthClient();
                    break;
                case self::AUTH_METHOD_SERVICE_ACCOUNT:
                    $this->initializeServiceAccountClient();
                    break;
                default:
                    Log::warning('Gmail API: No valid authentication method available, falling back to SMTP');
                    $this->currentAuthMethod = self::AUTH_METHOD_SMTP_FALLBACK;

                    return;
            }

            if ($this->client && $this->isClientAuthenticated()) {
                $this->service = new Gmail($this->client);
                Log::info('Gmail API: Successfully initialized', ['method' => $this->currentAuthMethod]);
            } else {
                Log::warning('Gmail API: Client not authenticated, falling back to SMTP');
                $this->currentAuthMethod = self::AUTH_METHOD_SMTP_FALLBACK;
            }
        } catch (\Exception $e) {
            Log::error('Gmail API: Failed to initialize', [
                'error' => $e->getMessage(),
                'method' => $this->currentAuthMethod,
            ]);
            $this->currentAuthMethod = self::AUTH_METHOD_SMTP_FALLBACK;
        }
    }

    private function selectAuthenticationMethod(): string
    {
        if ($this->isServiceAccountConfigured() && config('services.google.gmail_prefer_service_account', false)) {
            return self::AUTH_METHOD_SERVICE_ACCOUNT;
        }

        if ($this->isOAuthAvailable()) {
            return self::AUTH_METHOD_OAUTH;
        }

        if ($this->isServiceAccountConfigured()) {
            return self::AUTH_METHOD_SERVICE_ACCOUNT;
        }

        return self::AUTH_METHOD_SMTP_FALLBACK;
    }

    private function initializeOAuthClient(): void
    {
        $this->client = new Client;
        $this->client->setApplicationName(config('app.name', 'ICTServe'));
        $this->client->setScopes([Gmail::GMAIL_SEND]);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');

        $clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? config('services.google.client_id');
        $clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? config('services.google.client_secret');
        $redirectUri = $_ENV['GOOGLE_GMAIL_REDIRECT_URI'] ?? config('app.url').'/gmail/callback';

        $this->client->setClientId($clientId);
        $this->client->setClientSecret($clientSecret);
        $this->client->setRedirectUri($redirectUri);

        $tokenPath = storage_path("app/{$this->tokenPath}");
        if (file_exists($tokenPath)) {
            $token = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($token);

            if ($this->client->isAccessTokenExpired()) {
                if ($this->client->getRefreshToken()) {
                    $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    $this->saveToken($this->client->getAccessToken());
                } else {
                    Log::warning('Gmail: OAuth token expired and no refresh token available');
                    throw new \Exception('OAuth token expired and no refresh token available');
                }
            }

            Log::info('Gmail API: OAuth client initialized successfully');
        } else {
            Log::info('Gmail API: No OAuth token found. Run "php artisan gmail:authorize" to authenticate.');
            throw new \Exception('No OAuth token found');
        }
    }

    private function initializeServiceAccountClient(): void
    {
        $serviceAccountPath = config('services.google.gmail_service_account_path');
        $domainWideEmail = config('services.google.gmail_domain_wide_email');

        if (! $serviceAccountPath || ! file_exists($serviceAccountPath)) {
            throw new \Exception('Service account credentials file not found');
        }

        $this->client = new Client;
        $this->client->setApplicationName(config('app.name', 'ICTServe'));
        $this->client->setScopes([Gmail::GMAIL_SEND]);
        $this->client->setAuthConfig($serviceAccountPath);
        $this->client->useApplicationDefaultCredentials();

        if ($domainWideEmail) {
            $this->client->setSubject($domainWideEmail);
        }

        Log::info('Gmail API: Service account client initialized successfully');
    }

    private function isOAuthAvailable(): bool
    {
        $verificationStatus = $this->verificationService->getVerificationStatus();

        return $verificationStatus === GoogleOAuthVerificationService::STATUS_VERIFIED ||
            ($verificationStatus === GoogleOAuthVerificationService::STATUS_TESTING && $this->hasOAuthCredentials());
    }

    private function hasOAuthCredentials(): bool
    {
        $clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? config('services.google.client_id');
        $clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? config('services.google.client_secret');

        return ! empty($clientId) && ! empty($clientSecret);
    }

    private function isServiceAccountConfigured(): bool
    {
        $serviceAccountPath = config('services.google.gmail_service_account_path');

        return ! empty($serviceAccountPath) && file_exists($serviceAccountPath);
    }

    private function isClientAuthenticated(): bool
    {
        if (! $this->client) {
            return false;
        }

        try {
            if ($this->currentAuthMethod === self::AUTH_METHOD_OAUTH) {
                return $this->client->getAccessToken() && ! $this->client->isAccessTokenExpired();
            }

            if ($this->currentAuthMethod === self::AUTH_METHOD_SERVICE_ACCOUNT) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::warning('Gmail API: Authentication check failed', ['error' => $e->getMessage()]);

            return false;
        }
    }

    public function isAuthenticated(): bool
    {
        return $this->service !== null && $this->currentAuthMethod !== self::AUTH_METHOD_SMTP_FALLBACK;
    }

    public function getAuthenticationMethod(): string
    {
        return $this->currentAuthMethod;
    }

    public function getVerificationStatus(): array
    {
        return [
            'status' => $this->verificationService->getVerificationStatus(),
            'is_production_mode' => $this->verificationService->isInProductionMode(),
            'is_testing_mode' => $this->verificationService->isInTestingMode(),
            'authentication_method' => $this->currentAuthMethod,
            'is_authenticated' => $this->isAuthenticated(),
        ];
    }

    public function handleTestUserLimitation(string $email): array
    {
        $canAuthenticate = $this->verificationService->canUserAuthenticate($email);

        if ($canAuthenticate) {
            return [
                'allowed' => true,
                'message' => '',
            ];
        }

        $message = $this->verificationService->getTestUserLimitationMessage($email);

        return [
            'allowed' => false,
            'message' => $message,
            'is_test_user' => $this->verificationService->isTestUser($email),
            'verification_status' => $this->verificationService->getVerificationStatus(),
        ];
    }

    public function fallbackToSmtp(): void
    {
        $this->currentAuthMethod = self::AUTH_METHOD_SMTP_FALLBACK;
        $this->service = null;
        Log::info('Gmail API: Switched to SMTP fallback mode');
    }

    public function getQuotaUsage(): array
    {
        $cached = Cache::get(self::CACHE_KEY_QUOTA_USAGE);
        if ($cached !== null) {
            return $cached;
        }

        $dailySends = $this->getDailySendCount();
        $quotaUsed = $dailySends * self::SEND_QUOTA_COST;
        $percentageUsed = ($quotaUsed / self::DAILY_QUOTA_LIMIT) * 100;
        $emailPercentageUsed = ($dailySends / self::MAX_DAILY_EMAILS) * 100;

        $quotaInfo = [
            'daily_limit' => self::DAILY_QUOTA_LIMIT,
            'per_user_limit' => self::PER_USER_RATE_LIMIT,
            'current_usage' => $quotaUsed,
            'percentage_used' => round($percentageUsed, 2),
            'emails_sent_today' => $dailySends,
            'max_daily_emails' => self::MAX_DAILY_EMAILS,
            'email_percentage_used' => round($emailPercentageUsed, 2),
            'reset_time' => now()->addDay()->startOfDay()->toIso8601String(),
            'status' => $this->getQuotaStatus($emailPercentageUsed),
            'rate_limit' => $this->getRateLimitStatus(),
        ];

        Cache::put(self::CACHE_KEY_QUOTA_USAGE, $quotaInfo, self::CACHE_TTL);

        return $quotaInfo;
    }

    /**
     * Get daily email send count
     */
    private function getDailySendCount(): int
    {
        $key = self::CACHE_KEY_DAILY_SENDS.':'.now()->format('Y-m-d');

        return (int) Cache::get($key, 0);
    }

    /**
     * Increment daily send count
     */
    private function incrementDailySendCount(): void
    {
        $key = self::CACHE_KEY_DAILY_SENDS.':'.now()->format('Y-m-d');
        $ttl = now()->endOfDay()->diffInSeconds(now());

        $current = (int) Cache::get($key, 0);
        Cache::put($key, $current + 1, $ttl);
    }

    /**
     * Get quota status based on usage percentage
     */
    private function getQuotaStatus(float $percentageUsed): string
    {
        if ($percentageUsed >= self::QUOTA_CRITICAL_THRESHOLD) {
            return 'critical';
        }

        if ($percentageUsed >= self::QUOTA_WARNING_THRESHOLD) {
            return 'warning';
        }

        return 'healthy';
    }

    /**
     * Get rate limit status
     */
    private function getRateLimitStatus(): array
    {
        $key = self::CACHE_KEY_RATE_LIMIT.':'.now()->format('Y-m-d-H-i');
        $currentRequests = (int) Cache::get($key, 0);
        $remaining = max(0, self::MAX_REQUESTS_PER_WINDOW - $currentRequests);

        return [
            'window_seconds' => self::RATE_LIMIT_WINDOW,
            'max_requests' => self::MAX_REQUESTS_PER_WINDOW,
            'current_requests' => $currentRequests,
            'remaining' => $remaining,
            'is_limited' => $remaining === 0,
        ];
    }

    /**
     * Check and enforce rate limiting
     *
     * @throws GmailRateLimitException
     */
    private function checkRateLimit(): void
    {
        $key = self::CACHE_KEY_RATE_LIMIT.':'.now()->format('Y-m-d-H-i');
        $currentRequests = (int) Cache::get($key, 0);

        if ($currentRequests >= self::MAX_REQUESTS_PER_WINDOW) {
            Log::warning('Gmail API: Rate limit exceeded', [
                'current_requests' => $currentRequests,
                'max_requests' => self::MAX_REQUESTS_PER_WINDOW,
            ]);

            throw new GmailRateLimitException(
                __('auth.gmail_rate_limit_exceeded'),
                self::RATE_LIMIT_WINDOW
            );
        }

        // Increment request count
        Cache::put($key, $currentRequests + 1, self::RATE_LIMIT_WINDOW);
    }

    /**
     * Check if quota is exceeded
     *
     * @throws GmailQuotaExceededException
     */
    private function checkQuotaLimit(): void
    {
        $dailySends = $this->getDailySendCount();

        if ($dailySends >= self::MAX_DAILY_EMAILS) {
            Log::error('Gmail API: Daily quota exceeded', [
                'emails_sent' => $dailySends,
                'max_emails' => self::MAX_DAILY_EMAILS,
            ]);

            $this->sendQuotaAlert('exceeded');

            throw new GmailQuotaExceededException(
                __('auth.gmail_quota_exceeded'),
                self::MAX_DAILY_EMAILS,
                $dailySends
            );
        }

        // Check for warning threshold
        $percentageUsed = ($dailySends / self::MAX_DAILY_EMAILS) * 100;
        if ($percentageUsed >= self::QUOTA_WARNING_THRESHOLD) {
            $this->sendQuotaAlert('warning');
        }
    }

    /**
     * Send quota alert notification
     */
    private function sendQuotaAlert(string $level): void
    {
        $alertKey = self::CACHE_KEY_QUOTA_ALERTS.':'.$level.':'.now()->format('Y-m-d');

        // Only send one alert per day per level
        if (Cache::has($alertKey)) {
            return;
        }

        $quotaUsage = $this->getQuotaUsage();

        Log::warning("Gmail API: Quota {$level} alert", [
            'level' => $level,
            'emails_sent' => $quotaUsage['emails_sent_today'],
            'max_emails' => $quotaUsage['max_daily_emails'],
            'percentage_used' => $quotaUsage['email_percentage_used'],
        ]);

        // Log activity for audit
        activity('gmail_quota_alert')
            ->withProperties([
                'level' => $level,
                'quota_usage' => $quotaUsage,
                'timestamp' => now()->toIso8601String(),
            ])
            ->log("Gmail API quota {$level} alert triggered");

        // Mark alert as sent for today
        Cache::put($alertKey, true, now()->endOfDay()->diffInSeconds(now()));
    }

    /**
     * Handle quota exceeded scenario
     */
    public function handleQuotaExceeded(): array
    {
        $this->fallbackToSmtp();

        return [
            'fallback_activated' => true,
            'method' => self::AUTH_METHOD_SMTP_FALLBACK,
            'message' => __('auth.gmail_quota_exceeded'),
            'quota_usage' => $this->getQuotaUsage(),
            'reset_time' => now()->addDay()->startOfDay()->toIso8601String(),
        ];
    }

    /**
     * Check if quota allows sending
     */
    public function canSendEmail(): bool
    {
        try {
            $this->checkQuotaLimit();
            $this->checkRateLimit();

            return true;
        } catch (GmailQuotaExceededException|GmailRateLimitException $e) {
            return false;
        }
    }

    /**
     * Get quota monitoring dashboard data
     */
    public function getQuotaMonitoringData(): array
    {
        $quotaUsage = $this->getQuotaUsage();
        $rateLimitStatus = $this->getRateLimitStatus();

        return [
            'quota' => $quotaUsage,
            'rate_limit' => $rateLimitStatus,
            'health' => [
                'can_send' => $this->canSendEmail(),
                'authentication_method' => $this->currentAuthMethod,
                'is_authenticated' => $this->isAuthenticated(),
                'verification_status' => $this->verificationService->getVerificationStatus(),
            ],
            'alerts' => [
                'quota_warning_sent' => Cache::has(self::CACHE_KEY_QUOTA_ALERTS.':warning:'.now()->format('Y-m-d')),
                'quota_exceeded_sent' => Cache::has(self::CACHE_KEY_QUOTA_ALERTS.':exceeded:'.now()->format('Y-m-d')),
            ],
            'thresholds' => [
                'warning' => self::QUOTA_WARNING_THRESHOLD,
                'critical' => self::QUOTA_CRITICAL_THRESHOLD,
            ],
            'checked_at' => now()->toIso8601String(),
        ];
    }

    public function sendEmail(
        string $to,
        string $subject,
        string $body,
        ?string $from = null,
        array $attachments = []
    ): ?string {
        if (! $this->isAuthenticated()) {
            throw new \Exception("Gmail not authenticated. Authentication method: {$this->currentAuthMethod}");
        }

        // Check quota and rate limits before sending
        try {
            $this->checkQuotaLimit();
            $this->checkRateLimit();
        } catch (GmailQuotaExceededException $e) {
            Log::warning('Gmail API: Quota exceeded, falling back to SMTP', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);
            $this->fallbackToSmtp();
            throw $e;
        } catch (GmailRateLimitException $e) {
            Log::warning('Gmail API: Rate limit exceeded', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        if ($this->verificationService->isInTestingMode()) {
            $limitation = $this->handleTestUserLimitation($from ?? config('mail.from.address'));
            if (! $limitation['allowed']) {
                throw new \Exception('Gmail API access not allowed: '.$limitation['message']);
            }
        }

        try {
            $message = $this->createMessage($to, $subject, $body, $from, $attachments);
            $sentMessage = $this->service->users_messages->send('me', $message);

            // Increment daily send count after successful send
            $this->incrementDailySendCount();

            // Clear quota cache to reflect new usage
            Cache::forget(self::CACHE_KEY_QUOTA_USAGE);

            Log::info('Gmail API: Email sent successfully', [
                'message_id' => $sentMessage->getId(),
                'to' => $to,
                'subject' => $subject,
                'auth_method' => $this->currentAuthMethod,
                'daily_sends' => $this->getDailySendCount(),
            ]);

            return $sentMessage->getId();
        } catch (\Google\Service\Exception $e) {
            // Handle Google API specific errors
            $this->handleGoogleApiError($e, $to, $subject);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Gmail API: Send failed', [
                'error' => $e->getMessage(),
                'to' => $to,
                'auth_method' => $this->currentAuthMethod,
            ]);
            throw $e;
        }
    }

    /**
     * Handle Google API specific errors
     */
    private function handleGoogleApiError(\Google\Service\Exception $e, string $to, string $subject): void
    {
        $errorCode = $e->getCode();
        $errorMessage = $e->getMessage();

        Log::error('Gmail API: Google Service Exception', [
            'code' => $errorCode,
            'message' => $errorMessage,
            'to' => $to,
            'subject' => $subject,
        ]);

        // Handle quota exceeded error from Google
        if ($errorCode === 429 || str_contains($errorMessage, 'quota')) {
            $this->sendQuotaAlert('exceeded');
            $this->fallbackToSmtp();
        }

        // Handle rate limit error from Google
        if ($errorCode === 403 && str_contains($errorMessage, 'rate')) {
            Log::warning('Gmail API: Google rate limit hit, will retry later');
        }

        // Log for audit
        activity('gmail_api_error')
            ->withProperties([
                'error_code' => $errorCode,
                'error_message' => $errorMessage,
                'to' => $to,
                'subject' => $subject,
                'auth_method' => $this->currentAuthMethod,
                'timestamp' => now()->toIso8601String(),
            ])
            ->log('Gmail API error occurred');
    }

    private function createMessage(
        string $to,
        string $subject,
        string $body,
        ?string $from = null,
        array $attachments = []
    ): Message {
        $from = $from ?: config('mail.from.address');

        $rawMessage = "From: {$from}\r\n";
        $rawMessage .= "To: {$to}\r\n";
        $rawMessage .= "Subject: {$subject}\r\n";
        $rawMessage .= "Content-Type: text/html; charset=utf-8\r\n";
        $rawMessage .= "\r\n";
        $rawMessage .= $body;

        if (! empty($attachments)) {
            Log::info('Gmail API: Attachments provided but not yet implemented', [
                'attachment_count' => count($attachments),
            ]);
        }

        $message = new Message;
        $message->setRaw($this->base64UrlEncode($rawMessage));

        return $message;
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function getAuthUrl(): string
    {
        if (! $this->client) {
            $this->client = new Client;
            $this->client->setApplicationName(config('app.name', 'ICTServe'));
            $this->client->setScopes([Gmail::GMAIL_SEND]);
            $this->client->setAccessType('offline');
            $this->client->setPrompt('consent');

            $clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? config('services.google.client_id');
            $clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? config('services.google.client_secret');
            $redirectUri = 'urn:ietf:wg:oauth:2.0:oob';

            $this->client->setClientId($clientId);
            $this->client->setClientSecret($clientSecret);
            $this->client->setRedirectUri($redirectUri);
        }

        return $this->client->createAuthUrl();
    }

    public function handleCallback(string $code): bool
    {
        try {
            if (! $this->client) {
                $this->client = new Client;
                $this->client->setApplicationName(config('app.name', 'ICTServe'));
                $this->client->setScopes([Gmail::GMAIL_SEND]);
                $this->client->setAccessType('offline');

                $clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? config('services.google.client_id');
                $clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? config('services.google.client_secret');
                $redirectUri = 'urn:ietf:wg:oauth:2.0:oob';

                $this->client->setClientId($clientId);
                $this->client->setClientSecret($clientSecret);
                $this->client->setRedirectUri($redirectUri);
            }

            $token = $this->client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error'])) {
                Log::error('Gmail OAuth error', ['error' => $token['error']]);

                return false;
            }

            $this->saveToken($token);
            $this->client->setAccessToken($token);
            $this->service = new Gmail($this->client);
            $this->currentAuthMethod = self::AUTH_METHOD_OAUTH;

            Log::info('Gmail API: Successfully authenticated via OAuth');

            return true;
        } catch (\Exception $e) {
            Log::error('Gmail OAuth callback failed', ['error' => $e->getMessage()]);

            return false;
        }
    }

    public function authenticateWithCode(string $code): bool
    {
        return $this->handleCallback($code);
    }

    private function saveToken(array $token): void
    {
        $tokenPath = storage_path("app/{$this->tokenPath}");
        file_put_contents($tokenPath, json_encode($token));
        Log::info('Gmail API: Token saved');
    }

    public function getMessageStatus(string $messageId): array
    {
        if (! $this->service) {
            return [];
        }

        try {
            $message = $this->service->users_messages->get('me', $messageId);

            return [
                'id' => $message->getId(),
                'thread_id' => $message->getThreadId(),
                'label_ids' => $message->getLabelIds(),
                'snippet' => $message->getSnippet(),
                'size_estimate' => $message->getSizeEstimate(),
            ];
        } catch (\Exception $e) {
            Log::error('Gmail API: Get message failed', ['error' => $e->getMessage()]);

            return [];
        }
    }

    public function validateEmailAddress(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function testConnectivity(): bool
    {
        if (! $this->isAuthenticated()) {
            return false;
        }

        try {
            $this->service->users->getProfile('me');

            return true;
        } catch (\Exception $e) {
            Log::error('Gmail API: Connectivity test failed', ['error' => $e->getMessage()]);

            return false;
        }
    }

    public function getHealthStatus(): array
    {
        $quotaUsage = $this->getQuotaUsage();
        $rateLimitStatus = $this->getRateLimitStatus();

        return [
            'service_enabled' => config('services.google.gmail_enabled', false),
            'authentication_method' => $this->currentAuthMethod,
            'is_authenticated' => $this->isAuthenticated(),
            'verification_status' => $this->verificationService->getVerificationStatus(),
            'connectivity' => $this->testConnectivity(),
            'quota' => [
                'usage' => $quotaUsage,
                'status' => $quotaUsage['status'] ?? 'unknown',
                'can_send' => $this->canSendEmail(),
            ],
            'rate_limit' => $rateLimitStatus,
            'alerts' => [
                'quota_warning' => Cache::has(self::CACHE_KEY_QUOTA_ALERTS.':warning:'.now()->format('Y-m-d')),
                'quota_exceeded' => Cache::has(self::CACHE_KEY_QUOTA_ALERTS.':exceeded:'.now()->format('Y-m-d')),
            ],
            'checked_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Reset quota tracking (for testing or admin purposes)
     */
    public function resetQuotaTracking(): void
    {
        $dailySendsKey = self::CACHE_KEY_DAILY_SENDS.':'.now()->format('Y-m-d');
        $rateLimitKey = self::CACHE_KEY_RATE_LIMIT.':'.now()->format('Y-m-d-H-i');

        Cache::forget($dailySendsKey);
        Cache::forget($rateLimitKey);
        Cache::forget(self::CACHE_KEY_QUOTA_USAGE);
        Cache::forget(self::CACHE_KEY_QUOTA_ALERTS.':warning:'.now()->format('Y-m-d'));
        Cache::forget(self::CACHE_KEY_QUOTA_ALERTS.':exceeded:'.now()->format('Y-m-d'));

        Log::info('Gmail API: Quota tracking reset');
    }

    /**
     * Get quota history for reporting
     */
    public function getQuotaHistory(int $days = 7): array
    {
        $history = [];

        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($i)->format('Y-m-d');
            $key = self::CACHE_KEY_DAILY_SENDS.':'.$date;
            $sends = (int) Cache::get($key, 0);

            $history[] = [
                'date' => $date,
                'emails_sent' => $sends,
                'percentage_used' => round(($sends / self::MAX_DAILY_EMAILS) * 100, 2),
            ];
        }

        return array_reverse($history);
    }
}

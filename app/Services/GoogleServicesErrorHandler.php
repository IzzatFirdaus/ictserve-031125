<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\GoogleServicesErrorHandlerInterface;
use App\Exceptions\GmailAuthenticationException;
use App\Exceptions\GmailQuotaExceededException;
use App\Exceptions\GmailRateLimitException;
use App\Exceptions\GoogleServicesException;
use App\Exceptions\GoogleVerificationException;
use App\Exceptions\InvalidEmailDomainException;
use App\Models\GoogleServicesAuditLog;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Two\InvalidStateException;

/**
 * Centralized error handler for all Google services
 *
 * Provides consistent error handling, logging, and user feedback
 * for Google SSO, Gmail API, and OAuth verification operations.
 *
 * @see Requirements 7.1, 7.2, 7.4, 7.5
 */
class GoogleServicesErrorHandler implements GoogleServicesErrorHandlerInterface
{
    /**
     * Error response type constants
     */
    public const RESPONSE_REDIRECT = 'redirect';

    public const RESPONSE_JSON = 'json';

    public const RESPONSE_ARRAY = 'array';

    /**
     * Handle any Google services exception
     */
    public function handle(
        \Throwable $exception,
        string $serviceType = GoogleServicesException::SERVICE_SSO,
        ?string $email = null,
        string $responseType = self::RESPONSE_REDIRECT
    ): RedirectResponse|array {
        // Convert to GoogleServicesException if needed
        $googleException = $this->normalizeException($exception, $serviceType, $email);

        // Log the error
        $this->logError($googleException, $email);

        // Create audit log entry
        $this->createAuditLog($googleException, $email);

        // Return appropriate response
        return match ($responseType) {
            self::RESPONSE_JSON => $this->createJsonResponse($googleException),
            self::RESPONSE_ARRAY => $this->createArrayResponse($googleException),
            default => $this->createRedirectResponse($googleException),
        };
    }

    /**
     * Normalize any exception to GoogleServicesException
     */
    protected function normalizeException(
        \Throwable $exception,
        string $serviceType,
        ?string $email = null
    ): GoogleServicesException {
        // Already a GoogleServicesException
        if ($exception instanceof GoogleServicesException) {
            return $exception;
        }

        // InvalidEmailDomainException
        if ($exception instanceof InvalidEmailDomainException) {
            return new GoogleServicesException(
                $exception->getMessage(),
                GoogleServicesException::TYPE_DOMAIN,
                $serviceType,
                [
                    'email' => $exception->getEmail(),
                    'allowed_domains' => $exception->getAllowedDomains(),
                    'provided_domain' => $exception->getProvidedDomain(),
                ]
            );
        }

        // InvalidStateException (OAuth state error)
        if ($exception instanceof InvalidStateException) {
            return new GoogleServicesException(
                $exception->getMessage(),
                GoogleServicesException::TYPE_OAUTH_STATE,
                $serviceType,
                ['original_exception' => get_class($exception)]
            );
        }

        // ConnectException (Network error)
        if ($exception instanceof ConnectException) {
            return new GoogleServicesException(
                $exception->getMessage(),
                GoogleServicesException::TYPE_NETWORK,
                $serviceType,
                ['original_exception' => get_class($exception)]
            );
        }

        // RequestException (HTTP error)
        if ($exception instanceof RequestException) {
            $statusCode = $exception->hasResponse() ? $exception->getResponse()?->getStatusCode() : null;
            $errorType = $this->determineErrorTypeFromStatusCode($statusCode);

            return new GoogleServicesException(
                $exception->getMessage(),
                $errorType,
                $serviceType,
                [
                    'status_code' => $statusCode,
                    'original_exception' => get_class($exception),
                ]
            );
        }

        // Google Service Exception
        if ($exception instanceof \Google\Service\Exception) {
            return $this->handleGoogleServiceException($exception, $serviceType);
        }

        // Default: General error
        return new GoogleServicesException(
            $exception->getMessage(),
            GoogleServicesException::TYPE_GENERAL,
            $serviceType,
            [
                'original_exception' => get_class($exception),
                'email' => $email,
            ]
        );
    }

    /**
     * Handle Google Service Exception specifically
     */
    protected function handleGoogleServiceException(
        \Google\Service\Exception $exception,
        string $serviceType
    ): GoogleServicesException {
        $errorCode = $exception->getCode();
        $errorMessage = $exception->getMessage();

        // Quota exceeded
        if ($errorCode === 429 || str_contains($errorMessage, 'quota')) {
            return new GmailQuotaExceededException(
                $errorMessage,
                500,
                0,
                $errorCode
            );
        }

        // Rate limit
        if ($errorCode === 403 && str_contains($errorMessage, 'rate')) {
            return new GmailRateLimitException(
                $errorMessage,
                60,
                $errorCode
            );
        }

        // Authentication error
        if ($errorCode === 401) {
            return new GmailAuthenticationException(
                $errorMessage,
                GmailAuthenticationException::REASON_TOKEN_INVALID
            );
        }

        // Default
        return new GoogleServicesException(
            $errorMessage,
            GoogleServicesException::TYPE_GENERAL,
            $serviceType,
            ['google_error_code' => $errorCode]
        );
    }

    /**
     * Determine error type from HTTP status code
     */
    protected function determineErrorTypeFromStatusCode(?int $statusCode): string
    {
        return match ($statusCode) {
            401 => GoogleServicesException::TYPE_AUTHENTICATION,
            403 => GoogleServicesException::TYPE_OAUTH,
            429 => GoogleServicesException::TYPE_RATE_LIMIT,
            500, 502, 503, 504 => GoogleServicesException::TYPE_SERVICE_UNAVAILABLE,
            default => GoogleServicesException::TYPE_GENERAL,
        };
    }

    /**
     * Log the error with appropriate level
     */
    protected function logError(GoogleServicesException $exception, ?string $email = null): void
    {
        $logLevel = $this->determineLogLevel($exception);
        $context = array_merge($exception->context(), [
            'email' => $email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ]);

        Log::log($logLevel, "Google Services Error: {$exception->getMessage()}", $context);
    }

    /**
     * Determine appropriate log level for exception
     */
    protected function determineLogLevel(GoogleServicesException $exception): string
    {
        return match ($exception->getErrorType()) {
            GoogleServicesException::TYPE_DOMAIN => 'warning',
            GoogleServicesException::TYPE_OAUTH_STATE => 'warning',
            GoogleServicesException::TYPE_VERIFICATION => 'warning',
            GoogleServicesException::TYPE_RATE_LIMIT => 'warning',
            GoogleServicesException::TYPE_NETWORK => 'error',
            GoogleServicesException::TYPE_SERVICE_UNAVAILABLE => 'error',
            GoogleServicesException::TYPE_QUOTA => 'error',
            default => 'error',
        };
    }

    /**
     * Create audit log entry for the error
     */
    protected function createAuditLog(GoogleServicesException $exception, ?string $email = null): void
    {
        try {
            $serviceType = $exception->getServiceType();
            $operation = $serviceType === GoogleServicesException::SERVICE_GMAIL
                ? GoogleServicesAuditLog::OPERATION_SEND_EMAIL
                : GoogleServicesAuditLog::OPERATION_AUTHENTICATE;

            GoogleServicesAuditLog::create([
                'email' => $email ?? 'unknown',
                'service_type' => $serviceType,
                'operation_type' => $operation,
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => request()->userAgent(),
                'success' => false,
                'error_type' => $exception->getErrorType(),
                'error_message' => $exception->getMessage(),
                'metadata' => $exception->context(),
                'attempted_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to create Google services audit log', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create redirect response for web requests
     */
    protected function createRedirectResponse(GoogleServicesException $exception): RedirectResponse
    {
        $route = $exception->getServiceType() === GoogleServicesException::SERVICE_GMAIL
            ? 'dashboard'
            : 'login';

        $response = redirect()->route($route)
            ->withErrors(['email' => $exception->getUserMessage()]);

        if ($exception->shouldOfferFallback()) {
            $response->with('sso_fallback', true);
        }

        if ($exception instanceof GoogleVerificationException) {
            $response->with('verification_status', $exception->getVerificationStatus());
        }

        return $response;
    }

    /**
     * Create JSON response for API requests
     */
    protected function createJsonResponse(GoogleServicesException $exception): array
    {
        return [
            'success' => false,
            'error' => [
                'type' => $exception->getErrorType(),
                'message' => $exception->getUserMessage(),
                'help' => $exception->getHelpText(),
                'recoverable' => $exception->isRecoverable(),
                'fallback_available' => $exception->shouldOfferFallback(),
            ],
        ];
    }

    /**
     * Create array response for internal use
     */
    protected function createArrayResponse(GoogleServicesException $exception): array
    {
        return [
            'success' => false,
            'error_type' => $exception->getErrorType(),
            'service_type' => $exception->getServiceType(),
            'message' => $exception->getMessage(),
            'user_message' => $exception->getUserMessage(),
            'help_text' => $exception->getHelpText(),
            'recoverable' => $exception->isRecoverable(),
            'fallback_available' => $exception->shouldOfferFallback(),
            'context' => $exception->context(),
        ];
    }

    /**
     * Get user-friendly error message for a given error type
     */
    public function getErrorMessage(string $errorType): string
    {
        return match ($errorType) {
            GoogleServicesException::TYPE_DOMAIN => __('auth.google_sso_domain_error'),
            GoogleServicesException::TYPE_OAUTH => __('auth.google_sso_oauth_error'),
            GoogleServicesException::TYPE_OAUTH_STATE => __('auth.google_sso_oauth_error'),
            GoogleServicesException::TYPE_NETWORK => __('auth.google_sso_network_error'),
            GoogleServicesException::TYPE_VERIFICATION => __('auth.verification_pending'),
            GoogleServicesException::TYPE_QUOTA => __('auth.gmail_quota_exceeded'),
            GoogleServicesException::TYPE_RATE_LIMIT => __('auth.gmail_rate_limit_exceeded'),
            GoogleServicesException::TYPE_AUTHENTICATION => __('auth.gmail_auth_failed'),
            GoogleServicesException::TYPE_CONFIGURATION => __('auth.google_sso_unavailable'),
            GoogleServicesException::TYPE_SERVICE_UNAVAILABLE => __('auth.google_sso_unavailable'),
            default => __('auth.google_sso_failed'),
        };
    }

    /**
     * Get help text for a given error type
     */
    public function getHelpText(string $errorType): string
    {
        return match ($errorType) {
            GoogleServicesException::TYPE_DOMAIN => __('auth.google_services.help.domain'),
            GoogleServicesException::TYPE_VERIFICATION => __('auth.google_services.help.verification'),
            GoogleServicesException::TYPE_QUOTA => __('auth.google_services.help.quota'),
            GoogleServicesException::TYPE_RATE_LIMIT => __('auth.google_services.help.rate_limit'),
            GoogleServicesException::TYPE_NETWORK => __('auth.google_services.help.network'),
            default => __('auth.google_sso_fallback_hint'),
        };
    }

    /**
     * Check if an error type should trigger fallback
     */
    public function shouldTriggerFallback(string $errorType): bool
    {
        return in_array($errorType, [
            GoogleServicesException::TYPE_NETWORK,
            GoogleServicesException::TYPE_SERVICE_UNAVAILABLE,
            GoogleServicesException::TYPE_QUOTA,
            GoogleServicesException::TYPE_RATE_LIMIT,
        ]);
    }
}

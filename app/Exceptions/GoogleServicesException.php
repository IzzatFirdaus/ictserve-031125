<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Base exception for all Google services errors
 *
 * Provides common functionality for Google SSO, Gmail API, and OAuth verification errors.
 *
 * @see Requirements 7.1, 7.2, 7.4, 7.5
 */
class GoogleServicesException extends Exception
{
    /**
     * Error type constants
     */
    public const TYPE_DOMAIN = 'domain_error';

    public const TYPE_OAUTH = 'oauth_error';

    public const TYPE_OAUTH_STATE = 'oauth_state_error';

    public const TYPE_NETWORK = 'network_error';

    public const TYPE_VERIFICATION = 'verification_error';

    public const TYPE_QUOTA = 'quota_error';

    public const TYPE_RATE_LIMIT = 'rate_limit_error';

    public const TYPE_AUTHENTICATION = 'authentication_error';

    public const TYPE_CONFIGURATION = 'configuration_error';

    public const TYPE_SERVICE_UNAVAILABLE = 'service_unavailable';

    public const TYPE_GENERAL = 'general_error';

    /**
     * Service type constants
     */
    public const SERVICE_SSO = 'sso';

    public const SERVICE_GMAIL = 'gmail';

    public const SERVICE_OAUTH = 'oauth';

    public function __construct(
        string $message = '',
        protected string $errorType = self::TYPE_GENERAL,
        protected string $serviceType = self::SERVICE_SSO,
        protected array $context = [],
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the error type
     */
    public function getErrorType(): string
    {
        return $this->errorType;
    }

    /**
     * Get the service type
     */
    public function getServiceType(): string
    {
        return $this->serviceType;
    }

    /**
     * Get additional context
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Get user-friendly error message in Bahasa Melayu
     */
    public function getUserMessage(): string
    {
        return match ($this->errorType) {
            self::TYPE_DOMAIN => __('auth.google_sso_domain_error'),
            self::TYPE_OAUTH => __('auth.google_sso_oauth_error'),
            self::TYPE_OAUTH_STATE => __('auth.google_sso_oauth_error'),
            self::TYPE_NETWORK => __('auth.google_sso_network_error'),
            self::TYPE_VERIFICATION => __('auth.verification_pending'),
            self::TYPE_QUOTA => __('auth.gmail_quota_exceeded'),
            self::TYPE_RATE_LIMIT => __('auth.gmail_rate_limit_exceeded'),
            self::TYPE_AUTHENTICATION => __('auth.gmail_auth_failed'),
            self::TYPE_CONFIGURATION => __('auth.google_sso_unavailable'),
            self::TYPE_SERVICE_UNAVAILABLE => __('auth.google_sso_unavailable'),
            default => __('auth.google_sso_failed'),
        };
    }

    /**
     * Get help text for the error
     */
    public function getHelpText(): string
    {
        return match ($this->errorType) {
            self::TYPE_DOMAIN => __('auth.google_services.help.domain'),
            self::TYPE_VERIFICATION => __('auth.google_services.help.verification'),
            self::TYPE_QUOTA => __('auth.google_services.help.quota'),
            self::TYPE_RATE_LIMIT => __('auth.google_services.help.rate_limit'),
            self::TYPE_NETWORK => __('auth.google_services.help.network'),
            default => __('auth.google_sso_fallback_hint'),
        };
    }

    /**
     * Check if fallback should be offered
     */
    public function shouldOfferFallback(): bool
    {
        return in_array($this->errorType, [
            self::TYPE_NETWORK,
            self::TYPE_SERVICE_UNAVAILABLE,
            self::TYPE_OAUTH,
            self::TYPE_OAUTH_STATE,
            self::TYPE_VERIFICATION,
        ]);
    }

    /**
     * Check if error is recoverable
     */
    public function isRecoverable(): bool
    {
        return in_array($this->errorType, [
            self::TYPE_NETWORK,
            self::TYPE_RATE_LIMIT,
            self::TYPE_OAUTH_STATE,
        ]);
    }

    /**
     * Get exception context for logging
     */
    public function context(): array
    {
        return array_merge([
            'error_type' => $this->errorType,
            'service_type' => $this->serviceType,
            'is_recoverable' => $this->isRecoverable(),
            'should_offer_fallback' => $this->shouldOfferFallback(),
        ], $this->context);
    }
}

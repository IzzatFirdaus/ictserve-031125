<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception thrown when Gmail API authentication fails
 *
 * Handles OAuth token issues, service account failures, and authentication method problems.
 *
 * @see Requirements 3.3, 3.4, 6.1, 7.1
 */
class GmailAuthenticationException extends GoogleServicesException
{
    /**
     * Authentication failure reason constants
     */
    public const REASON_TOKEN_EXPIRED = 'token_expired';

    public const REASON_TOKEN_INVALID = 'token_invalid';

    public const REASON_NO_TOKEN = 'no_token';

    public const REASON_REFRESH_FAILED = 'refresh_failed';

    public const REASON_SERVICE_ACCOUNT_INVALID = 'service_account_invalid';

    public const REASON_SCOPE_INSUFFICIENT = 'scope_insufficient';

    public const REASON_CREDENTIALS_MISSING = 'credentials_missing';

    public function __construct(
        string $message = '',
        protected string $reason = self::REASON_TOKEN_INVALID,
        protected ?string $authMethod = null,
        array $context = [],
        int $code = 401,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            $message,
            GoogleServicesException::TYPE_AUTHENTICATION,
            GoogleServicesException::SERVICE_GMAIL,
            array_merge($context, [
                'reason' => $reason,
                'auth_method' => $authMethod,
            ]),
            $code,
            $previous
        );
    }

    /**
     * Get the failure reason
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * Get the authentication method that failed
     */
    public function getAuthMethod(): ?string
    {
        return $this->authMethod;
    }

    /**
     * Get user-friendly error message
     */
    public function getUserMessage(): string
    {
        return match ($this->reason) {
            self::REASON_TOKEN_EXPIRED => __('auth.google_services.gmail_token_expired'),
            self::REASON_NO_TOKEN => __('auth.google_services.gmail_not_configured'),
            self::REASON_CREDENTIALS_MISSING => __('auth.google_services.gmail_not_configured'),
            default => __('auth.gmail_auth_failed'),
        };
    }

    /**
     * Get help text for the error
     */
    public function getHelpText(): string
    {
        return match ($this->reason) {
            self::REASON_TOKEN_EXPIRED => __('auth.google_services.help.token_expired'),
            self::REASON_NO_TOKEN => __('auth.google_services.help.gmail_setup'),
            self::REASON_CREDENTIALS_MISSING => __('auth.google_services.help.gmail_setup'),
            default => __('auth.google_services.help.gmail_auth'),
        };
    }

    /**
     * Check if re-authentication might help
     */
    public function canReauthenticate(): bool
    {
        return in_array($this->reason, [
            self::REASON_TOKEN_EXPIRED,
            self::REASON_TOKEN_INVALID,
            self::REASON_REFRESH_FAILED,
        ]);
    }

    /**
     * Create exception for expired token
     */
    public static function tokenExpired(?string $authMethod = null): self
    {
        return new self(
            'Gmail OAuth token has expired',
            self::REASON_TOKEN_EXPIRED,
            $authMethod
        );
    }

    /**
     * Create exception for missing token
     */
    public static function noToken(): self
    {
        return new self(
            'No Gmail OAuth token found',
            self::REASON_NO_TOKEN
        );
    }

    /**
     * Create exception for missing credentials
     */
    public static function credentialsMissing(): self
    {
        return new self(
            'Gmail API credentials are not configured',
            self::REASON_CREDENTIALS_MISSING
        );
    }

    /**
     * Create exception for refresh failure
     */
    public static function refreshFailed(?string $authMethod = null): self
    {
        return new self(
            'Failed to refresh Gmail OAuth token',
            self::REASON_REFRESH_FAILED,
            $authMethod
        );
    }
}

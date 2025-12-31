<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception thrown when OAuth verification issues occur
 *
 * Handles test user limitations, verification pending status, and verification rejection.
 *
 * @see Requirements 1.4, 1.5, 4.1, 7.4
 */
class GoogleVerificationException extends GoogleServicesException
{
    /**
     * Verification status constants
     */
    public const STATUS_TESTING = 'testing';

    public const STATUS_PENDING = 'pending';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_UNKNOWN = 'unknown';

    public function __construct(
        string $message = '',
        protected string $verificationStatus = self::STATUS_UNKNOWN,
        protected ?string $email = null,
        protected bool $isTestUser = false,
        array $context = [],
        int $code = 403,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            $message,
            GoogleServicesException::TYPE_VERIFICATION,
            GoogleServicesException::SERVICE_OAUTH,
            array_merge($context, [
                'verification_status' => $verificationStatus,
                'email' => $email,
                'is_test_user' => $isTestUser,
            ]),
            $code,
            $previous
        );
    }

    /**
     * Get the verification status
     */
    public function getVerificationStatus(): string
    {
        return $this->verificationStatus;
    }

    /**
     * Get the email address
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Check if user is a test user
     */
    public function isTestUser(): bool
    {
        return $this->isTestUser;
    }

    /**
     * Get user-friendly error message
     */
    public function getUserMessage(): string
    {
        if ($this->verificationStatus === self::STATUS_TESTING && ! $this->isTestUser) {
            return __('auth.test_user_required', [
                'email' => $this->email ?? 'unknown',
                'status' => __('auth.oauth_status.'.$this->verificationStatus),
            ]);
        }

        return match ($this->verificationStatus) {
            self::STATUS_PENDING => __('auth.verification_pending'),
            self::STATUS_REJECTED => __('auth.google_services.verification_rejected'),
            default => __('auth.google_sso_failed'),
        };
    }

    /**
     * Get help text for the error
     */
    public function getHelpText(): string
    {
        if ($this->verificationStatus === self::STATUS_TESTING && ! $this->isTestUser) {
            return __('auth.google_services.help.test_user');
        }

        return match ($this->verificationStatus) {
            self::STATUS_PENDING => __('auth.google_services.help.verification_pending'),
            self::STATUS_REJECTED => __('auth.google_services.help.verification_rejected'),
            default => __('auth.google_sso_fallback_hint'),
        };
    }

    /**
     * Create exception for test user limitation
     */
    public static function testUserRequired(string $email, string $verificationStatus): self
    {
        return new self(
            "User {$email} is not in the test user list",
            $verificationStatus,
            $email,
            false
        );
    }

    /**
     * Create exception for verification pending
     */
    public static function verificationPending(): self
    {
        return new self(
            'OAuth application verification is pending',
            self::STATUS_PENDING
        );
    }

    /**
     * Create exception for verification rejected
     */
    public static function verificationRejected(): self
    {
        return new self(
            'OAuth application verification was rejected',
            self::STATUS_REJECTED
        );
    }
}

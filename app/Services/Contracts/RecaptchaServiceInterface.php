<?php

declare(strict_types=1);

namespace App\Services\Contracts;

/**
 * reCAPTCHA Enterprise Service Interface
 *
 * @see Requirements 14.2 - Invisible reCAPTCHA on all guest forms
 */
interface RecaptchaServiceInterface
{
    /**
     * Verify a reCAPTCHA token.
     *
     * @param  string  $token  The reCAPTCHA token from the frontend
     * @param  string  $action  The expected action name
     * @param  string|null  $ipAddress  The user's IP address (optional)
     * @return array{success: bool, score: float, action: string, error_codes: array<int, string>}
     */
    public function verify(string $token, string $action, ?string $ipAddress = null): array;

    /**
     * Check if reCAPTCHA is enabled.
     */
    public function isEnabled(): bool;

    /**
     * Get the site key for frontend rendering.
     */
    public function getSiteKey(): string;

    /**
     * Get the minimum score threshold.
     */
    public function getMinScore(): float;

    /**
     * Get the action name for a specific form type.
     */
    public function getActionName(string $formType): string;
}

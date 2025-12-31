<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Exceptions\GoogleServicesException;
use Illuminate\Http\RedirectResponse;

/**
 * Interface for Google Services Error Handler
 *
 * Defines the contract for handling errors across all Google services
 * including SSO, Gmail API, and OAuth verification.
 *
 * @see Requirements 7.1, 7.2, 7.4, 7.5
 */
interface GoogleServicesErrorHandlerInterface
{
    /**
     * Response type constants
     */
    public const RESPONSE_REDIRECT = 'redirect';

    public const RESPONSE_JSON = 'json';

    public const RESPONSE_ARRAY = 'array';

    /**
     * Handle any Google services exception
     *
     * @param  \Throwable  $exception  The exception to handle
     * @param  string  $serviceType  The service type (sso, gmail, oauth)
     * @param  string|null  $email  The user's email address if known
     * @param  string  $responseType  The type of response to return
     * @return RedirectResponse|array The error response
     */
    public function handle(
        \Throwable $exception,
        string $serviceType = GoogleServicesException::SERVICE_SSO,
        ?string $email = null,
        string $responseType = self::RESPONSE_REDIRECT
    ): RedirectResponse|array;

    /**
     * Get user-friendly error message for a given error type
     *
     * @param  string  $errorType  The error type constant
     * @return string The localized error message
     */
    public function getErrorMessage(string $errorType): string;

    /**
     * Get help text for a given error type
     *
     * @param  string  $errorType  The error type constant
     * @return string The localized help text
     */
    public function getHelpText(string $errorType): string;

    /**
     * Check if an error type should trigger fallback
     *
     * @param  string  $errorType  The error type constant
     * @return bool True if fallback should be triggered
     */
    public function shouldTriggerFallback(string $errorType): bool;
}

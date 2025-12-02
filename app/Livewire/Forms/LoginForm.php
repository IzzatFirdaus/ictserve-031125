<?php

declare(strict_types=1);

/**
 * Component name: Login Form
 * Description: Livewire form for user authentication with flexible login (email OR username)
 *
 * Implements Task 14.1 - Flexible Login Implementation:
 * - Accept full email (user@motac.gov.my) OR short username (user)
 * - Username extraction logic to convert short username to full email
 * - Generic error messages (no user enumeration)
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-001.1 (Authentication)
 * @trace D03 SRS-AUTH-001 (Flexible Login)
 * @trace D04 §5.2 (Security)
 * @trace D10 §7 (Component Documentation)
 * @trace Requirements 16.2, 16.3, 16.5
 *
 * @version 2.0.0
 *
 * @created 2025-11-03
 *
 * @updated 2025-12-02 - Task 14.1: Flexible login (email OR username)
 */

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    /**
     * The login identifier - can be full email or short username
     * Per Requirement 16.2, 16.3: Accept full email OR short username
     */
    #[Validate('required|string|max:255')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * The MOTAC email domain for username-to-email conversion
     */
    private const MOTAC_EMAIL_DOMAIN = 'motac.gov.my';

    /**
     * Attempt to authenticate the request's credentials.
     *
     * Task 14.1: Flexible Login Implementation
     * - Accepts full email (user@motac.gov.my) OR short username (user)
     * - Converts short username to full email before authentication
     * - Uses generic error messages to prevent user enumeration (Req 16.5)
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Normalize the login identifier to email format
        $email = $this->normalizeLoginIdentifier($this->email);

        // Attempt authentication with the normalized email
        if (! Auth::attempt(['email' => $email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            // Requirement 16.5: Generic error message - no user enumeration
            // Same message whether email/username exists or not
            throw ValidationException::withMessages([
                'form.email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Normalize the login identifier to a full email address.
     *
     * Per Requirements 16.2, 16.3:
     * - If input contains '@', treat as full email
     * - If input does not contain '@', append @motac.gov.my domain
     *
     * @param  string  $identifier  The login identifier (email or username)
     * @return string The normalized email address
     */
    protected function normalizeLoginIdentifier(string $identifier): string
    {
        $identifier = Str::lower(trim($identifier));

        // If already contains @, treat as full email
        if (str_contains($identifier, '@')) {
            return $identifier;
        }

        // Otherwise, append MOTAC domain to create full email
        return $identifier.'@'.self::MOTAC_EMAIL_DOMAIN;
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     *
     * Uses the normalized email for consistent rate limiting
     * whether user enters email or username.
     */
    protected function throttleKey(): string
    {
        $normalizedEmail = $this->normalizeLoginIdentifier($this->email);

        return Str::transliterate(Str::lower($normalizedEmail).'|'.request()->ip());
    }
}

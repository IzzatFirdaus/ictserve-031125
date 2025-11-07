<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

/**
 * Password Validation Service Provider
 *
 * Configures password complexity requirements for ICTServe.
 * Requirement 17.2: Password complexity requirements
 * Requirement D11 §8: Security standards
 *
 * Password Requirements:
 * - Minimum 8 characters
 * - At least one uppercase letter
 * - At least one lowercase letter
 * - At least one number
 * - At least one special character
 * - Not compromised in data breaches
 *
 * @version 1.0.0
 *
 * @since 2025-11-07
 * @see D03-FR-004.1 Authentication requirements
 * @see D11 §8 Security standards
 */
class PasswordValidationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * Configures default password validation rules for the application.
     */
    public function boot(): void
    {
        Password::defaults(function () {
            $rule = Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();

            // In production, enforce stricter rules
            if ($this->app->isProduction()) {
                $rule->min(12);
            }

            return $rule;
        });
    }
}

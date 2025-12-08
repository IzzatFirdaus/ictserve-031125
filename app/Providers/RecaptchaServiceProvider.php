<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Contracts\RecaptchaServiceInterface;
use App\Services\RecaptchaService;
use Illuminate\Support\ServiceProvider;

/**
 * reCAPTCHA Enterprise Service Provider
 *
 * Registers the reCAPTCHA service for dependency injection.
 *
 * @see Requirements 14.2 - Invisible reCAPTCHA on all guest forms
 */
class RecaptchaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(RecaptchaServiceInterface::class, RecaptchaService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

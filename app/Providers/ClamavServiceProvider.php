<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\ClamavService;
use App\Services\Contracts\ClamavServiceInterface;
use Illuminate\Support\ServiceProvider;

/**
 * ClamAV Antivirus Scanner Service Provider
 *
 * Registers the ClamAV service for dependency injection.
 *
 * @see Requirements 14.3 - Scan uploads before storage
 */
class ClamavServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ClamavServiceInterface::class, ClamavService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

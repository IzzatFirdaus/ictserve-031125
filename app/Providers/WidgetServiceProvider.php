<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\WidgetRegistryInterface;
use App\Services\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

/**
 * Widget Service Provider
 *
 * Registers widget-related services and bindings for the
 * dashboard widget optimization system.
 *
 * @trace Requirements: R1 (Widget Deduplication), R3 (Missing Widget Detection)
 *
 * @see D04 §3.2 Widget Management Architecture
 *
 * @version 3.6.1
 */
class WidgetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind the widget registry interface to implementation
        $this->app->bind(WidgetRegistryInterface::class, WidgetRegistry::class);

        // Register as singleton for performance
        $this->app->singleton(WidgetRegistry::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

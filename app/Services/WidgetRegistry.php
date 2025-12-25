<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\WidgetRegistryInterface;
use App\Models\WidgetRegistry as WidgetRegistryModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Widget Registry Service
 *
 * Centralized management and deduplication of dashboard widgets
 * following ICTServe v3.6.1 patterns and Filament v4.3.1 compliance.
 *
 * @trace Requirements: R1 (Widget Deduplication), R3 (Missing Widget Detection)
 *
 * @see D04 §3.2 Widget Management Architecture
 *
 * @version 3.6.1
 */
class WidgetRegistry implements WidgetRegistryInterface
{
    private const CACHE_KEY = 'widget_registry';

    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Register a widget with the registry
     */
    

/**
 * @param array<string, mixed> $config
 */
public function register(string $widgetClass, array $config = []): void
    {
        // Validate widget class
        if (! $this->validateWidget($widgetClass)) {
            Log::warning('Attempted to register invalid widget', [
                'widget_class' => $widgetClass,
                'config' => $config,
            ]);

            return;
        }

        // Extract configuration
        $category = $config['category'] ?? $this->detectCategory($widgetClass);
        $sortOrder = $config['sort_order'] ?? 1;
        $roles = $config['roles'] ?? ['staff', 'admin', 'superuser'];
        $refreshRate = $config['refresh_rate'] ?? 300;
        $cacheTtl = $config['cache_ttl'] ?? 600;
        $configuration = $config['configuration'] ?? [];

        // Create or update widget registration
        WidgetRegistryModel::updateOrCreate(
            ['widget_class' => $widgetClass],
            [
                'category' => $category,
                'sort_order' => $sortOrder,
                'is_active' => $config['is_active'] ?? true,
                'configuration' => $configuration,
                'roles' => $roles,
                'refresh_rate' => $refreshRate,
                'cache_ttl' => $cacheTtl,
            ]
        );

        // Clear cache
        $this->clearCache();

        Log::info('Widget registered successfully', [
            'widget_class' => $widgetClass,
            'category' => $category,
        ]);
    }

    /**
     * Remove a widget from the registry
     */
    public function deregister(string $widgetClass): void
    {
        $deleted = WidgetRegistryModel::where('widget_class', $widgetClass)->delete();

        if ($deleted) {
            $this->clearCache();

            Log::info('Widget deregistered successfully', [
                'widget_class' => $widgetClass,
            ]);
        }
    }

    /**
     * Get all registered widgets
     */
    public function getRegisteredWidgets(): array
    {
        return Cache::remember(self::CACHE_KEY.'.all', self::CACHE_TTL, function () {
            return WidgetRegistryModel::active()
                ->ordered()
                ->get()
                ->toArray();
        });
    }

    /**
     * Get widgets filtered by category
     */
    public function getWidgetsByCategory(string $category): array
    {
        return Cache::remember(self::CACHE_KEY.".category.{$category}", self::CACHE_TTL, function () use ($category) {
            return WidgetRegistryModel::active()
                ->byCategory($category)
                ->orderBy('sort_order')
                ->get()
                ->toArray();
        });
    }

    /**
     * Get widgets accessible to a specific role
     */
    public function getWidgetsByRole(string $role): array
    {
        return Cache::remember(self::CACHE_KEY.".role.{$role}", self::CACHE_TTL, function () use ($role) {
            return WidgetRegistryModel::active()
                ->byRole($role)
                ->ordered()
                ->get()
                ->toArray();
        });
    }

    /**
     * Validate widget class and configuration
     */
    public function validateWidget(string $widgetClass): bool
    {
        // Check if class exists
        if (! class_exists($widgetClass)) {
            return false;
        }

        try {
            $reflection = new \ReflectionClass($widgetClass);

            // Check if it extends a valid Filament widget base class
            $validBaseClasses = [
                'Filament\Widgets\Widget',
                'Filament\Widgets\StatsOverviewWidget',
                'Filament\Widgets\ChartWidget',
                'Filament\Widgets\TableWidget',
            ];

            foreach ($validBaseClasses as $baseClass) {
                if ($reflection->isSubclassOf($baseClass)) {
                    return true;
                }
            }

            return false;
        } catch (\ReflectionException $e) {
            Log::error('Widget validation failed', [
                'widget_class' => $widgetClass,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Detect duplicate widget registrations
     */
    public function detectDuplicates(): array
    {
        $widgets = WidgetRegistryModel::all();
        $duplicates = [];
        $seen = [];

        foreach ($widgets as $widget) {
            $signature = $widget->getSignature();

            if (isset($seen[$signature])) {
                $duplicates[] = [
                    'original' => $seen[$signature],
                    'duplicate' => $widget->toArray(),
                    'signature' => $signature,
                ];

                Log::warning('Duplicate widget detected', [
                    'original' => $seen[$signature]['widget_class'],
                    'duplicate' => $widget->widget_class,
                ]);
            } else {
                $seen[$signature] = $widget->toArray();
            }
        }

        return $duplicates;
    }

    /**
     * Check if a widget is registered
     */
    public function isRegistered(string $widgetClass): bool
    {
        return WidgetRegistryModel::where('widget_class', $widgetClass)->exists();
    }

    /**
     * Detect widget category based on class hierarchy
     */
    private function detectCategory(string $widgetClass): string
    {
        if (! class_exists($widgetClass)) {
            return 'content';
        }

        $reflection = new \ReflectionClass($widgetClass);

        // Check parent class to determine category
        if ($reflection->isSubclassOf('Filament\Widgets\StatsOverviewWidget')) {
            return 'header';
        }

        if ($reflection->isSubclassOf('Filament\Widgets\ChartWidget')) {
            return 'charts';
        }

        return 'content';
    }

    /**
     * Clear widget registry cache
     */
    private function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY.'.all');

        // Clear category caches
        foreach (['header', 'content', 'charts'] as $category) {
            Cache::forget(self::CACHE_KEY.".category.{$category}");
        }

        // Clear role caches
        foreach (['staff', 'admin', 'superuser'] as $role) {
            Cache::forget(self::CACHE_KEY.".role.{$role}");
        }
    }
}

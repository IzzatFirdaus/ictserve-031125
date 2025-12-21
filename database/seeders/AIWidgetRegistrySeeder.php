<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Filament\Widgets\AIPerformanceWidget;
use App\Filament\Widgets\AICostWidget;
use App\Filament\Widgets\AIHealthWidget;
use App\Services\WidgetRegistry;
use Illuminate\Database\Seeder;

/**
 * AI Widget Registry Seeder
 *
 * Registers AI performance, cost, and health widgets in the widget registry
 * with proper role-based access control and configuration.
 *
 * trace: D18-§4.1 (AI Dashboard Integration), R21 (Cloud Hybrid AI Dashboard Integration)
 */
class AIWidgetRegistrySeeder extends Seeder
{
    public function run(): void
    {
        $widgetRegistry = app(WidgetRegistry::class);

        // Register AI Performance Widget
        $widgetRegistry->register(AIPerformanceWidget::class, [
            'category' => 'header',
            'sort_order' => 15,
            'roles' => ['admin', 'superuser'],
            'refresh_rate' => 30, // 30 seconds
            'cache_ttl' => 120, // 2 minutes
            'is_active' => true,
            'configuration' => [
                'polling_interval' => '30s',
                'lazy_load' => false,
                'column_span' => 'full',
            ],
        ]);

        // Register AI Cost Widget
        $widgetRegistry->register(AICostWidget::class, [
            'category' => 'header',
            'sort_order' => 16,
            'roles' => ['admin', 'superuser'],
            'refresh_rate' => 300, // 5 minutes
            'cache_ttl' => 120, // 2 minutes
            'is_active' => true,
            'configuration' => [
                'polling_interval' => '5m',
                'lazy_load' => false,
                'column_span' => 'full',
            ],
        ]);

        // Register AI Health Widget
        $widgetRegistry->register(AIHealthWidget::class, [
            'category' => 'header',
            'sort_order' => 17,
            'roles' => ['admin', 'superuser'],
            'refresh_rate' => 60, // 1 minute
            'cache_ttl' => 120, // 2 minutes
            'is_active' => true,
            'configuration' => [
                'polling_interval' => '1m',
                'lazy_load' => false,
                'column_span' => 'full',
            ],
        ]);

        $this->command->info('AI widgets registered successfully in WidgetRegistry');
    }
}

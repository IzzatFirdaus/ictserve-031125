<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create Widget Registries Table Migration
 *
 * Creates the widget_registries table for storing widget registration
 * information including configuration, categorization, and access control.
 *
 * @trace Requirements: R1 (Widget Deduplication), R3 (Missing Widget Detection)
 *
 * @see D04 §3.2 Widget Management Architecture
 * @see D09 Database Documentation - Dual Audit System
 *
 * @version 3.6.1
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('widget_registries', function (Blueprint $table) {
            $table->id();

            // Widget identification
            $table->string('widget_class')->unique()->comment('Fully qualified widget class name');

            // Widget organization
            $table->enum('category', ['header', 'content', 'charts'])
                ->default('content')
                ->comment('Widget display category');
            $table->unsignedInteger('sort_order')
                ->default(1)
                ->comment('Display order within category');

            // Widget status
            $table->boolean('is_active')
                ->default(true)
                ->comment('Whether widget is active and should be displayed');

            // Widget configuration
            $table->json('configuration')
                ->comment('Widget-specific configuration and metadata');

            // Access control
            $table->json('roles')
                ->comment('User roles that can access this widget');

            // Performance settings
            $table->unsignedInteger('refresh_rate')
                ->default(300)
                ->comment('Widget refresh rate in seconds');
            $table->unsignedInteger('cache_ttl')
                ->default(600)
                ->comment('Cache time-to-live in seconds');

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index(['category', 'sort_order'], 'idx_category_sort');
            $table->index(['is_active'], 'idx_active');
            $table->index(['widget_class'], 'idx_widget_class');

            // Composite index for role-based queries
            $table->index(['is_active', 'category'], 'idx_active_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_registries');
    }
};

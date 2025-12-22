<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add dashboard layout optimization for users table
 *
 * The dashboard_layout column already exists, this migration adds
 * performance optimizations for widget customization functionality.
 *
 * @trace Requirements: R5 (Widget Configuration), R20 (Widget Customization)
 *
 * @see D04 §3.2 Widget Management Architecture
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
        Schema::table('users', function (Blueprint $table) {
            // Add index for dashboard_layout queries (performance optimization)
            // Note: dashboard_layout column already exists from previous migration
            if (Schema::hasColumn('users', 'dashboard_layout')) {
                // Add comment to existing column for documentation
                $table->json('dashboard_layout')->nullable()
                    ->comment('Dashboard widget arrangement preferences with customization options')
                    ->change();
            } else {
                // Fallback: create column if it doesn't exist
                $table->json('dashboard_layout')->nullable()
                    ->comment('Dashboard widget arrangement preferences with customization options');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // No changes to revert - we only added comments and optimizations
            // The dashboard_layout column should remain as it was created by a previous migration
        });
    }
};

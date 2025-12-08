<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add UI preferences fields to users table
 *
 * @trace /D09 schema guidelines
 *
 * @requirements 23.2, 23.3, 23.4, 25.2, 25.3
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Theme preference: light, dark, or system (default)
            // @requirements 25.2, 25.3
            // Note: theme_preference may already exist from earlier migration
            if (! Schema::hasColumn('users', 'theme_preference')) {
                $table->string('theme_preference', 10)
                    ->default('system')
                    ->after('remember_token')
                    ->comment('User theme preference: light|dark|system');
            }

            // Saved filter combinations for tables
            // @requirements 23.2
            if (! Schema::hasColumn('users', 'saved_filters')) {
                $table->json('saved_filters')
                    ->nullable()
                    ->after('theme_preference')
                    ->comment('Saved filter combinations for tables');
            }

            // Dashboard widget layout preferences
            // @requirements 23.3
            if (! Schema::hasColumn('users', 'dashboard_layout')) {
                $table->json('dashboard_layout')
                    ->nullable()
                    ->after('saved_filters')
                    ->comment('Dashboard widget arrangement preferences');
            }

            // Onboarding completion status
            // @requirements 21.3
            if (! Schema::hasColumn('users', 'onboarding_completed')) {
                $table->boolean('onboarding_completed')
                    ->default(false)
                    ->after('dashboard_layout')
                    ->comment('Whether user has completed onboarding tour');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];

            // Only drop columns that exist and were added by this migration
            // Note: theme_preference may have been added by earlier migration
            if (Schema::hasColumn('users', 'saved_filters')) {
                $columnsToDrop[] = 'saved_filters';
            }
            if (Schema::hasColumn('users', 'dashboard_layout')) {
                $columnsToDrop[] = 'dashboard_layout';
            }
            if (Schema::hasColumn('users', 'onboarding_completed')) {
                $columnsToDrop[] = 'onboarding_completed';
            }

            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};

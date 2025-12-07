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
            $table->string('theme_preference', 10)
                ->default('system')
                ->after('remember_token')
                ->comment('User theme preference: light|dark|system');

            // Saved filter combinations for tables
            // @requirements 23.2
            $table->json('saved_filters')
                ->nullable()
                ->after('theme_preference')
                ->comment('Saved filter combinations for tables');

            // Dashboard widget layout preferences
            // @requirements 23.3
            $table->json('dashboard_layout')
                ->nullable()
                ->after('saved_filters')
                ->comment('Dashboard widget arrangement preferences');

            // Onboarding completion status
            // @requirements 21.3
            $table->boolean('onboarding_completed')
                ->default(false)
                ->after('dashboard_layout')
                ->comment('Whether user has completed onboarding tour');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'theme_preference',
                'saved_filters',
                'dashboard_layout',
                'onboarding_completed',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('profile_picture')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->text('two_factor_backup_codes')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->timestamp('two_factor_enabled_at')->nullable();

            // Four-role RBAC system
            $table->enum('role', ['staff', 'approver', 'admin', 'superuser'])->default('staff');

            // Google OAuth SSO fields (v3.5.0 True Hybrid Architecture)
            $table->string('google_id', 255)->nullable()->unique()
                ->comment('Google OAuth user ID for SSO');
            $table->text('google_token')->nullable()
                ->comment('Encrypted Google OAuth access token');
            $table->text('google_refresh_token')->nullable()
                ->comment('Encrypted Google OAuth refresh token');

            // Locale preference (DEPRECATED v3.6.0: Always ms. Retained for potential future use.)
            $table->string('locale', 10)->default('ms')
                ->comment('DEPRECATED v3.6.0: Always ms. Retained for potential future use.');

            // Organizational structure
            $table->string('staff_id', 50)->unique()->nullable();
            $table->string('staff_number', 50)->nullable()
                ->comment('MOTAC staff number for identification');
            $table->string('division_code', 20)->nullable()
                ->comment('Division code for organizational structure');
            $table->foreignId('division_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('grade_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('position_id')->nullable()->constrained()->onDelete('set null');

            // Contact information
            $table->string('phone', 20)->nullable();
            $table->string('mobile', 20)->nullable();

            // Profile
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable()
                ->comment('IP address of last login for audit');

            // Account linking counter (v3.5.0)
            $table->unsignedInteger('guest_submissions_linked')->default(0)
                ->comment('Count of guest submissions linked to this account');

            // Password management
            $table->timestamp('password_changed_at')->nullable();
            $table->boolean('require_password_change')->default(false);

            // Portal features
            $table->boolean('has_completed_tour')->default(false);

            // Notification preferences (JSON)
            $table->json('notification_preferences')
                ->nullable()
                ->comment('User notification preferences for email alerts');

            // UI Preferences (v3.5.0+)
            $table->string('theme_preference', 10)->default('system')
                ->comment('User theme preference: light|dark|system');
            $table->json('saved_filters')->nullable()
                ->comment('Saved filter combinations for tables');
            $table->json('dashboard_layout')->nullable()
                ->comment('Dashboard widget arrangement preferences');
            $table->boolean('onboarding_completed')->default(false)
                ->comment('Whether user has completed onboarding tour');

            // Data governance
            $table->timestamp('anonymized_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('staff_id');
            $table->index('role');
            $table->index('is_active');
            $table->index(['division_id', 'grade_id']);
            $table->index(['role', 'is_active']);
            $table->index(['division_id', 'is_active']);
            $table->index('anonymized_at');

            // v3.5.0 True Hybrid Architecture indexes
            $table->index('google_id', 'idx_users_google_id');
            $table->index('locale', 'idx_users_locale');
            $table->index('staff_number', 'idx_users_staff_number');
            $table->index('division_code', 'idx_users_division_code');

            // Performance indexes (consolidated from 2025_01_21_000001)
            $table->index('grade_id', 'idx_users_grade'); // For approver filtering
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();

            // Performance index for cleanup (consolidated from 2025_01_21_000001)
            $table->index('last_activity', 'idx_sessions_last_activity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

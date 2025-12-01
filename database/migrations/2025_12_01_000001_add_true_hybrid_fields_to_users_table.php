<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add True Hybrid Architecture fields to users table
 *
 * Implements v3.5.0 True Hybrid Architecture fields for:
 * - Google OAuth SSO integration (Req 38)
 * - Enhanced audit trail (Req 19.1)
 * - Account linking for guest submissions (Req 18)
 * - Locale preferences (Req 11)
 *
 * @see D00 §4.1 True Hybrid Architecture
 * @see D03 SRS-AUTH-001 Self-registration and flexible login
 * @see D09 §4.6 Dual Audit System
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Google OAuth SSO fields (Req 38.3, 38.4)
            $table->string('google_id', 255)->nullable()->unique()->after('email')
                ->comment('Google OAuth user ID for SSO');
            $table->text('google_token')->nullable()->after('google_id')
                ->comment('Encrypted Google OAuth access token');
            $table->text('google_refresh_token')->nullable()->after('google_token')
                ->comment('Encrypted Google OAuth refresh token');

            // Locale preference (Req 11.2)
            $table->string('locale', 10)->default('ms')->after('role')
                ->comment('User language preference: ms (Bahasa Melayu) or en (English)');

            // Staff identification fields (Req 17.4)
            $table->string('staff_number', 50)->nullable()->after('locale')
                ->comment('MOTAC staff number for identification');
            $table->string('division_code', 20)->nullable()->after('staff_number')
                ->comment('Division code for organizational structure');

            // Enhanced audit trail (Req 19.1)
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at')
                ->comment('IP address of last login for audit');

            // Account linking counter (Req 18.4)
            $table->unsignedInteger('guest_submissions_linked')->default(0)->after('last_login_ip')
                ->comment('Count of guest submissions linked to this account');

            // Indexes for performance
            $table->index('google_id', 'idx_users_google_id');
            $table->index('locale', 'idx_users_locale');
            $table->index('staff_number', 'idx_users_staff_number');
            $table->index('division_code', 'idx_users_division_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_users_google_id');
            $table->dropIndex('idx_users_locale');
            $table->dropIndex('idx_users_staff_number');
            $table->dropIndex('idx_users_division_code');

            // Drop columns
            $table->dropColumn([
                'google_id',
                'google_token',
                'google_refresh_token',
                'locale',
                'staff_number',
                'division_code',
                'last_login_ip',
                'guest_submissions_linked',
            ]);
        });
    }
};

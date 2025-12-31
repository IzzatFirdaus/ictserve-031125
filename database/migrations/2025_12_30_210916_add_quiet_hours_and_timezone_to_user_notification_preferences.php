<?php

declare(strict_types=1);

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
        Schema::table('user_notification_preferences', function (Blueprint $table) {
            // Add quiet hours columns if they don't exist
            if (! Schema::hasColumn('user_notification_preferences', 'quiet_hours_enabled')) {
                $table->boolean('quiet_hours_enabled')->default(false)->after('preference_value');
            }
            if (! Schema::hasColumn('user_notification_preferences', 'quiet_hours_start')) {
                $table->time('quiet_hours_start')->nullable()->after('quiet_hours_enabled');
            }
            if (! Schema::hasColumn('user_notification_preferences', 'quiet_hours_end')) {
                $table->time('quiet_hours_end')->nullable()->after('quiet_hours_start');
            }
            if (! Schema::hasColumn('user_notification_preferences', 'timezone')) {
                $table->string('timezone', 64)->default('Asia/Kuala_Lumpur')->after('quiet_hours_end');
            }
            if (! Schema::hasColumn('user_notification_preferences', 'email_digest_enabled')) {
                $table->boolean('email_digest_enabled')->default(false)->after('timezone');
            }
            if (! Schema::hasColumn('user_notification_preferences', 'email_digest_frequency')) {
                $table->string('email_digest_frequency', 20)->default('immediate')->after('email_digest_enabled');
            }
            if (! Schema::hasColumn('user_notification_preferences', 'email_digest_time')) {
                $table->time('email_digest_time')->nullable()->after('email_digest_frequency');
            }
            if (! Schema::hasColumn('user_notification_preferences', 'browser_notifications_enabled')) {
                $table->boolean('browser_notifications_enabled')->default(true)->after('email_digest_time');
            }
            if (! Schema::hasColumn('user_notification_preferences', 'sound_enabled')) {
                $table->boolean('sound_enabled')->default(true)->after('browser_notifications_enabled');
            }
            if (! Schema::hasColumn('user_notification_preferences', 'group_notifications')) {
                $table->boolean('group_notifications')->default(true)->after('sound_enabled');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_notification_preferences', function (Blueprint $table) {
            $columns = [
                'quiet_hours_enabled',
                'quiet_hours_start',
                'quiet_hours_end',
                'timezone',
                'email_digest_enabled',
                'email_digest_frequency',
                'email_digest_time',
                'browser_notifications_enabled',
                'sound_enabled',
                'group_notifications',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('user_notification_preferences', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

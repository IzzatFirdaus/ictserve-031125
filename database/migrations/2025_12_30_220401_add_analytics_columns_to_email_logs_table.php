<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add analytics columns to email_logs table
 *
 * Adds columns required for email analytics and reporting:
 * - delivered_at: Timestamp when email was confirmed delivered
 * - retry_attempts: Number of retry attempts made
 * - last_retry_at: Timestamp of last retry attempt
 * - error_message: Detailed error message for failures
 *
 * @see D03 SRS-FR-008
 * @see D04 §6.2
 *
 * @requirements 10.1, 10.3, 10.5
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_logs', function (Blueprint $table): void {
            $table->timestamp('delivered_at')->nullable()->after('failed_at');
            $table->unsignedTinyInteger('retry_attempts')->default(0)->after('delivered_at');
            $table->timestamp('last_retry_at')->nullable()->after('retry_attempts');
            $table->text('error_message')->nullable()->after('last_retry_at');

            // Add indexes for analytics queries
            $table->index('delivered_at', 'idx_email_logs_delivered_at');
            $table->index('retry_attempts', 'idx_email_logs_retry_attempts');
            $table->index(['status', 'created_at'], 'idx_email_logs_status_created');
        });
    }

    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table): void {
            $table->dropIndex('idx_email_logs_delivered_at');
            $table->dropIndex('idx_email_logs_retry_attempts');
            $table->dropIndex('idx_email_logs_status_created');

            $table->dropColumn(['delivered_at', 'retry_attempts', 'last_retry_at', 'error_message']);
        });
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Enhanced Email Logs Migration for Unified Notification System
 *
 * Adds columns to support multi-channel notification tracking, retry scheduling,
 * and notification preference management integration.
 *
 * New Columns:
 * - channels: JSON array of notification channels used (email, database, broadcast)
 * - notification_type: Link to config/notifications.php types (ticket_status_changed, etc.)
 * - priority: Email priority level (critical, high, normal, low)
 * - next_retry_at: Timestamp for next scheduled retry (for UI display)
 * - final_status: Permanent outcome (delivered, permanently_failed, bounced)
 * - preference_bypassed: Whether user notification preference was overridden
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-008.1 Enhanced email workflows
 * @trace D03-FR-010.1 Notification preferences
 *
 * @version 1.0.0
 *
 * @created 2025-11-18
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            // Multi-channel tracking - JSON array of channels used for this notification
            // e.g., ['email', 'database', 'broadcast']
            $table->json('channels')->nullable()->after('meta')
                ->comment('Notification channels used for this dispatch');

            // Notification type from config/notifications.php
            // Links email to unified notification system types
            $table->string('notification_type', 100)->nullable()->after('channels')
                ->comment('Type from config/notifications.php (ticket_status_changed, loan_approval_request, etc.)');

            // Priority level from notification type configuration
            $table->enum('priority', ['critical', 'high', 'normal', 'low'])
                ->default('normal')
                ->after('notification_type')
                ->comment('Email priority level for queue and retry logic');

            // Next retry timestamp - helps UI show when retry will occur
            $table->timestamp('next_retry_at')->nullable()->after('priority')
                ->comment('Scheduled time for next retry attempt');

            // Permanent final outcome of email delivery
            $table->enum('final_status', ['delivered', 'permanently_failed', 'bounced', 'rejected'])
                ->nullable()
                ->after('next_retry_at')
                ->comment('Permanent delivery outcome after all retries exhausted');

            // Track if notification preference was bypassed (for critical notifications)
            $table->boolean('preference_bypassed')->default(false)->after('final_status')
                ->comment('True if user notification preference was overridden (critical notifications)');

            // Indexes for common queries
            $table->index('notification_type', 'idx_email_logs_notification_type');
            $table->index('priority', 'idx_email_logs_priority');
            $table->index('next_retry_at', 'idx_email_logs_next_retry_at');
            $table->index('final_status', 'idx_email_logs_final_status');
            $table->index(['notification_type', 'status'], 'idx_email_logs_type_status');
            $table->index(['priority', 'status'], 'idx_email_logs_priority_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_email_logs_notification_type');
            $table->dropIndex('idx_email_logs_priority');
            $table->dropIndex('idx_email_logs_next_retry_at');
            $table->dropIndex('idx_email_logs_final_status');
            $table->dropIndex('idx_email_logs_type_status');
            $table->dropIndex('idx_email_logs_priority_status');

            // Drop columns
            $table->dropColumn([
                'channels',
                'notification_type',
                'priority',
                'next_retry_at',
                'final_status',
                'preference_bypassed',
            ]);
        });
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add notification preferences for authenticated users to control
     * email notifications for ticket updates, system announcements, etc.
     *
     * Notification Preferences Structure (JSON):
     * {
     *   "ticket_updates": true,
     *   "ticket_assignments": true,
     *   "ticket_comments": true,
     *   "system_announcements": true,
     *   "loan_approvals": true,
     *   "loan_reminders": true,
     *   "sla_alerts": true
     * }
     *
     * Note: The users table already has:
     * - Four-role RBAC (staff, approver, admin, superuser)
     * - grade_id and division_id columns
     * - Performance indexes
     *
     * Requirements: Requirement 3.1, Requirement 7.4, Requirement 10.1
     * Traceability: D03-FR-003.1, D03-FR-007.4, D04 §2.3
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Notification preferences (JSON)
            $table->json('notification_preferences')
                ->nullable()
                ->after('is_active')
                ->comment('User notification preferences for email alerts');

            // Additional performance indexes for authenticated portal
            $table->index(['role', 'is_active']);
            $table->index(['division_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes first (using column arrays, not index names)
            $table->dropIndex(['role', 'is_active']);
            $table->dropIndex(['division_id', 'is_active']);

            // Drop column
            $table->dropColumn('notification_preferences');
        });
    }
};

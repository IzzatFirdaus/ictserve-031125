<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Add performance indexes to portal tables
 *
 * Adds database indexes for optimizing query performance on frequently accessed columns.
 *
 * @see .kiro/specs/staff-dashboard-profile/tasks.md - Task 6.2.2
 * @see .kiro/specs/staff-dashboard-profile/requirements.md - Requirement 13.4
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = Schema::getConnection();

        // Add indexes to portal_activities table
        if (! $this->indexExists($connection, 'portal_activities', 'portal_activities_activity_type_index')) {
            $connection->statement('CREATE INDEX portal_activities_activity_type_index ON portal_activities(activity_type)');
        }

        // Add indexes to internal_comments table
        if (! $this->indexExists($connection, 'internal_comments', 'internal_comments_commentable_index')) {
            $connection->statement('CREATE INDEX internal_comments_commentable_index ON internal_comments(commentable_type, commentable_id)');
        }
        if (! $this->indexExists($connection, 'internal_comments', 'internal_comments_user_id_index')) {
            $connection->statement('CREATE INDEX internal_comments_user_id_index ON internal_comments(user_id)');
        }

        // Add indexes to saved_searches table
        if (! $this->indexExists($connection, 'saved_searches', 'saved_searches_user_id_index')) {
            $connection->statement('CREATE INDEX saved_searches_user_id_index ON saved_searches(user_id)');
        }

        // Add indexes to user_notification_preferences table
        if (! $this->indexExists($connection, 'user_notification_preferences', 'user_notification_preferences_user_id_index')) {
            $connection->statement('CREATE INDEX user_notification_preferences_user_id_index ON user_notification_preferences(user_id)');
        }

        // Add indexes to helpdesk_tickets table
        if (! $this->indexExists($connection, 'helpdesk_tickets', 'helpdesk_tickets_user_status_index')) {
            $connection->statement('CREATE INDEX helpdesk_tickets_user_status_index ON helpdesk_tickets(user_id, status)');
        }
        if (! $this->indexExists($connection, 'helpdesk_tickets', 'helpdesk_tickets_created_at_index')) {
            $connection->statement('CREATE INDEX helpdesk_tickets_created_at_index ON helpdesk_tickets(created_at)');
        }

        // Add indexes to loan_applications table
        if (! $this->indexExists($connection, 'loan_applications', 'loan_applications_user_status_index')) {
            $connection->statement('CREATE INDEX loan_applications_user_status_index ON loan_applications(user_id, status)');
        }
        if (! $this->indexExists($connection, 'loan_applications', 'loan_applications_loan_end_date_index')) {
            $connection->statement('CREATE INDEX loan_applications_loan_end_date_index ON loan_applications(loan_end_date)');
        }
    }

    /**
     * Check if index exists on table
     */
    private function indexExists($connection, string $table, string $index): bool
    {
        $driver = $connection->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite: Check using pragma
            $result = $connection->select("PRAGMA index_list({$table})");
            foreach ($result as $indexInfo) {
                if ($indexInfo->name === $index) {
                    return true;
                }
            }

            return false;
        }

        // MySQL/PostgreSQL: Use SHOW INDEX or information_schema
        $result = $connection->select(
            "SHOW INDEX FROM {$table} WHERE Key_name = ?",
            [$index]
        );

        return count($result) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = Schema::getConnection();

        // Drop indexes safely using raw SQL
        try {
            $connection->statement('DROP INDEX portal_activities_activity_type_index ON portal_activities');
        } catch (\Exception $e) {
            // Index may not exist
        }

        try {
            $connection->statement('DROP INDEX internal_comments_commentable_index ON internal_comments');
        } catch (\Exception $e) {
            // Index may not exist
        }

        try {
            $connection->statement('DROP INDEX internal_comments_user_id_index ON internal_comments');
        } catch (\Exception $e) {
            // Index may not exist
        }

        try {
            $connection->statement('DROP INDEX saved_searches_user_id_index ON saved_searches');
        } catch (\Exception $e) {
            // Index may not exist
        }

        try {
            $connection->statement('DROP INDEX user_notification_preferences_user_id_index ON user_notification_preferences');
        } catch (\Exception $e) {
            // Index may not exist
        }

        try {
            $connection->statement('DROP INDEX helpdesk_tickets_user_status_index ON helpdesk_tickets');
        } catch (\Exception $e) {
            // Index may not exist
        }

        try {
            $connection->statement('DROP INDEX helpdesk_tickets_created_at_index ON helpdesk_tickets');
        } catch (\Exception $e) {
            // Index may not exist
        }

        try {
            $connection->statement('DROP INDEX loan_applications_user_status_index ON loan_applications');
        } catch (\Exception $e) {
            // Index may not exist
        }

        try {
            $connection->statement('DROP INDEX loan_applications_loan_end_date_index ON loan_applications');
        } catch (\Exception $e) {
            // Index may not exist
        }
    }
};

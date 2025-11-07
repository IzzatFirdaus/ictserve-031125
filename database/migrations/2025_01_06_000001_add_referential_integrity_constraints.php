<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add Referential Integrity Constraints
 *
 * Ensures proper foreign key relationships with CASCADE and RESTRICT constraints
 * for cross-module integration between helpdesk tickets and asset loans.
 *
 * @trace Requirements 7.5
 */
return new class extends Migration
{
    public function up(): void
    {
        // Add foreign key constraints for helpdesk_tickets table
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            // Asset relationship - RESTRICT to prevent deletion of assets with tickets
            if (! $this->foreignKeyExists('helpdesk_tickets', 'helpdesk_tickets_asset_id_foreign')) {
                $table->foreign('asset_id')
                    ->references('id')
                    ->on('assets')
                    ->onDelete('restrict')
                    ->onUpdate('cascade');
            }

            // User relationship - SET NULL when user is deleted
            if (! $this->foreignKeyExists('helpdesk_tickets', 'helpdesk_tickets_user_id_foreign')) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            }

            // Assigned user relationship - SET NULL when user is deleted
            if (! $this->foreignKeyExists('helpdesk_tickets', 'helpdesk_tickets_assigned_to_foreign')) {
                $table->foreign('assigned_to')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            }

            // Division relationship - SET NULL when division is deleted
            if (! $this->foreignKeyExists('helpdesk_tickets', 'helpdesk_tickets_assigned_to_division_foreign')) {
                $table->foreign('assigned_to_division')
                    ->references('id')
                    ->on('divisions')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            }

            // Category relationship - RESTRICT to prevent deletion of categories with tickets
            if (! $this->foreignKeyExists('helpdesk_tickets', 'helpdesk_tickets_category_id_foreign')) {
                $table->foreign('category_id')
                    ->references('id')
                    ->on('ticket_categories')
                    ->onDelete('restrict')
                    ->onUpdate('cascade');
            }
        });

        // Add foreign key constraints for loan_applications table
        Schema::table('loan_applications', function (Blueprint $table) {
            // User relationship - SET NULL when user is deleted
            if (! $this->foreignKeyExists('loan_applications', 'loan_applications_user_id_foreign')) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            }

            // Division relationship - SET NULL when division is deleted
            if (! $this->foreignKeyExists('loan_applications', 'loan_applications_division_id_foreign')) {
                $table->foreign('division_id')
                    ->references('id')
                    ->on('divisions')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            }

            // Grade relationship - SET NULL when grade is deleted
            if (! $this->foreignKeyExists('loan_applications', 'loan_applications_grade_id_foreign')) {
                $table->foreign('grade_id')
                    ->references('id')
                    ->on('grades')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            }
        });

        // Add foreign key constraints for loan_items table
        Schema::table('loan_items', function (Blueprint $table) {
            // Loan application relationship - CASCADE delete when loan is deleted
            if (! $this->foreignKeyExists('loan_items', 'loan_items_loan_application_id_foreign')) {
                $table->foreign('loan_application_id')
                    ->references('id')
                    ->on('loan_applications')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            }

            // Asset relationship - RESTRICT to prevent deletion of assets with active loans
            if (! $this->foreignKeyExists('loan_items', 'loan_items_asset_id_foreign')) {
                $table->foreign('asset_id')
                    ->references('id')
                    ->on('assets')
                    ->onDelete('restrict')
                    ->onUpdate('cascade');
            }
        });

        // Add foreign key constraints for loan_transactions table
        Schema::table('loan_transactions', function (Blueprint $table) {
            // Loan application relationship - CASCADE delete when loan is deleted
            if (! $this->foreignKeyExists('loan_transactions', 'loan_transactions_loan_application_id_foreign')) {
                $table->foreign('loan_application_id')
                    ->references('id')
                    ->on('loan_applications')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            }

            // User relationships - SET NULL when user is deleted
            if (! $this->foreignKeyExists('loan_transactions', 'loan_transactions_issued_by_user_id_foreign')) {
                $table->foreign('issued_by_user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            }

            if (! $this->foreignKeyExists('loan_transactions', 'loan_transactions_returned_by_user_id_foreign')) {
                $table->foreign('returned_by_user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            }
        });

        // Add foreign key constraints for cross_module_integrations table
        Schema::table('cross_module_integrations', function (Blueprint $table) {
            // No specific foreign keys as source_id and target_id are polymorphic
            // But we can add indexes for better performance
            if (! $this->indexExists('cross_module_integrations', 'cross_module_integrations_source_index')) {
                $table->index(['source_module', 'source_id'], 'cross_module_integrations_source_index');
            }

            if (! $this->indexExists('cross_module_integrations', 'cross_module_integrations_target_index')) {
                $table->index(['target_module', 'target_id'], 'cross_module_integrations_target_index');
            }

            if (! $this->indexExists('cross_module_integrations', 'cross_module_integrations_type_index')) {
                $table->index('integration_type', 'cross_module_integrations_type_index');
            }
        });

        // Add foreign key constraints for assets table
        Schema::table('assets', function (Blueprint $table) {
            // Category relationship - RESTRICT to prevent deletion of categories with assets
            if (! $this->foreignKeyExists('assets', 'assets_category_id_foreign')) {
                $table->foreign('category_id')
                    ->references('id')
                    ->on('asset_categories')
                    ->onDelete('restrict')
                    ->onUpdate('cascade');
            }
        });

        // Add foreign key constraints for helpdesk_comments table
        Schema::table('helpdesk_comments', function (Blueprint $table) {
            // Ticket relationship - CASCADE delete when ticket is deleted
            if (! $this->foreignKeyExists('helpdesk_comments', 'helpdesk_comments_ticket_id_foreign')) {
                $table->foreign('ticket_id')
                    ->references('id')
                    ->on('helpdesk_tickets')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            }

            // User relationship - SET NULL when user is deleted
            if (! $this->foreignKeyExists('helpdesk_comments', 'helpdesk_comments_user_id_foreign')) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            }
        });

        // Add foreign key constraints for helpdesk_attachments table
        Schema::table('helpdesk_attachments', function (Blueprint $table) {
            // Ticket relationship - CASCADE delete when ticket is deleted
            if (! $this->foreignKeyExists('helpdesk_attachments', 'helpdesk_attachments_ticket_id_foreign')) {
                $table->foreign('ticket_id')
                    ->references('id')
                    ->on('helpdesk_tickets')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            }
        });
    }

    public function down(): void
    {
        // Drop foreign key constraints in reverse order
        Schema::table('helpdesk_attachments', function (Blueprint $table) {
            $table->dropForeign(['ticket_id']);
        });

        Schema::table('helpdesk_comments', function (Blueprint $table) {
            $table->dropForeign(['ticket_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        Schema::table('cross_module_integrations', function (Blueprint $table) {
            $table->dropIndex('cross_module_integrations_source_index');
            $table->dropIndex('cross_module_integrations_target_index');
            $table->dropIndex('cross_module_integrations_type_index');
        });

        Schema::table('loan_transactions', function (Blueprint $table) {
            $table->dropForeign(['loan_application_id']);
            $table->dropForeign(['issued_by_user_id']);
            $table->dropForeign(['returned_by_user_id']);
        });

        Schema::table('loan_items', function (Blueprint $table) {
            $table->dropForeign(['loan_application_id']);
            $table->dropForeign(['asset_id']);
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['division_id']);
            $table->dropForeign(['grade_id']);
        });

        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['assigned_to_division']);
            $table->dropForeign(['category_id']);
        });
    }

    /**
     * Check if foreign key constraint exists
     */
    private function foreignKeyExists(string $table, string $constraint): bool
    {
        $schema = Schema::getConnection()->getDoctrineSchemaManager();
        $foreignKeys = $schema->listTableForeignKeys($table);

        foreach ($foreignKeys as $foreignKey) {
            if ($foreignKey->getName() === $constraint) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if index exists
     */
    private function indexExists(string $table, string $index): bool
    {
        $schema = Schema::getConnection()->getDoctrineSchemaManager();
        $indexes = $schema->listTableIndexes($table);

        return array_key_exists($index, $indexes);
    }
};

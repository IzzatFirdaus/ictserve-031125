<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add Performance Indexes Migration
 *
 * Adds database indexes for frequently queried columns to improve
 * query performance and achieve Core Web Vitals targets.
 *
 * @see D09 Database Documentation - Performance Optimization
 * @see docs/deployment-checklist.md - Database Optimization
 *
 * @requirements R08 Performance Optimization
 *
 * @version 1.0.0
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Helpdesk tickets performance indexes
        if (Schema::hasTable('helpdesk_tickets')) {
            Schema::table('helpdesk_tickets', function (Blueprint $table): void {
                // Composite index for status and priority filtering
                if (! $this->indexExists('helpdesk_tickets', 'idx_tickets_status_priority')) {
                    $table->index(['status', 'priority'], 'idx_tickets_status_priority');
                }

                // Index for user's tickets lookup
                if (! $this->indexExists('helpdesk_tickets', 'idx_tickets_user_status')) {
                    $table->index(['user_id', 'status'], 'idx_tickets_user_status');
                }

                // Index for date-based queries
                if (! $this->indexExists('helpdesk_tickets', 'idx_tickets_created_at')) {
                    $table->index(['created_at'], 'idx_tickets_created_at');
                }

                // Index for SLA tracking
                if (! $this->indexExists('helpdesk_tickets', 'idx_tickets_sla_due')) {
                    $table->index(['sla_due_at', 'status'], 'idx_tickets_sla_due');
                }
            });
        }

        // Loan applications performance indexes
        if (Schema::hasTable('loan_applications')) {
            Schema::table('loan_applications', function (Blueprint $table): void {
                // Composite index for status and created_at
                if (! $this->indexExists('loan_applications', 'idx_loans_status_created')) {
                    $table->index(['status', 'created_at'], 'idx_loans_status_created');
                }

                // Index for user's applications lookup
                if (! $this->indexExists('loan_applications', 'idx_loans_user_status')) {
                    $table->index(['user_id', 'status'], 'idx_loans_user_status');
                }

                // Index for approval workflow
                if (! $this->indexExists('loan_applications', 'idx_loans_approver')) {
                    $table->index(['approver_id', 'status'], 'idx_loans_approver');
                }

                // Index for date range queries
                if (! $this->indexExists('loan_applications', 'idx_loans_dates')) {
                    $table->index(['start_date', 'end_date'], 'idx_loans_dates');
                }
            });
        }

        // Assets performance indexes
        if (Schema::hasTable('assets')) {
            Schema::table('assets', function (Blueprint $table): void {
                // Composite index for status and availability
                if (! $this->indexExists('assets', 'idx_assets_status_availability')) {
                    $table->index(['status', 'availability_status'], 'idx_assets_status_availability');
                }

                // Index for category filtering
                if (! $this->indexExists('assets', 'idx_assets_category')) {
                    $table->index(['category_id', 'status'], 'idx_assets_category');
                }
            });
        }

        // Audits performance indexes (for 7-year retention queries)
        if (Schema::hasTable('audits')) {
            Schema::table('audits', function (Blueprint $table): void {
                // Composite index for user and date queries
                if (! $this->indexExists('audits', 'idx_audits_user_created')) {
                    $table->index(['user_id', 'created_at'], 'idx_audits_user_created');
                }

                // Index for auditable type queries
                if (! $this->indexExists('audits', 'idx_audits_auditable')) {
                    $table->index(['auditable_type', 'auditable_id'], 'idx_audits_auditable');
                }

                // Index for event type filtering
                if (! $this->indexExists('audits', 'idx_audits_event')) {
                    $table->index(['event', 'created_at'], 'idx_audits_event');
                }
            });
        }

        // Users performance indexes
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table): void {
                // Index for department filtering
                if (Schema::hasColumn('users', 'department_id') && ! $this->indexExists('users', 'idx_users_department')) {
                    $table->index(['department_id'], 'idx_users_department');
                }

                // Index for grade filtering (approvers)
                if (Schema::hasColumn('users', 'grade') && ! $this->indexExists('users', 'idx_users_grade')) {
                    $table->index(['grade'], 'idx_users_grade');
                }
            });
        }

        // Sessions table index for cleanup
        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table): void {
                if (! $this->indexExists('sessions', 'idx_sessions_last_activity')) {
                    $table->index(['last_activity'], 'idx_sessions_last_activity');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop helpdesk_tickets indexes
        if (Schema::hasTable('helpdesk_tickets')) {
            Schema::table('helpdesk_tickets', function (Blueprint $table): void {
                $table->dropIndex('idx_tickets_status_priority');
                $table->dropIndex('idx_tickets_user_status');
                $table->dropIndex('idx_tickets_created_at');
                $table->dropIndex('idx_tickets_sla_due');
            });
        }

        // Drop loan_applications indexes
        if (Schema::hasTable('loan_applications')) {
            Schema::table('loan_applications', function (Blueprint $table): void {
                $table->dropIndex('idx_loans_status_created');
                $table->dropIndex('idx_loans_user_status');
                $table->dropIndex('idx_loans_approver');
                $table->dropIndex('idx_loans_dates');
            });
        }

        // Drop assets indexes
        if (Schema::hasTable('assets')) {
            Schema::table('assets', function (Blueprint $table): void {
                $table->dropIndex('idx_assets_status_availability');
                $table->dropIndex('idx_assets_category');
            });
        }

        // Drop audits indexes
        if (Schema::hasTable('audits')) {
            Schema::table('audits', function (Blueprint $table): void {
                $table->dropIndex('idx_audits_user_created');
                $table->dropIndex('idx_audits_auditable');
                $table->dropIndex('idx_audits_event');
            });
        }

        // Drop users indexes
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table): void {
                if ($this->indexExists('users', 'idx_users_department')) {
                    $table->dropIndex('idx_users_department');
                }
                if ($this->indexExists('users', 'idx_users_grade')) {
                    $table->dropIndex('idx_users_grade');
                }
            });
        }

        // Drop sessions index
        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table): void {
                $table->dropIndex('idx_sessions_last_activity');
            });
        }
    }

    /**
     * Check if an index exists on a table.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $indexes = $connection->getDoctrineSchemaManager()->listTableIndexes($table);

        return isset($indexes[$indexName]);
    }
};

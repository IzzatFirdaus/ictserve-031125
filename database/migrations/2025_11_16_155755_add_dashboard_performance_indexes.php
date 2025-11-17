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
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_helpdesk_status_created');
            $table->index(['user_id', 'status'], 'idx_helpdesk_user_status');
            $table->index(['sla_resolution_due_at', 'status'], 'idx_helpdesk_sla_status');
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_loan_status_created');
            $table->index(['user_id', 'status'], 'idx_loan_user_status');
            $table->index(['status', 'loan_end_date'], 'idx_status_end_date');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->index(['status', 'updated_at'], 'idx_assets_status_updated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            $table->dropIndex('idx_helpdesk_status_created');
            $table->dropIndex('idx_helpdesk_user_status');
            $table->dropIndex('idx_helpdesk_sla_status');
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropIndex('idx_loan_status_created');
            $table->dropIndex('idx_loan_user_status');
            $table->dropIndex('idx_status_end_date');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex('idx_assets_status_updated');
        });
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add advanced SLA tracking columns to helpdesk_tickets table.
 *
 * @see D03-FR-008 SLA management requirements
 * @see D04 §5.3 SLA escalation workflow
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            // First response tracking (for response SLA)
            $table->timestamp('first_response_at')->nullable()->after('resolved_at');

            // Escalation tracking
            $table->unsignedTinyInteger('escalation_level')->default(0)->after('first_response_at');
            $table->timestamp('escalation_notified_at')->nullable()->after('escalation_level');

            // SLA breach tracking
            $table->timestamp('sla_breached_at')->nullable()->after('escalation_notified_at');
            $table->string('sla_breach_type', 20)->nullable()->after('sla_breached_at');

            // SLA pause tracking (for pending_user status)
            $table->timestamp('sla_paused_at')->nullable()->after('sla_breach_type');
            $table->string('sla_pause_reason', 255)->nullable()->after('sla_paused_at');
            $table->unsignedInteger('sla_total_paused_hours')->default(0)->after('sla_pause_reason');

            // Closure reason for auto-close
            $table->string('closure_reason', 255)->nullable()->after('sla_total_paused_hours');

            // Indexes for SLA queries
            $table->index('first_response_at');
            $table->index('escalation_level');
            $table->index('sla_breached_at');
            $table->index(['status', 'sla_breached_at'], 'idx_helpdesk_status_sla_breach');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            $table->dropIndex('idx_helpdesk_status_sla_breach');
            $table->dropIndex(['sla_breached_at']);
            $table->dropIndex(['escalation_level']);
            $table->dropIndex(['first_response_at']);

            $table->dropColumn([
                'first_response_at',
                'escalation_level',
                'escalation_notified_at',
                'sla_breached_at',
                'sla_breach_type',
                'sla_paused_at',
                'sla_pause_reason',
                'sla_total_paused_hours',
                'closure_reason',
            ]);
        });
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PKS CSIRT Integration (Requirement 28) - Security Incidents Tables
 *
 * Creates tables for security incident management, CSIRT coordination,
 * and NACSA/MyCERT reporting compliance.
 *
 * @see D03-FR-028 (CSIRT Integration)
 *
 * @trace Requirements 28.1, 28.2, 28.3, 28.4, 28.5
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Security Incidents table
        Schema::create('security_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_number', 20)->unique();

            // Incident classification (PKS 28.1, 28.5)
            $table->string('type', 50)->index();
            $table->string('severity', 20)->index();
            $table->string('status', 30)->default('detected')->index();

            // Incident details
            $table->string('title');
            $table->text('description');

            // User tracking (PKS 5.2.1 accountability)
            $table->foreignId('detected_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Source and target information
            $table->string('source_ip', 45)->nullable()->index();
            $table->string('target_system')->nullable();

            // Affected assets and detection details
            $table->json('affected_assets')->nullable();
            $table->json('detection_rules_triggered')->nullable();
            $table->json('indicators_of_compromise')->nullable();

            // Timeline tracking (PKS 28.4 - 15 minute escalation SLA)
            $table->timestamp('detected_at')->index();
            $table->timestamp('escalated_at')->nullable();
            $table->timestamp('csirt_notified_at')->nullable();
            $table->timestamp('nacsa_reported_at')->nullable();
            $table->timestamp('mycert_reported_at')->nullable();
            $table->timestamp('contained_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            // External reporting IDs (PKS 28.2)
            $table->string('nacsa_report_id', 100)->nullable();
            $table->string('mycert_report_id', 100)->nullable();

            // Resolution and lessons learned (PKS 28.3)
            $table->text('resolution_summary')->nullable();
            $table->text('lessons_learned')->nullable();

            // Detailed tracking
            $table->json('timeline')->nullable();
            $table->json('response_actions')->nullable();

            // Flags
            $table->boolean('requires_escalation')->default(false);
            $table->boolean('is_false_positive')->default(false);

            $table->timestamps();

            // Indexes for reporting and queries
            $table->index(['severity', 'status']);
            $table->index(['type', 'detected_at']);
            $table->index(['requires_escalation', 'csirt_notified_at']);
        });

        // Security Incident Logs table (7-year retention per PKS 28.3)
        Schema::create('security_incident_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('security_incident_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('action_type', 50)->index();
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();

            $table->timestamps();

            $table->index(['security_incident_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_incident_logs');
        Schema::dropIfExists('security_incidents');
    }
};

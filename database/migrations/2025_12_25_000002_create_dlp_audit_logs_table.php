<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DLP Audit Log Table Migration
 *
 * PKS 9.2.1 Compliance - Data Transfer and DLP Audit Logging
 *
 * Logs all DLP filter decisions for compliance monitoring and audit.
 *
 * @see D03-FR-025.4 (DLP audit logging)
 * @see D04 §6.4 (AI Architecture)
 *
 * @trace Requirements 25.4, 25.6
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dlp_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 64)->nullable()->index();

            // Classification details
            $table->enum('classification', ['PUBLIC', 'SENSITIVE', 'RESTRICTED'])->index();
            $table->enum('routing_decision', ['CLOUD_ALLOWED', 'LOCAL_ONLY', 'BLOCKED'])->index();
            $table->unsignedTinyInteger('risk_score')->default(0);

            // Detection details
            $table->json('detected_patterns')->nullable();
            $table->unsignedInteger('content_length')->default(0);
            $table->string('content_hash', 64)->nullable()->index();

            // Provider routing
            $table->enum('target_provider', ['bedrock', 'ollama', 'blocked'])->index();
            $table->string('model_id', 100)->nullable();

            // Request context
            $table->string('operation_type', 50)->nullable()->index();
            $table->string('source_component', 100)->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            // Bypass tracking (for superuser review)
            $table->boolean('was_bypassed')->default(false)->index();
            $table->foreignId('bypassed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('bypass_reason')->nullable();

            // Timestamps
            $table->timestamp('processed_at')->useCurrent();
            $table->timestamps();

            // Indexes for reporting
            $table->index(['user_id', 'processed_at']);
            $table->index(['classification', 'processed_at']);
            $table->index(['routing_decision', 'processed_at']);
            $table->index(['was_bypassed', 'processed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dlp_audit_logs');
    }
};

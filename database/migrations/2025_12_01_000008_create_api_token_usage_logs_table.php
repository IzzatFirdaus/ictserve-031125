<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create api_token_usage_logs table for API token audit trail
 *
 * Tracks all API token usage for security monitoring and compliance.
 * Stores hashed IP addresses for privacy compliance (PDPA 2010).
 *
 * @see D03 SRS-API-001 API token authentication
 * @see D09 §4.6 Audit requirements
 * @see Requirements 37.5
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('api_token_usage_logs', function (Blueprint $table) {
            $table->id();

            // Foreign key to personal_access_tokens
            $table->foreignId('personal_access_token_id')
                ->constrained('personal_access_tokens')
                ->cascadeOnDelete()
                ->comment('Reference to the API token used');

            // User reference (denormalized for query performance)
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('User who owns the token');

            // Action details
            $table->string('action', 100)
                ->comment('Action performed (e.g., tickets.index, loans.store)');
            $table->string('endpoint', 255)
                ->comment('API endpoint accessed');

            // Request metadata (privacy-compliant)
            $table->string('ip_hash', 128)->nullable()
                ->comment('SHA-512 hashed IP address for privacy');
            $table->string('user_agent', 500)->nullable()
                ->comment('User agent string (truncated)');

            // Response tracking
            $table->unsignedSmallInteger('response_status')
                ->comment('HTTP response status code');

            // Timestamp (no updated_at needed for logs)
            $table->timestamp('created_at')->useCurrent()
                ->comment('Log entry timestamp');

            // Indexes for performance and audit queries
            $table->index('personal_access_token_id', 'idx_api_log_token');
            $table->index('user_id', 'idx_api_log_user');
            $table->index('created_at', 'idx_api_log_created');
            $table->index('action', 'idx_api_log_action');
            $table->index('response_status', 'idx_api_log_status');
            $table->index(['user_id', 'created_at'], 'idx_api_log_user_created');
            $table->index(['personal_access_token_id', 'created_at'], 'idx_api_log_token_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_token_usage_logs');
    }
};

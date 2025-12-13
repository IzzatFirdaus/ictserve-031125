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
     * Creates the sso_audit_logs table for comprehensive SSO authentication logging.
     * Supports Requirements 4.1, 4.2 - Enhanced Security and Audit Logging
     */
    public function up(): void
    {
        Schema::create('sso_audit_logs', function (Blueprint $table) {
            $table->id();

            // User reference (nullable for failed attempts before user identification)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Associated user ID, null for failed attempts');

            // Authentication attempt details
            $table->string('email', 255)
                ->comment('Email address used in authentication attempt');
            $table->string('google_id', 255)
                ->nullable()
                ->comment('Google OAuth ID from the authentication attempt');

            // Request metadata
            $table->string('ip_address', 45)
                ->comment('IP address of the authentication request');
            $table->text('user_agent')
                ->nullable()
                ->comment('User agent string from the request');

            // Authentication outcome
            $table->boolean('success')
                ->default(false)
                ->comment('Whether the authentication attempt was successful');
            $table->string('error_type', 50)
                ->nullable()
                ->comment('Type of error: domain_error, oauth_error, network_error, etc.');
            $table->text('error_message')
                ->nullable()
                ->comment('Detailed error message for failed attempts');

            // Timing
            $table->timestamp('attempted_at')
                ->useCurrent()
                ->comment('Timestamp of the authentication attempt');

            $table->timestamps();

            // Indexes for performance (Requirements 4.2)
            $table->index('email', 'idx_sso_audit_email');
            $table->index('ip_address', 'idx_sso_audit_ip');
            $table->index('success', 'idx_sso_audit_success');
            $table->index('attempted_at', 'idx_sso_audit_attempted_at');
            $table->index('error_type', 'idx_sso_audit_error_type');

            // Composite indexes for common queries
            $table->index(['user_id', 'success'], 'idx_sso_audit_user_success');
            $table->index(['email', 'attempted_at'], 'idx_sso_audit_email_time');
            $table->index(['ip_address', 'attempted_at'], 'idx_sso_audit_ip_time');
            $table->index(['success', 'attempted_at'], 'idx_sso_audit_success_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sso_audit_logs');
    }
};

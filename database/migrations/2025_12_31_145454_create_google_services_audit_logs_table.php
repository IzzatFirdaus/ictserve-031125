<?php

declare(strict_types=1);

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
        Schema::create('google_services_audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email');
            $table->string('google_id')->nullable();
            $table->string('service_type'); // 'sso', 'gmail', 'calendar', etc.
            $table->string('operation_type'); // 'authenticate', 'send_email', 'authorize', 'refresh_token', etc.
            $table->string('authentication_method')->nullable(); // 'oauth', 'service_account', 'smtp_fallback'
            $table->string('verification_status')->nullable(); // 'verified', 'testing', 'pending', 'rejected'
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->boolean('success')->default(false);
            $table->string('error_type')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable(); // Additional context data
            $table->timestamp('attempted_at');
            $table->timestamps();

            // Indexes for common queries
            $table->index('email');
            $table->index('service_type');
            $table->index('operation_type');
            $table->index('authentication_method');
            $table->index('verification_status');
            $table->index('success');
            $table->index('attempted_at');
            $table->index(['service_type', 'success']);
            $table->index(['user_id', 'service_type']);
            $table->index(['email', 'service_type', 'attempted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_services_audit_logs');
    }
};

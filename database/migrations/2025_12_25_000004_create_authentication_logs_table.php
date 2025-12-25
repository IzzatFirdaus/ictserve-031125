<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PKS 5.4.3 Password Policy Compliance - Authentication Logging
 *
 * Creates table to log all authentication attempts for security monitoring.
 *
 * @see D03-FR-027 (Authentication Requirements)
 * @see PKS 5.4.3 (Password Policy Requirements)
 *
 * @trace Requirements 27.4, 27.5
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authentication_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('username', 255)->index();
            $table->string('auth_method', 50)->index(); // ldap, local, sso
            $table->string('status', 20)->index(); // success, failed, locked, expired
            $table->string('failure_reason', 100)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('location', 255)->nullable();
            $table->integer('failed_attempts')->default(0);
            $table->boolean('is_lockout_event')->default(false);
            $table->timestamp('lockout_until')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes for security monitoring
            $table->index(['created_at', 'status']);
            $table->index(['ip_address', 'created_at']);
            $table->index(['username', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authentication_logs');
    }
};

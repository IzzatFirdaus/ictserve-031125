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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            // Four-role RBAC system
            $table->enum('role', ['staff', 'approver', 'admin', 'superuser'])->default('staff');

            // Organizational structure
            $table->string('staff_id', 50)->unique()->nullable();
            $table->foreignId('division_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('grade_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('position_id')->nullable()->constrained()->onDelete('set null');

            // Contact information
            $table->string('phone', 20)->nullable();
            $table->string('mobile', 20)->nullable();

            // Profile
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();

            // Password management
            $table->timestamp('password_changed_at')->nullable();
            $table->boolean('require_password_change')->default(false);

            // Portal features
            $table->boolean('has_completed_tour')->default(false);

            // Notification preferences (JSON)
            $table->json('notification_preferences')
                ->nullable()
                ->comment('User notification preferences for email alerts');

            // Data governance
            $table->timestamp('anonymized_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('staff_id');
            $table->index('role');
            $table->index('is_active');
            $table->index(['division_id', 'grade_id']);
            $table->index(['role', 'is_active']);
            $table->index(['division_id', 'is_active']);
            $table->index('anonymized_at');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

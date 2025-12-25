<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create disaster_recovery_logs table
 *
 * PKS Business Continuity (Requirement 29) - DR Event Logging
 *
 * @trace D03-FR-029 (Business Continuity)
 * @trace Requirements 29.2, 29.3, 29.4
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('disaster_recovery_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique()->comment('Unique event identifier');
            $table->string('event_type')->comment('Type: health_check, failover_test, failover_initiated, etc.');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->comment('User who triggered the event');
            $table->text('reason')->nullable()->comment('Reason for failover/event');
            $table->string('status')->comment('Event status: healthy, degraded, failed, passed, etc.');
            $table->json('metadata')->nullable()->comment('Additional event data');
            $table->timestamps();

            // Indexes for common queries
            $table->index('event_type');
            $table->index('status');
            $table->index('created_at');
            $table->index(['event_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disaster_recovery_logs');
    }
};

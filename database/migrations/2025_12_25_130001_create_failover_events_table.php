<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create failover_events table
 *
 * PKS Business Continuity (Requirement 29) - Failover Event Tracking
 *
 * @trace D03-FR-029 (Business Continuity)
 * @trace Requirements 29.3, 29.4
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('failover_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique()->comment('Unique event identifier');
            $table->string('type')->comment('Type: automated, manual, test, failback');
            $table->string('status')->comment('Status: pending, in_progress, completed, failed, rolled_back, passed');
            $table->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete()->comment('User who triggered the event');
            $table->text('reason')->nullable()->comment('Reason for failover');
            $table->timestamp('started_at')->nullable()->comment('When failover started');
            $table->timestamp('completed_at')->nullable()->comment('When failover completed');
            $table->json('metadata')->nullable()->comment('Additional event data');
            $table->timestamps();

            // Indexes for common queries
            $table->index('type');
            $table->index('status');
            $table->index('started_at');
            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failover_events');
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create activity_log table for spatie/laravel-activitylog
 *
 * Part of the Dual Audit System - this table stores user activity logs
 * for operational dashboards and reports, complementing the field-level
 * audits table from owen-it/laravel-auditing.
 *
 * @see D09 §4.7 Activity logging requirements
 * @see spatie/laravel-activitylog documentation
 * @see Requirements 19.2, 19.4
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();

            // Log name for categorization
            $table->string('log_name')->nullable()
                ->comment('Category of activity (e.g., default, auth, helpdesk, loan)');

            // Activity description
            $table->text('description')
                ->comment('Human-readable description of the activity');

            // Subject - the model being acted upon
            $table->nullableMorphs('subject', 'subject');

            // Event type
            $table->string('event')->nullable()
                ->comment('Event type (created, updated, deleted, etc.)');

            // Causer - the user/model that caused the activity
            $table->nullableMorphs('causer', 'causer');

            // Additional properties
            $table->json('properties')->nullable()
                ->comment('Additional context data as JSON');

            // Batch UUID for grouping related activities
            $table->uuid('batch_uuid')->nullable()
                ->comment('UUID for grouping related activities');

            $table->timestamps();

            // Indexes for performance and audit queries
            $table->index('log_name', 'idx_activity_log_name');
            $table->index('event', 'idx_activity_event');
            $table->index('batch_uuid', 'idx_activity_batch');
            $table->index('created_at', 'idx_activity_created');
            $table->index(['log_name', 'created_at'], 'idx_activity_log_created');
            $table->index(['causer_type', 'causer_id', 'created_at'], 'idx_activity_causer_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};

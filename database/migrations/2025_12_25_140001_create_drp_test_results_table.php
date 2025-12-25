<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create drp_test_results table
 *
 * PKS Business Continuity (Requirement 29) - DRP Test Result Tracking
 *
 * @trace D03-FR-029 (Business Continuity)
 * @trace Requirements 29.5
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('drp_test_results', function (Blueprint $table) {
            $table->id();
            $table->string('test_id')->unique()->comment('Unique test identifier');
            $table->string('test_type')->comment('Type: tabletop, walkthrough, simulation, full');
            $table->timestamp('test_date')->comment('Date when test was conducted');
            $table->foreignId('conducted_by')->constrained('users')->comment('User who conducted the test');
            $table->string('status')->comment('Status: passed, failed, partial');
            $table->unsignedInteger('rto_achieved_minutes')->nullable()->comment('RTO achieved in minutes (target: 240)');
            $table->decimal('rpo_achieved_hours', 5, 2)->nullable()->comment('RPO achieved in hours (target: 24)');
            $table->json('participants')->nullable()->comment('List of test participants');
            $table->json('findings')->nullable()->comment('Test findings');
            $table->json('recommendations')->nullable()->comment('Recommendations from test');
            $table->json('metadata')->nullable()->comment('Additional test data');
            $table->timestamps();

            // Indexes for common queries
            $table->index('test_type');
            $table->index('status');
            $table->index('test_date');
            $table->index(['test_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drp_test_results');
    }
};

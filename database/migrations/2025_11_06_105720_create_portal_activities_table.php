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
        Schema::create('portal_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('activity_type'); // e.g., 'ticket_submitted', 'loan_approved'
            $table->string('subject_type')->nullable(); // Polymorphic relation to ticket/loan
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('metadata')->nullable(); // Additional activity data
            $table->timestamp('created_at'); // No updated_at needed for activity log

            $table->index(['user_id', 'created_at']);
            $table->index('activity_type');
            $table->index(['subject_type', 'subject_id']);
            $table->index('subject_type');
            $table->index('subject_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portal_activities');
    }
};

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
        Schema::create('approval_delegations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_approver_id');
            $table->unsignedBigInteger('delegated_approver_id');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->text('reason');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('original_approver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('delegated_approver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            // Indexes for performance
            $table->index(['original_approver_id', 'is_active', 'start_date', 'end_date'], 'deleg_orig_active_dates_idx');
            $table->index(['delegated_approver_id', 'is_active'], 'deleg_delegated_active_idx');
            $table->index(['start_date', 'end_date'], 'deleg_dates_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_delegations');
    }
};

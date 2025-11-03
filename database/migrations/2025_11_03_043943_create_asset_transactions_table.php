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
        Schema::create('asset_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_application_id')->nullable()->constrained()->onDelete('set null');

            $table->enum('type', ['loan', 'return', 'maintenance', 'transfer', 'retirement'])->default('loan');

            // Transaction details
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');

            // Condition tracking
            $table->enum('condition_before', ['excellent', 'good', 'fair', 'poor', 'damaged'])->nullable();
            $table->enum('condition_after', ['excellent', 'good', 'fair', 'poor', 'damaged'])->nullable();

            // Accessories and notes
            $table->json('accessories')->nullable(); // List of accessories included
            $table->text('notes')->nullable();
            $table->text('damage_description')->nullable();

            // Location tracking
            $table->string('location_from')->nullable();
            $table->string('location_to')->nullable();

            $table->timestamp('transaction_date');
            $table->timestamps();

            // Indexes
            $table->index('asset_id');
            $table->index('loan_application_id');
            $table->index('type');
            $table->index('transaction_date');
            $table->index(['asset_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_transactions');
    }
};

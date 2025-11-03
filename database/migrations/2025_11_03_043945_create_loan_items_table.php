<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Loan Items Junction Table Migration
 *
 * Links loan applications to specific assets with condition tracking
 * and damage reporting capabilities.
 *
 * @see D03-FR-003.2 Asset issuance tracking
 * @see D03-FR-003.3 Asset return processing
 * @see D04 §2.1 Database schema design
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained('loan_applications')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->restrictOnDelete();
            $table->integer('quantity')->default(1)->comment('Number of units (usually 1)');
            $table->decimal('unit_value', 10, 2)->comment('Value per unit (RM)');
            $table->decimal('total_value', 10, 2)->comment('Total value (RM)');
            $table->enum('condition_before', [
                'excellent',
                'good',
                'fair',
                'poor',
                'damaged',
            ])->nullable()->comment('Condition at issuance');
            $table->enum('condition_after', [
                'excellent',
                'good',
                'fair',
                'poor',
                'damaged',
            ])->nullable()->comment('Condition at return');
            $table->json('accessories_issued')->nullable()->comment('Accessories provided with asset');
            $table->json('accessories_returned')->nullable()->comment('Accessories returned with asset');
            $table->text('damage_report')->nullable()->comment('Damage description if applicable');
            $table->timestamps();

            // Indexes
            $table->index('loan_application_id', 'idx_loan_item_app');
            $table->index('asset_id', 'idx_loan_item_asset');

            // Unique constraint to prevent duplicate asset assignments
            $table->unique(['loan_application_id', 'asset_id'], 'unique_loan_asset');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_items');
    }
};

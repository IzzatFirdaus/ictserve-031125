<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Loan Transactions Migration
 *
 * Complete audit trail for all asset loan transactions including
 * issuance, returns, extensions, and recalls.
 *
 * @see D03-FR-010.2 Comprehensive audit logging
 * @see D03-FR-018.3 Asset lifecycle tracking
 * @see D04 §2.1 Database schema design
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loan_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained('loan_applications')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->restrictOnDelete();
            $table->enum('transaction_type', [
                'issue',
                'return',
                'extend',
                'recall',
            ])->comment('Type of transaction');
            $table->foreignId('processed_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('processed_at')->comment('Transaction timestamp');

            // v3.5.0 Enhanced check-out/check-in workflow fields
            $table->timestamp('transaction_at')->nullable()
                ->comment('Explicit timestamp of the transaction event');
            $table->foreignId('admin_id')->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Admin user who performed the transaction (v3.5.0 alias)');
            $table->enum('condition_before', [
                'excellent',
                'good',
                'fair',
                'poor',
                'damaged',
            ])->nullable()->comment('Asset condition before transaction');
            $table->enum('condition_after', [
                'excellent',
                'good',
                'fair',
                'poor',
                'damaged',
            ])->nullable()->comment('Asset condition after transaction');
            $table->json('accessories')->nullable()->comment('Accessories involved in transaction');
            $table->text('damage_report')->nullable()->comment('Damage description if applicable');

            // v3.5.0 Damage reporting fields
            $table->boolean('damage_reported')->default(false)
                ->comment('Flag indicating if damage was reported during transaction');
            $table->json('damage_photos')->nullable()
                ->comment('JSON array of damage photo file paths');

            $table->text('notes')->nullable()->comment('Additional transaction notes');
            $table->timestamp('created_at')->useCurrent()->comment('Record creation timestamp');

            // Indexes for performance and audit queries
            $table->index('loan_application_id', 'idx_trans_loan_app');
            $table->index('asset_id', 'idx_trans_asset');
            $table->index('processed_by', 'idx_trans_processed_by');
            $table->index('processed_at', 'idx_trans_processed_at');
            $table->index('transaction_type', 'idx_trans_type');

            // v3.5.0 indexes
            $table->index('damage_reported', 'idx_trans_damage_reported');
            $table->index('transaction_at', 'idx_trans_transaction_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_transactions');
    }
};

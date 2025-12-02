<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Update loan_transactions table for v3.5.0 True Hybrid Architecture
 *
 * Adds fields for enhanced check-out/check-in workflow:
 * - damage_photos JSON for photo evidence
 * - transaction_at for explicit transaction timestamp
 * - damage_reported boolean flag
 *
 * @see D03 SRS-LOAN-007 Asset check-out/check-in
 * @see D03 SRS-LOAN-009 Damage reporting
 * @see Requirements 6.1, 6.2
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loan_transactions', function (Blueprint $table) {
            // Damage reporting fields (Req 6.2, 6.3)
            $table->boolean('damage_reported')->default(false)->after('damage_report')
                ->comment('Flag indicating if damage was reported during transaction');
            $table->json('damage_photos')->nullable()->after('damage_reported')
                ->comment('JSON array of damage photo file paths');

            // Explicit transaction timestamp (Req 6.1)
            $table->timestamp('transaction_at')->nullable()->after('processed_at')
                ->comment('Explicit timestamp of the transaction event');

            // Admin reference for v3.5.0 compatibility
            $table->foreignId('admin_id')->nullable()->after('processed_by')
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Admin user who performed the transaction (v3.5.0 alias)');

            // Indexes
            $table->index('damage_reported', 'idx_trans_damage_reported');
            $table->index('transaction_at', 'idx_trans_transaction_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_transactions', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_trans_damage_reported');
            $table->dropIndex('idx_trans_transaction_at');

            // Drop foreign key constraint
            $table->dropForeign(['admin_id']);

            // Drop columns
            $table->dropColumn([
                'damage_reported',
                'damage_photos',
                'transaction_at',
                'admin_id',
            ]);
        });
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create loan_transaction_accessories table for accessory tracking
 *
 * Tracks accessories during check-out and check-in transactions.
 * Supports standard accessories (Power Adapter, Bag, Mouse, etc.) and custom items.
 *
 * @see D03 SRS-LOAN-007 Asset check-out/check-in with accessories
 * @see PK.(S).MOTAC.07.(L3) Loan Application Form - Accessory checklist
 * @see Requirements 26.6
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loan_transaction_accessories', function (Blueprint $table) {
            $table->id();

            // Foreign key to loan transaction
            $table->foreignId('loan_transaction_id')
                ->constrained('loan_transactions')
                ->cascadeOnDelete()
                ->comment('Reference to the loan transaction');

            // Accessory type enum
            $table->enum('accessory_type', [
                'POWER_ADAPTER',
                'BAG',
                'MOUSE',
                'USB_CABLE',
                'HDMI_VGA_CABLE',
                'REMOTE',
                'OTHERS',
            ])->comment('Type of accessory from standard list');

            // Custom accessory name (for OTHERS type)
            $table->string('accessory_name', 100)->nullable()
                ->comment('Custom accessory name when type is OTHERS');

            // Presence tracking
            $table->boolean('present_at_checkout')->default(false)
                ->comment('Whether accessory was present during check-out');
            $table->boolean('present_at_checkin')->nullable()
                ->comment('Whether accessory was present during check-in (null if not yet checked in)');

            // Condition notes
            $table->text('condition_notes')->nullable()
                ->comment('Notes about accessory condition');

            $table->timestamps();

            // Indexes for performance
            $table->index('loan_transaction_id', 'idx_acc_transaction');
            $table->index('accessory_type', 'idx_acc_type');
            $table->index(['loan_transaction_id', 'accessory_type'], 'idx_acc_trans_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_transaction_accessories');
    }
};

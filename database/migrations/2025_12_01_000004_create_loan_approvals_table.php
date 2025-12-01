<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create loan_approvals table for email-based approval workflow
 *
 * Records approval decisions made via email links by Grade 41+ officers.
 * Supports the True Hybrid Architecture where approvers don't need system login.
 *
 * @see D03 SRS-LOAN-004 Email-based approval workflow
 * @see D03 SRS-LOAN-006 Approval decision recording
 * @see D09 §4.6 Audit requirements
 * @see Requirements 4.3
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loan_approvals', function (Blueprint $table) {
            $table->id();

            // Foreign key to loan application
            $table->foreignId('loan_application_id')
                ->constrained('loan_applications')
                ->cascadeOnDelete()
                ->comment('Reference to the loan application being approved');

            // Approver information (Grade 41+ officer)
            $table->string('approver_email')
                ->comment('Email of the approving officer');
            $table->string('approver_grade', 50)->nullable()
                ->comment('Grade of the approving officer (e.g., 41, 44, 48)');

            // Decision details
            $table->enum('decision', ['approved', 'rejected'])
                ->comment('Approval decision: approved or rejected');
            $table->text('remarks')->nullable()
                ->comment('Optional remarks provided with the decision');

            // Audit trail
            $table->timestamp('decision_at')
                ->comment('Timestamp when decision was made');
            $table->string('decision_ip_hash', 128)->nullable()
                ->comment('SHA-512 hashed IP address for privacy-compliant audit');

            // Token verification
            $table->string('token_hash', 128)
                ->comment('SHA-512 hash of the approval token used');

            // Metadata for additional context
            $table->json('metadata')->nullable()
                ->comment('Additional metadata (user agent, etc.)');

            $table->timestamps();

            // Indexes for performance
            $table->index('loan_application_id', 'idx_approval_loan_app');
            $table->index('approver_email', 'idx_approval_email');
            $table->index('decision', 'idx_approval_decision');
            $table->index('decision_at', 'idx_approval_decision_at');
            $table->index('token_hash', 'idx_approval_token_hash');
            $table->index(['loan_application_id', 'decision'], 'idx_approval_app_decision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_approvals');
    }
};

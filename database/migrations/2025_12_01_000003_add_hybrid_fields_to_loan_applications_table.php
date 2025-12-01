<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add True Hybrid Architecture fields to loan_applications table
 *
 * Implements v3.5.0 fields for:
 * - Status token for guest status checking (Req 3.5)
 * - Approval token hash for secure email approval (Req 4.1)
 * - Form reference code for official document tracking (Req 24.3)
 * - Responsible officer acknowledgement (Req 25.4)
 *
 * Note: user_id nullable FK and many responsible officer fields already exist
 *
 * @see D03 SRS-LOAN-003 Application creation with status token
 * @see D03 SRS-LOAN-004 Email-based approval workflow
 * @see PK.(S).MOTAC.07.(L3) Loan Application Form Reference
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            // Status token for guest status checking (Req 3.5)
            $table->string('status_token_hash', 128)->nullable()->after('tracking_token')
                ->comment('SHA-512 hash of status token for guest status checking');

            // Approval token hash (Req 4.1) - separate from existing approval_token
            $table->string('approval_token_hash', 128)->nullable()->after('approval_token')
                ->comment('SHA-512 hash of approval token for secure validation');

            // Form reference code (Req 24.3)
            $table->string('form_reference_code', 50)->default('PK.(S).MOTAC.07.(L3)')->after('application_number')
                ->comment('Official MOTAC form reference code');

            // Responsible officer acknowledgement (Req 25.4)
            $table->boolean('responsible_officer_acknowledgement')->default(false)->after('responsible_officer_email')
                ->comment('Responsible officer has acknowledged responsibility');

            // Indexes for performance
            $table->index('status_token_hash', 'idx_loan_status_token');
            $table->index('approval_token_hash', 'idx_loan_approval_token_hash');
            $table->index('form_reference_code', 'idx_loan_form_ref');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_loan_status_token');
            $table->dropIndex('idx_loan_approval_token_hash');
            $table->dropIndex('idx_loan_form_ref');

            // Drop columns
            $table->dropColumn([
                'status_token_hash',
                'approval_token_hash',
                'form_reference_code',
                'responsible_officer_acknowledgement',
            ]);
        });
    }
};

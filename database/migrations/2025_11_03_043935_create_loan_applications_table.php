<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Enhanced Loan Applications Migration with ICTServe Integration
 *
 * Implements hybrid architecture supporting both guest and authenticated applications
 * with email-based approval workflows and cross-module helpdesk integration.
 *
 * @see D03-FR-001.2 Hybrid architecture support
 * @see D03-FR-002.1 Email approval workflow
 * @see D03-FR-016.1 Cross-module integration
 * @see D04 §2.1 Database schema design
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number', 20)->unique()->comment('Format: LA[YYYY][MM][0001-9999]');

            // v3.5.0 True Hybrid Architecture fields
            $table->string('form_reference_code', 50)->default('PK.(S).MOTAC.07.(L3)')
                ->comment('Official MOTAC form reference code');

            $table->string('tracking_token', 64)->nullable()->unique();
            $table->string('status_token_hash', 128)->nullable()
                ->comment('SHA-512 hash of status token for guest status checking');
            $table->timestamp('tracking_token_expires_at')->nullable();

            // Hybrid architecture: user_id nullable for guest applications
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Guest applicant information (always populated for both guest and authenticated)
            $table->string('applicant_name')->comment('Full name of applicant');
            $table->string('applicant_position');
            $table->string('applicant_grade');
            $table->string('applicant_email')->comment('Email for notifications');
            $table->string('applicant_phone', 20)->comment('Contact phone number');
            $table->string('staff_id', 20)->comment('MOTAC staff ID');
            $table->string('grade', 10)->comment('Staff grade (41, 44, 48, 52, 54)');
            $table->foreignId('division_id')->constrained('divisions')->restrictOnDelete();
            $table->boolean('is_applicant_responsible')->default(true);
            $table->boolean('is_delegate')->default(false)->comment('True if application submitted on behalf of another staff member');
            $table->json('responsible_officer_details')->nullable()->comment('JSON: {name, position, grade, email, phone, staff_id, division_id} for delegation workflow');

            // Application details
            $table->text('purpose')->comment('Purpose of loan request');
            $table->string('location')->comment('Location where assets will be used');
            $table->string('return_location')->comment('Location for asset return');
            $table->date('loan_start_date')->comment('Requested loan start date');
            $table->date('loan_end_date')->comment('Requested loan end date');
            $table->date('expected_return_date');
            $table->enum('status', [
                'draft',
                'submitted',
                'under_review',
                'pending_info',
                'approved',
                'rejected',
                'ready_issuance',
                'issued',
                'in_use',
                'return_due',
                'returning',
                'returned',
                'completed',
                'overdue',
                'maintenance_required',
            ])->default('draft')->comment('Current application status');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->decimal('total_value', 10, 2)->default(0.00)->comment('Total value of loaned assets');

            // Email approval workflow fields
            $table->string('approver_email')->nullable()->comment('Email of Grade 41+ approver');
            $table->string('approved_by_name')->nullable()->comment('Name of approver');
            $table->timestamp('approved_at')->nullable()->comment('Approval timestamp');
            $table->string('approval_token')->nullable()->unique()->comment('Secure token for email approval');
            $table->string('approval_token_hash', 128)->nullable()
                ->comment('SHA-512 hash of approval token for secure validation');
            $table->timestamp('approval_token_expires_at')->nullable()->comment('Token expiration (7 days)');
            $table->string('approval_method', 20)->nullable()->comment('Approval decision source: email or portal');
            $table->text('approval_remarks')->nullable()->comment('Remarks provided during the approval decision');
            $table->text('rejected_reason')->nullable()->comment('Reason for rejection');
            $table->text('special_instructions')->nullable()->comment('Special handling instructions');
            $table->string('pickup_otp_hash')->nullable();
            $table->timestamp('pickup_otp_expires_at')->nullable();
            $table->integer('pickup_otp_attempts')->default(0);
            $table->timestamp('pickup_otp_generated_at')->nullable();
            $table->timestamp('pickup_otp_validated_at')->nullable();
            $table->foreignId('pickup_otp_validated_by')->nullable()->constrained('users')->nullOnDelete();

            // Responsible officer fields
            $table->string('responsible_officer_name')->nullable();
            $table->string('responsible_officer_position')->nullable();
            $table->string('responsible_officer_grade')->nullable();
            $table->string('responsible_officer_phone')->nullable();
            $table->string('responsible_officer_email')->nullable();
            $table->boolean('responsible_officer_acknowledgement')->default(false)
                ->comment('Responsible officer has acknowledged responsibility');
            $table->timestamp('responsible_officer_acknowledged_at')->nullable();
            $table->string('sponsorship_token')->nullable()->unique();
            $table->timestamp('sponsorship_token_expires_at')->nullable();

            // Applicant declaration
            $table->timestamp('applicant_declaration_date')->nullable();
            $table->string('applicant_digital_signature')->nullable();
            $table->boolean('terms_acknowledged')->default(false);
            $table->timestamp('declared_at')->nullable();

            // Approver fields (Grade 41+)
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approval_date')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->string('approver_digital_signature')->nullable();
            $table->text('approval_notes')->nullable();

            // Cross-module integration with helpdesk
            $table->json('related_helpdesk_tickets')->nullable()->comment('Array of related ticket IDs');
            $table->boolean('maintenance_required')->default(false)->comment('Flag for maintenance needs');
            $table->json('accessories')->nullable();

            // Data governance
            $table->timestamp('anonymized_at')->nullable();
            $table->timestamp('claimed_at')->nullable();

            // Audit fields
            $table->timestamps();
            $table->softDeletes();

            // Performance indexes
            $table->index('application_number', 'idx_loan_app_number');
            $table->index('tracking_token');
            $table->index('is_delegate', 'idx_loan_is_delegate');
            $table->index('responsible_officer_email');
            $table->index('sponsorship_token');
            $table->index('user_id', 'idx_loan_user_id');
            $table->index('applicant_email', 'idx_loan_applicant_email');
            $table->index('staff_id', 'idx_loan_staff_id');
            $table->index('status', 'idx_loan_status');
            $table->index(['loan_start_date', 'loan_end_date'], 'idx_loan_dates');
            $table->index('approval_token', 'idx_loan_approval_token');
            $table->index('created_at', 'idx_loan_created_at');
            $table->index('division_id', 'idx_loan_division_id');
            $table->index('loan_end_date', 'idx_loan_end_date');
            $table->index('anonymized_at', 'idx_loan_anonymized_at');
            $table->index('claimed_at', 'idx_loan_claimed_at');
            $table->index(['status', 'created_at'], 'idx_loan_status_created');
            $table->index(['user_id', 'status'], 'idx_loan_user_status');
            $table->index(['status', 'loan_end_date'], 'idx_status_end_date');

            // v3.5.0 True Hybrid Architecture indexes
            $table->index('status_token_hash', 'idx_loan_status_token');
            $table->index('approval_token_hash', 'idx_loan_approval_token_hash');
            $table->index('form_reference_code', 'idx_loan_form_ref');

            // Performance indexes (consolidated from 2025_01_21_000001)
            $table->index(['approver_id', 'status'], 'idx_loans_approver');
        });

        // Add FULLTEXT index for cross-module search (MySQL only)
        if (config('database.default') === 'mysql') {
            DB::statement('
                ALTER TABLE loan_applications
                ADD FULLTEXT INDEX idx_loan_fulltext_search (
                    application_number, purpose, applicant_name, applicant_email
                )
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_applications');
    }
};

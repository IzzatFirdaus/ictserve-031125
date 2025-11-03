<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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

            // Hybrid architecture: user_id nullable for guest applications
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Guest applicant information (always populated for both guest and authenticated)
            $table->string('applicant_name')->comment('Full name of applicant');
            $table->string('applicant_email')->comment('Email for notifications');
            $table->string('applicant_phone', 20)->comment('Contact phone number');
            $table->string('staff_id', 20)->comment('MOTAC staff ID');
            $table->string('grade', 10)->comment('Staff grade (41, 44, 48, 52, 54)');
            $table->foreignId('division_id')->constrained('divisions')->restrictOnDelete();

            // Application details
            $table->text('purpose')->comment('Purpose of loan request');
            $table->string('location')->comment('Location where assets will be used');
            $table->string('return_location')->comment('Location for asset return');
            $table->date('loan_start_date')->comment('Requested loan start date');
            $table->date('loan_end_date')->comment('Requested loan end date');
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
            $table->timestamp('approval_token_expires_at')->nullable()->comment('Token expiration (7 days)');
            $table->text('rejected_reason')->nullable()->comment('Reason for rejection');
            $table->text('special_instructions')->nullable()->comment('Special handling instructions');

            // Cross-module integration with helpdesk
            $table->json('related_helpdesk_tickets')->nullable()->comment('Array of related ticket IDs');
            $table->boolean('maintenance_required')->default(false)->comment('Flag for maintenance needs');

            // Audit fields
            $table->timestamps();
            $table->softDeletes();

            // Performance indexes
            $table->index('application_number', 'idx_loan_app_number');
            $table->index('user_id', 'idx_loan_user_id');
            $table->index('applicant_email', 'idx_loan_applicant_email');
            $table->index('staff_id', 'idx_loan_staff_id');
            $table->index('status', 'idx_loan_status');
            $table->index(['loan_start_date', 'loan_end_date'], 'idx_loan_dates');
            $table->index('approval_token', 'idx_loan_approval_token');
            $table->index('created_at', 'idx_loan_created_at');
            $table->index('division_id', 'idx_loan_division_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_applications');
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add delegation columns to loan_applications table
 *
 * Enables "On Behalf" submission workflow where a user can submit
 * a loan application on behalf of another staff member (delegation).
 *
 * @see D03-FR-003.7 Delegation workflow
 * @see Task 1.1.6 Database schema update for delegation
 * @see Task 3.2.7 "On Behalf" Toggle implementation
 *
 * trace: SRS-FR-003.7; D04 §2.1; D11 §5
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            // Delegation flag: indicates if application is submitted on behalf of another user
            $table->boolean('is_delegate')
                ->default(false)
                ->after('is_applicant_responsible')
                ->comment('True if application submitted on behalf of another staff member');

            // JSON field for comprehensive responsible officer details when delegating
            $table->json('responsible_officer_details')
                ->nullable()
                ->after('is_delegate')
                ->comment('JSON: {name, position, grade, email, phone, staff_id, division_id} for delegation workflow');

            // Index for delegation queries
            $table->index('is_delegate', 'idx_loan_is_delegate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropIndex('idx_loan_is_delegate');
            $table->dropColumn(['is_delegate', 'responsible_officer_details']);
        });
    }
};

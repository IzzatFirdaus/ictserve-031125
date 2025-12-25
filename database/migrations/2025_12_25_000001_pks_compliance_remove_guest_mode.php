<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PKS 5.2.1 Compliance Migration - Remove Guest Mode
     *
     * This migration removes all guest-related fields and enforces mandatory user_id
     * for full accountability per PKS 5.2.1 requirements.
     */
    public function up(): void
    {
        // Step 1: Update helpdesk_tickets table
        // First, drop existing foreign key if exists (it may have SET NULL which conflicts with NOT NULL)
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            // Drop existing foreign key constraint
            try {
                $table->dropForeign(['user_id']);
            } catch (\Exception $e) {
                // Foreign key may not exist, continue
            }
        });

        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            // Remove guest fields if they exist
            $columnsToDrop = [];
            foreach (['guest_name', 'guest_email', 'guest_phone', 'guest_staff_id', 'guest_grade', 'guest_division', 'status_token_hash'] as $col) {
                if (Schema::hasColumn('helpdesk_tickets', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            // Make user_id NOT NULL (PKS 5.2.1 requirement)
            $table->unsignedBigInteger('user_id')->nullable(false)->change();

            // Add foreign key constraint with CASCADE
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Step 2: Update loan_applications table
        // First, drop existing foreign key if exists
        Schema::table('loan_applications', function (Blueprint $table) {
            try {
                $table->dropForeign(['user_id']);
            } catch (\Exception $e) {
                // Foreign key may not exist, continue
            }
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            // Remove guest fields if they exist
            $columnsToDrop = [];
            foreach (['applicant_name', 'applicant_email', 'applicant_phone', 'status_token_hash', 'approval_token_hash'] as $col) {
                if (Schema::hasColumn('loan_applications', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            // Make user_id NOT NULL (PKS 5.2.1 requirement)
            $table->unsignedBigInteger('user_id')->nullable(false)->change();

            // Add HRMIS verification timestamp if not exists
            if (! Schema::hasColumn('loan_applications', 'hrmis_verified_at')) {
                $table->timestamp('hrmis_verified_at')->nullable()->after('approved_at');
            }

            // Add foreign key constraint with CASCADE
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Step 3: Update users table for PKS compliance
        Schema::table('users', function (Blueprint $table) {
            // Add HRMIS synchronization fields if not exists
            if (! Schema::hasColumn('users', 'hrmis_synced_at')) {
                $table->timestamp('hrmis_synced_at')->nullable()->after('email_verified_at');
            }
            if (! Schema::hasColumn('users', 'ldap_guid')) {
                $table->string('ldap_guid')->nullable()->after('hrmis_synced_at');
            }

            // Add PKS security training fields if not exists
            if (! Schema::hasColumn('users', 'security_training_completed_at')) {
                $table->timestamp('security_training_completed_at')->nullable()->after('is_active');
            }
            if (! Schema::hasColumn('users', 'training_expiry_date')) {
                $table->date('training_expiry_date')->nullable()->after('security_training_completed_at');
            }

            // Add PKS third-party access fields if not exists
            if (! Schema::hasColumn('users', 'is_third_party')) {
                $table->boolean('is_third_party')->default(false)->after('training_expiry_date');
            }
            if (! Schema::hasColumn('users', 'contract_end_date')) {
                $table->date('contract_end_date')->nullable()->after('is_third_party');
            }
            if (! Schema::hasColumn('users', 'nda_acknowledged_at')) {
                $table->timestamp('nda_acknowledged_at')->nullable()->after('contract_end_date');
            }
        });

        // Step 4: Create indexes for performance (if not exists)
        try {
            Schema::table('helpdesk_tickets', function (Blueprint $table) {
                $table->index('user_id', 'idx_helpdesk_tickets_user_id');
            });
        } catch (\Exception $e) {
            // Index may already exist
        }

        try {
            Schema::table('loan_applications', function (Blueprint $table) {
                $table->index('user_id', 'idx_loan_applications_user_id');
            });
        } catch (\Exception $e) {
            // Index may already exist
        }

        try {
            Schema::table('loan_applications', function (Blueprint $table) {
                $table->index('hrmis_verified_at', 'idx_loan_applications_hrmis_verified');
            });
        } catch (\Exception $e) {
            // Index may already exist
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index('ldap_guid', 'idx_users_ldap_guid');
            });
        } catch (\Exception $e) {
            // Index may already exist
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index('hrmis_synced_at', 'idx_users_hrmis_synced');
            });
        } catch (\Exception $e) {
            // Index may already exist
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index('is_third_party', 'idx_users_third_party');
            });
        } catch (\Exception $e) {
            // Index may already exist
        }
    }

    /**
     * Reverse the migrations.
     *
     * WARNING: This will restore guest mode functionality but may cause
     * data loss for PKS compliance fields. Use with caution.
     */
    public function down(): void
    {
        // Step 1: Restore helpdesk_tickets guest fields
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['user_id']);

            // Make user_id nullable again
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Restore guest fields
            $table->string('guest_name')->nullable()->after('user_id');
            $table->string('guest_email')->nullable()->after('guest_name');
            $table->string('guest_phone')->nullable()->after('guest_email');
            $table->string('guest_staff_id')->nullable()->after('guest_phone');
            $table->string('guest_grade')->nullable()->after('guest_staff_id');
            $table->string('guest_division')->nullable()->after('guest_grade');
            $table->string('status_token_hash')->nullable()->after('form_reference_code');

            // Drop indexes
            $table->dropIndex('idx_helpdesk_tickets_user_id');
        });

        // Step 2: Restore loan_applications guest fields
        Schema::table('loan_applications', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['user_id']);

            // Make user_id nullable again
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Restore guest fields
            $table->string('applicant_name')->after('user_id');
            $table->string('applicant_email')->after('applicant_name');
            $table->string('applicant_phone')->after('applicant_email');
            $table->string('status_token_hash')->nullable()->after('form_reference_code');
            $table->string('approval_token_hash')->nullable()->after('approval_token');

            // Remove HRMIS field
            $table->dropColumn('hrmis_verified_at');

            // Drop indexes
            $table->dropIndex('idx_loan_applications_user_id');
            $table->dropIndex('idx_loan_applications_hrmis_verified');
        });

        // Step 3: Remove PKS compliance fields from users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'hrmis_synced_at',
                'ldap_guid',
                'security_training_completed_at',
                'training_expiry_date',
                'is_third_party',
                'contract_end_date',
                'nda_acknowledged_at',
            ]);

            // Drop indexes
            $table->dropIndex('idx_users_ldap_guid');
            $table->dropIndex('idx_users_hrmis_synced');
            $table->dropIndex('idx_users_third_party');
        });
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            // Rename existing column if it exists, otherwise create new one
            if (Schema::hasColumn('loan_applications', 'is_responsible_officer')) {
                $table->renameColumn('is_responsible_officer', 'is_applicant_responsible');
            } else {
                $table->boolean('is_applicant_responsible')->default(true)->after('division_id');
            }

            // Responsible Officer details (some might already exist, check before adding)
            if (! Schema::hasColumn('loan_applications', 'responsible_officer_email')) {
                $table->string('responsible_officer_email')->nullable()->after('responsible_officer_phone');
            }

            // Sponsorship workflow
            $table->timestamp('responsible_officer_acknowledged_at')->nullable()->after('responsible_officer_email');
            $table->string('sponsorship_token')->nullable()->unique()->after('responsible_officer_acknowledged_at');
            $table->timestamp('sponsorship_token_expires_at')->nullable()->after('sponsorship_token');

            // OTP Handshake
            $table->string('pickup_otp_hash')->nullable()->after('special_instructions');
            $table->timestamp('pickup_otp_expires_at')->nullable()->after('pickup_otp_hash');
            $table->integer('pickup_otp_attempts')->default(0)->after('pickup_otp_expires_at');
            $table->timestamp('pickup_otp_generated_at')->nullable()->after('pickup_otp_attempts');
            $table->timestamp('pickup_otp_validated_at')->nullable()->after('pickup_otp_generated_at');
            $table->foreignId('pickup_otp_validated_by')->nullable()->constrained('users')->nullOnDelete()->after('pickup_otp_validated_at');

            // Declaration
            $table->timestamp('declared_at')->nullable()->after('terms_acknowledged');

            // Indexes
            $table->index('responsible_officer_email');
            $table->index('sponsorship_token');
            // pickup_otp_hash doesn't strictly need an index if we don't search by it (we verify against it), but if we look up by hash (unlikely), we'd need it.
            // Usually we look up by ID then verify hash.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columnsToDrop = array_filter([
            'responsible_officer_email',
            'responsible_officer_acknowledged_at',
            'sponsorship_token',
            'sponsorship_token_expires_at',
            'pickup_otp_hash',
            'pickup_otp_expires_at',
            'pickup_otp_attempts',
            'pickup_otp_generated_at',
            'pickup_otp_validated_at',
            'declared_at',
        ], static fn (string $column): bool => Schema::hasColumn('loan_applications', $column));

        Schema::table('loan_applications', function (Blueprint $table) use ($columnsToDrop): void {
            if (Schema::hasColumn('loan_applications', 'is_applicant_responsible')) {
                $table->renameColumn('is_applicant_responsible', 'is_responsible_officer');
            }

            if (Schema::hasColumn('loan_applications', 'responsible_officer_email')) {
                $table->dropIndex(['responsible_officer_email']);
            }

            if (Schema::hasColumn('loan_applications', 'sponsorship_token')) {
                $table->dropUnique(['sponsorship_token']);
                $table->dropIndex(['sponsorship_token']);
            }

            if (Schema::hasColumn('loan_applications', 'pickup_otp_validated_by')) {
                $table->dropConstrainedForeignId('pickup_otp_validated_by');
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};

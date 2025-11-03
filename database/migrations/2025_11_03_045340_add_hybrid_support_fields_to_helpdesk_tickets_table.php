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
     * Add hybrid support fields for guest submissions:
     * - guest_grade: Grade level for guest submissions
     * - guest_division: Division for guest submissions
     * - guest_staff_id: Staff ID for guest submissions
     * - damage_type: Type of damage/issue reported
     * - internal_notes: Internal notes for authenticated users
     *
     * Requirements: Requirement 1.2, Requirement 2.4, Requirement 10.2
     * Traceability: D03-FR-001.2, D04 §2.1
     */
    public function up(): void
    {
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            // Enhanced guest submission fields
            $table->string('guest_grade', 10)->nullable()->after('guest_phone');
            $table->string('guest_division', 100)->nullable()->after('guest_grade');
            $table->string('guest_staff_id', 50)->nullable()->after('guest_email');

            // Ticket details enhancements
            $table->string('damage_type')->nullable()->after('description');
            $table->text('internal_notes')->nullable()->after('admin_notes');

            // Performance indexes for hybrid architecture
            $table->index('guest_grade');
            $table->index('guest_division');
            $table->index('guest_staff_id');
            $table->index(['guest_email', 'status']);

            // Check constraint for guest vs authenticated submissions
            // Either user_id is set (authenticated) OR all guest fields are set (guest)
            // This is enforced at application level due to MySQL limitations with nullable columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            // Drop indexes first (Laravel automatically prefixes with table name)
            $table->dropIndex(['guest_grade']);
            $table->dropIndex(['guest_division']);
            $table->dropIndex(['guest_staff_id']);
            $table->dropIndex(['guest_email', 'status']);

            // Drop columns
            $table->dropColumn([
                'guest_grade',
                'guest_division',
                'guest_staff_id',
                'damage_type',
                'internal_notes',
            ]);
        });
    }
};

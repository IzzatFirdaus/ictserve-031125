<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add True Hybrid Architecture fields to helpdesk_tickets table
 *
 * Implements v3.5.0 fields for:
 * - Status token for guest status checking (Req 1.5, 2.1)
 * - Form reference code for official document tracking (Req 24.3)
 *
 * Note: user_id nullable FK already exists in base migration
 *
 * @see D03 SRS-HELP-003 Ticket creation with status token
 * @see D03 SRS-HELP-004 Token-based status checking
 * @see PK.(S).MOTAC.07.(L1) Helpdesk Form Reference
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            // Status token for guest status checking (Req 1.5, 2.1)
            $table->string('status_token_hash', 128)->nullable()->after('ticket_number')
                ->comment('SHA-512 hash of status token for guest status checking');

            // Form reference code (Req 24.3)
            $table->string('form_reference_code', 50)->default('PK.(S).MOTAC.07.(L1)')->after('status_token_hash')
                ->comment('Official MOTAC form reference code');

            // Indexes for performance
            $table->index('status_token_hash', 'idx_helpdesk_status_token');
            $table->index('form_reference_code', 'idx_helpdesk_form_ref');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_helpdesk_status_token');
            $table->dropIndex('idx_helpdesk_form_ref');

            // Drop columns
            $table->dropColumn([
                'status_token_hash',
                'form_reference_code',
            ]);
        });
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Add FULLTEXT indexes for cross-module search optimization.
 *
 * @see App\Services\CrossModuleSearchService
 * @see D03-FR-011.2 (Cross-module search functionality)
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! $this->supportFulltext()) {
            return;
        }

        DB::statement('
            ALTER TABLE helpdesk_tickets
            ADD FULLTEXT INDEX idx_helpdesk_fulltext_search (
                ticket_number, subject, description, guest_name, guest_email
            )
        ');

        DB::statement('
            ALTER TABLE loan_applications
            ADD FULLTEXT INDEX idx_loan_fulltext_search (
                application_number, purpose, applicant_name, applicant_email
            )
        ');
    }

    public function down(): void
    {
        if (! $this->supportFulltext()) {
            return;
        }

        DB::statement('ALTER TABLE helpdesk_tickets DROP INDEX IF EXISTS idx_helpdesk_fulltext_search');
        DB::statement('ALTER TABLE loan_applications DROP INDEX IF EXISTS idx_loan_fulltext_search');
    }

    private function supportFulltext(): bool
    {
        return \in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true);
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;

/**
 * Add SERVICE_REQUEST and GENERAL categories to ticket_categories table
 *
 * Task 3.3.8: Implement Service Request Routing
 * Task 3.3.6: Implement Contact Form Integration
 *
 * These categories enable:
 * - SERVICE_REQUEST: Pre-filled category for "Permintaan Perkhidmatan" card routing
 * - GENERAL: Category for Contact form submissions ("General Enquiry")
 *
 * @trace D03-FR-021, R21 (Contact Form and Service Request Integration)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Categories are now seeded via TicketCategorySeeder
        // This migration is kept for historical reference
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed
    }
};

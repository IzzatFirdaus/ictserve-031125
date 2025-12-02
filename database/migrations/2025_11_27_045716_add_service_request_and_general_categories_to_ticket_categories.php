<?php

declare(strict_types=1);

use App\Models\TicketCategory;
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
        // Add SERVICE_REQUEST category for service request routing
        TicketCategory::firstOrCreate(
            ['code' => 'SERVICE_REQUEST'],
            [
                'name' => 'Service Request',
                'description' => 'Request new ICT services, software installations, account creations, and system access.',
                'sla_response_hours' => 24,
                'sla_resolution_hours' => 72,
                'is_active' => true,
            ]
        );

        // Add GENERAL category for contact form submissions
        TicketCategory::firstOrCreate(
            ['code' => 'GENERAL'],
            [
                'name' => 'General Enquiry',
                'description' => 'General enquiries and contact form submissions.',
                'sla_response_hours' => 24,
                'sla_resolution_hours' => 48,
                'is_active' => true,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        TicketCategory::where('code', 'SERVICE_REQUEST')->delete();
        TicketCategory::where('code', 'GENERAL')->delete();
    }
};

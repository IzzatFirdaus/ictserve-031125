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
     * Create cross_module_integrations table for linking helpdesk tickets
     * with asset loan applications and tracking integration events.
     *
     * Integration Types:
     * - asset_damage_report: Ticket created from damaged asset return
     * - maintenance_request: Maintenance ticket for asset
     * - asset_ticket_link: Manual linking of ticket to asset
     *
     * Trigger Events:
     * - asset_returned_damaged: Asset returned with damage
     * - ticket_asset_selected: User selected asset in ticket
     * - maintenance_scheduled: Maintenance scheduled for asset
     *
     * Requirements: Requirement 2.2, Requirement 2.3, Requirement 2.5
     * Traceability: D03-FR-002.2, D03-FR-002.3, D04 §2.2
     */
    public function up(): void
    {
        Schema::create('cross_module_integrations', function (Blueprint $table) {
            $table->id();

            // Foreign keys to related modules
            $table->foreignId('helpdesk_ticket_id')
                ->nullable()
                ->constrained('helpdesk_tickets')
                ->onDelete('cascade');

            $table->foreignId('loan_application_id')
                ->nullable()
                ->constrained('loan_applications')
                ->onDelete('cascade');

            // Integration type enum
            $table->enum('integration_type', [
                'asset_damage_report',
                'maintenance_request',
                'asset_ticket_link',
            ])->index();

            // Trigger event enum
            $table->enum('trigger_event', [
                'asset_returned_damaged',
                'ticket_asset_selected',
                'maintenance_scheduled',
            ])->index();

            // Integration metadata (JSON)
            $table->json('integration_data')->nullable();

            // Processing status
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('processed_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Performance indexes
            $table->index('helpdesk_ticket_id', 'cmi_ticket_id_idx');
            $table->index('loan_application_id', 'cmi_loan_id_idx');
            $table->index(['integration_type', 'trigger_event'], 'cmi_type_event_idx');
            $table->index(['helpdesk_ticket_id', 'loan_application_id'], 'cmi_ticket_loan_idx');
            $table->index('processed_at', 'cmi_processed_at_idx');

            // Ensure at least one module reference exists
            // This is enforced at application level due to MySQL limitations
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cross_module_integrations');
    }
};

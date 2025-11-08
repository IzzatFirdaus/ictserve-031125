<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\CrossModuleIntegration;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Cross Module Integration Seeder
 *
 * Seeds cross-module integration records linking helpdesk tickets
 * with asset loan applications for testing integration features.
 *
 * Requirements: Requirement 3.1
 * Traceability: D03-FR-003.1, D04 §3.1
 */
class CrossModuleIntegrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get required data
        $asset = Asset::where('status', 'available')->first();
        $user = User::where('role', 'staff')->first();
        $adminUser = User::where('role', 'admin')->first();

        if (! $asset || ! $user || ! $adminUser) {
            $this->command->warn('⚠ Skipping cross-module integration seeding - required data not found');

            return;
        }

        // Create a loan application for the asset
        $loanApplication = LoanApplication::firstOrCreate(
            ['application_number' => LoanApplication::generateApplicationNumber()],
            [
                'user_id' => $user->id,
                'applicant_name' => $user->name,
                'applicant_email' => $user->email,
                'applicant_phone' => $user->mobile ?? '012-3456789',
                'staff_id' => $user->staff_id,
                'grade' => '44',
                'division_id' => $user->division_id,
                'purpose' => 'Testing cross-module integration between helpdesk and asset loan',
                'location' => 'Putrajaya HQ',
                'return_location' => 'Putrajaya HQ',
                'loan_start_date' => now()->subDays(10),
                'loan_end_date' => now()->addDays(5),
                'status' => 'in_use',
                'priority' => 'normal',
                'total_value' => $asset->current_value,
                'approver_email' => $adminUser->email,
                'approved_by_name' => $adminUser->name,
                'approved_at' => now()->subDays(9),
            ]
        );

        // Create a helpdesk ticket linked to the asset
        $helpdeskTicket = HelpdeskTicket::where('asset_id', $asset->id)->first();

        if (! $helpdeskTicket) {
            // Create a new ticket if none exists
            $category = DB::table('ticket_categories')->where('code', 'MAINTENANCE')->first();

            $helpdeskTicket = HelpdeskTicket::firstOrCreate(
                ['ticket_number' => HelpdeskTicket::generateTicketNumber()],
                [
                    'user_id' => $user->id,
                    'staff_id' => $user->staff_id,
                    'division_id' => $user->division_id,
                    'category_id' => $category?->id,
                    'priority' => 'normal',
                    'subject' => 'Asset damage report - '.$asset->name,
                    'description' => 'Asset returned with minor damage. Screen has scratches and keyboard keys are sticky.',
                    'status' => 'open',
                    'asset_id' => $asset->id,
                    'admin_notes' => 'Auto-created from asset return with damage condition.',
                ]
            );

            $helpdeskTicket->calculateSLADueDates();
            $helpdeskTicket->save();
        }

        // Create cross-module integration records
        $integrations = [
            [
                'helpdesk_ticket_id' => $helpdeskTicket->id,
                'loan_application_id' => $loanApplication->id,
                'integration_type' => 'asset_ticket_link',
                'trigger_event' => 'ticket_asset_selected',
                'integration_data' => json_encode([
                    'asset_id' => $asset->id,
                    'asset_tag' => $asset->asset_tag,
                    'asset_name' => $asset->name,
                    'linked_by' => 'system',
                    'linked_at' => now()->toIso8601String(),
                    'reason' => 'Ticket created for asset under active loan',
                ]),
                'processed_at' => now(),
                'processed_by' => $adminUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'helpdesk_ticket_id' => $helpdeskTicket->id,
                'loan_application_id' => $loanApplication->id,
                'integration_type' => 'asset_damage_report',
                'trigger_event' => 'asset_returned_damaged',
                'integration_data' => json_encode([
                    'asset_id' => $asset->id,
                    'asset_tag' => $asset->asset_tag,
                    'damage_description' => 'Screen scratches and sticky keyboard keys',
                    'return_condition' => 'damaged',
                    'reported_by' => $user->name,
                    'reported_at' => now()->toIso8601String(),
                    'maintenance_required' => true,
                ]),
                'processed_at' => now(),
                'processed_by' => $adminUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'helpdesk_ticket_id' => $helpdeskTicket->id,
                'loan_application_id' => null,
                'integration_type' => 'maintenance_request',
                'trigger_event' => 'maintenance_scheduled',
                'integration_data' => json_encode([
                    'asset_id' => $asset->id,
                    'asset_number' => $asset->asset_number,
                    'maintenance_type' => 'repair',
                    'scheduled_date' => now()->addDays(2)->toIso8601String(),
                    'estimated_duration' => '2 hours',
                    'technician' => $adminUser->name,
                ]),
                'processed_at' => null,
                'processed_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($integrations as $integration) {
            CrossModuleIntegration::firstOrCreate(
                [
                    'helpdesk_ticket_id' => $integration['helpdesk_ticket_id'],
                    'loan_application_id' => $integration['loan_application_id'],
                    'integration_type' => $integration['integration_type'],
                ],
                $integration
            );
        }

        $this->command->info('✓ Created cross-module integration records');
        $this->command->info('  - Asset-ticket linkage');
        $this->command->info('  - Asset damage report integration');
        $this->command->info('  - Maintenance request integration');
    }
}

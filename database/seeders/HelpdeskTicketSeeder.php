<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * PKS 5.2.1 Compliant Helpdesk Ticket Seeder
 *
 * All tickets require authenticated user_id (NOT NULL).
 * Guest submission functionality has been removed per PKS Accountability requirements.
 */
class HelpdeskTicketSeeder extends Seeder
{
    public function run(): void
    {
        $division = Division::where('code', 'ICT')->first();
        $users = User::whereIn('role', ['staff', 'admin'])->get();
        $adminUser = User::where('role', 'admin')->first();

        $hardwareCategory = TicketCategory::firstOrCreate(
            ['code' => 'HARDWARE'],
            [
                'name_ms' => 'Perkakasan',
                'name_en' => 'Hardware',
                'description_ms' => 'Masalah berkaitan perkakasan komputer',
                'description_en' => 'Computer hardware related issues',
                'sla_response_hours' => 4,
                'sla_resolution_hours' => 24,
                'is_active' => true,
            ]
        );

        $softwareCategory = TicketCategory::firstOrCreate(
            ['code' => 'SOFTWARE'],
            [
                'name_ms' => 'Perisian',
                'name_en' => 'Software',
                'description_ms' => 'Masalah berkaitan perisian',
                'description_en' => 'Software related issues',
                'sla_response_hours' => 2,
                'sla_resolution_hours' => 8,
                'is_active' => true,
            ]
        );

        $networkCategory = TicketCategory::firstOrCreate(
            ['code' => 'NETWORK'],
            [
                'name_ms' => 'Rangkaian',
                'name_en' => 'Network',
                'description_ms' => 'Masalah berkaitan rangkaian',
                'description_en' => 'Network related issues',
                'sla_response_hours' => 1,
                'sla_resolution_hours' => 4,
                'is_active' => true,
            ]
        );

        $maintenanceCategory = TicketCategory::firstOrCreate(
            ['code' => 'MAINTENANCE'],
            [
                'name_ms' => 'Penyelenggaraan',
                'name_en' => 'Maintenance',
                'description_ms' => 'Penyelenggaraan aset ICT',
                'description_en' => 'ICT asset maintenance',
                'sla_response_hours' => 8,
                'sla_resolution_hours' => 48,
                'is_active' => true,
            ]
        );

        $asset = Asset::where('status', 'available')->first();

        if ($users->isEmpty() || $adminUser === null) {
            $this->command->warn('⚠️ Skipping helpdesk tickets - required users/admin not found');

            return;
        }

        $primaryUser = $users->first();
        $secondaryUser = $users->skip(1)->first() ?? $primaryUser;
        $divisionId = $division ? $division->id : $primaryUser->division_id;

        // PKS 5.2.1: All tickets require authenticated user_id (NO GUEST MODE)
        // Create sample users for tickets if needed
        $sampleUsers = [];
        $sampleUserData = [
            ['name' => 'Nurul Aisyah', 'email' => 'nurul.aisyah@motac.gov.my', 'staff_id' => 'MOTAC101'],
            ['name' => 'Mohd Hafiz', 'email' => 'mohd.hafiz@motac.gov.my', 'staff_id' => 'MOTAC102'],
            ['name' => 'Lim Wei Ling', 'email' => 'lim.weiling@motac.gov.my', 'staff_id' => 'MOTAC103'],
            ['name' => 'Rajesh Kumar', 'email' => 'rajesh.kumar@motac.gov.my', 'staff_id' => 'MOTAC104'],
            ['name' => 'Siti Aminah', 'email' => 'siti.aminah@motac.gov.my', 'staff_id' => 'MOTAC105'],
        ];

        foreach ($sampleUserData as $userData) {
            $sampleUsers[] = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'staff_id' => $userData['staff_id'],
                    'division_id' => $divisionId,
                    'role' => 'staff',
                    'password' => bcrypt('password'),
                ]
            );
        }

        // PKS 5.2.1 Compliant tickets - all with authenticated user_id
        $authenticatedTickets = [
            [
                'user_id' => $sampleUsers[0]->id,
                'staff_id' => $sampleUsers[0]->staff_id,
                'division_id' => $divisionId,
                'category_id' => $hardwareCategory->id,
                'priority' => 'high',
                'subject' => 'Komputer tidak boleh boot',
                'description' => 'Komputer saya tidak boleh boot selepas update Windows semalam.',
                'status' => 'open',
            ],
            [
                'user_id' => $sampleUsers[1]->id,
                'staff_id' => $sampleUsers[1]->staff_id,
                'division_id' => $divisionId,
                'category_id' => $softwareCategory->id,
                'priority' => 'normal',
                'subject' => 'Tidak boleh login ke sistem e-Perolehan',
                'description' => 'Saya tidak dapat login ke sistem e-Perolehan.',
                'status' => 'in_progress',
                'assigned_to_user' => $adminUser->id,
                'assigned_at' => now()->subHours(2),
            ],
            [
                'user_id' => $sampleUsers[2]->id,
                'staff_id' => $sampleUsers[2]->staff_id,
                'division_id' => $divisionId,
                'category_id' => $networkCategory->id,
                'priority' => 'urgent',
                'subject' => 'Internet connection sangat perlahan',
                'description' => 'Sambungan internet di pejabat saya sangat perlahan sejak pagi tadi.',
                'status' => 'open',
            ],
            [
                'user_id' => $sampleUsers[3]->id,
                'staff_id' => $sampleUsers[3]->staff_id,
                'division_id' => $divisionId,
                'category_id' => $softwareCategory->id,
                'priority' => 'low',
                'subject' => 'Request untuk install Adobe Acrobat Pro',
                'description' => 'Saya memerlukan Adobe Acrobat Pro untuk edit dokumen PDF.',
                'status' => 'resolved',
                'assigned_to_user' => $adminUser->id,
                'assigned_at' => now()->subDays(2),
                'resolved_at' => now()->subDays(1),
                'resolution_notes' => 'Adobe Acrobat Pro telah diinstall dan diaktifkan.',
            ],
            [
                'user_id' => $sampleUsers[4]->id,
                'staff_id' => $sampleUsers[4]->staff_id,
                'division_id' => $divisionId,
                'category_id' => $hardwareCategory->id,
                'priority' => 'normal',
                'subject' => 'Printer tidak berfungsi',
                'description' => 'Printer di bilik saya tidak dapat print.',
                'status' => 'closed',
                'assigned_to_user' => $adminUser->id,
                'assigned_at' => now()->subDays(5),
                'resolved_at' => now()->subDays(4),
                'closed_at' => now()->subDays(3),
                'resolution_notes' => 'Toner printer telah diganti.',
            ],
            [
                'user_id' => $primaryUser->id,
                'staff_id' => $primaryUser->staff_id,
                'division_id' => $primaryUser->division_id,
                'category_id' => $hardwareCategory->id,
                'priority' => 'high',
                'subject' => 'Laptop screen flickering',
                'description' => 'My laptop screen has been flickering for the past two days.',
                'status' => 'open',
                'admin_notes' => 'User reported via authenticated portal.',
            ],
            [
                'user_id' => $secondaryUser->id,
                'staff_id' => $secondaryUser->staff_id,
                'division_id' => $secondaryUser->division_id,
                'category_id' => $softwareCategory->id,
                'priority' => 'normal',
                'subject' => 'Microsoft Office activation issue',
                'description' => 'Microsoft Office shows activation error.',
                'status' => 'in_progress',
                'assigned_to_user' => $adminUser->id,
                'assigned_at' => now()->subHours(3),
                'admin_notes' => 'Checking Office 365 license allocation.',
            ],
            [
                'user_id' => $primaryUser->id,
                'staff_id' => $primaryUser->staff_id,
                'division_id' => $primaryUser->division_id,
                'category_id' => $networkCategory->id,
                'priority' => 'urgent',
                'subject' => 'Cannot access shared drive',
                'description' => 'I cannot access the shared network drive.',
                'status' => 'open',
                'admin_notes' => 'Critical issue affecting multiple users.',
            ],
            [
                'user_id' => $secondaryUser->id,
                'staff_id' => $secondaryUser->staff_id,
                'division_id' => $secondaryUser->division_id,
                'category_id' => $maintenanceCategory->id,
                'priority' => 'low',
                'subject' => 'Request for keyboard replacement',
                'description' => 'Some keys on my keyboard are not working properly.',
                'status' => 'resolved',
                'assigned_to_user' => $adminUser->id,
                'assigned_at' => now()->subDays(3),
                'resolved_at' => now()->subDays(1),
                'resolution_notes' => 'New keyboard issued and installed.',
            ],
        ];

        if ($asset) {
            $authenticatedTickets[] = [
                'user_id' => $primaryUser->id,
                'staff_id' => $primaryUser->staff_id,
                'division_id' => $primaryUser->division_id,
                'category_id' => $maintenanceCategory->id,
                'priority' => 'normal',
                'subject' => 'Asset maintenance required',
                'description' => 'The borrowed asset requires maintenance.',
                'status' => 'open',
                'asset_id' => $asset->id,
                'admin_notes' => 'Linked to asset loan.',
            ];
        }

        foreach ($authenticatedTickets as $index => $ticketData) {
            $ticket_number = 'HD'.date('Y').str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT);
            $ticket = HelpdeskTicket::firstOrCreate(
                ['ticket_number' => $ticket_number],
                $ticketData
            );
            $ticket->calculateSLADueDates();
            $ticket->save();
        }

        $this->command->info('✓ Created sample helpdesk tickets (PKS 5.2.1 compliant - authenticated only)');
    }
}

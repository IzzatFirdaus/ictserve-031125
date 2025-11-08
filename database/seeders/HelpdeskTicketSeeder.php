<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

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

        $guestTickets = [
            [
                'user_id' => null,
                'guest_name' => 'Nurul Aisyah',
                'guest_email' => 'nurul.aisyah@motac.gov.my',
                'guest_phone' => '012-9876543',
                'staff_id' => 'MOTAC101',
                'division_id' => $division?->id,
                'category_id' => $hardwareCategory->id,
                'priority' => 'high',
                'subject' => 'Komputer tidak boleh boot',
                'description' => 'Komputer saya tidak boleh boot selepas update Windows semalam.',
                'status' => 'open',
            ],
            [
                'user_id' => null,
                'guest_name' => 'Mohd Hafiz',
                'guest_email' => 'mohd.hafiz@motac.gov.my',
                'guest_phone' => '013-1234567',
                'staff_id' => 'MOTAC102',
                'division_id' => $division?->id,
                'category_id' => $softwareCategory->id,
                'priority' => 'normal',
                'subject' => 'Tidak boleh login ke sistem e-Perolehan',
                'description' => 'Saya tidak dapat login ke sistem e-Perolehan.',
                'status' => 'in_progress',
                'assigned_to_user' => $adminUser?->id,
                'assigned_at' => now()->subHours(2),
            ],
            [
                'user_id' => null,
                'guest_name' => 'Lim Wei Ling',
                'guest_email' => 'lim.weiling@motac.gov.my',
                'guest_phone' => '014-9876543',
                'staff_id' => 'MOTAC103',
                'division_id' => $division?->id,
                'category_id' => $networkCategory->id,
                'priority' => 'urgent',
                'subject' => 'Internet connection sangat perlahan',
                'description' => 'Sambungan internet di pejabat saya sangat perlahan sejak pagi tadi.',
                'status' => 'open',
            ],
            [
                'user_id' => null,
                'guest_name' => 'Rajesh Kumar',
                'guest_email' => 'rajesh.kumar@motac.gov.my',
                'guest_phone' => '016-5432109',
                'staff_id' => 'MOTAC104',
                'division_id' => $division?->id,
                'category_id' => $softwareCategory->id,
                'priority' => 'low',
                'subject' => 'Request untuk install Adobe Acrobat Pro',
                'description' => 'Saya memerlukan Adobe Acrobat Pro untuk edit dokumen PDF.',
                'status' => 'resolved',
                'assigned_to_user' => $adminUser?->id,
                'assigned_at' => now()->subDays(2),
                'resolved_at' => now()->subDays(1),
                'resolution_notes' => 'Adobe Acrobat Pro telah diinstall dan diaktifkan.',
            ],
            [
                'user_id' => null,
                'guest_name' => 'Siti Aminah',
                'guest_email' => 'siti.aminah@motac.gov.my',
                'guest_phone' => '017-8765432',
                'staff_id' => 'MOTAC105',
                'division_id' => $division?->id,
                'category_id' => $hardwareCategory->id,
                'priority' => 'normal',
                'subject' => 'Printer tidak berfungsi',
                'description' => 'Printer di bilik saya tidak dapat print.',
                'status' => 'closed',
                'assigned_to_user' => $adminUser?->id,
                'assigned_at' => now()->subDays(5),
                'resolved_at' => now()->subDays(4),
                'closed_at' => now()->subDays(3),
                'resolution_notes' => 'Toner printer telah diganti.',
            ],
        ];

        foreach ($guestTickets as $index => $ticketData) {
            $ticket_number = 'HD'.date('Y').str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT);
            $ticket = HelpdeskTicket::firstOrCreate(
                ['ticket_number' => $ticket_number],
                $ticketData
            );
            $ticket->calculateSLADueDates();
            $ticket->save();
        }

        if ($users->count() > 0) {
            $authenticatedTickets = [
                [
                    'user_id' => $users->first()->id,
                    'staff_id' => $users->first()->staff_id,
                    'division_id' => $users->first()->division_id,
                    'category_id' => $hardwareCategory->id,
                    'priority' => 'high',
                    'subject' => 'Laptop screen flickering',
                    'description' => 'My laptop screen has been flickering for the past two days.',
                    'status' => 'open',
                    'admin_notes' => 'User reported via authenticated portal.',
                ],
                [
                    'user_id' => $users->skip(1)->first()?->id ?? $users->first()->id,
                    'staff_id' => $users->skip(1)->first()?->staff_id ?? $users->first()->staff_id,
                    'division_id' => $users->skip(1)->first()?->division_id ?? $users->first()->division_id,
                    'category_id' => $softwareCategory->id,
                    'priority' => 'normal',
                    'subject' => 'Microsoft Office activation issue',
                    'description' => 'Microsoft Office shows activation error.',
                    'status' => 'in_progress',
                    'assigned_to_user' => $adminUser?->id,
                    'assigned_at' => now()->subHours(3),
                    'admin_notes' => 'Checking Office 365 license allocation.',
                ],
                [
                    'user_id' => $users->first()->id,
                    'staff_id' => $users->first()->staff_id,
                    'division_id' => $users->first()->division_id,
                    'category_id' => $networkCategory->id,
                    'priority' => 'urgent',
                    'subject' => 'Cannot access shared drive',
                    'description' => 'I cannot access the shared network drive.',
                    'status' => 'open',
                    'admin_notes' => 'Critical issue affecting multiple users.',
                ],
                [
                    'user_id' => $users->skip(1)->first()?->id ?? $users->first()->id,
                    'staff_id' => $users->skip(1)->first()?->staff_id ?? $users->first()->staff_id,
                    'division_id' => $users->skip(1)->first()?->division_id ?? $users->first()->division_id,
                    'category_id' => $maintenanceCategory->id,
                    'priority' => 'low',
                    'subject' => 'Request for keyboard replacement',
                    'description' => 'Some keys on my keyboard are not working properly.',
                    'status' => 'resolved',
                    'assigned_to_user' => $adminUser?->id,
                    'assigned_at' => now()->subDays(3),
                    'resolved_at' => now()->subDays(1),
                    'resolution_notes' => 'New keyboard issued and installed.',
                ],
            ];

            if ($asset) {
                $authenticatedTickets[] = [
                    'user_id' => $users->first()->id,
                    'staff_id' => $users->first()->staff_id,
                    'division_id' => $users->first()->division_id,
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
                $ticket_number = 'HD'.date('Y').str_pad((string) ($index + 100), 6, '0', STR_PAD_LEFT);
                $ticket = HelpdeskTicket::firstOrCreate(
                    ['ticket_number' => $ticket_number],
                    $ticketData
                );
                $ticket->calculateSLADueDates();
                $ticket->save();
            }
        }

        $this->command->info('✓ Created sample helpdesk tickets (guest and authenticated)');
    }
}

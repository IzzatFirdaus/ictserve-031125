<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TicketCategory;
use Illuminate\Database\Seeder;

/**
 * Ticket Category Seeder
 *
 * Seeds the canonical Helpdesk categories as required by the project plan.
 * trace: SRS-FR-005; D04 §3.2; author: ictserve-team
 */
class TicketCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'HARDWARE_IPAD', 'name_ms' => 'Perkakasan: iPad', 'name_en' => 'Hardware: iPad', 'sla_response_hours' => 4, 'sla_resolution_hours' => 24],
            ['code' => 'HARDWARE_COMPUTER', 'name_ms' => 'Perkakasan: Komputer', 'name_en' => 'Hardware: Computer', 'sla_response_hours' => 4, 'sla_resolution_hours' => 24],
            ['code' => 'HARDWARE_ACCESSORY', 'name_ms' => 'Perkakasan: Aksesori', 'name_en' => 'Hardware: Accessory', 'sla_response_hours' => 8, 'sla_resolution_hours' => 48],
            ['code' => 'SOFTWARE', 'name_ms' => 'Perisian', 'name_en' => 'Software', 'sla_response_hours' => 2, 'sla_resolution_hours' => 8],
            ['code' => 'NETWORK', 'name_ms' => 'Rangkaian', 'name_en' => 'Network', 'sla_response_hours' => 1, 'sla_resolution_hours' => 4],
            ['code' => 'PRINTER_CONSUMABLES', 'name_ms' => 'Pencetak & Consumables', 'name_en' => 'Printer & Consumables', 'sla_response_hours' => 6, 'sla_resolution_hours' => 24],
            ['code' => 'DATA_RECOVERY', 'name_ms' => 'Pemulihan Data', 'name_en' => 'Data Recovery', 'sla_response_hours' => 12, 'sla_resolution_hours' => 72],
            ['code' => 'MAINTENANCE', 'name_ms' => 'Penyelenggaraan', 'name_en' => 'Maintenance', 'sla_response_hours' => 8, 'sla_resolution_hours' => 48],
            ['code' => 'ASSET_REQUEST', 'name_ms' => 'Permohonan Aset', 'name_en' => 'Asset Request', 'sla_response_hours' => 24, 'sla_resolution_hours' => 72],
            ['code' => 'LOAN_REQUEST', 'name_ms' => 'Permohonan Peminjaman', 'name_en' => 'Loan Request', 'sla_response_hours' => 24, 'sla_resolution_hours' => 72],
            ['code' => 'GENERAL', 'name_ms' => 'Lain-lain', 'name_en' => 'Others', 'sla_response_hours' => 24, 'sla_resolution_hours' => 72],
        ];

        foreach ($categories as $c) {
            TicketCategory::updateOrCreate([
                'code' => $c['code'],
            ], [
                'name_ms' => $c['name_ms'],
                'name_en' => $c['name_en'],
                'description_ms' => $c['name_ms'],
                'description_en' => $c['name_en'],
                'sla_response_hours' => $c['sla_response_hours'],
                'sla_resolution_hours' => $c['sla_resolution_hours'],
                'is_active' => true,
            ]);
        }
    }
}

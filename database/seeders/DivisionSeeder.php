<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

/**
 * Division Seeder
 *
 * Seeds MOTAC organizational divisions with bilingual names.
 *
 * @see D03-FR-005.1 Model factories and seeders for testing
 * @see D03-FR-016.2 Shared organizational data
 */
class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            [
                'code' => 'ICT',
                'name_ms' => 'Bahagian Teknologi Maklumat dan Komunikasi',
                'name_en' => 'Information and Communication Technology Division',
                'description_ms' => 'Menguruskan infrastruktur dan perkhidmatan ICT untuk MOTAC',
                'description_en' => 'Manages ICT infrastructure and services for MOTAC',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'HR',
                'name_ms' => 'Bahagian Sumber Manusia',
                'name_en' => 'Human Resources Division',
                'description_ms' => 'Menguruskan hal ehwal kakitangan dan pembangunan sumber manusia',
                'description_en' => 'Manages staff affairs and human resource development',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'FIN',
                'name_ms' => 'Bahagian Kewangan',
                'name_en' => 'Finance Division',
                'description_ms' => 'Menguruskan kewangan, perakaunan dan belanjawan',
                'description_en' => 'Manages finance, accounting and budgeting',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'ADMIN',
                'name_ms' => 'Bahagian Pentadbiran',
                'name_en' => 'Administration Division',
                'description_ms' => 'Menguruskan pentadbiran am dan kemudahan pejabat',
                'description_en' => 'Manages general administration and office facilities',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'TOURISM',
                'name_ms' => 'Bahagian Pelancongan',
                'name_en' => 'Tourism Division',
                'description_ms' => 'Menguruskan pembangunan dan promosi pelancongan',
                'description_en' => 'Manages tourism development and promotion',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'ARTS',
                'name_ms' => 'Bahagian Kesenian',
                'name_en' => 'Arts Division',
                'description_ms' => 'Menguruskan pembangunan seni dan budaya',
                'description_en' => 'Manages arts and cultural development',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'CULTURE',
                'name_ms' => 'Bahagian Kebudayaan',
                'name_en' => 'Culture Division',
                'description_ms' => 'Menguruskan warisan budaya dan pemuliharaan',
                'description_en' => 'Manages cultural heritage and conservation',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'LEGAL',
                'name_ms' => 'Bahagian Perundangan',
                'name_en' => 'Legal Division',
                'description_ms' => 'Menguruskan hal ehwal perundangan dan pematuhan',
                'description_en' => 'Manages legal affairs and compliance',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'COMMS',
                'name_ms' => 'Bahagian Komunikasi Korporat',
                'name_en' => 'Corporate Communications Division',
                'description_ms' => 'Menguruskan komunikasi korporat dan perhubungan awam',
                'description_en' => 'Manages corporate communications and public relations',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'PLANNING',
                'name_ms' => 'Bahagian Perancangan',
                'name_en' => 'Planning Division',
                'description_ms' => 'Menguruskan perancangan strategik dan pembangunan',
                'description_en' => 'Manages strategic planning and development',
                'parent_id' => null,
                'is_active' => true,
            ],
        ];

        foreach ($divisions as $divisionData) {
            Division::firstOrCreate(
                ['code' => $divisionData['code']],
                $divisionData
            );
        }

        $this->command->info('Divisions seeded successfully.');
    }
}

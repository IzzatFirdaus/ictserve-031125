<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

/**
 * Division Seeder
 *
 * Seeds MOTAC organizational divisions (Bahagian/Unit) with bilingual names.
 * Based on official MOTAC organizational structure.
 *
 * @see D03-FR-005.1 Model factories and seeders for testing
 * @see D03-FR-016.2 Shared organizational data
 * @see _reference/SERVICEDESK-ICT-INTRANET.pdf
 */
class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            // Pejabat Menteri / Minister's Office
            [
                'code' => 'PMT',
                'name_ms' => 'Pejabat Menteri',
                'name_en' => "Minister's Office",
                'description_ms' => 'Pejabat Menteri Pelancongan, Seni dan Budaya',
                'description_en' => 'Office of the Minister of Tourism, Arts and Culture',
                'parent_id' => null,
                'is_active' => true,
            ],
            // Pejabat Timbalan Menteri / Deputy Minister's Office
            [
                'code' => 'PTMT',
                'name_ms' => 'Pejabat Timbalan Menteri',
                'name_en' => "Deputy Minister's Office",
                'description_ms' => 'Pejabat Timbalan Menteri Pelancongan, Seni dan Budaya',
                'description_en' => 'Office of the Deputy Minister of Tourism, Arts and Culture',
                'parent_id' => null,
                'is_active' => true,
            ],
            // Pejabat Ketua Setiausaha / Secretary General's Office
            [
                'code' => 'PKSU',
                'name_ms' => 'Pejabat Ketua Setiausaha',
                'name_en' => "Secretary General's Office",
                'description_ms' => 'Pejabat Ketua Setiausaha Kementerian',
                'description_en' => "Office of the Ministry's Secretary General",
                'parent_id' => null,
                'is_active' => true,
            ],
            // Pejabat Timbalan Ketua Setiausaha / Deputy Secretary General's Office
            [
                'code' => 'PTKSU',
                'name_ms' => 'Pejabat Timbalan Ketua Setiausaha',
                'name_en' => "Deputy Secretary General's Office",
                'description_ms' => 'Pejabat Timbalan Ketua Setiausaha Kementerian',
                'description_en' => "Office of the Ministry's Deputy Secretary General",
                'parent_id' => null,
                'is_active' => true,
            ],
            // Bahagian Pengurusan Maklumat (BPM) - ICT Division
            [
                'code' => 'BPM',
                'name_ms' => 'Bahagian Pengurusan Maklumat',
                'name_en' => 'Information Management Division',
                'description_ms' => 'Menguruskan infrastruktur dan perkhidmatan ICT untuk MOTAC',
                'description_en' => 'Manages ICT infrastructure and services for MOTAC',
                'parent_id' => null,
                'is_active' => true,
            ],
            // Bahagian Khidmat Pengurusan
            [
                'code' => 'BKP',
                'name_ms' => 'Bahagian Khidmat Pengurusan',
                'name_en' => 'Management Services Division',
                'description_ms' => 'Menguruskan pentadbiran am dan kemudahan pejabat',
                'description_en' => 'Manages general administration and office facilities',
                'parent_id' => null,
                'is_active' => true,
            ],
            // Bahagian Pengurusan Sumber Manusia
            [
                'code' => 'BPSM',
                'name_ms' => 'Bahagian Pengurusan Sumber Manusia',
                'name_en' => 'Human Resource Management Division',
                'description_ms' => 'Menguruskan hal ehwal kakitangan dan pembangunan sumber manusia',
                'description_en' => 'Manages staff affairs and human resource development',
                'parent_id' => null,
                'is_active' => true,
            ],
            // Bahagian Kewangan
            [
                'code' => 'BK',
                'name_ms' => 'Bahagian Kewangan',
                'name_en' => 'Finance Division',
                'description_ms' => 'Menguruskan kewangan, perakaunan dan belanjawan',
                'description_en' => 'Manages finance, accounting and budgeting',
                'parent_id' => null,
                'is_active' => true,
            ],
            // Bahagian Perancangan Strategik
            [
                'code' => 'BPS',
                'name_ms' => 'Bahagian Perancangan Strategik',
                'name_en' => 'Strategic Planning Division',
                'description_ms' => 'Menguruskan perancangan strategik dan pembangunan',
                'description_en' => 'Manages strategic planning and development',
                'parent_id' => null,
                'is_active' => true,
            ],
            // Bahagian Pembangunan Pelancongan
            [
                'code' => 'BPP',
                'name_ms' => 'Bahagian Pembangunan Pelancongan',
                'name_en' => 'Tourism Development Division',
                'description_ms' => 'Menguruskan pembangunan dan promosi pelancongan',
                'description_en' => 'Manages tourism development and promotion',
                'parent_id' => null,
                'is_active' => true,
            ],
            // Bahagian Dasar Kebudayaan
            [
                'code' => 'BDK',
                'name_ms' => 'Bahagian Dasar Kebudayaan',
                'name_en' => 'Cultural Policy Division',
                'description_ms' => 'Menguruskan dasar dan pembangunan kebudayaan',
                'description_en' => 'Manages cultural policy and development',
                'parent_id' => null,
                'is_active' => true,
            ],
            // Bahagian Dasar Kesenian
            [
                'code' => 'BDKS',
                'name_ms' => 'Bahagian Dasar Kesenian',
                'name_en' => 'Arts Policy Division',
                'description_ms' => 'Menguruskan dasar dan pembangunan kesenian',
                'description_en' => 'Manages arts policy and development',
                'parent_id' => null,
                'is_active' => true,
            ],
            // Bahagian Antarabangsa
            [
                'code' => 'BA',
                'name_ms' => 'Bahagian Antarabangsa',
                'name_en' => 'International Division',
                'description_ms' => 'Menguruskan hubungan antarabangsa dan kerjasama',
                'description_en' => 'Manages international relations and cooperation',
                'parent_id' => null,
                'is_active' => true,
            ],
            // Bahagian Komunikasi Korporat
            [
                'code' => 'BKK',
                'name_ms' => 'Bahagian Komunikasi Korporat',
                'name_en' => 'Corporate Communications Division',
                'description_ms' => 'Menguruskan komunikasi korporat dan perhubungan awam',
                'description_en' => 'Manages corporate communications and public relations',
                'parent_id' => null,
                'is_active' => true,
            ],
            // Unit Audit Dalam
            [
                'code' => 'UAD',
                'name_ms' => 'Unit Audit Dalam',
                'name_en' => 'Internal Audit Unit',
                'description_ms' => 'Menguruskan audit dalaman kementerian',
                'description_en' => 'Manages internal audit of the ministry',
                'parent_id' => null,
                'is_active' => true,
            ],
            // Unit Integriti
            [
                'code' => 'UI',
                'name_ms' => 'Unit Integriti',
                'name_en' => 'Integrity Unit',
                'description_ms' => 'Menguruskan hal ehwal integriti dan tadbir urus',
                'description_en' => 'Manages integrity and governance affairs',
                'parent_id' => null,
                'is_active' => true,
            ],
            // Unit Undang-Undang
            [
                'code' => 'UUU',
                'name_ms' => 'Unit Undang-Undang',
                'name_en' => 'Legal Unit',
                'description_ms' => 'Menguruskan hal ehwal perundangan dan pematuhan',
                'description_en' => 'Manages legal affairs and compliance',
                'parent_id' => null,
                'is_active' => true,
            ],
            // Agensi di bawah MOTAC
            [
                'code' => 'TM',
                'name_ms' => 'Tourism Malaysia',
                'name_en' => 'Tourism Malaysia',
                'description_ms' => 'Agensi promosi pelancongan Malaysia',
                'description_en' => 'Malaysia tourism promotion agency',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'JKKN',
                'name_ms' => 'Jabatan Kebudayaan dan Kesenian Negara',
                'name_en' => 'National Department for Culture and Arts',
                'description_ms' => 'Jabatan yang menguruskan kebudayaan dan kesenian negara',
                'description_en' => 'Department managing national culture and arts',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'ASWARA',
                'name_ms' => 'Akademi Seni Budaya dan Warisan Kebangsaan',
                'name_en' => 'National Academy of Arts, Culture and Heritage',
                'description_ms' => 'Akademi seni budaya dan warisan kebangsaan',
                'description_en' => 'National academy for arts, culture and heritage',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'ISTANA',
                'name_ms' => 'Istana Budaya',
                'name_en' => 'Istana Budaya (National Theatre)',
                'description_ms' => 'Teater kebangsaan Malaysia',
                'description_en' => 'Malaysia national theatre',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'JMM',
                'name_ms' => 'Jabatan Muzium Malaysia',
                'name_en' => 'Department of Museums Malaysia',
                'description_ms' => 'Jabatan yang menguruskan muzium-muzium negara',
                'description_en' => 'Department managing national museums',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'ARKIB',
                'name_ms' => 'Arkib Negara Malaysia',
                'name_en' => 'National Archives of Malaysia',
                'description_ms' => 'Arkib negara Malaysia',
                'description_en' => 'National archives of Malaysia',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'PNM',
                'name_ms' => 'Perpustakaan Negara Malaysia',
                'name_en' => 'National Library of Malaysia',
                'description_ms' => 'Perpustakaan negara Malaysia',
                'description_en' => 'National library of Malaysia',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'KRAFTANGAN',
                'name_ms' => 'Perbadanan Kemajuan Kraftangan Malaysia',
                'name_en' => 'Malaysian Handicraft Development Corporation',
                'description_ms' => 'Perbadanan kemajuan kraftangan Malaysia',
                'description_en' => 'Malaysian handicraft development corporation',
                'parent_id' => null,
                'is_active' => true,
            ],
            // Lain-lain / Others
            [
                'code' => 'LAIN',
                'name_ms' => 'Lain-lain',
                'name_en' => 'Others',
                'description_ms' => 'Bahagian/Unit lain yang tidak tersenarai',
                'description_en' => 'Other divisions/units not listed',
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

        $this->command->info('MOTAC Divisions/Units seeded successfully.');
    }
}

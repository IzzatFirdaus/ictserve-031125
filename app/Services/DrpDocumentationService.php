<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DrpTestResult;
use Illuminate\Support\Facades\Log;

/**
 * DRP Documentation Service
 *
 * PKS Business Continuity (Requirement 29) - DRP Documentation Management
 *
 * Implements:
 * - Complete DRP procedures documentation in Bahasa Melayu
 * - Annual DRP testing schedule
 * - DRP test result templates
 *
 * @trace D03-FR-029 (Business Continuity)
 * @trace Requirements 29.5
 */
class DrpDocumentationService
{
    // DRP Test Types
    public const TEST_TABLETOP = 'tabletop';

    public const TEST_WALKTHROUGH = 'walkthrough';

    public const TEST_SIMULATION = 'simulation';

    public const TEST_FULL = 'full';

    // Test Result Status
    public const RESULT_PASSED = 'passed';

    public const RESULT_FAILED = 'failed';

    public const RESULT_PARTIAL = 'partial';

    // PKS 29.1 compliance
    public const RTO_HOURS = 4;

    public const RPO_HOURS = 24;

    /**
     * Get complete DRP procedures in Bahasa Melayu
     *
     * @return array<string, mixed>
     */
    public function getDrpProcedures(): array
    {
        return [
            'document_info' => [
                'title' => 'Pelan Pemulihan Bencana (DRP)',
                'version' => '1.0',
                'last_updated' => now()->format('d/m/Y'),
                'owner' => 'Bahagian Teknologi Maklumat, MOTAC',
                'classification' => 'TERHAD',
            ],
            'objectives' => [
                'rto' => [
                    'target' => self::RTO_HOURS.' jam',
                    'description' => 'Masa maksimum untuk memulihkan perkhidmatan kritikal',
                ],
                'rpo' => [
                    'target' => self::RPO_HOURS.' jam',
                    'description' => 'Kehilangan data maksimum yang boleh diterima',
                ],
            ],
            'phases' => $this->getDrpPhases(),
            'roles' => $this->getDrpRoles(),
            'contact_list' => $this->getEmergencyContacts(),
            'recovery_procedures' => $this->getRecoveryProcedures(),
            'communication_plan' => $this->getCommunicationPlan(),
            'testing_schedule' => $this->getTestingSchedule(),
        ];
    }

    /**
     * Get DRP phases
     *
     * @return array<int, array<string, mixed>>
     */
    private function getDrpPhases(): array
    {
        return [
            [
                'phase' => 1,
                'name' => 'Pengesanan dan Penilaian',
                'duration' => '0-30 minit',
                'activities' => [
                    'Kenal pasti jenis dan skop bencana',
                    'Nilai impak kepada sistem dan perkhidmatan',
                    'Aktifkan pasukan pemulihan bencana',
                    'Maklumkan pihak pengurusan',
                ],
            ],
            [
                'phase' => 2,
                'name' => 'Pengaktifan DRP',
                'duration' => '30-60 minit',
                'activities' => [
                    'Isytihar bencana secara rasmi',
                    'Aktifkan tapak pemulihan (DR site)',
                    'Maklumkan semua pihak berkepentingan',
                    'Mulakan prosedur failover',
                ],
            ],
            [
                'phase' => 3,
                'name' => 'Pemulihan Sistem',
                'duration' => '1-4 jam',
                'activities' => [
                    'Laksanakan failover pangkalan data',
                    'Aktifkan aplikasi di tapak DR',
                    'Sahkan integriti data',
                    'Uji fungsi kritikal',
                ],
            ],
            [
                'phase' => 4,
                'name' => 'Pemulihan Operasi',
                'duration' => '4-8 jam',
                'activities' => [
                    'Pulihkan semua perkhidmatan',
                    'Sahkan akses pengguna',
                    'Pantau prestasi sistem',
                    'Dokumentasi status pemulihan',
                ],
            ],
            [
                'phase' => 5,
                'name' => 'Kembali ke Normal',
                'duration' => 'Selepas bencana selesai',
                'activities' => [
                    'Nilai kerosakan tapak utama',
                    'Rancang failback ke tapak utama',
                    'Laksanakan failback',
                    'Dokumentasi pengajaran',
                ],
            ],
        ];
    }

    /**
     * Get DRP roles and responsibilities
     *
     * @return array<string, array<string, mixed>>
     */
    private function getDrpRoles(): array
    {
        return [
            'drp_coordinator' => [
                'title' => 'Penyelaras DRP',
                'responsibilities' => [
                    'Menyelaras semua aktiviti pemulihan',
                    'Membuat keputusan pengaktifan DRP',
                    'Berkomunikasi dengan pengurusan',
                    'Memastikan pematuhan RTO/RPO',
                ],
            ],
            'technical_lead' => [
                'title' => 'Ketua Teknikal',
                'responsibilities' => [
                    'Melaksanakan prosedur failover',
                    'Memantau status sistem',
                    'Menyelesaikan isu teknikal',
                    'Mengesahkan integriti data',
                ],
            ],
            'communication_lead' => [
                'title' => 'Ketua Komunikasi',
                'responsibilities' => [
                    'Memaklumkan pihak berkepentingan',
                    'Menguruskan komunikasi luaran',
                    'Menyediakan kemas kini status',
                    'Menyelaras dengan CSIRT',
                ],
            ],
            'operations_lead' => [
                'title' => 'Ketua Operasi',
                'responsibilities' => [
                    'Memastikan kesinambungan perkhidmatan',
                    'Menguruskan pengguna terjejas',
                    'Menyelaras dengan bahagian lain',
                    'Memantau SLA',
                ],
            ],
        ];
    }

    /**
     * Get emergency contact list
     *
     * @return array<string, array<string, string>>
     */
    private function getEmergencyContacts(): array
    {
        return [
            'internal' => [
                'drp_coordinator' => [
                    'name' => '[Nama Penyelaras DRP]',
                    'phone' => '[No. Telefon]',
                    'email' => '[Email]',
                ],
                'technical_lead' => [
                    'name' => '[Nama Ketua Teknikal]',
                    'phone' => '[No. Telefon]',
                    'email' => '[Email]',
                ],
                'management' => [
                    'name' => '[Nama Pengurusan]',
                    'phone' => '[No. Telefon]',
                    'email' => '[Email]',
                ],
            ],
            'external' => [
                'csirt_motac' => [
                    'name' => 'CSIRT MOTAC',
                    'phone' => '[No. Telefon CSIRT]',
                    'email' => 'csirt@motac.gov.my',
                ],
                'nacsa' => [
                    'name' => 'NACSA',
                    'phone' => '03-8000 8000',
                    'email' => 'incident@nacsa.gov.my',
                ],
                'mycert' => [
                    'name' => 'MyCERT',
                    'phone' => '1-300-88-2999',
                    'email' => 'mycert@cybersecurity.my',
                ],
            ],
        ];
    }

    /**
     * Get detailed recovery procedures
     *
     * @return array<string, array<string, mixed>>
     */
    private function getRecoveryProcedures(): array
    {
        return [
            'database' => [
                'title' => 'Pemulihan Pangkalan Data',
                'steps' => [
                    [
                        'step' => 1,
                        'action' => 'Sahkan status replikasi',
                        'command' => 'SHOW SLAVE STATUS',
                        'expected' => 'Seconds_Behind_Master < 60',
                    ],
                    [
                        'step' => 2,
                        'action' => 'Hentikan replikasi',
                        'command' => 'STOP SLAVE',
                        'expected' => 'Query OK',
                    ],
                    [
                        'step' => 3,
                        'action' => 'Naikkan taraf slave ke master',
                        'command' => 'RESET SLAVE ALL',
                        'expected' => 'Query OK',
                    ],
                    [
                        'step' => 4,
                        'action' => 'Kemaskini konfigurasi aplikasi',
                        'command' => 'Update .env DB_HOST',
                        'expected' => 'Sambungan berjaya',
                    ],
                ],
            ],
            'application' => [
                'title' => 'Pemulihan Aplikasi',
                'steps' => [
                    [
                        'step' => 1,
                        'action' => 'Aktifkan mod penyelenggaraan',
                        'command' => 'php artisan down',
                        'expected' => 'Application is now in maintenance mode',
                    ],
                    [
                        'step' => 2,
                        'action' => 'Kosongkan cache',
                        'command' => 'php artisan cache:clear',
                        'expected' => 'Application cache cleared',
                    ],
                    [
                        'step' => 3,
                        'action' => 'Kemaskini konfigurasi',
                        'command' => 'php artisan config:cache',
                        'expected' => 'Configuration cached successfully',
                    ],
                    [
                        'step' => 4,
                        'action' => 'Nyahaktif mod penyelenggaraan',
                        'command' => 'php artisan up',
                        'expected' => 'Application is now live',
                    ],
                ],
            ],
            'redis' => [
                'title' => 'Pemulihan Redis',
                'steps' => [
                    [
                        'step' => 1,
                        'action' => 'Sahkan status Redis DR',
                        'command' => 'redis-cli -h dr-host ping',
                        'expected' => 'PONG',
                    ],
                    [
                        'step' => 2,
                        'action' => 'Naikkan taraf slave ke master',
                        'command' => 'redis-cli SLAVEOF NO ONE',
                        'expected' => 'OK',
                    ],
                    [
                        'step' => 3,
                        'action' => 'Kemaskini konfigurasi aplikasi',
                        'command' => 'Update .env REDIS_HOST',
                        'expected' => 'Sambungan berjaya',
                    ],
                ],
            ],
        ];
    }

    /**
     * Get communication plan
     *
     * @return array<string, mixed>
     */
    private function getCommunicationPlan(): array
    {
        return [
            'notification_timeline' => [
                [
                    'time' => '0-15 minit',
                    'audience' => 'Pasukan DRP',
                    'method' => 'Panggilan telefon, WhatsApp',
                    'message' => 'Pengaktifan DRP - Sila hadir segera',
                ],
                [
                    'time' => '15-30 minit',
                    'audience' => 'Pengurusan',
                    'method' => 'Email, Panggilan',
                    'message' => 'Laporan awal bencana dan tindakan',
                ],
                [
                    'time' => '30-60 minit',
                    'audience' => 'CSIRT MOTAC',
                    'method' => 'Email rasmi',
                    'message' => 'Laporan insiden mengikut format PKS',
                ],
                [
                    'time' => '1-2 jam',
                    'audience' => 'Pengguna sistem',
                    'method' => 'Email, Portal',
                    'message' => 'Makluman gangguan perkhidmatan',
                ],
            ],
            'templates' => [
                'initial_alert' => 'AMARAN: Bencana dikesan pada [TARIKH/MASA]. Jenis: [JENIS]. Pasukan DRP diaktifkan.',
                'status_update' => 'KEMAS KINI: Status pemulihan pada [MASA]. Fasa semasa: [FASA]. Anggaran siap: [MASA].',
                'resolution' => 'SELESAI: Perkhidmatan dipulihkan pada [MASA]. Tempoh gangguan: [TEMPOH].',
            ],
        ];
    }

    /**
     * Get annual testing schedule
     *
     * @return array<string, mixed>
     */
    public function getTestingSchedule(): array
    {
        $currentYear = (int) date('Y');

        return [
            'annual_schedule' => [
                [
                    'quarter' => 'Q1',
                    'month' => 'Mac',
                    'test_type' => self::TEST_TABLETOP,
                    'description' => 'Ujian meja - Semakan prosedur dan peranan',
                    'duration' => '2 jam',
                    'participants' => 'Pasukan DRP',
                ],
                [
                    'quarter' => 'Q2',
                    'month' => 'Jun',
                    'test_type' => self::TEST_WALKTHROUGH,
                    'description' => 'Ujian walkthrough - Simulasi langkah demi langkah',
                    'duration' => '4 jam',
                    'participants' => 'Pasukan DRP + Teknikal',
                ],
                [
                    'quarter' => 'Q3',
                    'month' => 'September',
                    'test_type' => self::TEST_SIMULATION,
                    'description' => 'Ujian simulasi - Failover tanpa gangguan',
                    'duration' => '1 hari',
                    'participants' => 'Semua pasukan',
                ],
                [
                    'quarter' => 'Q4',
                    'month' => 'Disember',
                    'test_type' => self::TEST_FULL,
                    'description' => 'Ujian penuh - Failover sebenar (di luar waktu pejabat)',
                    'duration' => '1 hari',
                    'participants' => 'Semua pasukan + Pengurusan',
                ],
            ],
            'next_test' => $this->getNextScheduledTest($currentYear),
            'compliance_note' => 'Ujian DRP tahunan adalah mandatori mengikut PKS 29.5',
        ];
    }

    /**
     * Get next scheduled test
     *
     * @return array<string, mixed>
     */
    private function getNextScheduledTest(int $year): array
    {
        $currentMonth = (int) date('n');

        $schedule = [
            3 => ['type' => self::TEST_TABLETOP, 'month' => 'Mac'],
            6 => ['type' => self::TEST_WALKTHROUGH, 'month' => 'Jun'],
            9 => ['type' => self::TEST_SIMULATION, 'month' => 'September'],
            12 => ['type' => self::TEST_FULL, 'month' => 'Disember'],
        ];

        foreach ($schedule as $month => $test) {
            if ($month > $currentMonth) {
                return [
                    'type' => $test['type'],
                    'month' => $test['month'],
                    'year' => $year,
                    'type_label' => $this->getTestTypeLabel($test['type']),
                ];
            }
        }

        // Next year Q1
        return [
            'type' => self::TEST_TABLETOP,
            'month' => 'Mac',
            'year' => $year + 1,
            'type_label' => $this->getTestTypeLabel(self::TEST_TABLETOP),
        ];
    }

    /**
     * Record DRP test result
     *
     * @param  array<string, mixed>  $testData
     * @return array<string, mixed>
     */
    public function recordTestResult(array $testData, int $userId): array
    {
        $testId = 'DRP_'.date('Ymd_His').'_'.substr(md5(uniqid()), 0, 6);

        $result = DrpTestResult::create([
            'test_id' => $testId,
            'test_type' => $testData['type'],
            'test_date' => $testData['date'] ?? now(),
            'conducted_by' => $userId,
            'status' => $testData['status'],
            'rto_achieved_minutes' => $testData['rto_achieved_minutes'] ?? null,
            'rpo_achieved_hours' => $testData['rpo_achieved_hours'] ?? null,
            'participants' => $testData['participants'] ?? [],
            'findings' => $testData['findings'] ?? [],
            'recommendations' => $testData['recommendations'] ?? [],
            'metadata' => $testData['metadata'] ?? [],
        ]);

        Log::info('DRP test result recorded', [
            'test_id' => $testId,
            'type' => $testData['type'],
            'status' => $testData['status'],
        ]);

        return [
            'success' => true,
            'test_id' => $testId,
            'message' => 'Keputusan ujian DRP berjaya direkodkan',
        ];
    }

    /**
     * Generate DRP test report
     *
     * @return array<string, mixed>
     */
    public function generateTestReport(string $testId): array
    {
        $result = DrpTestResult::where('test_id', $testId)->first();

        if (! $result) {
            return ['success' => false, 'error' => 'Ujian tidak ditemui'];
        }

        $rtoTarget = self::RTO_HOURS * 60; // in minutes
        $rtoAchieved = $result->rto_achieved_minutes ?? 0;
        $rtoCompliant = $rtoAchieved <= $rtoTarget;

        $rpoTarget = self::RPO_HOURS;
        $rpoAchieved = $result->rpo_achieved_hours ?? 0;
        $rpoCompliant = $rpoAchieved <= $rpoTarget;

        return [
            'report_info' => [
                'title' => 'Laporan Ujian DRP',
                'test_id' => $testId,
                'generated_at' => now()->format('d/m/Y H:i'),
            ],
            'test_details' => [
                'type' => $result->test_type,
                'type_label' => $this->getTestTypeLabel($result->test_type),
                'date' => $result->test_date->format('d/m/Y'),
                'status' => $result->status,
                'status_label' => $this->getStatusLabel($result->status),
            ],
            'compliance' => [
                'rto' => [
                    'target_minutes' => $rtoTarget,
                    'achieved_minutes' => $rtoAchieved,
                    'compliant' => $rtoCompliant,
                    'message' => $rtoCompliant
                        ? 'RTO dicapai dalam sasaran'
                        : 'RTO melebihi sasaran',
                ],
                'rpo' => [
                    'target_hours' => $rpoTarget,
                    'achieved_hours' => $rpoAchieved,
                    'compliant' => $rpoCompliant,
                    'message' => $rpoCompliant
                        ? 'RPO dicapai dalam sasaran'
                        : 'RPO melebihi sasaran',
                ],
            ],
            'participants' => $result->participants,
            'findings' => $result->findings,
            'recommendations' => $result->recommendations,
        ];
    }

    /**
     * Get test type label in Bahasa Melayu
     */
    public function getTestTypeLabel(string $type): string
    {
        return match ($type) {
            self::TEST_TABLETOP => 'Ujian Meja',
            self::TEST_WALKTHROUGH => 'Ujian Walkthrough',
            self::TEST_SIMULATION => 'Ujian Simulasi',
            self::TEST_FULL => 'Ujian Penuh',
            default => $type,
        };
    }

    /**
     * Get status label in Bahasa Melayu
     */
    public function getStatusLabel(string $status): string
    {
        return match ($status) {
            self::RESULT_PASSED => 'Lulus',
            self::RESULT_FAILED => 'Gagal',
            self::RESULT_PARTIAL => 'Separa Lulus',
            default => $status,
        };
    }

    /**
     * Get DRP test statistics
     *
     * @return array<string, mixed>
     */
    public function getTestStatistics(int $year): array
    {
        $tests = DrpTestResult::whereYear('test_date', $year)->get();

        return [
            'year' => $year,
            'total_tests' => $tests->count(),
            'passed' => $tests->where('status', self::RESULT_PASSED)->count(),
            'failed' => $tests->where('status', self::RESULT_FAILED)->count(),
            'partial' => $tests->where('status', self::RESULT_PARTIAL)->count(),
            'by_type' => [
                self::TEST_TABLETOP => $tests->where('test_type', self::TEST_TABLETOP)->count(),
                self::TEST_WALKTHROUGH => $tests->where('test_type', self::TEST_WALKTHROUGH)->count(),
                self::TEST_SIMULATION => $tests->where('test_type', self::TEST_SIMULATION)->count(),
                self::TEST_FULL => $tests->where('test_type', self::TEST_FULL)->count(),
            ],
            'compliance_rate' => $tests->count() > 0
                ? round(($tests->where('status', self::RESULT_PASSED)->count() / $tests->count()) * 100, 2)
                : 0,
        ];
    }
}

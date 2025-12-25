<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\DrpTestResult;
use App\Models\User;
use App\Services\DrpDocumentationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * DRP Documentation Service Test
 *
 * PKS Business Continuity (Requirement 29) - DRP Documentation Testing
 */
class DrpDocumentationServiceTest extends TestCase
{
    use RefreshDatabase;

    private DrpDocumentationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DrpDocumentationService;
    }

    public function test_get_drp_procedures_returns_complete_structure(): void
    {
        $procedures = $this->service->getDrpProcedures();

        $this->assertArrayHasKey('document_info', $procedures);
        $this->assertArrayHasKey('objectives', $procedures);
        $this->assertArrayHasKey('phases', $procedures);
        $this->assertArrayHasKey('roles', $procedures);
        $this->assertArrayHasKey('contact_list', $procedures);
        $this->assertArrayHasKey('recovery_procedures', $procedures);
        $this->assertArrayHasKey('communication_plan', $procedures);
        $this->assertArrayHasKey('testing_schedule', $procedures);
    }

    public function test_drp_procedures_contain_bahasa_melayu_content(): void
    {
        $procedures = $this->service->getDrpProcedures();

        $this->assertEquals('Pelan Pemulihan Bencana (DRP)', $procedures['document_info']['title']);
        $this->assertEquals('TERHAD', $procedures['document_info']['classification']);
    }

    public function test_drp_phases_are_complete(): void
    {
        $procedures = $this->service->getDrpProcedures();
        $phases = $procedures['phases'];

        $this->assertCount(5, $phases);
        $this->assertEquals('Pengesanan dan Penilaian', $phases[0]['name']);
        $this->assertEquals('Kembali ke Normal', $phases[4]['name']);
    }

    public function test_drp_roles_are_defined(): void
    {
        $procedures = $this->service->getDrpProcedures();
        $roles = $procedures['roles'];

        $this->assertArrayHasKey('drp_coordinator', $roles);
        $this->assertArrayHasKey('technical_lead', $roles);
        $this->assertEquals('Penyelaras DRP', $roles['drp_coordinator']['title']);
    }

    public function test_get_testing_schedule_returns_annual_schedule(): void
    {
        $schedule = $this->service->getTestingSchedule();

        $this->assertArrayHasKey('annual_schedule', $schedule);
        $this->assertArrayHasKey('next_test', $schedule);
        $this->assertCount(4, $schedule['annual_schedule']);
    }

    public function test_rto_constant_is_four_hours(): void
    {
        $this->assertEquals(4, DrpDocumentationService::RTO_HOURS);
    }

    public function test_rpo_constant_is_twenty_four_hours(): void
    {
        $this->assertEquals(24, DrpDocumentationService::RPO_HOURS);
    }

    public function test_record_test_result_creates_database_record(): void
    {
        $user = User::factory()->create();

        $testData = [
            'type' => DrpDocumentationService::TEST_TABLETOP,
            'status' => DrpDocumentationService::RESULT_PASSED,
            'rto_achieved_minutes' => 180,
            'rpo_achieved_hours' => 12,
            'participants' => ['Penyelaras DRP', 'Ketua Teknikal'],
            'findings' => ['Prosedur berjalan lancar'],
            'recommendations' => ['Tiada penambahbaikan diperlukan'],
        ];

        $result = $this->service->recordTestResult($testData, $user->id);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('test_id', $result);
        $this->assertStringStartsWith('DRP_', $result['test_id']);

        $this->assertDatabaseHas('drp_test_results', [
            'test_type' => 'tabletop',
            'status' => 'passed',
            'conducted_by' => $user->id,
        ]);
    }

    public function test_generate_test_report_returns_correct_format(): void
    {
        $user = User::factory()->create();

        DrpTestResult::create([
            'test_id' => 'DRP_TEST_001',
            'test_type' => DrpDocumentationService::TEST_SIMULATION,
            'test_date' => now(),
            'conducted_by' => $user->id,
            'status' => DrpDocumentationService::RESULT_PASSED,
            'rto_achieved_minutes' => 200,
            'rpo_achieved_hours' => 20,
            'participants' => ['Pasukan DRP'],
            'findings' => ['Ujian berjaya'],
            'recommendations' => [],
        ]);

        $report = $this->service->generateTestReport('DRP_TEST_001');

        $this->assertArrayHasKey('report_info', $report);
        $this->assertArrayHasKey('test_details', $report);
        $this->assertArrayHasKey('compliance', $report);
        $this->assertEquals('DRP_TEST_001', $report['report_info']['test_id']);
        $this->assertEquals('Ujian Simulasi', $report['test_details']['type_label']);
    }

    public function test_generate_test_report_returns_error_for_non_existent_test(): void
    {
        $report = $this->service->generateTestReport('NON_EXISTENT_TEST');

        $this->assertFalse($report['success']);
        $this->assertEquals('Ujian tidak ditemui', $report['error']);
    }

    public function test_compliance_check_for_rto_in_report(): void
    {
        $user = User::factory()->create();

        DrpTestResult::create([
            'test_id' => 'DRP_RTO_PASS',
            'test_type' => DrpDocumentationService::TEST_FULL,
            'test_date' => now(),
            'conducted_by' => $user->id,
            'status' => DrpDocumentationService::RESULT_PASSED,
            'rto_achieved_minutes' => 200,
            'rpo_achieved_hours' => 20,
        ]);

        $report = $this->service->generateTestReport('DRP_RTO_PASS');
        $this->assertTrue($report['compliance']['rto']['compliant']);

        DrpTestResult::create([
            'test_id' => 'DRP_RTO_FAIL',
            'test_type' => DrpDocumentationService::TEST_FULL,
            'test_date' => now(),
            'conducted_by' => $user->id,
            'status' => DrpDocumentationService::RESULT_FAILED,
            'rto_achieved_minutes' => 300,
            'rpo_achieved_hours' => 20,
        ]);

        $report = $this->service->generateTestReport('DRP_RTO_FAIL');
        $this->assertFalse($report['compliance']['rto']['compliant']);
    }

    public function test_test_type_labels_in_bahasa_melayu(): void
    {
        $this->assertEquals('Ujian Meja', $this->service->getTestTypeLabel(DrpDocumentationService::TEST_TABLETOP));
        $this->assertEquals('Ujian Walkthrough', $this->service->getTestTypeLabel(DrpDocumentationService::TEST_WALKTHROUGH));
        $this->assertEquals('Ujian Simulasi', $this->service->getTestTypeLabel(DrpDocumentationService::TEST_SIMULATION));
        $this->assertEquals('Ujian Penuh', $this->service->getTestTypeLabel(DrpDocumentationService::TEST_FULL));
    }

    public function test_status_labels_in_bahasa_melayu(): void
    {
        $this->assertEquals('Lulus', $this->service->getStatusLabel(DrpDocumentationService::RESULT_PASSED));
        $this->assertEquals('Gagal', $this->service->getStatusLabel(DrpDocumentationService::RESULT_FAILED));
        $this->assertEquals('Separa Lulus', $this->service->getStatusLabel(DrpDocumentationService::RESULT_PARTIAL));
    }

    public function test_get_test_statistics_returns_correct_counts(): void
    {
        $user = User::factory()->create();
        $currentYear = (int) date('Y');

        DrpTestResult::create([
            'test_id' => 'DRP_STAT_1',
            'test_type' => DrpDocumentationService::TEST_TABLETOP,
            'test_date' => now(),
            'conducted_by' => $user->id,
            'status' => DrpDocumentationService::RESULT_PASSED,
        ]);

        DrpTestResult::create([
            'test_id' => 'DRP_STAT_2',
            'test_type' => DrpDocumentationService::TEST_SIMULATION,
            'test_date' => now(),
            'conducted_by' => $user->id,
            'status' => DrpDocumentationService::RESULT_FAILED,
        ]);

        $stats = $this->service->getTestStatistics($currentYear);

        $this->assertEquals($currentYear, $stats['year']);
        $this->assertEquals(2, $stats['total_tests']);
        $this->assertEquals(1, $stats['passed']);
        $this->assertEquals(1, $stats['failed']);
    }

    public function test_recovery_procedures_contain_all_components(): void
    {
        $procedures = $this->service->getDrpProcedures();
        $recovery = $procedures['recovery_procedures'];

        $this->assertArrayHasKey('database', $recovery);
        $this->assertArrayHasKey('application', $recovery);
        $this->assertArrayHasKey('redis', $recovery);
    }

    public function test_emergency_contacts_include_internal_and_external(): void
    {
        $procedures = $this->service->getDrpProcedures();
        $contacts = $procedures['contact_list'];

        $this->assertArrayHasKey('internal', $contacts);
        $this->assertArrayHasKey('external', $contacts);
        $this->assertArrayHasKey('nacsa', $contacts['external']);
        $this->assertArrayHasKey('mycert', $contacts['external']);
    }
}

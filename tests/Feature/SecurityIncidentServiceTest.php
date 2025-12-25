<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\SecurityIncident;
use App\Models\SecurityIncidentLog;
use App\Models\User;
use App\Services\SecurityIncidentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Security Incident Service Test
 *
 * PKS CSIRT Integration (Requirement 28) - Tests for security incident management
 *
 * @see D03-FR-028 (CSIRT Integration)
 *
 * @trace Requirements 28.1, 28.2, 28.3, 28.4, 28.5
 */
class SecurityIncidentServiceTest extends TestCase
{
    use RefreshDatabase;

    private SecurityIncidentService $service;

    private User $superuser;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->superuser = User::factory()->create([
            'role' => 'superuser',
            'email' => 'superuser@motac.gov.my',
        ]);

        $this->service = app(SecurityIncidentService::class);
    }

    /**
     * Test incident creation with mandatory fields
     */
    public function test_can_create_security_incident(): void
    {
        $incidentData = [
            'type' => SecurityIncident::TYPE_BRUTE_FORCE,
            'severity' => SecurityIncident::SEVERITY_HIGH,
            'title' => 'Test Brute Force Attack',
            'description' => 'Test description for brute force attack',
            'source_ip' => '192.168.1.100',
        ];

        $incident = SecurityIncident::createIncident($incidentData);

        $this->assertNotNull($incident->id);
        $this->assertNotNull($incident->incident_number);
        $this->assertStringStartsWith('SEC', $incident->incident_number);
        $this->assertEquals(SecurityIncident::TYPE_BRUTE_FORCE, $incident->type);
        $this->assertEquals(SecurityIncident::SEVERITY_HIGH, $incident->severity);
        $this->assertEquals(SecurityIncident::STATUS_DETECTED, $incident->status);
        $this->assertTrue($incident->requires_escalation);
    }

    /**
     * Test incident number generation is unique
     */
    public function test_incident_number_is_unique(): void
    {
        $incident1 = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_UNAUTHORIZED_ACCESS,
            'severity' => SecurityIncident::SEVERITY_MEDIUM,
            'title' => 'Test Incident 1',
            'description' => 'Description 1',
        ]);

        $incident2 = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_ANOMALY,
            'severity' => SecurityIncident::SEVERITY_LOW,
            'title' => 'Test Incident 2',
            'description' => 'Description 2',
        ]);

        $this->assertNotEquals($incident1->incident_number, $incident2->incident_number);
    }

    /**
     * Test CSIRT escalation SLA tracking (PKS 28.4)
     */
    public function test_csirt_escalation_sla_tracking(): void
    {
        $incident = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_DATA_BREACH,
            'severity' => SecurityIncident::SEVERITY_CRITICAL,
            'title' => 'Critical Data Breach',
            'description' => 'Critical data breach detected',
        ]);

        // Initially, SLA should not be breached
        $this->assertFalse($incident->isCSIRTSLABreached());
        $this->assertNotNull($incident->getCSIRTSLATimeRemaining());

        // After escalation, SLA check should return null for time remaining
        $incident->escalateToCSIRT();
        $this->assertNull($incident->getCSIRTSLATimeRemaining());
    }

    /**
     * Test CSIRT escalation updates incident correctly
     */
    public function test_csirt_escalation_updates_incident(): void
    {
        $incident = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_MALWARE,
            'severity' => SecurityIncident::SEVERITY_HIGH,
            'title' => 'Malware Detected',
            'description' => 'Malware detected on system',
        ]);

        $this->assertNull($incident->escalated_at);
        $this->assertNull($incident->csirt_notified_at);

        $incident->escalateToCSIRT();

        $incident->refresh();
        $this->assertNotNull($incident->escalated_at);
        $this->assertNotNull($incident->csirt_notified_at);
        $this->assertEquals(SecurityIncident::STATUS_ESCALATED, $incident->status);
    }

    /**
     * Test NACSA report generation (PKS 28.2)
     */
    public function test_nacsa_report_generation(): void
    {
        $incident = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_DATA_BREACH,
            'severity' => SecurityIncident::SEVERITY_CRITICAL,
            'title' => 'Data Breach for NACSA',
            'description' => 'Critical data breach requiring NACSA reporting',
            'source_ip' => '10.0.0.1',
            'target_system' => 'ICTServe Database',
        ]);

        $report = $this->service->generateNACSAReport($incident);

        $this->assertEquals('NACSA_INCIDENT_REPORT', $report['report_type']);
        $this->assertArrayHasKey('organization', $report);
        $this->assertArrayHasKey('incident', $report);
        $this->assertEquals($incident->incident_number, $report['incident']['reference_number']);
        $this->assertEquals($incident->type, $report['incident']['type']);
        $this->assertEquals($incident->severity, $report['incident']['severity']);
    }

    /**
     * Test NACSA submission marks incident correctly
     */
    public function test_nacsa_submission_marks_incident(): void
    {
        $incident = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_DATA_BREACH,
            'severity' => SecurityIncident::SEVERITY_CRITICAL,
            'title' => 'Data Breach',
            'description' => 'Data breach for NACSA submission',
        ]);

        $this->assertNull($incident->nacsa_reported_at);
        $this->assertNull($incident->nacsa_report_id);

        $reportId = $this->service->submitToNACSA($incident);

        $incident->refresh();
        $this->assertNotNull($reportId);
        $this->assertNotNull($incident->nacsa_reported_at);
        $this->assertEquals($reportId, $incident->nacsa_report_id);
    }

    /**
     * Test MyCERT report generation (PKS 28.2)
     */
    public function test_mycert_report_generation(): void
    {
        $incident = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_PHISHING,
            'severity' => SecurityIncident::SEVERITY_HIGH,
            'title' => 'Phishing Attack',
            'description' => 'Phishing attack detected',
        ]);

        $report = $this->service->generateMyCERTReport($incident);

        $this->assertEquals('MYCERT_INCIDENT_REPORT', $report['report_type']);
        $this->assertArrayHasKey('reporter', $report);
        $this->assertArrayHasKey('incident_details', $report);
        $this->assertEquals('Fraud', $report['incident_details']['category']); // Phishing maps to Fraud
    }

    /**
     * Test MyCERT submission marks incident correctly
     */
    public function test_mycert_submission_marks_incident(): void
    {
        $incident = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_DOS_ATTACK,
            'severity' => SecurityIncident::SEVERITY_HIGH,
            'title' => 'DoS Attack',
            'description' => 'DoS attack for MyCERT submission',
        ]);

        $reportId = $this->service->submitToMyCERT($incident);

        $incident->refresh();
        $this->assertNotNull($reportId);
        $this->assertNotNull($incident->mycert_reported_at);
        $this->assertEquals($reportId, $incident->mycert_report_id);
    }

    /**
     * Test incident timeline tracking
     */
    public function test_incident_timeline_tracking(): void
    {
        $incident = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_UNAUTHORIZED_ACCESS,
            'severity' => SecurityIncident::SEVERITY_MEDIUM,
            'title' => 'Unauthorized Access',
            'description' => 'Unauthorized access attempt',
        ]);

        // Initial timeline should have creation entry
        $this->assertNotEmpty($incident->timeline);
        $this->assertCount(1, $incident->timeline);

        // Add timeline entry
        $incident->addTimelineEntry('Investigation started', 'Beginning investigation');

        $incident->refresh();
        $this->assertCount(2, $incident->timeline);
    }

    /**
     * Test incident response actions tracking
     */
    public function test_incident_response_actions_tracking(): void
    {
        $incident = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_BRUTE_FORCE,
            'severity' => SecurityIncident::SEVERITY_HIGH,
            'title' => 'Brute Force',
            'description' => 'Brute force attack',
        ]);

        $incident->addResponseAction('ip_blocked', 'IP address blocked');

        $incident->refresh();
        $this->assertNotEmpty($incident->response_actions);
        $this->assertEquals('ip_blocked', $incident->response_actions[0]['action_type']);
    }

    /**
     * Test incident containment
     */
    public function test_incident_containment(): void
    {
        $incident = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_MALWARE,
            'severity' => SecurityIncident::SEVERITY_HIGH,
            'title' => 'Malware',
            'description' => 'Malware detected',
        ]);

        $this->service->containIncident($incident, 'Malware isolated and quarantined');

        $incident->refresh();
        $this->assertEquals(SecurityIncident::STATUS_CONTAINED, $incident->status);
        $this->assertNotNull($incident->contained_at);
    }

    /**
     * Test incident resolution
     */
    public function test_incident_resolution(): void
    {
        $incident = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_UNAUTHORIZED_ACCESS,
            'severity' => SecurityIncident::SEVERITY_MEDIUM,
            'title' => 'Unauthorized Access',
            'description' => 'Unauthorized access',
        ]);

        $this->service->resolveIncident(
            $incident,
            'Access revoked and security measures enhanced',
            'Implement stronger access controls'
        );

        $incident->refresh();
        $this->assertEquals(SecurityIncident::STATUS_RESOLVED, $incident->status);
        $this->assertNotNull($incident->resolved_at);
        $this->assertNotNull($incident->resolution_summary);
        $this->assertNotNull($incident->lessons_learned);
    }

    /**
     * Test incident closure
     */
    public function test_incident_closure(): void
    {
        $incident = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_ANOMALY,
            'severity' => SecurityIncident::SEVERITY_LOW,
            'title' => 'Anomaly',
            'description' => 'Anomaly detected',
        ]);

        $incident->markResolved('Resolved', null);
        $this->service->closeIncident($incident);

        $incident->refresh();
        $this->assertEquals(SecurityIncident::STATUS_CLOSED, $incident->status);
        $this->assertNotNull($incident->closed_at);
    }

    /**
     * Test false positive marking
     */
    public function test_false_positive_marking(): void
    {
        $incident = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_ANOMALY,
            'severity' => SecurityIncident::SEVERITY_LOW,
            'title' => 'False Alarm',
            'description' => 'Suspected false alarm',
        ]);

        $this->service->markFalsePositive($incident, 'Confirmed as legitimate activity');

        $incident->refresh();
        $this->assertEquals(SecurityIncident::STATUS_FALSE_POSITIVE, $incident->status);
        $this->assertTrue($incident->is_false_positive);
    }

    /**
     * Test incident assignment
     */
    public function test_incident_assignment(): void
    {
        $incident = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_UNAUTHORIZED_ACCESS,
            'severity' => SecurityIncident::SEVERITY_MEDIUM,
            'title' => 'Unauthorized Access',
            'description' => 'Unauthorized access',
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $this->service->assignIncident($incident, $admin);

        $incident->refresh();
        $this->assertEquals($admin->id, $incident->assigned_to_user_id);
    }

    /**
     * Test incident log creation
     */
    public function test_incident_log_creation(): void
    {
        $incident = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_BRUTE_FORCE,
            'severity' => SecurityIncident::SEVERITY_HIGH,
            'title' => 'Brute Force',
            'description' => 'Brute force attack',
        ]);

        SecurityIncidentLog::log(
            $incident->id,
            SecurityIncidentLog::ACTION_COMMENT_ADDED,
            'Investigation notes added'
        );

        $logs = SecurityIncidentLog::where('security_incident_id', $incident->id)->get();
        $this->assertGreaterThanOrEqual(1, $logs->count());
    }

    /**
     * Test incident statistics (PKS 28.3)
     */
    public function test_incident_statistics(): void
    {
        // Create multiple incidents
        SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_BRUTE_FORCE,
            'severity' => SecurityIncident::SEVERITY_CRITICAL,
            'title' => 'Critical Incident',
            'description' => 'Critical',
        ]);

        SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_UNAUTHORIZED_ACCESS,
            'severity' => SecurityIncident::SEVERITY_HIGH,
            'title' => 'High Incident',
            'description' => 'High',
        ]);

        SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_ANOMALY,
            'severity' => SecurityIncident::SEVERITY_LOW,
            'title' => 'Low Incident',
            'description' => 'Low',
        ]);

        $stats = $this->service->getIncidentStats(24);

        $this->assertEquals(3, $stats['total_incidents']);
        $this->assertEquals(1, $stats['critical_count']);
        $this->assertEquals(1, $stats['high_count']);
        $this->assertArrayHasKey('by_type', $stats);
        $this->assertArrayHasKey('by_severity', $stats);
    }

    /**
     * Test open incidents query
     */
    public function test_open_incidents_query(): void
    {
        $openIncident = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_BRUTE_FORCE,
            'severity' => SecurityIncident::SEVERITY_HIGH,
            'title' => 'Open Incident',
            'description' => 'Open',
        ]);

        $closedIncident = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_ANOMALY,
            'severity' => SecurityIncident::SEVERITY_LOW,
            'title' => 'Closed Incident',
            'description' => 'Closed',
        ]);
        $closedIncident->close();

        $openIncidents = $this->service->getOpenIncidents();

        $this->assertEquals(1, $openIncidents->count());
        $this->assertEquals($openIncident->id, $openIncidents->first()->id);
    }

    /**
     * Test severity classification for escalation
     */
    public function test_severity_classification_for_escalation(): void
    {
        $criticalIncident = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_DATA_BREACH,
            'severity' => SecurityIncident::SEVERITY_CRITICAL,
            'title' => 'Critical',
            'description' => 'Critical incident',
        ]);

        $lowIncident = SecurityIncident::createIncident([
            'type' => SecurityIncident::TYPE_ANOMALY,
            'severity' => SecurityIncident::SEVERITY_LOW,
            'title' => 'Low',
            'description' => 'Low incident',
        ]);

        $this->assertTrue($criticalIncident->requires_escalation);
        $this->assertFalse($lowIncident->requires_escalation);
    }

    /**
     * Test incident type labels in Bahasa Melayu
     */
    public function test_incident_type_labels_bahasa_melayu(): void
    {
        $types = SecurityIncident::getTypes();

        $this->assertArrayHasKey(SecurityIncident::TYPE_UNAUTHORIZED_ACCESS, $types);
        $this->assertEquals('Akses Tanpa Kebenaran', $types[SecurityIncident::TYPE_UNAUTHORIZED_ACCESS]);
        $this->assertEquals('Pelanggaran Data', $types[SecurityIncident::TYPE_DATA_BREACH]);
    }

    /**
     * Test incident severity labels in Bahasa Melayu
     */
    public function test_incident_severity_labels_bahasa_melayu(): void
    {
        $severities = SecurityIncident::getSeverities();

        $this->assertArrayHasKey(SecurityIncident::SEVERITY_CRITICAL, $severities);
        $this->assertEquals('Kritikal', $severities[SecurityIncident::SEVERITY_CRITICAL]);
        $this->assertEquals('Tinggi', $severities[SecurityIncident::SEVERITY_HIGH]);
    }

    /**
     * Test incident status labels in Bahasa Melayu
     */
    public function test_incident_status_labels_bahasa_melayu(): void
    {
        $statuses = SecurityIncident::getStatuses();

        $this->assertArrayHasKey(SecurityIncident::STATUS_DETECTED, $statuses);
        $this->assertEquals('Dikesan', $statuses[SecurityIncident::STATUS_DETECTED]);
        $this->assertEquals('Dieskalasi', $statuses[SecurityIncident::STATUS_ESCALATED]);
    }
}

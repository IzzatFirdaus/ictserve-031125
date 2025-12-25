<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\DataResidencyLog;
use App\Models\DlpAuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * PKS 4.2 Data Sovereignty Compliance Tests
 *
 * Tests data residency logging and compliance monitoring.
 *
 * @see D03-FR-025 (Data Sovereignty Requirements)
 * @see PKS 4.2 (Data Residency Requirements)
 *
 * @trace Requirements 26.2, 26.4
 */
class DataSovereigntyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_log_data_residency_operation(): void
    {
        $user = User::factory()->create();

        $log = DataResidencyLog::logOperation([
            'user_id' => $user->id,
            'service' => 'ollama',
            'operation' => 'generate',
            'data_classification' => 'SENSITIVE',
            'processing_location' => 'local-server',
            'is_local_processing' => true,
            'is_compliant' => true,
            'model_id' => 'llama3.1',
            'response_time_ms' => 1500,
        ]);

        $this->assertDatabaseHas('data_residency_logs', [
            'id' => $log->id,
            'user_id' => $user->id,
            'service' => 'ollama',
            'operation' => 'generate',
            'data_classification' => 'SENSITIVE',
            'is_local_processing' => true,
            'is_compliant' => true,
        ]);
    }

    #[Test]
    public function local_processing_scope_filters_correctly(): void
    {
        $user = User::factory()->create();

        // Create local processing log
        DataResidencyLog::logOperation([
            'user_id' => $user->id,
            'service' => 'ollama',
            'operation' => 'generate',
            'data_classification' => 'SENSITIVE',
            'processing_location' => 'local-server',
            'is_local_processing' => true,
            'is_compliant' => true,
        ]);

        // Create cloud processing log
        DataResidencyLog::logOperation([
            'user_id' => $user->id,
            'service' => 'bedrock',
            'operation' => 'generate',
            'data_classification' => 'PUBLIC',
            'processing_location' => 'aws-ap-southeast-1',
            'is_local_processing' => false,
            'is_compliant' => true,
        ]);

        $this->assertEquals(1, DataResidencyLog::localOnly()->count());
        $this->assertEquals(1, DataResidencyLog::cloudProcessing()->count());
    }

    #[Test]
    public function sensitive_data_scope_filters_correctly(): void
    {
        $user = User::factory()->create();

        DataResidencyLog::logOperation([
            'user_id' => $user->id,
            'service' => 'ollama',
            'operation' => 'generate',
            'data_classification' => 'SENSITIVE',
            'processing_location' => 'local-server',
            'is_local_processing' => true,
            'is_compliant' => true,
        ]);

        DataResidencyLog::logOperation([
            'user_id' => $user->id,
            'service' => 'bedrock',
            'operation' => 'generate',
            'data_classification' => 'PUBLIC',
            'processing_location' => 'aws-ap-southeast-1',
            'is_local_processing' => false,
            'is_compliant' => true,
        ]);

        $this->assertEquals(1, DataResidencyLog::sensitive()->count());
    }

    #[Test]
    public function compliance_stats_calculation(): void
    {
        $user = User::factory()->create();

        // Create compliant logs
        for ($i = 0; $i < 8; $i++) {
            DataResidencyLog::logOperation([
                'user_id' => $user->id,
                'service' => 'ollama',
                'operation' => 'generate',
                'data_classification' => 'SENSITIVE',
                'processing_location' => 'local-server',
                'is_local_processing' => true,
                'is_compliant' => true,
            ]);
        }

        // Create non-compliant logs
        for ($i = 0; $i < 2; $i++) {
            DataResidencyLog::logOperation([
                'user_id' => $user->id,
                'service' => 'bedrock',
                'operation' => 'generate',
                'data_classification' => 'SENSITIVE',
                'processing_location' => 'aws-ap-southeast-1',
                'is_local_processing' => false,
                'is_compliant' => false,
            ]);
        }

        $stats = DataResidencyLog::getComplianceStats();

        $this->assertEquals(10, $stats['total_operations']);
        $this->assertEquals(8, $stats['compliant_count']);
        $this->assertEquals(2, $stats['non_compliant_count']);
        $this->assertEquals(80.0, $stats['compliance_rate']);
        $this->assertEquals(8, $stats['local_processing_count']);
        $this->assertEquals(2, $stats['cloud_processing_count']);
    }

    #[Test]
    public function dlp_audit_log_allowed_scope(): void
    {
        $user = User::factory()->create();

        // Create allowed log
        DlpAuditLog::logDecision([
            'classification' => DlpAuditLog::CLASSIFICATION_PUBLIC,
            'routing_decision' => DlpAuditLog::ROUTE_CLOUD_ALLOWED,
            'risk_score' => 10,
        ], [
            'user_id' => $user->id,
            'content_length' => 100,
        ]);

        // Create blocked log
        DlpAuditLog::logDecision([
            'classification' => DlpAuditLog::CLASSIFICATION_SENSITIVE,
            'routing_decision' => DlpAuditLog::ROUTE_BLOCKED,
            'risk_score' => 90,
        ], [
            'user_id' => $user->id,
            'content_length' => 100,
        ]);

        $this->assertEquals(1, DlpAuditLog::allowed()->count());
        $this->assertEquals(1, DlpAuditLog::blocked()->count());
    }

    #[Test]
    public function data_residency_log_belongs_to_user(): void
    {
        $user = User::factory()->create();

        $log = DataResidencyLog::logOperation([
            'user_id' => $user->id,
            'service' => 'ollama',
            'operation' => 'generate',
            'data_classification' => 'PUBLIC',
            'processing_location' => 'local-server',
            'is_local_processing' => true,
            'is_compliant' => true,
        ]);

        $this->assertInstanceOf(User::class, $log->user);
        $this->assertEquals($user->id, $log->user->id);
    }
}

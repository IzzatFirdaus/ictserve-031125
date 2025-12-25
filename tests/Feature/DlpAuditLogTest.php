<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\DlpAuditLog;
use App\Models\User;
use App\Services\DlpFilteringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * DLP Audit Log Tests
 *
 * Tests for PKS 9.2.1 DLP Audit Logging Compliance
 *
 * @see Requirements 25.4, 25.6 - DLP audit logging
 */
class DlpAuditLogTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function dlp_audit_log_can_be_created(): void
    {
        $user = User::factory()->create();

        $log = DlpAuditLog::create([
            'user_id' => $user->id,
            'session_id' => 'test-session-123',
            'classification' => DlpAuditLog::CLASSIFICATION_SENSITIVE,
            'routing_decision' => DlpAuditLog::ROUTE_LOCAL_ONLY,
            'risk_score' => 50,
            'detected_patterns' => [['type' => 'PII', 'subtype' => 'ic_number']],
            'content_length' => 100,
            'content_hash' => sha1('test content'),
            'target_provider' => DlpAuditLog::PROVIDER_OLLAMA,
            'operation_type' => 'text_generation',
            'source_component' => 'ModelRouter',
            'processed_at' => now(),
        ]);

        $this->assertDatabaseHas('dlp_audit_logs', [
            'id' => $log->id,
            'classification' => DlpAuditLog::CLASSIFICATION_SENSITIVE,
            'routing_decision' => DlpAuditLog::ROUTE_LOCAL_ONLY,
        ]);
    }

    #[Test]
    public function log_decision_static_method_works(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $dlpResult = [
            'classification' => DlpFilteringService::CLASSIFICATION_SENSITIVE,
            'routing_decision' => DlpFilteringService::ROUTE_LOCAL_ONLY,
            'risk_score' => 30,
            'detected_patterns' => [['type' => 'PII', 'subtype' => 'email']],
        ];

        $log = DlpAuditLog::logDecision($dlpResult, [
            'content_length' => 50,
            'content_hash' => sha1('test'),
            'target_provider' => DlpAuditLog::PROVIDER_OLLAMA,
            'operation_type' => 'text_generation',
            'source_component' => 'ModelRouter',
        ]);

        $this->assertInstanceOf(DlpAuditLog::class, $log);
        $this->assertEquals($user->id, $log->user_id);
        $this->assertEquals(DlpFilteringService::CLASSIFICATION_SENSITIVE, $log->classification);
    }

    #[Test]
    public function sensitive_scope_filters_correctly(): void
    {
        DlpAuditLog::create([
            'classification' => DlpAuditLog::CLASSIFICATION_SENSITIVE,
            'routing_decision' => DlpAuditLog::ROUTE_LOCAL_ONLY,
            'risk_score' => 50,
            'content_length' => 100,
            'target_provider' => DlpAuditLog::PROVIDER_OLLAMA,
            'processed_at' => now(),
        ]);

        DlpAuditLog::create([
            'classification' => DlpAuditLog::CLASSIFICATION_PUBLIC,
            'routing_decision' => DlpAuditLog::ROUTE_CLOUD_ALLOWED,
            'risk_score' => 0,
            'content_length' => 50,
            'target_provider' => DlpAuditLog::PROVIDER_BEDROCK,
            'processed_at' => now(),
        ]);

        $sensitiveCount = DlpAuditLog::sensitive()->count();
        $this->assertEquals(1, $sensitiveCount);
    }

    #[Test]
    public function high_risk_scope_filters_correctly(): void
    {
        DlpAuditLog::create([
            'classification' => DlpAuditLog::CLASSIFICATION_SENSITIVE,
            'routing_decision' => DlpAuditLog::ROUTE_LOCAL_ONLY,
            'risk_score' => 75,
            'content_length' => 100,
            'target_provider' => DlpAuditLog::PROVIDER_OLLAMA,
            'processed_at' => now(),
        ]);

        DlpAuditLog::create([
            'classification' => DlpAuditLog::CLASSIFICATION_SENSITIVE,
            'routing_decision' => DlpAuditLog::ROUTE_LOCAL_ONLY,
            'risk_score' => 25,
            'content_length' => 50,
            'target_provider' => DlpAuditLog::PROVIDER_OLLAMA,
            'processed_at' => now(),
        ]);

        $highRiskCount = DlpAuditLog::highRisk(50)->count();
        $this->assertEquals(1, $highRiskCount);
    }

    #[Test]
    public function get_summary_stats_returns_correct_structure(): void
    {
        DlpAuditLog::create([
            'classification' => DlpAuditLog::CLASSIFICATION_SENSITIVE,
            'routing_decision' => DlpAuditLog::ROUTE_LOCAL_ONLY,
            'risk_score' => 50,
            'content_length' => 100,
            'target_provider' => DlpAuditLog::PROVIDER_OLLAMA,
            'processed_at' => now(),
        ]);

        $stats = DlpAuditLog::getSummaryStats(
            now()->subDay()->toDateString(),
            now()->addDay()->toDateString()
        );

        $this->assertArrayHasKey('total_requests', $stats);
        $this->assertArrayHasKey('sensitive_count', $stats);
        $this->assertArrayHasKey('blocked_count', $stats);
        $this->assertArrayHasKey('cloud_allowed', $stats);
        $this->assertArrayHasKey('local_only', $stats);
        $this->assertArrayHasKey('avg_risk_score', $stats);
        $this->assertArrayHasKey('by_classification', $stats);
        $this->assertArrayHasKey('by_provider', $stats);
    }

    #[Test]
    public function user_relationship_works(): void
    {
        $user = User::factory()->create();

        $log = DlpAuditLog::create([
            'user_id' => $user->id,
            'classification' => DlpAuditLog::CLASSIFICATION_PUBLIC,
            'routing_decision' => DlpAuditLog::ROUTE_CLOUD_ALLOWED,
            'risk_score' => 0,
            'content_length' => 50,
            'target_provider' => DlpAuditLog::PROVIDER_BEDROCK,
            'processed_at' => now(),
        ]);

        $this->assertInstanceOf(User::class, $log->user);
        $this->assertEquals($user->id, $log->user->id);
    }
}

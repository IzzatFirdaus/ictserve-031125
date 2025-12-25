<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Services\DlpFilteringService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * DLP Filtering Service Tests
 *
 * Tests for PKS 9.2.1 Data Transfer and DLP Compliance
 *
 * @see Requirements 25.1, 25.2, 25.3 - PKS 9.2.1 Data Transfer Compliance
 */
class DlpFilteringServiceTest extends TestCase
{
    private DlpFilteringService $dlpService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dlpService = new DlpFilteringService;
    }

    /**
     * Test that public content is classified correctly
     */
    #[Test]
    public function public_content_is_classified_as_public(): void
    {
        $content = 'This is a general question about ICT services.';

        $result = $this->dlpService->classifyData($content);

        $this->assertEquals(DlpFilteringService::CLASSIFICATION_PUBLIC, $result['classification']);
        $this->assertEquals(DlpFilteringService::ROUTE_CLOUD_ALLOWED, $result['routing_decision']);
        $this->assertEquals(0, $result['risk_score']);
    }

    /**
     * Test that Malaysian IC numbers are detected as PII
     */
    #[Test]
    public function malaysian_ic_number_is_detected(): void
    {
        $content = 'My IC number is 850101-14-5678';

        $result = $this->dlpService->classifyData($content);

        $this->assertEquals(DlpFilteringService::CLASSIFICATION_SENSITIVE, $result['classification']);
        $this->assertEquals(DlpFilteringService::ROUTE_LOCAL_ONLY, $result['routing_decision']);
        $this->assertGreaterThan(0, $result['risk_score']);
        $this->assertNotEmpty($result['detected_patterns']);
    }

    /**
     * Test that phone numbers are detected as PII
     */
    #[Test]
    public function phone_number_is_detected(): void
    {
        $content = 'Contact me at 012-3456789';

        $result = $this->dlpService->classifyData($content);

        $this->assertEquals(DlpFilteringService::CLASSIFICATION_SENSITIVE, $result['classification']);
        $this->assertEquals(DlpFilteringService::ROUTE_LOCAL_ONLY, $result['routing_decision']);
    }

    /**
     * Test that email addresses are detected as PII
     */
    #[Test]
    public function email_is_detected(): void
    {
        $content = 'Send to user@motac.gov.my';

        $result = $this->dlpService->classifyData($content);

        $this->assertEquals(DlpFilteringService::CLASSIFICATION_SENSITIVE, $result['classification']);
        $this->assertEquals(DlpFilteringService::ROUTE_LOCAL_ONLY, $result['routing_decision']);
    }

    /**
     * Test that classified keywords are detected
     */
    #[Test]
    public function classified_keywords_are_detected(): void
    {
        $content = 'This document is SULIT and contains sensitive information.';

        $result = $this->dlpService->classifyData($content);

        $this->assertEquals(DlpFilteringService::CLASSIFICATION_SENSITIVE, $result['classification']);
        $this->assertEquals(DlpFilteringService::ROUTE_LOCAL_ONLY, $result['routing_decision']);
    }

    /**
     * Test that HRMIS references are detected
     */
    #[Test]
    public function hrmis_reference_is_detected(): void
    {
        $content = 'Please check the HRMIS system for employee details.';

        $result = $this->dlpService->classifyData($content);

        $this->assertEquals(DlpFilteringService::CLASSIFICATION_SENSITIVE, $result['classification']);
        $this->assertEquals(DlpFilteringService::ROUTE_LOCAL_ONLY, $result['routing_decision']);
    }

    /**
     * Test that salary information is detected
     */
    #[Test]
    public function salary_information_is_detected(): void
    {
        $content = 'The employee salary is RM5000 per month.';

        $result = $this->dlpService->classifyData($content);

        $this->assertEquals(DlpFilteringService::CLASSIFICATION_SENSITIVE, $result['classification']);
        $this->assertEquals(DlpFilteringService::ROUTE_LOCAL_ONLY, $result['routing_decision']);
    }

    /**
     * Test canSendToCloud returns true for public content
     */
    #[Test]
    public function can_send_to_cloud_returns_true_for_public_content(): void
    {
        $content = 'What are the office hours?';

        $result = $this->dlpService->canSendToCloud($content);

        $this->assertTrue($result);
    }

    /**
     * Test canSendToCloud returns false for sensitive content
     */
    #[Test]
    public function can_send_to_cloud_returns_false_for_sensitive_content(): void
    {
        $content = 'My IC is 900101-14-1234';

        $result = $this->dlpService->canSendToCloud($content);

        $this->assertFalse($result);
    }

    /**
     * Test filterForCloud blocks sensitive content
     */
    #[Test]
    public function filter_for_cloud_blocks_sensitive_content(): void
    {
        $content = 'This is RAHSIA information about the minister.';

        $result = $this->dlpService->filterForCloud($content);

        $this->assertTrue($result['blocked']);
        $this->assertNull($result['filtered_content']);
        $this->assertStringContainsString('PKS 9.2.1', $result['reason']);
    }

    /**
     * Test filterForCloud allows public content
     */
    #[Test]
    public function filter_for_cloud_allows_public_content(): void
    {
        $content = 'What are the office hours?';

        $result = $this->dlpService->filterForCloud($content);

        $this->assertFalse($result['blocked']);
        $this->assertEquals($content, $result['filtered_content']);
        $this->assertNull($result['reason']);
    }

    /**
     * Test sanitizeContent redacts PII
     */
    #[Test]
    public function sanitize_content_redacts_pii(): void
    {
        $content = 'Contact 012-3456789 or email test@example.com';

        $result = $this->dlpService->sanitizeContent($content);

        $this->assertStringContainsString('[PHONE REDACTED]', $result);
        $this->assertStringContainsString('[EMAIL REDACTED]', $result);
        $this->assertStringNotContainsString('012-3456789', $result);
        $this->assertStringNotContainsString('test@example.com', $result);
    }

    /**
     * Test getConfiguration returns expected structure
     */
    #[Test]
    public function get_configuration_returns_expected_structure(): void
    {
        $config = $this->dlpService->getConfiguration();

        $this->assertArrayHasKey('classification_levels', $config);
        $this->assertArrayHasKey('routing_decisions', $config);
        $this->assertArrayHasKey('pii_patterns_count', $config);
        $this->assertArrayHasKey('classified_keywords_count', $config);
        $this->assertContains(DlpFilteringService::CLASSIFICATION_SENSITIVE, $config['classification_levels']);
        $this->assertContains(DlpFilteringService::CLASSIFICATION_PUBLIC, $config['classification_levels']);
    }

    /**
     * Test getDetailedAnalysis returns recommendations
     */
    #[Test]
    public function get_detailed_analysis_returns_recommendations(): void
    {
        $content = 'This is SULIT information.';

        $result = $this->dlpService->getDetailedAnalysis($content);

        $this->assertArrayHasKey('summary', $result);
        $this->assertArrayHasKey('patterns_detected', $result);
        $this->assertArrayHasKey('metadata', $result);
        $this->assertArrayHasKey('recommendations', $result);
        $this->assertNotEmpty($result['recommendations']);
    }

    /**
     * Test user_id is included in analysis when provided
     */
    #[Test]
    public function user_id_is_included_in_analysis(): void
    {
        $content = 'Test content';
        $userId = 123;

        $result = $this->dlpService->classifyData($content, $userId);

        $this->assertEquals($userId, $result['user_id']);
    }

    /**
     * Detects internal MOTAC reference numbers
     */
    #[Test]
    public function internal_motac_references_are_detected(): void
    {
        $content = 'Reference number BPM/2024/0001';

        $result = $this->dlpService->classifyData($content);

        $this->assertEquals(DlpFilteringService::CLASSIFICATION_SENSITIVE, $result['classification']);
    }
}

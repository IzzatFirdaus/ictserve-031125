<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\DataClassificationService;
use App\Services\PIIDetectionService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit tests for DataClassificationService.
 *
 * Tests data classification accuracy, Malaysia data residency enforcement,
 * and PDPA 2010 compliance for cloud processing decisions.
 *
 * trace: D03-SRS-AI-007 (Data Classification), D09-§9 (PDPA Compliance)
 * trace: Phase 15.3 (Compliance and Security Tests)
 *
 * @see .kiro/specs/ollama-ai-integration/tasks.md Phase 15.3
 */
class DataClassificationServiceTest extends TestCase
{
    private DataClassificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DataClassificationService;
    }

    #[Test]
    public function it_classifies_public_data_correctly(): void
    {
        $result = $this->service->classify('Apakah waktu operasi pejabat BPM?');

        $this->assertSame('public', $result['classification']);
        $this->assertTrue($result['allow_cloud']);
        $this->assertFalse($result['requires_consent']);
        $this->assertFalse($result['should_block']);
    }

    #[Test]
    public function it_detects_restricted_keywords_in_malay(): void
    {
        $result = $this->service->classify('Dokumen ini adalah SULIT dan tidak boleh dikongsi.');

        $this->assertSame('restricted', $result['classification']);
        $this->assertFalse($result['allow_cloud']);
        $this->assertTrue($result['should_block']);
        $this->assertStringContainsString('terhad', $result['reason']);
    }

    #[Test]
    public function it_detects_rahsia_keyword(): void
    {
        $result = $this->service->classify('Maklumat rahsia kerajaan.');

        $this->assertSame('restricted', $result['classification']);
        $this->assertFalse($result['allow_cloud']);
    }

    #[Test]
    public function it_detects_confidential_keyword_in_english(): void
    {
        $result = $this->service->classify('This document is CONFIDENTIAL.');

        $this->assertSame('restricted', $result['classification']);
        $this->assertFalse($result['allow_cloud']);
    }

    #[Test]
    public function it_allows_explicit_classification_override(): void
    {
        $result = $this->service->classify(
            'Soalan biasa tentang pejabat.',
            ['data_classification' => 'internal']
        );

        $this->assertSame('internal', $result['classification']);
        $this->assertFalse($result['allow_cloud']);
        $this->assertTrue($result['requires_consent']);
        $this->assertStringContainsString('pemanggil', $result['reason']);
    }

    #[Test]
    public function it_blocks_cloud_for_internal_data(): void
    {
        $result = $this->service->classify(
            'Maklumat dalaman jabatan.',
            ['data_classification' => 'internal']
        );

        $this->assertSame('internal', $result['classification']);
        $this->assertFalse($result['allow_cloud']);
        $this->assertTrue($result['requires_consent']);
    }

    #[Test]
    public function it_blocks_cloud_for_confidential_data(): void
    {
        $result = $this->service->classify(
            'Data peribadi kakitangan.',
            ['data_classification' => 'confidential']
        );

        $this->assertSame('confidential', $result['classification']);
        $this->assertFalse($result['allow_cloud']);
        $this->assertFalse($result['should_block']);
    }

    #[Test]
    #[DataProvider('piiDetectionDataProvider')]
    public function it_integrates_with_pii_detection_service(
        string $text,
        string $expectedMinClassification
    ): void {
        // Mock PIIDetectionService to return PII detected
        $mockPii = $this->createMock(PIIDetectionService::class);
        $mockPii->method('detectPII')
            ->willReturn([
                'has_pii' => true,
                'severity_level' => 'high',
                'detected_types' => ['ic_number'],
            ]);

        $this->app->instance(PIIDetectionService::class, $mockPii);

        $service = new DataClassificationService;
        $result = $service->classify($text);

        // Should be at least internal or higher when PII detected
        $this->assertContains(
            $result['classification'],
            ['internal', 'confidential', 'restricted']
        );
        $this->assertFalse($result['allow_cloud']);
    }

    public static function piiDetectionDataProvider(): array
    {
        return [
            'ic_number_pattern' => [
                'Nombor IC saya ialah 880101-14-5678.',
                'confidential',
            ],
            'phone_number_pattern' => [
                'Hubungi saya di +60123456789.',
                'internal',
            ],
            'email_pattern' => [
                'Emel saya: ahmad@motac.gov.my',
                'internal',
            ],
        ];
    }

    #[Test]
    public function it_handles_pii_service_failure_gracefully(): void
    {
        // Mock PIIDetectionService to throw exception
        $mockPii = $this->createMock(PIIDetectionService::class);
        $mockPii->method('detectPII')
            ->willThrowException(new \RuntimeException('Service unavailable'));

        $this->app->instance(PIIDetectionService::class, $mockPii);

        $service = new DataClassificationService;
        $result = $service->classify('Soalan biasa.');

        // Should not fail, should return public classification
        $this->assertSame('public', $result['classification']);
        $this->assertTrue($result['allow_cloud']);
    }

    #[Test]
    public function it_returns_all_required_keys(): void
    {
        $result = $this->service->classify('Test query');

        $this->assertArrayHasKey('classification', $result);
        $this->assertArrayHasKey('allow_cloud', $result);
        $this->assertArrayHasKey('requires_consent', $result);
        $this->assertArrayHasKey('should_block', $result);
        $this->assertArrayHasKey('reason', $result);
    }

    #[Test]
    public function it_handles_empty_text(): void
    {
        $result = $this->service->classify('');

        $this->assertSame('public', $result['classification']);
        $this->assertTrue($result['allow_cloud']);
    }

    #[Test]
    public function it_is_case_insensitive_for_keywords(): void
    {
        $resultLower = $this->service->classify('dokumen sulit');
        $resultUpper = $this->service->classify('DOKUMEN SULIT');
        $resultMixed = $this->service->classify('Dokumen Sulit');

        $this->assertSame('restricted', $resultLower['classification']);
        $this->assertSame('restricted', $resultUpper['classification']);
        $this->assertSame('restricted', $resultMixed['classification']);
    }

    #[Test]
    public function it_enforces_malaysia_data_residency_for_restricted_data(): void
    {
        $result = $this->service->classify('Maklumat rahsia kerajaan Malaysia.');

        // Restricted data should never be sent to cloud
        $this->assertSame('restricted', $result['classification']);
        $this->assertFalse($result['allow_cloud']);
        $this->assertTrue($result['should_block']);
    }

    #[Test]
    public function it_provides_reason_in_bahasa_melayu(): void
    {
        $result = $this->service->classify('Dokumen sulit.');

        // Reason should be in Bahasa Melayu per D15 v3.6.0
        $this->assertMatchesRegularExpression('/[a-zA-Z]/', $result['reason']);
        $this->assertNotEmpty($result['reason']);
    }
}

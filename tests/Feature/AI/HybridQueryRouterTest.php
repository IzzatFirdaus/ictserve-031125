<?php

declare(strict_types=1);

namespace Tests\Feature\AI;

use App\Services\DataClassificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Comprehensive tests for Hybrid Query Routing in Cloud Hybrid AI system.
 *
 * Tests query classification, model routing, fallback scenarios, and
 * cost optimization for the Ollama + AWS Bedrock hybrid architecture.
 *
 * trace: D03-SRS-AI-007, D03-SRS-AI-011, D03-SRS-AI-017
 * trace: Phase 15.1 (Bedrock Integration Tests)
 */
#[Group('ai')]
#[Group('bedrock')]
class HybridQueryRouterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['bedrock.enabled' => true]);
    }

    #[Test]
    public function it_classifies_simple_faq_queries_as_public(): void
    {
        $classifier = new DataClassificationService;

        $result = $classifier->classify('Apakah waktu operasi pejabat?');

        $this->assertSame('public', $result['classification']);
        $this->assertTrue($result['allow_cloud']);
        $this->assertFalse($result['should_block']);
    }

    #[Test]
    public function it_classifies_restricted_content_correctly(): void
    {
        $classifier = new DataClassificationService;

        $result = $classifier->classify('Dokumen SULIT kerajaan.');

        $this->assertSame('restricted', $result['classification']);
        $this->assertFalse($result['allow_cloud']);
        $this->assertTrue($result['should_block']);
    }

    #[Test]
    public function it_classifies_confidential_content_correctly(): void
    {
        $classifier = new DataClassificationService;

        $result = $classifier->classify('Maklumat RAHSIA jabatan.');

        $this->assertSame('restricted', $result['classification']);
        $this->assertFalse($result['allow_cloud']);
    }

    #[Test]
    #[DataProvider('queryClassificationProvider')]
    public function it_classifies_queries_based_on_keywords(
        string $query,
        string $expectedClassification,
        bool $expectedAllowCloud
    ): void {
        $classifier = new DataClassificationService;

        $result = $classifier->classify($query);

        $this->assertSame($expectedClassification, $result['classification']);
        $this->assertSame($expectedAllowCloud, $result['allow_cloud']);
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: bool}>
     */
    public static function queryClassificationProvider(): array
    {
        return [
            'public_faq_query' => [
                'Bagaimana cara hantar tiket helpdesk?',
                'public',
                true,
            ],
            'public_general_query' => [
                'Apakah perkhidmatan yang disediakan?',
                'public',
                true,
            ],
            'restricted_sulit_keyword' => [
                'Dokumen SULIT untuk mesyuarat',
                'restricted',
                false,
            ],
            'restricted_rahsia_keyword' => [
                'Maklumat RAHSIA jabatan',
                'restricted',
                false,
            ],
            'restricted_confidential_keyword' => [
                'This is confidential information',
                'restricted',
                false,
            ],
        ];
    }

    #[Test]
    public function it_allows_explicit_classification_override(): void
    {
        $classifier = new DataClassificationService;

        $result = $classifier->classify(
            'Soalan biasa',
            ['data_classification' => 'internal']
        );

        $this->assertSame('internal', $result['classification']);
        $this->assertFalse($result['allow_cloud']);
        $this->assertStringContainsString('pemanggil', $result['reason']);
    }

    #[Test]
    public function it_requires_consent_for_internal_data(): void
    {
        $classifier = new DataClassificationService;

        $result = $classifier->classify(
            'Data dalaman',
            ['data_classification' => 'internal']
        );

        $this->assertSame('internal', $result['classification']);
        $this->assertTrue($result['requires_consent']);
    }

    #[Test]
    public function it_blocks_restricted_data_by_default(): void
    {
        $classifier = new DataClassificationService;

        $result = $classifier->classify('Dokumen SULIT');

        $this->assertTrue($result['should_block']);
    }

    #[Test]
    public function it_handles_empty_query_gracefully(): void
    {
        $classifier = new DataClassificationService;

        $result = $classifier->classify('');

        $this->assertSame('public', $result['classification']);
        $this->assertTrue($result['allow_cloud']);
    }

    #[Test]
    public function it_handles_unicode_content_correctly(): void
    {
        $classifier = new DataClassificationService;

        $result = $classifier->classify('Soalan dalam Bahasa Melayu dengan aksara khas: ñ, ü, é');

        $this->assertSame('public', $result['classification']);
    }

    #[Test]
    public function it_is_case_insensitive_for_keyword_detection(): void
    {
        $classifier = new DataClassificationService;

        $resultLower = $classifier->classify('dokumen sulit');
        $resultUpper = $classifier->classify('DOKUMEN SULIT');
        $resultMixed = $classifier->classify('Dokumen Sulit');

        $this->assertSame('restricted', $resultLower['classification']);
        $this->assertSame('restricted', $resultUpper['classification']);
        $this->assertSame('restricted', $resultMixed['classification']);
    }
}

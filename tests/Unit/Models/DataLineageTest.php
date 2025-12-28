<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\DataLineage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit Tests for DataLineage Model
 *
 * @requirements 6.5, 1.7, 3.6
 *
 * @compliance D09 v3.6.0 Dual Audit System, PDPA 2010
 */
class DataLineageTest extends TestCase
{
    #[Test]
    public function it_has_correct_fillable_attributes(): void
    {
        $dataLineage = new DataLineage;

        $expected = [
            'lineage_id',
            'source_type',
            'source_id',
            'transformation_type',
            'transformation_metadata',
            'destination_type',
            'destination_id',
            'processed_at',
        ];

        $this->assertEquals($expected, $dataLineage->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $dataLineage = new DataLineage;

        $casts = $dataLineage->getCasts();

        $this->assertEquals('array', $casts['transformation_metadata']);
        $this->assertEquals('datetime', $casts['processed_at']);
    }

    #[Test]
    public function it_can_filter_by_source_type(): void
    {
        DataLineage::factory()->create(['source_type' => 'helpdesk_ticket']);
        DataLineage::factory()->create(['source_type' => 'document']);
        DataLineage::factory()->create(['source_type' => 'helpdesk_ticket']);

        $results = DataLineage::bySourceType('helpdesk_ticket')->get();

        $this->assertCount(2, $results);
        foreach ($results as $lineage) {
            $this->assertEquals('helpdesk_ticket', $lineage->source_type);
        }
    }

    #[Test]
    public function it_can_filter_by_transformation_type(): void
    {
        DataLineage::factory()->create(['transformation_type' => 'embedding_generation']);
        DataLineage::factory()->create(['transformation_type' => 'text_sanitization']);
        DataLineage::factory()->create(['transformation_type' => 'embedding_generation']);

        $results = DataLineage::byTransformationType('embedding_generation')->get();

        $this->assertCount(2, $results);
        foreach ($results as $lineage) {
            $this->assertEquals('embedding_generation', $lineage->transformation_type);
        }
    }

    #[Test]
    public function it_can_filter_by_source(): void
    {
        DataLineage::factory()->create([
            'source_type' => 'helpdesk_ticket',
            'source_id' => 1,
        ]);
        DataLineage::factory()->create([
            'source_type' => 'helpdesk_ticket',
            'source_id' => 2,
        ]);
        DataLineage::factory()->create([
            'source_type' => 'document',
            'source_id' => 1,
        ]);

        $results = DataLineage::bySource('helpdesk_ticket', 1)->get();

        $this->assertCount(1, $results);
        $lineage = $results->first();
        $this->assertEquals('helpdesk_ticket', $lineage->source_type);
        $this->assertEquals(1, $lineage->source_id);
    }

    #[Test]
    public function it_can_filter_by_destination_type_only(): void
    {
        DataLineage::factory()->create(['destination_type' => 'faq']);
        DataLineage::factory()->create(['destination_type' => 'document_chunk']);
        DataLineage::factory()->create(['destination_type' => 'faq']);

        $results = DataLineage::byDestination('faq')->get();

        $this->assertCount(2, $results);
        foreach ($results as $lineage) {
            $this->assertEquals('faq', $lineage->destination_type);
        }
    }

    #[Test]
    public function it_can_filter_by_destination_with_id(): void
    {
        DataLineage::factory()->create([
            'destination_type' => 'faq',
            'destination_id' => 1,
        ]);
        DataLineage::factory()->create([
            'destination_type' => 'faq',
            'destination_id' => 2,
        ]);

        $results = DataLineage::byDestination('faq', 1)->get();

        $this->assertCount(1, $results);
        $lineage = $results->first();
        $this->assertEquals('faq', $lineage->destination_type);
        $this->assertEquals(1, $lineage->destination_id);
    }

    #[Test]
    public function it_generates_transformation_description(): void
    {
        $lineage = DataLineage::factory()->create([
            'source_type' => 'helpdesk_ticket',
            'source_id' => 123,
            'destination_type' => 'faq',
            'transformation_type' => 'ai_analysis',
        ]);

        $expected = 'Helpdesk_ticket (helpdesk_ticket:123) → Faq melalui ai_analysis';
        $this->assertEquals($expected, $lineage->transformation_description);
    }

    #[Test]
    public function it_stores_transformation_metadata_as_array(): void
    {
        $metadata = [
            'model' => 'llama3.1',
            'confidence' => 0.95,
            'processing_time_ms' => 1500,
            'tokens_used' => 250,
        ];

        $lineage = DataLineage::factory()->create([
            'transformation_metadata' => $metadata,
        ]);

        $this->assertEquals($metadata, $lineage->fresh()->transformation_metadata);
        $this->assertIsArray($lineage->fresh()->transformation_metadata);
    }

    #[Test]
    public function it_stores_processed_at_as_datetime(): void
    {
        $processedAt = now()->subHours(3);
        $lineage = DataLineage::factory()->create(['processed_at' => $processedAt]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $lineage->fresh()->processed_at);
        $this->assertEquals(
            $processedAt->format('Y-m-d H:i:s'),
            $lineage->fresh()->processed_at->format('Y-m-d H:i:s')
        );
    }

    #[Test]
    public function it_implements_auditable_contract(): void
    {
        $lineage = new DataLineage;

        $this->assertInstanceOf(\OwenIt\Auditing\Contracts\Auditable::class, $lineage);
    }

    #[Test]
    public function it_uses_logs_activity_trait(): void
    {
        $lineage = new DataLineage;

        $this->assertTrue(method_exists($lineage, 'getActivitylogOptions'));
    }

    #[Test]
    public function it_has_correct_table_name(): void
    {
        $lineage = new DataLineage;

        $this->assertEquals('data_lineage', $lineage->getTable());
    }

    #[Test]
    public function it_has_correct_activity_log_configuration(): void
    {
        $lineage = new DataLineage;
        $options = $lineage->getActivitylogOptions();

        $this->assertInstanceOf(\Spatie\Activitylog\LogOptions::class, $options);
    }

    #[Test]
    public function it_can_handle_null_destination_id(): void
    {
        $lineage = DataLineage::factory()->create(['destination_id' => null]);

        $this->assertNull($lineage->fresh()->destination_id);
    }

    #[Test]
    public function it_can_handle_complex_transformation_metadata(): void
    {
        $complexMetadata = [
            'source_analysis' => [
                'text_length' => 1500,
                'language' => 'ms',
                'sentiment' => 'neutral',
            ],
            'transformation_steps' => [
                'sanitization' => ['pii_removed' => true, 'profanity_filtered' => false],
                'embedding' => ['model' => 'llama3.1', 'dimensions' => 768],
                'similarity_check' => ['threshold' => 0.8, 'matches_found' => 3],
            ],
            'output_quality' => [
                'confidence_score' => 0.92,
                'validation_passed' => true,
            ],
        ];

        $lineage = DataLineage::factory()->create([
            'transformation_metadata' => $complexMetadata,
        ]);

        $fresh = $lineage->fresh();
        $this->assertEquals($complexMetadata, $fresh->transformation_metadata);
        $this->assertEquals('llama3.1', $fresh->transformation_metadata['transformation_steps']['embedding']['model']);
        $this->assertTrue($fresh->transformation_metadata['output_quality']['validation_passed']);
    }

    #[Test]
    public function it_has_timestamps(): void
    {
        $lineage = DataLineage::factory()->create();

        $this->assertNotNull($lineage->created_at);
        $this->assertNotNull($lineage->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $lineage->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $lineage->updated_at);
    }
}

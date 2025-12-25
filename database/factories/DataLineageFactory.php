<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DataLineage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory untuk model DataLineage
 *
 * Menjana data lineage yang realistik untuk penjejakan transformasi data AI
  *
 * @extends Factory<\App\Models\DataLineage>
 */
class DataLineageFactory extends Factory
{
    /**
     * Model yang berkaitan dengan factory ini
     */
    protected $model = DataLineage::class;

    /**
     * Takrifkan keadaan lalai model
     */
    public function definition(): array
    {
        $sourceTypes = ['document', 'faq', 'user_input', 'template'];
        $transformationTypes = ['embedding', 'chunking', 'sanitization', 'template_processing'];
        $destinationTypes = ['embedding', 'chunk', 'response', 'draft'];

        $sourceType = $this->faker->randomElement($sourceTypes);
        $transformationType = $this->faker->randomElement($transformationTypes);
        $destinationType = $this->faker->randomElement($destinationTypes);

        return [
            'lineage_id' => Str::uuid(),
            'source_type' => $sourceType,
            'source_id' => $this->faker->numberBetween(1, 1000),
            'transformation_type' => $transformationType,
            'transformation_metadata' => $this->generateTransformationMetadata($transformationType),
            'destination_type' => $destinationType,
            'destination_id' => $this->faker->boolean(80) ? $this->faker->numberBetween(1, 1000) : null,
            'processed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * State untuk transformasi embedding
     */
    public function embedding(): static
    {
        return $this->state(fn (array $attributes) => [
            'transformation_type' => 'embedding',
            'destination_type' => 'embedding',
            'transformation_metadata' => [
                'model' => 'llama3.1',
                'vector_dimensions' => 4096,
                'processing_time_ms' => $this->faker->numberBetween(50, 500),
                'similarity_threshold' => 0.3,
                'chunk_size' => $this->faker->numberBetween(500, 1000),
            ],
        ]);
    }

    /**
     * State untuk transformasi chunking
     */
    public function chunking(): static
    {
        return $this->state(fn (array $attributes) => [
            'source_type' => 'document',
            'transformation_type' => 'chunking',
            'destination_type' => 'chunk',
            'transformation_metadata' => [
                'chunk_size' => $this->faker->numberBetween(500, 1500),
                'overlap_size' => $this->faker->numberBetween(50, 200),
                'chunks_created' => $this->faker->numberBetween(5, 50),
                'total_characters' => $this->faker->numberBetween(2500, 75000),
                'algorithm' => 'sliding_window',
            ],
        ]);
    }

    /**
     * State untuk transformasi sanitization
     */
    public function sanitization(): static
    {
        return $this->state(fn (array $attributes) => [
            'transformation_type' => 'sanitization',
            'transformation_metadata' => [
                'pii_patterns_detected' => $this->faker->numberBetween(0, 5),
                'redaction_count' => $this->faker->numberBetween(0, 10),
                'patterns_checked' => ['ic_number', 'phone', 'email', 'address'],
                'sanitization_level' => $this->faker->randomElement(['redact', 'anonymize', 'encrypt']),
                'processing_time_ms' => $this->faker->numberBetween(10, 100),
            ],
        ]);
    }

    /**
     * State untuk transformasi template processing
     */
    public function templateProcessing(): static
    {
        return $this->state(fn (array $attributes) => [
            'source_type' => 'template',
            'transformation_type' => 'template_processing',
            'destination_type' => 'draft',
            'transformation_metadata' => [
                'template_id' => $this->faker->numberBetween(1, 20),
                'variables_substituted' => $this->faker->numberBetween(3, 15),
                'context_sources' => $this->faker->numberBetween(1, 5),
                'processing_time_ms' => $this->faker->numberBetween(200, 2000),
                'approval_required' => $this->faker->boolean(85),
            ],
        ]);
    }

    /**
     * State untuk sumber dokumen
     */
    public function fromDocument(): static
    {
        return $this->state(fn (array $attributes) => [
            'source_type' => 'document',
            'source_id' => $this->faker->numberBetween(1, 100),
        ]);
    }

    /**
     * State untuk sumber FAQ
     */
    public function fromFaq(): static
    {
        return $this->state(fn (array $attributes) => [
            'source_type' => 'faq',
            'source_id' => $this->faker->numberBetween(1, 200),
        ]);
    }

    /**
     * State untuk sumber input pengguna
     */
    public function fromUserInput(): static
    {
        return $this->state(fn (array $attributes) => [
            'source_type' => 'user_input',
            'source_id' => $this->faker->numberBetween(1, 500),
        ]);
    }

    /**
     * Menjana metadata transformasi berdasarkan jenis
     */
    private function generateTransformationMetadata(string $transformationType): array
    {
        $baseMetadata = [
            'timestamp' => now()->toISOString(),
            'processing_time_ms' => $this->faker->numberBetween(10, 5000),
        ];

        return match ($transformationType) {
            'embedding' => array_merge($baseMetadata, [
                'model' => 'llama3.1',
                'vector_dimensions' => 4096,
                'similarity_threshold' => $this->faker->randomFloat(2, 0.2, 0.8),
                'normalization' => 'l2',
            ]),
            'chunking' => array_merge($baseMetadata, [
                'chunk_size' => $this->faker->numberBetween(500, 1500),
                'overlap_size' => $this->faker->numberBetween(50, 200),
                'chunks_created' => $this->faker->numberBetween(1, 100),
                'algorithm' => $this->faker->randomElement(['sliding_window', 'sentence_boundary', 'paragraph']),
            ]),
            'sanitization' => array_merge($baseMetadata, [
                'pii_patterns' => $this->faker->randomElements(['ic_number', 'phone', 'email', 'address'], $this->faker->numberBetween(1, 4)),
                'redaction_count' => $this->faker->numberBetween(0, 15),
                'sanitization_level' => $this->faker->randomElement(['redact', 'anonymize', 'hash']),
            ]),
            'template_processing' => array_merge($baseMetadata, [
                'template_id' => $this->faker->numberBetween(1, 50),
                'variables_count' => $this->faker->numberBetween(1, 20),
                'context_injected' => $this->faker->boolean(80),
                'approval_workflow' => $this->faker->boolean(90),
            ]),
            default => $baseMetadata,
        };
    }
}

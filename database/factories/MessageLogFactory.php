<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MessageLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory untuk model MessageLog
 *
 * Menyokong True Hybrid Architecture dengan nullable user_id
 * Menjana data audit yang realistik untuk testing
 */
class MessageLogFactory extends Factory
{
    /**
     * Model yang berkaitan dengan factory ini
     */
    protected $model = MessageLog::class;

    /**
     * Takrifkan keadaan lalai model
     */
    public function definition(): array
    {
        $operationTypes = ['faq_query', 'document_analysis', 'auto_reply_generation'];
        $operationType = $this->faker->randomElement($operationTypes);

        return [
            'request_id' => Str::uuid(),
            'operation_type' => $operationType,
            'user_id' => $this->faker->boolean(70) ? User::factory() : null, // 70% authenticated, 30% guest
            'sanitized_input' => $this->generateSanitizedInput($operationType),
            'response_summary' => $this->generateResponseSummary($operationType),
            'metadata' => $this->generateMetadata($operationType),
            'hash' => hash('sha256', $this->faker->uuid . microtime()),
            'previous_hash' => $this->faker->boolean(80) ? hash('sha256', $this->faker->uuid) : null,
            'processed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * State untuk operasi FAQ query
     */
    public function faqQuery(): static
    {
        return $this->state(fn (array $attributes) => [
            'operation_type' => 'faq_query',
            'sanitized_input' => 'Bagaimana cara [REDACTED] sistem helpdesk?',
            'response_summary' => 'Respons FAQ mengenai penggunaan sistem helpdesk',
            'metadata' => [
                'model' => 'llama3.1',
                'tokens_used' => $this->faker->numberBetween(50, 200),
                'processing_time_ms' => $this->faker->numberBetween(800, 3000),
                'confidence_score' => $this->faker->randomFloat(2, 0.6, 0.95),
                'sources_found' => $this->faker->numberBetween(1, 5),
            ],
        ]);
    }

    /**
     * State untuk operasi document analysis
     */
    public function documentAnalysis(): static
    {
        return $this->state(fn (array $attributes) => [
            'operation_type' => 'document_analysis',
            'sanitized_input' => 'Analisis dokumen: [FILENAME_REDACTED].pdf',
            'response_summary' => 'Dokumen diproses dan dijana embedding',
            'metadata' => [
                'model' => 'llama3.1',
                'document_size_kb' => $this->faker->numberBetween(100, 5000),
                'chunks_generated' => $this->faker->numberBetween(5, 50),
                'processing_time_ms' => $this->faker->numberBetween(5000, 30000),
                'pii_detected' => $this->faker->boolean(20),
                'embeddings_created' => $this->faker->numberBetween(5, 50),
            ],
        ]);
    }

    /**
     * State untuk operasi auto-reply generation
     */
    public function autoReplyGeneration(): static
    {
        return $this->state(fn (array $attributes) => [
            'operation_type' => 'auto_reply_generation',
            'sanitized_input' => 'Tiket #[REDACTED]: Masalah [CATEGORY]',
            'response_summary' => 'Draf auto-reply dijana untuk kelulusan',
            'metadata' => [
                'model' => 'llama3.1',
                'template_used' => $this->faker->randomElement(['helpdesk_resolution', 'loan_approval', 'general_response']),
                'tokens_used' => $this->faker->numberBetween(100, 400),
                'processing_time_ms' => $this->faker->numberBetween(1500, 5000),
                'approval_required' => true,
                'draft_id' => $this->faker->numberBetween(1, 100),
            ],
        ]);
    }

    /**
     * State untuk pengguna tetamu (guest)
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
        ]);
    }

    /**
     * State untuk pengguna authenticated
     */
    public function authenticated(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory(),
        ]);
    }

    /**
     * Menjana input yang disanitasi berdasarkan jenis operasi
     */
    private function generateSanitizedInput(string $operationType): string
    {
        return match ($operationType) {
            'faq_query' => $this->faker->randomElement([
                'Bagaimana cara menggunakan sistem [REDACTED]?',
                'Apakah prosedur untuk [REDACTED]?',
                'Saya menghadapi masalah dengan [REDACTED]',
                'Bolehkah saya mendapat bantuan untuk [REDACTED]?',
            ]),
            'document_analysis' => 'Analisis dokumen: [FILENAME_REDACTED].' . $this->faker->randomElement(['pdf', 'docx', 'txt']),
            'auto_reply_generation' => 'Tiket #[REDACTED]: ' . $this->faker->randomElement([
                'Masalah komputer',
                'Permohonan pinjaman aset',
                'Sokongan teknikal',
                'Kemas kini sistem',
            ]),
            default => 'Input yang disanitasi untuk ' . $operationType,
        };
    }

    /**
     * Menjana ringkasan respons berdasarkan jenis operasi
     */
    private function generateResponseSummary(string $operationType): string
    {
        return match ($operationType) {
            'faq_query' => $this->faker->randomElement([
                'Respons FAQ dengan 3 sumber berkaitan',
                'Jawapan komprehensif dengan pautan panduan',
                'Respons automatik dengan skor keyakinan tinggi',
                'Fallback ke sokongan manusia',
            ]),
            'document_analysis' => $this->faker->randomElement([
                'Dokumen berjaya diproses dan dijana embedding',
                'Analisis selesai dengan PII disanitasi',
                'Chunking dokumen dan penyimpanan vektor',
                'Pemprosesan gagal - format tidak disokong',
            ]),
            'auto_reply_generation' => $this->faker->randomElement([
                'Draf auto-reply dijana untuk kelulusan admin',
                'Respons templat dengan konteks tiket',
                'Penjanaan gagal - konteks tidak mencukupi',
                'Draf dihantar untuk semakan manual',
            ]),
            default => 'Ringkasan respons untuk ' . $operationType,
        };
    }

    /**
     * Menjana metadata berdasarkan jenis operasi
     */
    private function generateMetadata(string $operationType): array
    {
        $baseMetadata = [
            'model' => 'llama3.1',
            'processing_time_ms' => $this->faker->numberBetween(500, 10000),
            'timestamp' => now()->toISOString(),
        ];

        return match ($operationType) {
            'faq_query' => array_merge($baseMetadata, [
                'tokens_used' => $this->faker->numberBetween(50, 300),
                'confidence_score' => $this->faker->randomFloat(2, 0.3, 0.95),
                'sources_found' => $this->faker->numberBetween(0, 8),
                'cache_hit' => $this->faker->boolean(40),
            ]),
            'document_analysis' => array_merge($baseMetadata, [
                'document_size_kb' => $this->faker->numberBetween(50, 10000),
                'chunks_generated' => $this->faker->numberBetween(1, 100),
                'pii_detected' => $this->faker->boolean(15),
                'embeddings_created' => $this->faker->numberBetween(1, 100),
            ]),
            'auto_reply_generation' => array_merge($baseMetadata, [
                'template_id' => $this->faker->numberBetween(1, 20),
                'tokens_used' => $this->faker->numberBetween(100, 500),
                'approval_required' => $this->faker->boolean(90),
                'context_sources' => $this->faker->numberBetween(1, 5),
            ]),
            default => $baseMetadata,
        };
    }
}

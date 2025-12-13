<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model Document
 *
 * Per Requirements 2.1, 2.2: Document management dengan True Hybrid Architecture
 * Selaras dengan D09 v3.6.0 (nullable user_id FK pattern)
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filenames = [
            'Panduan_Pengguna_Sistem.pdf',
            'Manual_Teknikal_ICT.docx',
            'Prosedur_Keselamatan.pdf',
            'Dasar_Penggunaan_Sistem.docx',
            'FAQ_Sistem_ICTServe.txt',
            'Laporan_Audit_Keselamatan.pdf',
            'Garis_Panduan_Backup.docx',
            'Manual_Pemulihan_Bencana.pdf',
            'Prosedur_Pengurusan_Akaun.txt',
            'Dokumentasi_API_Sistem.pdf',
        ];

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt' => 'text/plain',
        ];

        $filename = $this->faker->randomElement($filenames);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

        return [
            'filename' => $filename,
            'metadata' => [
                'original_name' => $filename,
                'mime_type' => $mimeType,
                'size' => $this->faker->numberBetween(1024, 10485760), // 1KB to 10MB
                'hash' => $this->faker->sha256(),
                'uploaded_at' => now()->toISOString(),
            ],
            // True Hybrid Architecture: nullable uploaded_by untuk guest/authenticated
            'uploaded_by' => $this->faker->boolean(80) ? User::factory() : null,
            'status' => $this->faker->randomElement([
                Document::STATUS_PENDING,
                Document::STATUS_PROCESSING,
                Document::STATUS_COMPLETED,
                Document::STATUS_FAILED,
            ]),
        ];
    }

    /**
     * Document dengan status completed
     *
     * @return static
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Document::STATUS_COMPLETED,
        ]);
    }

    /**
     * Document dengan status failed
     *
     * @return static
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Document::STATUS_FAILED,
        ]);
    }

    /**
     * Document yang diupload oleh guest
     *
     * @return static
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'uploaded_by' => null,
        ]);
    }

    /**
     * Document PDF
     *
     * @return static
     */
    public function pdf(): static
    {
        return $this->state(fn (array $attributes) => [
            'filename' => $this->faker->words(3, true) . '.pdf',
            'metadata' => array_merge($attributes['metadata'] ?? [], [
                'mime_type' => 'application/pdf',
            ]),
        ]);
    }

    /**
     * Document DOCX
     *
     * @return static
     */
    public function docx(): static
    {
        return $this->state(fn (array $attributes) => [
            'filename' => $this->faker->words(3, true) . '.docx',
            'metadata' => array_merge($attributes['metadata'] ?? [], [
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]),
        ]);
    }
}

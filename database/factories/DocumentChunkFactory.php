<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model DocumentChunk
 *
 * Per Requirements 2.1, 2.2: Document chunking untuk vector embeddings
 * Selaras dengan D09 Database Documentation v3.6.0
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocumentChunk>
 */
class DocumentChunkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $chunkTexts = [
            'Sistem ICTServe adalah platform perkhidmatan digital dalaman untuk MOTAC yang menyediakan pengurusan tiket helpdesk dan pinjaman aset ICT. Platform ini menggunakan seni bina hibrid sebenar yang membolehkan akses melalui borang tetamu atau portal yang disahkan.',
            'Untuk mengakses sistem, pengguna boleh mendaftar sendiri menggunakan e-mel @motac.gov.my atau menggunakan borang tetamu untuk akses pantas. Sistem menyokong log masuk fleksibel menggunakan e-mel penuh atau nama pengguna pendek.',
            'Ciri keselamatan sistem termasuk enkripsi AES-256 untuk data at rest, TLS 1.3 untuk data in transit, dan sistem audit dwi menggunakan owen-it untuk pematuhan dan spatie untuk operasi.',
            'Sistem mematuhi standard WCAG 2.2 Level AA untuk kebolehcapaian, PDPA 2010 untuk perlindungan data, dan MyGOV Digital Service Standards v2.1.0 untuk perkhidmatan digital kerajaan.',
            'Modul helpdesk membolehkan pengguna menghantar tiket sokongan dengan penjejakan SLA automatik, notifikasi multi-saluran, dan aliran kerja kelulusan untuk respons yang dijana AI.',
            'Modul pinjaman aset menyediakan pengurusan inventori masa nyata, pengesanan konflik, dan kelulusan berasaskan e-mel untuk pegawai gred 41 ke atas tanpa perlu log masuk ke sistem.',
            'Panel pentadbiran menggunakan Filament v4 dengan kawalan akses berasaskan peranan empat peringkat: staf, pelulus, admin, dan superuser dengan akses Laravel Telescope.',
            'Sistem pemantauan prestasi menggunakan Laravel Pulse untuk metrik masa nyata, Laravel Reverb untuk notifikasi WebSocket, dan Laravel Horizon untuk pengurusan baris gilir.',
            'Integrasi AI Ollama menyediakan FAQ Bot, analisis dokumen, dan auto-reply dengan pemprosesan LLM tempatan tanpa panggilan API luaran untuk privasi data.',
            'Sistem menyokong residensi data Malaysia dengan semua data disimpan dalam infrastruktur MOTAC dan tiada pemindahan data merentas sempadan.',
        ];

        // Generate random embedding vector (768 dimensions for typical LLM)
        $embedding = [];
        for ($i = 0; $i < 768; $i++) {
            $embedding[] = $this->faker->randomFloat(6, -1, 1);
        }

        return [
            'document_id' => Document::factory(),
            'chunk_text' => $this->faker->randomElement($chunkTexts),
            'embedding' => $embedding,
            'source' => $this->faker->optional()->randomElement(['page_1', 'section_2', 'paragraph_3']),
            'chunk_index' => $this->faker->numberBetween(0, 50),
        ];
    }

    /**
     * Chunk tanpa embedding
     *
     * @return static
     */
    public function withoutEmbedding(): static
    {
        return $this->state(fn (array $attributes) => [
            'embedding' => [],
        ]);
    }

    /**
     * Chunk dengan indeks tertentu
     *
     * @param int $index
     * @return static
     */
    public function withIndex(int $index): static
    {
        return $this->state(fn (array $attributes) => [
            'chunk_index' => $index,
        ]);
    }

    /**
     * Chunk dengan teks tertentu
     *
     * @param string $text
     * @return static
     */
    public function withText(string $text): static
    {
        return $this->state(fn (array $attributes) => [
            'chunk_text' => $text,
        ]);
    }

    /**
     * Chunk dengan embedding berkualiti tinggi (normalized)
     *
     * @return static
     */
    public function highQualityEmbedding(): static
    {
        return $this->state(function (array $attributes) {
            // Generate normalized embedding vector
            $embedding = [];
            $sum = 0;

            for ($i = 0; $i < 768; $i++) {
                $value = $this->faker->randomFloat(6, -1, 1);
                $embedding[] = $value;
                $sum += $value * $value;
            }

            // Normalize the vector
            $norm = sqrt($sum);
            if ($norm > 0) {
                for ($i = 0; $i < 768; $i++) {
                    $embedding[$i] = $embedding[$i] / $norm;
                }
            }

            return ['embedding' => $embedding];
        });
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\OllamaClientContract;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Perkhidmatan Embedding untuk operasi vektor AI
 *
 * Mengendalikan penjanaan dan operasi vector embeddings untuk
 * carian semantik dalam sistem ICTServe v3.6.0.
 *
 * @version 3.6.0
 * @author Pasukan Pembangunan BPM MOTAC
 * @compliance D10 Source Code Documentation v3.6.0
 * @requirements 2.2, 8.1, 8.4
 */
class EmbeddingService
{
    /**
     * Klien Ollama untuk AI operations
     */
    private OllamaClientContract $ollamaClient;

    /**
     * Konfigurasi perkhidmatan
     */
    private array $config;

    /**
     * Konstruktor
     */
    public function __construct(OllamaClientContract $ollamaClient)
    {
        $this->ollamaClient = $ollamaClient;
        $this->config = config('ollama.embedding', [
            'cache_ttl' => 86400, // 24 jam
            'batch_size' => 10,
            'max_text_length' => 8192,
            'performance_target' => 0.1, // 100ms
        ]);
    }

    /**
     * Jana embedding untuk teks tunggal
     *
     * @param string $text Teks untuk dijadikan embedding
     * @param string|null $model Model untuk digunakan (opsyen)
     * @return array Vector embedding
     * @throws InvalidArgumentException Jika teks tidak sah
     */
    public function generateEmbedding(string $text, ?string $model = null): array
    {
        $startTime = microtime(true);

        try {
            // Validasi input
            $this->validateText($text);

            // Periksa cache terlebih dahulu
            $cacheKey = $this->generateCacheKey($text, $model);
            $cachedEmbedding = Cache::get($cacheKey);

            if ($cachedEmbedding !== null) {
                return $cachedEmbedding;
            }

            // Jana embedding menggunakan Ollama
            $response = $this->ollamaClient->embeddings($text, $model);
            $embedding = $response['embedding'] ?? [];

            if (empty($embedding)) {
                throw new \RuntimeException('Gagal menjana embedding: respons kosong');
            }

            // Cache embedding
            Cache::put($cacheKey, $embedding, $this->config['cache_ttl']);

            // Log prestasi jika melebihi target
            $processingTime = microtime(true) - $startTime;
            if ($processingTime > $this->config['performance_target']) {
                Log::warning('Embedding generation exceeded performance target', [
                    'processing_time' => $processingTime,
                    'target' => $this->config['performance_target'],
                    'text_length' => strlen($text),
                ]);
            }

            return $embedding;

        } catch (\Exception $e) {
            Log::error('Embedding generation failed', [
                'text_length' => strlen($text),
                'model' => $model,
                'error' => $e->getMessage(),
                'processing_time' => microtime(true) - $startTime,
            ]);

            throw $e;
        }
    }
    /**
     * Jana embeddings untuk multiple texts secara batch
     *
     * @param array $texts Array teks untuk dijadikan embeddings
     * @param string|null $model Model untuk digunakan (opsyen)
     * @return array Array embeddings dengan indeks yang sama
     */
    public function generateBatchEmbeddings(array $texts, ?string $model = null): array
    {
        $embeddings = [];
        $batches = array_chunk($texts, $this->config['batch_size']);

        foreach ($batches as $batchIndex => $batch) {
            Log::info('Processing embedding batch', [
                'batch_index' => $batchIndex + 1,
                'batch_size' => count($batch),
                'total_batches' => count($batches),
            ]);

            foreach ($batch as $index => $text) {
                try {
                    $embeddings[] = $this->generateEmbedding($text, $model);
                } catch (\Exception $e) {
                    Log::error('Failed to generate embedding in batch', [
                        'batch_index' => $batchIndex,
                        'text_index' => $index,
                        'error' => $e->getMessage(),
                    ]);

                    // Tambah embedding kosong untuk mengekalkan indeks
                    $embeddings[] = [];
                }
            }

            // Pause sebentar antara batch untuk elakkan overload
            if ($batchIndex < count($batches) - 1) {
                usleep(100000); // 100ms
            }
        }

        return $embeddings;
    }

    /**
     * Kira cosine similarity antara dua embeddings
     *
     * @param array $embedding1 Embedding pertama
     * @param array $embedding2 Embedding kedua
     * @return float Skor similarity (0.0 - 1.0)
     */
    public function cosineSimilarity(array $embedding1, array $embedding2): float
    {
        if (empty($embedding1) || empty($embedding2)) {
            return 0.0;
        }

        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        $length = min(count($embedding1), count($embedding2));

        for ($i = 0; $i < $length; $i++) {
            $dotProduct += $embedding1[$i] * $embedding2[$i];
            $normA += $embedding1[$i] * $embedding1[$i];
            $normB += $embedding2[$i] * $embedding2[$i];
        }

        $normA = sqrt($normA);
        $normB = sqrt($normB);

        if ($normA == 0.0 || $normB == 0.0) {
            return 0.0;
        }

        return $dotProduct / ($normA * $normB);
    }

    /**
     * Cari embeddings yang paling serupa dari koleksi
     *
     * @param array $queryEmbedding Embedding query
     * @param array $candidateEmbeddings Array embeddings calon
     * @param float $threshold Threshold minimum similarity
     * @param int $limit Bilangan maksimum hasil
     * @return array Array hasil dengan indeks dan skor similarity
     */
    public function findSimilarEmbeddings(
        array $queryEmbedding,
        array $candidateEmbeddings,
        float $threshold = 0.3,
        int $limit = 5
    ): array {
        $similarities = [];

        foreach ($candidateEmbeddings as $index => $embedding) {
            $similarity = $this->cosineSimilarity($queryEmbedding, $embedding);

            if ($similarity >= $threshold) {
                $similarities[] = [
                    'index' => $index,
                    'similarity' => $similarity,
                ];
            }
        }

        // Susun mengikut similarity (tinggi ke rendah)
        usort($similarities, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        // Hadkan hasil
        return array_slice($similarities, 0, $limit);
    }
    /**
     * Validasi teks input
     */
    private function validateText(string $text): void
    {
        if (empty(trim($text))) {
            throw new InvalidArgumentException('Teks tidak boleh kosong');
        }

        if (strlen($text) > $this->config['max_text_length']) {
            throw new InvalidArgumentException(
                "Teks terlalu panjang (maksimum {$this->config['max_text_length']} aksara)"
            );
        }
    }

    /**
     * Jana kunci cache untuk embedding
     */
    private function generateCacheKey(string $text, ?string $model = null): string
    {
        $model = $model ?? config('ollama.model');
        $hash = hash('sha256', $text . $model);

        return "embedding:{$model}:{$hash}";
    }

    /**
     * Bersihkan cache embeddings
     *
     * @param string|null $pattern Pattern untuk kunci cache (opsyen)
     * @return bool Status pembersihan
     */
    public function clearCache(?string $pattern = null): bool
    {
        try {
            if ($pattern) {
                // Bersihkan cache dengan pattern tertentu
                $keys = Cache::getRedis()->keys("embedding:*{$pattern}*");
                if (!empty($keys)) {
                    Cache::getRedis()->del($keys);
                }
            } else {
                // Bersihkan semua cache embedding
                Cache::tags(['embedding'])->flush();
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to clear embedding cache', [
                'pattern' => $pattern,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Dapatkan statistik cache embedding
     *
     * @return array Statistik cache
     */
    public function getCacheStats(): array
    {
        try {
            $redis = Cache::getRedis();
            $keys = $redis->keys('embedding:*');

            $stats = [
                'total_cached_embeddings' => count($keys),
                'cache_size_bytes' => 0,
                'oldest_cache' => null,
                'newest_cache' => null,
            ];

            if (!empty($keys)) {
                $totalSize = 0;
                $timestamps = [];

                foreach ($keys as $key) {
                    $size = $redis->memory('usage', $key);
                    $totalSize += $size ?? 0;

                    $ttl = $redis->ttl($key);
                    if ($ttl > 0) {
                        $timestamps[] = time() + $ttl - $this->config['cache_ttl'];
                    }
                }

                $stats['cache_size_bytes'] = $totalSize;

                if (!empty($timestamps)) {
                    $stats['oldest_cache'] = date('Y-m-d H:i:s', min($timestamps));
                    $stats['newest_cache'] = date('Y-m-d H:i:s', max($timestamps));
                }
            }

            return $stats;

        } catch (\Exception $e) {
            Log::error('Failed to get cache stats', [
                'error' => $e->getMessage(),
            ]);

            return [
                'total_cached_embeddings' => 0,
                'cache_size_bytes' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Precompute embeddings untuk teks yang kerap digunakan
     *
     * @param array $texts Array teks untuk precompute
     * @param string|null $model Model untuk digunakan
     * @return int Bilangan embeddings yang berjaya di-precompute
     */
    public function precomputeEmbeddings(array $texts, ?string $model = null): int
    {
        $successCount = 0;

        Log::info('Starting embedding precomputation', [
            'text_count' => count($texts),
            'model' => $model ?? config('ollama.model'),
        ]);

        foreach ($texts as $index => $text) {
            try {
                $this->generateEmbedding($text, $model);
                $successCount++;

                if (($index + 1) % 10 === 0) {
                    Log::info('Precomputation progress', [
                        'completed' => $index + 1,
                        'total' => count($texts),
                        'success_rate' => round(($successCount / ($index + 1)) * 100, 2) . '%',
                    ]);
                }

            } catch (\Exception $e) {
                Log::warning('Failed to precompute embedding', [
                    'index' => $index,
                    'text_preview' => substr($text, 0, 100),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Embedding precomputation completed', [
            'total_texts' => count($texts),
            'successful' => $successCount,
            'success_rate' => round(($successCount / count($texts)) * 100, 2) . '%',
        ]);

        return $successCount;
    }
}

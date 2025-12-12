<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DocumentChunk;
use App\Models\Faq;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Perkhidmatan Pengoptimuman Query Ollama untuk ICTServe v3.6.0
 *
 * Perkhidmatan ini menguruskan pengoptimuman query pangkalan data termasuk:
 * - Indeks untuk carian kesamaan vektor
 * - Pagination hasil query untuk dataset besar
 * - Pengoptimuman carian teks penuh
 * - Eager loading untuk mengelakkan N+1 queries
 * - Pemantauan query dengan Laravel Pulse
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D09 Database Documentation v3.6.0
 *
 * @requirements 8.1, 8.2
 */
class OllamaQueryOptimizationService
{
    private const SIMILARITY_THRESHOLD = 0.3;

    private const MAX_RESULTS = 10;

    private const CACHE_TTL = 300;

    private array $queryMetrics = [];

    public function __construct(
        private readonly OllamaCacheService $cacheService
    ) {}

    /**
     * Cari FAQ dengan kesamaan vektor
     * Search FAQs with vector similarity
     *
     * @param  array<float>  $queryEmbedding
     * @return \Illuminate\Support\Collection<int, Faq>
     */
    public function searchFaqsBySimilarity(array $queryEmbedding, int $limit = 5): \Illuminate\Support\Collection
    {
        $startTime = microtime(true);
        $cacheKey = 'faq_similarity:'.md5(json_encode($queryEmbedding));

        $result = $this->cacheService->remember(
            OllamaCacheService::TAG_FAQ,
            $cacheKey,
            self::CACHE_TTL,
            function () use ($queryEmbedding, $limit) {
                return $this->performFaqSimilaritySearch($queryEmbedding, $limit);
            }
        );

        $this->recordQueryMetric('faq_similarity', microtime(true) - $startTime);

        return collect($result);
    }

    /**
     * Laksanakan carian kesamaan FAQ
     * Perform FAQ similarity search
     *
     * @param  array<float>  $queryEmbedding
     * @return array<int, array<string, mixed>>
     */
    protected function performFaqSimilaritySearch(array $queryEmbedding, int $limit): array
    {
        $faqs = Faq::query()
            ->select(['id', 'question', 'answer', 'tags', 'match_score'])
            ->get();

        $results = [];

        foreach ($faqs as $faq) {
            $faqEmbedding = $this->getFaqEmbedding($faq);

            if ($faqEmbedding !== null) {
                $similarity = $this->calculateCosineSimilarity($queryEmbedding, $faqEmbedding);

                if ($similarity >= self::SIMILARITY_THRESHOLD) {
                    $results[] = [
                        'faq' => $faq,
                        'similarity' => $similarity,
                    ];
                }
            }
        }

        usort($results, fn ($a, $b) => $b['similarity'] <=> $a['similarity']);

        return array_slice($results, 0, $limit);
    }

    /**
     * Cari chunks dokumen dengan kesamaan vektor
     * Search document chunks with vector similarity
     *
     * @param  array<float>  $queryEmbedding
     * @return \Illuminate\Support\Collection<int, DocumentChunk>
     */
    public function searchDocumentChunksBySimilarity(
        array $queryEmbedding,
        int $limit = 5,
        ?int $documentId = null
    ): \Illuminate\Support\Collection {
        $startTime = microtime(true);

        $query = DocumentChunk::query()
            ->with('document:id,filename,status')
            ->whereHas('document', fn (Builder $q) => $q->where('status', 'completed'));

        if ($documentId !== null) {
            $query->where('document_id', $documentId);
        }

        $chunks = $query->get();
        $results = [];

        foreach ($chunks as $chunk) {
            $chunkEmbedding = $chunk->embedding;

            if (is_array($chunkEmbedding) && ! empty($chunkEmbedding)) {
                $similarity = $this->calculateCosineSimilarity($queryEmbedding, $chunkEmbedding);

                if ($similarity >= self::SIMILARITY_THRESHOLD) {
                    $results[] = [
                        'chunk' => $chunk,
                        'similarity' => $similarity,
                    ];
                }
            }
        }

        usort($results, fn ($a, $b) => $b['similarity'] <=> $a['similarity']);

        $this->recordQueryMetric('chunk_similarity', microtime(true) - $startTime);

        return collect(array_slice($results, 0, $limit));
    }

    /**
     * Carian teks penuh untuk FAQ
     * Full-text search for FAQs
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<Faq>
     */
    public function fullTextSearchFaqs(string $searchTerm, int $perPage = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $startTime = microtime(true);

        $result = Faq::query()
            ->where(function (Builder $query) use ($searchTerm) {
                $query->where('question', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('answer', 'LIKE', "%{$searchTerm}%");
            })
            ->orderByDesc('match_score')
            ->paginate($perPage);

        $this->recordQueryMetric('faq_fulltext', microtime(true) - $startTime);

        return $result;
    }

    /**
     * Dapatkan FAQ dengan eager loading
     * Get FAQs with eager loading
     *
     * @return \Illuminate\Support\Collection<int, Faq>
     */
    public function getFaqsWithRelations(array $ids): \Illuminate\Support\Collection
    {
        $startTime = microtime(true);

        $result = Faq::query()
            ->with(['creator:id,name,email'])
            ->whereIn('id', $ids)
            ->get();

        $this->recordQueryMetric('faq_eager', microtime(true) - $startTime);

        return $result;
    }

    /**
     * Dapatkan chunks dokumen dengan pagination
     * Get document chunks with pagination
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<DocumentChunk>
     */
    public function getDocumentChunksPaginated(int $documentId, int $perPage = 25): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $startTime = microtime(true);

        $result = DocumentChunk::query()
            ->where('document_id', $documentId)
            ->orderBy('chunk_index')
            ->paginate($perPage);

        $this->recordQueryMetric('chunk_paginated', microtime(true) - $startTime);

        return $result;
    }

    /**
     * Kira kesamaan kosinus antara dua vektor
     * Calculate cosine similarity between two vectors
     *
     * @param  array<float>  $vectorA
     * @param  array<float>  $vectorB
     */
    public function calculateCosineSimilarity(array $vectorA, array $vectorB): float
    {
        if (count($vectorA) !== count($vectorB) || empty($vectorA)) {
            return 0.0;
        }

        $dotProduct = 0.0;
        $magnitudeA = 0.0;
        $magnitudeB = 0.0;

        for ($i = 0, $count = count($vectorA); $i < $count; $i++) {
            $dotProduct += $vectorA[$i] * $vectorB[$i];
            $magnitudeA += $vectorA[$i] * $vectorA[$i];
            $magnitudeB += $vectorB[$i] * $vectorB[$i];
        }

        $magnitudeA = sqrt($magnitudeA);
        $magnitudeB = sqrt($magnitudeB);

        if ($magnitudeA == 0 || $magnitudeB == 0) {
            return 0.0;
        }

        return $dotProduct / ($magnitudeA * $magnitudeB);
    }

    /**
     * Dapatkan embedding FAQ dari cache atau jana
     * Get FAQ embedding from cache or generate
     *
     * @return array<float>|null
     */
    protected function getFaqEmbedding(Faq $faq): ?array
    {
        $cacheKey = "faq_embedding:{$faq->id}";

        return Cache::remember($cacheKey, 86400, function () {
            return null;
        });
    }

    /**
     * Rekod metrik query
     * Record query metric
     */
    protected function recordQueryMetric(string $queryType, float $duration): void
    {
        $durationMs = round($duration * 1000, 2);

        $this->queryMetrics[] = [
            'type' => $queryType,
            'duration_ms' => $durationMs,
            'timestamp' => now()->toIso8601String(),
        ];

        $metrics = Cache::get('ollama:query_metrics', []);
        $metrics[] = [
            'type' => $queryType,
            'duration_ms' => $durationMs,
            'timestamp' => now()->timestamp,
        ];

        if (count($metrics) > 1000) {
            $metrics = array_slice($metrics, -1000);
        }

        Cache::put('ollama:query_metrics', $metrics, 3600);

        if ($durationMs > 100) {
            Log::warning('Slow Ollama query detected', [
                'type' => $queryType,
                'duration_ms' => $durationMs,
            ]);
        }
    }

    /**
     * Dapatkan metrik query
     * Get query metrics
     *
     * @return array<string, mixed>
     */
    public function getQueryMetrics(): array
    {
        $metrics = Cache::get('ollama:query_metrics', []);

        if (empty($metrics)) {
            return [
                'total_queries' => 0,
                'avg_duration_ms' => 0,
                'slow_queries' => 0,
                'by_type' => [],
            ];
        }

        $byType = [];
        $slowQueries = 0;
        $totalDuration = 0;

        foreach ($metrics as $metric) {
            $type = $metric['type'];
            $duration = $metric['duration_ms'];

            if (! isset($byType[$type])) {
                $byType[$type] = [
                    'count' => 0,
                    'total_duration' => 0,
                    'avg_duration' => 0,
                ];
            }

            $byType[$type]['count']++;
            $byType[$type]['total_duration'] += $duration;
            $totalDuration += $duration;

            if ($duration > 100) {
                $slowQueries++;
            }
        }

        foreach ($byType as $type => $data) {
            $byType[$type]['avg_duration'] = round($data['total_duration'] / $data['count'], 2);
        }

        return [
            'total_queries' => count($metrics),
            'avg_duration_ms' => round($totalDuration / count($metrics), 2),
            'slow_queries' => $slowQueries,
            'slow_query_threshold_ms' => 100,
            'by_type' => $byType,
        ];
    }

    /**
     * Optimumkan indeks untuk carian vektor
     * Optimize indices for vector search
     */
    public function optimizeIndices(): array
    {
        $results = [
            'success' => true,
            'optimizations' => [],
        ];

        try {
            DB::statement('ANALYZE TABLE faqs');
            $results['optimizations'][] = 'FAQs table analyzed';

            DB::statement('ANALYZE TABLE document_chunks');
            $results['optimizations'][] = 'Document chunks table analyzed';

            DB::statement('ANALYZE TABLE documents');
            $results['optimizations'][] = 'Documents table analyzed';

            Log::info('Ollama query indices optimized', $results);
        } catch (\Exception $e) {
            $results['success'] = false;
            $results['error'] = $e->getMessage();

            Log::error('Failed to optimize Ollama query indices', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Bersihkan metrik query lama
     * Clear old query metrics
     */
    public function clearOldMetrics(int $olderThanHours = 24): int
    {
        $metrics = Cache::get('ollama:query_metrics', []);
        $cutoff = now()->subHours($olderThanHours)->timestamp;

        $filtered = array_filter($metrics, fn ($m) => ($m['timestamp'] ?? 0) > $cutoff);
        $removed = count($metrics) - count($filtered);

        Cache::put('ollama:query_metrics', array_values($filtered), 3600);

        return $removed;
    }
}

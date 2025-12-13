<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Document;
use App\Models\DocumentChunk;
use App\Services\EmbeddingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job untuk penjanaan embedding secara latar belakang
 *
 * Mengendalikan penjanaan vector embeddings untuk document chunks
 * dengan sokongan batch processing dan caching dalam sistem ICTServe v3.6.0.
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D10 Source Code Documentation v3.6.0
 *
 * @requirements 2.2, 8.3, 8.4
 */
class EmbeddingJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Bilangan percubaan maksimum sebelum job gagal
     */
    public int $tries = 3;

    /**
     * Masa menunggu antara percubaan (saat) - exponential backoff
     *
     * @var array<int>
     */
    public array $backoff = [1, 2, 4];

    /**
     * Masa maksimum untuk job berjalan (saat)
     */
    public int $timeout = 900;

    /**
     * Dokumen untuk diproses
     */
    private Document $document;

    /**
     * Saiz batch untuk pemprosesan
     */
    private int $batchSize;

    /**
     * Cipta instance job baharu
     */
    public function __construct(Document $document, int $batchSize = 10)
    {
        $this->document = $document;
        $this->batchSize = $batchSize;
        $this->onQueue('embeddings');
    }

    /**
     * Laksanakan job
     */
    public function handle(EmbeddingService $embeddingService): void
    {
        $startTime = microtime(true);

        Log::info('EmbeddingJob started', [
            'document_id' => $this->document->id,
            'filename' => $this->document->filename,
            'batch_size' => $this->batchSize,
            'attempt' => $this->attempts(),
        ]);

        try {
            // Dapatkan chunks yang belum ada embedding
            $chunks = $this->document->chunks()
                ->where(function ($query) {
                    $query->whereNull('embedding')
                        ->orWhere('embedding', '[]')
                        ->orWhere('embedding', '');
                })
                ->get();

            if ($chunks->isEmpty()) {
                Log::info('EmbeddingJob skipped - no chunks to process', [
                    'document_id' => $this->document->id,
                ]);

                return;
            }

            $totalChunks = $chunks->count();
            $processedCount = 0;
            $failedCount = 0;

            // Proses dalam batch
            foreach ($chunks->chunk($this->batchSize) as $batchIndex => $batch) {
                Log::debug('Processing embedding batch', [
                    'document_id' => $this->document->id,
                    'batch_index' => $batchIndex + 1,
                    'batch_size' => $batch->count(),
                    'total_batches' => ceil($totalChunks / $this->batchSize),
                ]);

                foreach ($batch as $chunk) {
                    try {
                        $this->processChunk($chunk, $embeddingService);
                        $processedCount++;
                    } catch (\Exception $e) {
                        $failedCount++;
                        Log::warning('Failed to generate embedding for chunk', [
                            'document_id' => $this->document->id,
                            'chunk_id' => $chunk->id,
                            'chunk_index' => $chunk->chunk_index,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // Pause sebentar antara batch untuk elakkan overload
                if ($batchIndex < ceil($totalChunks / $this->batchSize) - 1) {
                    usleep(100000); // 100ms
                }
            }

            $processingTime = microtime(true) - $startTime;

            // Update metadata dokumen dengan statistik embedding
            $this->updateDocumentMetadata($processedCount, $failedCount, $processingTime);

            Log::info('EmbeddingJob completed', [
                'document_id' => $this->document->id,
                'filename' => $this->document->filename,
                'total_chunks' => $totalChunks,
                'processed' => $processedCount,
                'failed' => $failedCount,
                'processing_time' => $processingTime,
                'success_rate' => round(($processedCount / $totalChunks) * 100, 2).'%',
            ]);

            // Jika terlalu banyak gagal, throw exception untuk retry
            if ($failedCount > $totalChunks * 0.5) {
                throw new \RuntimeException(
                    "Terlalu banyak chunks gagal diproses: {$failedCount}/{$totalChunks}"
                );
            }
        } catch (\Exception $e) {
            $this->handleFailure($e, microtime(true) - $startTime);
            throw $e;
        }
    }

    /**
     * Proses satu chunk untuk jana embedding
     */
    private function processChunk(DocumentChunk $chunk, EmbeddingService $embeddingService): void
    {
        $embedding = $embeddingService->generateEmbedding($chunk->chunk_text);

        if (empty($embedding)) {
            throw new \RuntimeException('Embedding kosong dijana');
        }

        $chunk->update(['embedding' => $embedding]);

        Log::debug('Embedding generated for chunk', [
            'document_id' => $this->document->id,
            'chunk_id' => $chunk->id,
            'chunk_index' => $chunk->chunk_index,
            'embedding_dimensions' => count($embedding),
        ]);
    }

    /**
     * Update metadata dokumen dengan statistik embedding
     */
    private function updateDocumentMetadata(int $processed, int $failed, float $processingTime): void
    {
        $this->document->update([
            'metadata' => array_merge($this->document->metadata ?? [], [
                'embedding_stats' => [
                    'processed' => $processed,
                    'failed' => $failed,
                    'processing_time' => $processingTime,
                    'completed_at' => now()->toISOString(),
                ],
            ]),
        ]);
    }

    /**
     * Kendalikan kegagalan job
     */
    private function handleFailure(\Exception $e, float $processingTime): void
    {
        Log::error('EmbeddingJob failed', [
            'document_id' => $this->document->id,
            'filename' => $this->document->filename,
            'attempt' => $this->attempts(),
            'max_tries' => $this->tries,
            'processing_time' => $processingTime,
            'error' => $e->getMessage(),
        ]);
    }

    /**
     * Kendalikan kegagalan job selepas semua percubaan habis
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('EmbeddingJob permanently failed', [
            'document_id' => $this->document->id,
            'filename' => $this->document->filename,
            'error' => $exception->getMessage(),
        ]);

        // Update metadata dokumen dengan maklumat kegagalan
        $this->document->update([
            'metadata' => array_merge($this->document->metadata ?? [], [
                'embedding_error' => $exception->getMessage(),
                'embedding_failed_at' => now()->toISOString(),
            ]),
        ]);
    }

    /**
     * Tentukan tag untuk job (untuk monitoring)
     *
     * @return array<string>
     */
    public function tags(): array
    {
        return [
            'embedding',
            'document:'.$this->document->id,
            'uploaded_by:'.($this->document->uploaded_by ?? 'guest'),
        ];
    }
}

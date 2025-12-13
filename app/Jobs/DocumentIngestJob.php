<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job untuk pemprosesan dokumen secara latar belakang
 *
 * Mengendalikan ekstraksi teks, chunking, dan penjanaan embedding
 * untuk dokumen yang dimuat naik dalam sistem ICTServe v3.6.0.
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D10 Source Code Documentation v3.6.0
 *
 * @requirements 2.1, 2.2, 8.3
 */
class DocumentIngestJob implements ShouldQueue
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
    public int $timeout = 600;

    /**
     * Dokumen untuk diproses
     */
    private Document $document;

    /**
     * Cipta instance job baharu
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
        $this->onQueue('documents');
    }

    /**
     * Laksanakan job
     */
    public function handle(DocumentService $documentService): void
    {
        $startTime = microtime(true);

        Log::info('DocumentIngestJob started', [
            'document_id' => $this->document->id,
            'filename' => $this->document->filename,
            'attempt' => $this->attempts(),
        ]);

        try {
            // Update status ke processing
            $this->document->update(['status' => Document::STATUS_PROCESSING]);

            // Proses dokumen menggunakan DocumentService
            $success = $documentService->processDocument($this->document);

            if (! $success) {
                throw new \RuntimeException('Pemprosesan dokumen gagal');
            }

            $processingTime = microtime(true) - $startTime;

            Log::info('DocumentIngestJob completed successfully', [
                'document_id' => $this->document->id,
                'filename' => $this->document->filename,
                'processing_time' => $processingTime,
                'chunks_created' => $this->document->chunks()->count(),
            ]);

            // Dispatch EmbeddingJob untuk jana embeddings
            EmbeddingJob::dispatch($this->document)->onQueue('embeddings');
        } catch (\Exception $e) {
            $this->handleFailure($e, microtime(true) - $startTime);
            throw $e;
        }
    }

    /**
     * Kendalikan kegagalan job
     */
    private function handleFailure(\Exception $e, float $processingTime): void
    {
        Log::error('DocumentIngestJob failed', [
            'document_id' => $this->document->id,
            'filename' => $this->document->filename,
            'attempt' => $this->attempts(),
            'max_tries' => $this->tries,
            'processing_time' => $processingTime,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        // Update status dokumen ke failed jika ini percubaan terakhir
        if ($this->attempts() >= $this->tries) {
            $this->document->update([
                'status' => Document::STATUS_FAILED,
                'metadata' => array_merge($this->document->metadata ?? [], [
                    'error' => $e->getMessage(),
                    'failed_at' => now()->toISOString(),
                    'attempts' => $this->attempts(),
                ]),
            ]);
        }
    }

    /**
     * Kendalikan kegagalan job selepas semua percubaan habis
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('DocumentIngestJob permanently failed', [
            'document_id' => $this->document->id,
            'filename' => $this->document->filename,
            'error' => $exception->getMessage(),
        ]);

        // Update status dokumen ke failed
        $this->document->update([
            'status' => Document::STATUS_FAILED,
            'metadata' => array_merge($this->document->metadata ?? [], [
                'error' => $exception->getMessage(),
                'failed_at' => now()->toISOString(),
                'permanently_failed' => true,
            ]),
        ]);

        // Notifikasi admin tentang kegagalan (boleh diperluas)
        // Notification::send($admins, new DocumentProcessingFailed($this->document, $exception));
    }

    /**
     * Tentukan tag untuk job (untuk monitoring)
     *
     * @return array<string>
     */
    public function tags(): array
    {
        return [
            'document-ingest',
            'document:'.$this->document->id,
            'uploaded_by:'.($this->document->uploaded_by ?? 'guest'),
        ];
    }

    /**
     * Dapatkan middleware untuk job
     *
     * @return array<object>
     */
    public function middleware(): array
    {
        return [
            // Boleh tambah rate limiting atau throttling middleware
        ];
    }
}

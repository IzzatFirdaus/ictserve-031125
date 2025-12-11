<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentChunk;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use Spatie\PdfToText\Pdf;

/**
 * Perkhidmatan Document untuk pemprosesan fail AI
 *
 * Mengendalikan upload, ekstraksi teks, chunking, dan PII detection
 * untuk dokumen dalam sistem ICTServe v3.6.0.
 *
 * @version 3.6.0
 * @author Pasukan Pembangunan BPM MOTAC
 * @compliance D10 Source Code Documentation v3.6.0
 * @requirements 2.1, 2.3, 6.2
 */
class DocumentService
{
    /**
     * Konfigurasi perkhidmatan
     */
    private array $config;

    /**
     * Perkhidmatan embedding untuk vector operations
     */
    private EmbeddingService $embeddingService;

    /**
     * Konstruktor
     */
    public function __construct(EmbeddingService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
        $this->config = config('ollama.document', [
            'max_file_size' => 10485760, // 10MB
            'allowed_types' => ['pdf', 'docx', 'txt'],
            'chunk_size' => 750, // 500-1000 characters
            'chunk_overlap' => 100,
            'storage_disk' => 'local',
            'storage_path' => 'documents',
        ]);
    }

    /**
     * Upload dan proses dokumen
     *
     * @param UploadedFile $file Fail yang dimuat naik
     * @param int|null $uploadedBy ID pengguna yang memuat naik (nullable untuk tetamu)
     * @return Document Model dokumen yang dicipta
     * @throws \InvalidArgumentException Jika fail tidak sah
     */
    public function uploadDocument(UploadedFile $file, ?int $uploadedBy = null): Document
    {
        try {
            // Validasi fail
            $this->validateFile($file);

            // Simpan fail ke storage
            $filename = $this->storeFile($file);

            // Cipta record dokumen
            $document = Document::create([
                'filename' => $file->getClientOriginalName(),
                'metadata' => [
                    'original_name' => $file->getClientOriginalName(),
                    'stored_name' => $filename,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'extension' => $file->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ],
                'uploaded_by' => $uploadedBy,
                'status' => Document::STATUS_PENDING,
            ]);

            Log::info('Document uploaded successfully', [
                'document_id' => $document->id,
                'filename' => $document->filename,
                'size' => $file->getSize(),
                'uploaded_by' => $uploadedBy,
            ]);

            return $document;

        } catch (\Exception $e) {
            Log::error('Document upload failed', [
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'uploaded_by' => $uploadedBy,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
    /**
     * Proses dokumen untuk ekstraksi teks dan chunking
     *
     * @param Document $document Model dokumen untuk diproses
     * @return bool Status pemprosesan
     */
    public function processDocument(Document $document): bool
    {
        try {
            // Update status ke processing
            $document->update(['status' => Document::STATUS_PROCESSING]);

            // Ekstrak teks dari dokumen
            $text = $this->extractText($document);

            if (empty($text)) {
                throw new \RuntimeException('Gagal mengekstrak teks dari dokumen');
            }

            // Deteksi dan sanitasi PII
            $sanitizedText = $this->detectAndSanitizePii($text);

            // Buat chunks dari teks
            $chunks = $this->createChunks($sanitizedText);

            // Simpan chunks ke database
            $this->saveChunks($document, $chunks);

            // Jana embeddings untuk chunks
            $this->generateChunkEmbeddings($document);

            // Update status ke completed
            $document->update(['status' => Document::STATUS_COMPLETED]);

            Log::info('Document processed successfully', [
                'document_id' => $document->id,
                'chunks_created' => count($chunks),
                'text_length' => strlen($text),
            ]);

            return true;

        } catch (\Exception $e) {
            // Update status ke failed
            $document->update([
                'status' => Document::STATUS_FAILED,
                'metadata' => array_merge($document->metadata ?? [], [
                    'error' => $e->getMessage(),
                    'failed_at' => now()->toISOString(),
                ]),
            ]);

            Log::error('Document processing failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Validasi fail yang dimuat naik
     */
    private function validateFile(UploadedFile $file): void
    {
        // Periksa saiz fail
        if ($file->getSize() > $this->config['max_file_size']) {
            throw new \InvalidArgumentException(
                'Saiz fail melebihi had maksimum ' .
                number_format($this->config['max_file_size'] / 1024 / 1024, 1) . 'MB'
            );
        }

        // Periksa jenis fail
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $this->config['allowed_types'])) {
            throw new \InvalidArgumentException(
                'Jenis fail tidak disokong. Hanya ' .
                implode(', ', $this->config['allowed_types']) . ' dibenarkan'
            );
        }

        // Periksa MIME type untuk keselamatan
        $allowedMimes = [
            'pdf' => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt' => 'text/plain',
        ];

        $expectedMime = $allowedMimes[$extension] ?? null;
        if ($expectedMime && $file->getMimeType() !== $expectedMime) {
            throw new \InvalidArgumentException('Jenis fail tidak sepadan dengan sambungan');
        }
    }

    /**
     * Simpan fail ke storage
     */
    private function storeFile(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $this->config['storage_path'] . '/' . $filename;

        Storage::disk($this->config['storage_disk'])->putFileAs(
            $this->config['storage_path'],
            $file,
            $filename
        );

        return $filename;
    }
    /**
     * Ekstrak teks dari dokumen berdasarkan jenis fail
     */
    private function extractText(Document $document): string
    {
        $storedName = $document->metadata['stored_name'];
        $extension = $document->metadata['extension'];
        $filePath = Storage::disk($this->config['storage_disk'])
            ->path($this->config['storage_path'] . '/' . $storedName);

        switch (strtolower($extension)) {
            case 'pdf':
                return $this->extractPdfText($filePath);

            case 'docx':
                return $this->extractDocxText($filePath);

            case 'txt':
                return $this->extractTxtText($filePath);

            default:
                throw new \InvalidArgumentException("Jenis fail tidak disokong: {$extension}");
        }
    }

    /**
     * Ekstrak teks dari fail PDF
     */
    private function extractPdfText(string $filePath): string
    {
        try {
            return Pdf::getText($filePath);
        } catch (\Exception $e) {
            Log::error('PDF text extraction failed', [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Gagal mengekstrak teks dari PDF: ' . $e->getMessage());
        }
    }

    /**
     * Ekstrak teks dari fail DOCX
     */
    private function extractDocxText(string $filePath): string
    {
        try {
            $phpWord = IOFactory::load($filePath);
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    } elseif (method_exists($element, 'getElements')) {
                        foreach ($element->getElements() as $childElement) {
                            if (method_exists($childElement, 'getText')) {
                                $text .= $childElement->getText() . "\n";
                            }
                        }
                    }
                }
            }

            return trim($text);

        } catch (\Exception $e) {
            Log::error('DOCX text extraction failed', [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Gagal mengekstrak teks dari DOCX: ' . $e->getMessage());
        }
    }

    /**
     * Ekstrak teks dari fail TXT
     */
    private function extractTxtText(string $filePath): string
    {
        try {
            $content = file_get_contents($filePath);

            if ($content === false) {
                throw new \RuntimeException('Gagal membaca fail TXT');
            }

            // Detect encoding dan convert ke UTF-8 jika perlu
            $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);

            if ($encoding && $encoding !== 'UTF-8') {
                $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            }

            return trim($content);

        } catch (\Exception $e) {
            Log::error('TXT text extraction failed', [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Gagal mengekstrak teks dari TXT: ' . $e->getMessage());
        }
    }
    /**
     * Deteksi dan sanitasi PII dalam teks
     */
    private function detectAndSanitizePii(string $text): string
    {
        $piiPatterns = [
            'ic' => [
                'pattern' => '/\d{6}-\d{2}-\d{4}/',
                'replacement' => '[REDACTED_IC]',
            ],
            'phone' => [
                'pattern' => '/\+?60\d{9,10}/',
                'replacement' => '[REDACTED_PHONE]',
            ],
            'email' => [
                'pattern' => '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/',
                'replacement' => '[REDACTED_EMAIL]',
            ],
        ];

        $sanitizedText = $text;
        $detectedPii = [];

        foreach ($piiPatterns as $type => $config) {
            $matches = [];
            if (preg_match_all($config['pattern'], $text, $matches)) {
                $detectedPii[$type] = count($matches[0]);
                $sanitizedText = preg_replace($config['pattern'], $config['replacement'], $sanitizedText);
            }
        }

        // Log PII detection untuk audit
        if (!empty($detectedPii)) {
            Log::warning('PII detected in document', [
                'detected_types' => $detectedPii,
                'total_instances' => array_sum($detectedPii),
            ]);
        }

        return $sanitizedText;
    }

    /**
     * Buat chunks dari teks dengan overlap
     */
    private function createChunks(string $text): array
    {
        $chunks = [];
        $chunkSize = $this->config['chunk_size'];
        $overlap = $this->config['chunk_overlap'];

        // Bersihkan teks
        $text = preg_replace('/\s+/', ' ', trim($text));

        if (strlen($text) <= $chunkSize) {
            return [['text' => $text, 'index' => 0]];
        }

        $position = 0;
        $chunkIndex = 0;

        while ($position < strlen($text)) {
            $chunkEnd = min($position + $chunkSize, strlen($text));

            // Cari word boundary untuk elakkan potong perkataan
            if ($chunkEnd < strlen($text)) {
                $lastSpace = strrpos(substr($text, $position, $chunkSize), ' ');
                if ($lastSpace !== false && $lastSpace > $chunkSize * 0.8) {
                    $chunkEnd = $position + $lastSpace;
                }
            }

            $chunkText = substr($text, $position, $chunkEnd - $position);
            $chunkText = trim($chunkText);

            if (!empty($chunkText)) {
                $chunks[] = [
                    'text' => $chunkText,
                    'index' => $chunkIndex,
                ];
                $chunkIndex++;
            }

            // Move position dengan overlap
            $position = $chunkEnd - $overlap;

            // Elakkan infinite loop
            if ($position <= 0) {
                $position = $chunkEnd;
            }
        }

        return $chunks;
    }

    /**
     * Simpan chunks ke database
     */
    private function saveChunks(Document $document, array $chunks): void
    {
        foreach ($chunks as $chunk) {
            DocumentChunk::create([
                'document_id' => $document->id,
                'chunk_text' => $chunk['text'],
                'chunk_index' => $chunk['index'],
                'source' => $document->filename,
                'embedding' => [], // Akan diisi kemudian
            ]);
        }
    }
    /**
     * Jana embeddings untuk semua chunks dokumen
     */
    private function generateChunkEmbeddings(Document $document): void
    {
        $chunks = $document->chunks()->whereNull('embedding')->orWhere('embedding', '[]')->get();

        if ($chunks->isEmpty()) {
            return;
        }

        Log::info('Generating embeddings for document chunks', [
            'document_id' => $document->id,
            'chunk_count' => $chunks->count(),
        ]);

        foreach ($chunks as $chunk) {
            try {
                $embedding = $this->embeddingService->generateEmbedding($chunk->chunk_text);

                $chunk->update(['embedding' => $embedding]);

                Log::debug('Generated embedding for chunk', [
                    'document_id' => $document->id,
                    'chunk_id' => $chunk->id,
                    'chunk_index' => $chunk->chunk_index,
                ]);

            } catch (\Exception $e) {
                Log::error('Failed to generate embedding for chunk', [
                    'document_id' => $document->id,
                    'chunk_id' => $chunk->id,
                    'chunk_index' => $chunk->chunk_index,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Proses semula dokumen yang gagal
     */
    public function reprocessDocument(Document $document): bool
    {
        if ($document->status !== Document::STATUS_FAILED) {
            throw new \InvalidArgumentException('Hanya dokumen yang gagal boleh diproses semula');
        }

        // Padam chunks sedia ada jika ada
        $document->chunks()->delete();

        // Reset status dan metadata
        $document->update([
            'status' => Document::STATUS_PENDING,
            'metadata' => array_merge($document->metadata ?? [], [
                'reprocessed_at' => now()->toISOString(),
                'error' => null,
                'failed_at' => null,
            ]),
        ]);

        return $this->processDocument($document);
    }

    /**
     * Padam dokumen dan fail berkaitan
     */
    public function deleteDocument(Document $document): bool
    {
        try {
            // Padam fail dari storage
            $storedName = $document->metadata['stored_name'] ?? null;
            if ($storedName) {
                $filePath = $this->config['storage_path'] . '/' . $storedName;
                Storage::disk($this->config['storage_disk'])->delete($filePath);
            }

            // Padam chunks (cascade delete akan berlaku)
            $document->chunks()->delete();

            // Padam record dokumen
            $document->delete();

            Log::info('Document deleted successfully', [
                'document_id' => $document->id,
                'filename' => $document->filename,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to delete document', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Dapatkan statistik dokumen
     */
    public function getDocumentStats(): array
    {
        return [
            'total_documents' => Document::count(),
            'pending_documents' => Document::where('status', Document::STATUS_PENDING)->count(),
            'processing_documents' => Document::where('status', Document::STATUS_PROCESSING)->count(),
            'completed_documents' => Document::where('status', Document::STATUS_COMPLETED)->count(),
            'failed_documents' => Document::where('status', Document::STATUS_FAILED)->count(),
            'total_chunks' => DocumentChunk::count(),
            'chunks_with_embeddings' => DocumentChunk::whereNotNull('embedding')
                ->where('embedding', '!=', '[]')->count(),
        ];
    }
}

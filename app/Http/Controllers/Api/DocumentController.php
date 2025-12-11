<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentUploadRequest;
use App\Jobs\DocumentIngestJob;
use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Pengawal API untuk Analisis Dokumen AI
 *
 * Menyediakan endpoint untuk muat naik dan analisis dokumen
 * dengan akses terhad kepada admin sahaja dalam sistem ICTServe v3.6.0.
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D10 Source Code Documentation v3.6.0, D15 v3.6.0 (Bahasa Melayu sahaja)
 *
 * @requirements 2.1, 2.5, 7.1
 */
class DocumentController extends Controller
{
    /**
     * Perkhidmatan dokumen untuk pemprosesan
     */
    private DocumentService $documentService;

    /**
     * Konstruktor
     */
    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Muat naik dokumen baharu untuk analisis
     *
     * @param  DocumentUploadRequest  $request  Permintaan yang telah disahkan
     */
    public function upload(DocumentUploadRequest $request): JsonResponse
    {
        $requestId = $request->header('X-Request-ID', Str::uuid()->toString());

        try {
            $file = $request->file('file');
            $userId = $request->user()->id;

            // Muat naik dokumen
            $document = $this->documentService->uploadDocument($file, $userId);

            // Dispatch job untuk pemprosesan latar belakang
            DocumentIngestJob::dispatch($document);

            Log::info('Document uploaded successfully', [
                'request_id' => $requestId,
                'document_id' => $document->id,
                'filename' => $document->filename,
                'user_id' => $userId,
            ]);

            return $this->successResponse([
                'message' => 'Dokumen berjaya dimuat naik dan sedang diproses.',
                'document' => [
                    'id' => $document->id,
                    'filename' => $document->filename,
                    'status' => $document->status,
                    'created_at' => $document->created_at->toISOString(),
                ],
                'request_id' => $requestId,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Document upload failed', [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse(
                'Gagal memuat naik dokumen: '.$e->getMessage(),
                500,
                $requestId
            );
        }
    }

    /**
     * Dapatkan status pemprosesan dokumen
     *
     * @param  int  $id  ID dokumen
     */
    public function status(int $id): JsonResponse
    {
        try {
            $document = Document::with('chunks')->findOrFail($id);

            return $this->successResponse([
                'document' => [
                    'id' => $document->id,
                    'filename' => $document->filename,
                    'status' => $document->status,
                    'chunks_count' => $document->chunks->count(),
                    'chunks_with_embeddings' => $document->chunks->filter(fn ($c) => ! empty($c->embedding))->count(),
                    'metadata' => $document->metadata,
                    'created_at' => $document->created_at->toISOString(),
                    'updated_at' => $document->updated_at->toISOString(),
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Dokumen tidak dijumpai.', 404);
        }
    }

    /**
     * Senaraikan semua dokumen
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->integer('per_page', 15), 100);
        $status = $request->input('status');

        $query = Document::query()
            ->with('uploader:id,name')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest();

        $documents = $query->paginate($perPage);

        return $this->successResponse([
            'documents' => $documents->items(),
            'pagination' => [
                'current_page' => $documents->currentPage(),
                'last_page' => $documents->lastPage(),
                'per_page' => $documents->perPage(),
                'total' => $documents->total(),
            ],
        ]);
    }

    /**
     * Proses semula dokumen yang gagal
     *
     * @param  int  $id  ID dokumen
     */
    public function reprocess(int $id): JsonResponse
    {
        try {
            $document = Document::findOrFail($id);

            if ($document->status !== Document::STATUS_FAILED) {
                return $this->errorResponse(
                    'Hanya dokumen yang gagal boleh diproses semula.',
                    400
                );
            }

            // Reset dan dispatch job baharu
            $document->update(['status' => Document::STATUS_PENDING]);
            DocumentIngestJob::dispatch($document);

            Log::info('Document reprocessing initiated', [
                'document_id' => $document->id,
                'filename' => $document->filename,
            ]);

            return $this->successResponse([
                'message' => 'Dokumen sedang diproses semula.',
                'document' => [
                    'id' => $document->id,
                    'filename' => $document->filename,
                    'status' => $document->status,
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Dokumen tidak dijumpai.', 404);
        }
    }

    /**
     * Padam dokumen
     *
     * @param  int  $id  ID dokumen
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $document = Document::findOrFail($id);

            $success = $this->documentService->deleteDocument($document);

            if ($success) {
                Log::info('Document deleted', [
                    'document_id' => $id,
                ]);

                return $this->successResponse([
                    'message' => 'Dokumen berjaya dipadam.',
                ]);
            }

            return $this->errorResponse('Gagal memadam dokumen.', 500);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Dokumen tidak dijumpai.', 404);
        }
    }

    /**
     * Dapatkan statistik dokumen
     */
    public function stats(): JsonResponse
    {
        $stats = $this->documentService->getDocumentStats();

        return $this->successResponse(['stats' => $stats]);
    }

    /**
     * Format respons berjaya
     */
    private function successResponse(array $data, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    /**
     * Format respons ralat dalam Bahasa Melayu
     */
    private function errorResponse(string $message, int $status, ?string $requestId = null): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => [
                'message' => $message,
                'code' => $status,
            ],
        ];

        if ($requestId) {
            $response['request_id'] = $requestId;
        }

        return response()->json($response, $status);
    }
}

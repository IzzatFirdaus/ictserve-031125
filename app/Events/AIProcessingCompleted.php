<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * Event broadcast when AI processing completes successfully
 *
 * Digunakan untuk memberitahu admin/superuser apabila pemprosesan AI selesai
 * dengan jayanya termasuk document ingestion, FAQ processing, atau auto-reply.
 *
 * @version 3.6.0
 *
 * @compliance D16 Broadcasting Setup v3.6.0
 *
 * @requirements 11.1, 11.2 - Real-time AI notifications
 */
class AIProcessingCompleted implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public string $operationType;

    public string $requestId;

    public string $message;

    public array $result;

    public float $processingTime;

    public string $completedAt;

    /**
     * Create a new event instance.
     *
     * @param  string  $operationType  Jenis operasi yang selesai
     * @param  array  $result  Hasil pemprosesan (sanitized)
     * @param  float  $processingTime  Masa pemprosesan dalam saat
     * @param  string|null  $requestId  X-Request-ID untuk audit trail
     */
    public function __construct(
        string $operationType,
        array $result = [],
        float $processingTime = 0.0,
        ?string $requestId = null
    ) {
        $this->operationType = $operationType;
        $this->requestId = $requestId ?? (string) Str::uuid();
        $this->result = $this->sanitizeResult($result);
        $this->processingTime = round($processingTime, 3);
        $this->completedAt = now()->timezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s');
        $this->message = $this->generateMessage($operationType);
    }

    private function generateMessage(string $operationType): string
    {
        return match ($operationType) {
            'document_ingestion' => 'Pemprosesan dokumen selesai dengan jayanya',
            'faq_query' => 'Pertanyaan FAQ telah dijawab',
            'auto_reply_generation' => 'Draf balasan automatik telah dijana',
            'embedding_generation' => 'Penjanaan embedding selesai',
            'document_analysis' => 'Analisis dokumen selesai',
            default => 'Pemprosesan AI selesai dengan jayanya',
        };
    }

    private function sanitizeResult(array $result): array
    {
        $sanitized = [];
        $allowedKeys = [
            'document_id',
            'chunks_processed',
            'embeddings_generated',
            'confidence_score',
            'sources_count',
            'draft_id',
            'status',
            'cache_hit',
        ];

        foreach ($allowedKeys as $key) {
            if (isset($result[$key])) {
                $sanitized[$key] = $result[$key];
            }
        }

        return $sanitized;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ai-status'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'AIProcessingCompleted';
    }

    public function broadcastWith(): array
    {
        return [
            'operationType' => $this->operationType,
            'requestId' => $this->requestId,
            'message' => $this->message,
            'result' => $this->result,
            'processingTime' => $this->processingTime,
            'completedAt' => $this->completedAt,
            'locale' => 'ms',
        ];
    }
}

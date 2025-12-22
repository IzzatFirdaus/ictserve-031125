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
 * Event broadcast when AI processing starts
 *
 * Digunakan untuk memberitahu admin/superuser apabila pemprosesan AI dimulakan
 * seperti document ingestion, FAQ processing, atau auto-reply generation.
 *
 * @version 3.6.0
 *
 * @compliance D16 Broadcasting Setup v3.6.0
 *
 * @requirements 11.1, 11.2 - Real-time AI notifications
 */
class AIProcessingStarted implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Jenis operasi AI yang dimulakan
     */
    public string $operationType;

    /**
     * ID permintaan untuk kebolehkesanan (X-Request-ID)
     */
    public string $requestId;

    /**
     * Mesej notifikasi dalam Bahasa Melayu
     */
    public string $message;

    /**
     * Metadata tambahan untuk operasi
     */
    public array $metadata;

    /**
     * Cap masa operasi dimulakan
     */
    public string $startedAt;

    /**
     * Create a new event instance.
     *
     * @param  string  $operationType  Jenis operasi: 'document_ingestion', 'faq_query', 'auto_reply_generation', 'embedding_generation'
     * @param  array  $metadata  Metadata tambahan (document_id, user_id, etc.)
     * @param  string|null  $requestId  X-Request-ID untuk audit trail
     */
    

/**
 * @param array<string, mixed> $metadata
 */
public function __construct(
        string $operationType,
        array $metadata = [],
        ?string $requestId = null
    ) {
        $this->operationType = $operationType;
        $this->requestId = $requestId ?? (string) Str::uuid();
        $this->metadata = $this->sanitizeMetadata($metadata);
        $this->startedAt = now()->timezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s');
        $this->message = $this->generateMessage($operationType);
    }

    /**
     * Generate message in Bahasa Melayu based on operation type
     */
    private function generateMessage(string $operationType): string
    {
        return match ($operationType) {
            'document_ingestion' => 'Pemprosesan dokumen dimulakan...',
            'faq_query' => 'Pertanyaan FAQ sedang diproses...',
            'auto_reply_generation' => 'Penjanaan balasan automatik dimulakan...',
            'embedding_generation' => 'Penjanaan embedding sedang berjalan...',
            'document_analysis' => 'Analisis dokumen dimulakan...',
            default => 'Pemprosesan AI dimulakan...',
        };
    }

    /**
     * Sanitize metadata to remove PII
     */
    

/**
  * @param array<string, mixed> $metadata

 * @return array<string, mixed>
 */
private function sanitizeMetadata(array $metadata): array
    {
        $sanitized = [];
        $allowedKeys = [
            'document_id',
            'document_name',
            'operation_id',
            'chunk_count',
            'estimated_time',
            'priority',
            'queue_position',
        ];

        foreach ($allowedKeys as $key) {
            if (isset($metadata[$key])) {
                $sanitized[$key] = $metadata[$key];
            }
        }

        return $sanitized;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    

/**
 * @return array<string, mixed>
 */
public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ai-status'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'AIProcessingStarted';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    

/**
 * @return array<string, mixed>
 */
public function broadcastWith(): array
    {
        return [
            'operationType' => $this->operationType,
            'requestId' => $this->requestId,
            'message' => $this->message,
            'metadata' => $this->metadata,
            'startedAt' => $this->startedAt,
            'locale' => 'ms', // Bahasa Melayu sahaja (D15 v3.6.0)
        ];
    }
}

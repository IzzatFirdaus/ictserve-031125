<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * Event broadcast when AI service enters degraded mode
 *
 * Digunakan untuk memberitahu admin/superuser apabila perkhidmatan AI
 * memasuki mod degradasi disebabkan beban tinggi atau masalah prestasi.
 *
 * @version 3.6.0
 *
 * @compliance D16 Broadcasting Setup v3.6.0, D11 Technical Design v3.6.0
 *
 * @requirements 8.3, 8.4 - Graceful degradation
 */
class AIServiceDegraded implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public int $previousTier;

    public int $currentTier;

    public string $requestId;

    public string $message;

    public string $reason;

    public array $availableFeatures;

    public string $degradedAt;

    /**
     * Create a new event instance.
     *
     * @param  int  $previousTier  Tahap degradasi sebelum (1-4)
     * @param  int  $currentTier  Tahap degradasi semasa (1-4)
     * @param  string  $reason  Sebab degradasi
     * @param  string|null  $requestId  X-Request-ID untuk audit trail
     */
    public function __construct(
        int $previousTier,
        int $currentTier,
        string $reason = '',
        ?string $requestId = null
    ) {
        $this->previousTier = $previousTier;
        $this->currentTier = $currentTier;
        $this->reason = $reason;
        $this->requestId = $requestId ?? (string) Str::uuid();
        $this->message = $this->generateMessage($currentTier);
        $this->availableFeatures = $this->getAvailableFeatures($currentTier);
        $this->degradedAt = now()->timezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s');
    }

    private function generateMessage(int $tier): string
    {
        return match ($tier) {
            2 => 'Perkhidmatan AI dalam mod terhad - respons cache digunakan',
            3 => 'Perkhidmatan AI dalam mod minimum - carian FAQ statik sahaja',
            4 => 'Perkhidmatan AI dalam mod kecemasan - fungsi terhad',
            default => 'Perkhidmatan AI beroperasi normal',
        };
    }

    private function getAvailableFeatures(int $tier): array
    {
        return match ($tier) {
            1 => ['faq_bot', 'document_analysis', 'auto_reply', 'embeddings'],
            2 => ['faq_bot_cached', 'document_analysis_limited', 'auto_reply_queued'],
            3 => ['faq_static_search', 'cached_responses_only'],
            4 => ['emergency_fallback', 'human_support_redirect'],
            default => [],
        };
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ai-alerts'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'AIServiceDegraded';
    }

    public function broadcastWith(): array
    {
        return [
            'previousTier' => $this->previousTier,
            'currentTier' => $this->currentTier,
            'requestId' => $this->requestId,
            'message' => $this->message,
            'reason' => $this->reason,
            'availableFeatures' => $this->availableFeatures,
            'degradedAt' => $this->degradedAt,
            'locale' => 'ms',
        ];
    }
}

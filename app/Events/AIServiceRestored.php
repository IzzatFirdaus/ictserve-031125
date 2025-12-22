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
 * Event broadcast when AI service is restored to normal operation
 *
 * Digunakan untuk memberitahu admin/superuser apabila perkhidmatan AI
 * dipulihkan ke operasi normal selepas degradasi.
 *
 * @version 3.6.0
 *
 * @compliance D16 Broadcasting Setup v3.6.0
 *
 * @requirements 8.3, 8.4 - Service restoration notification
 */
class AIServiceRestored implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public int $previousTier;

    public int $currentTier;

    public string $requestId;

    public string $message;

    public float $downtimeDuration;

    public string $restoredAt;

    /**
     * Create a new event instance.
     *
     * @param  int  $previousTier  Tahap degradasi sebelum
     * @param  int  $currentTier  Tahap semasa (biasanya 1)
     * @param  float  $downtimeDuration  Tempoh degradasi dalam saat
     * @param  string|null  $requestId  X-Request-ID untuk audit trail
     */
    public function __construct(
        int $previousTier,
        int $currentTier = 1,
        float $downtimeDuration = 0.0,
        ?string $requestId = null
    ) {
        $this->previousTier = $previousTier;
        $this->currentTier = $currentTier;
        $this->downtimeDuration = round($downtimeDuration, 2);
        $this->requestId = $requestId ?? (string) Str::uuid();
        $this->message = $this->generateMessage($previousTier, $downtimeDuration);
        $this->restoredAt = now()->timezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s');
    }

    private function generateMessage(int $previousTier, float $duration): string
    {
        $tierName = match ($previousTier) {
            2 => 'mod terhad',
            3 => 'mod minimum',
            4 => 'mod kecemasan',
            default => 'mod degradasi',
        };

        $durationText = $duration > 60
            ? sprintf('%.1f minit', $duration / 60)
            : sprintf('%.0f saat', $duration);

        return sprintf(
            'Perkhidmatan AI dipulihkan dari %s (tempoh: %s)',
            $tierName,
            $durationText
        );
    }

    

/**
 * @return array<string, mixed>
 */
public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ai-alerts'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'AIServiceRestored';
    }

    

/**
 * @return array<string, mixed>
 */
public function broadcastWith(): array
    {
        return [
            'previousTier' => $this->previousTier,
            'currentTier' => $this->currentTier,
            'requestId' => $this->requestId,
            'message' => $this->message,
            'downtimeDuration' => $this->downtimeDuration,
            'restoredAt' => $this->restoredAt,
            'locale' => 'ms',
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event broadcast for real-time AI cache statistics
 *
 * Digunakan untuk menghantar statistik cache AI secara masa nyata
 * ke dashboard admin melalui Laravel Pulse integration.
 *
 * @version 3.6.0
 *
 * @compliance D16 Broadcasting Setup v3.6.0
 *
 * @requirements 8.4, 11.3 - Cache performance monitoring
 */
class AICacheStatsUpdate implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public array $stats;

    public string $timestamp;

    /**
     * Create a new event instance.
     *
     * @param  array<string, mixed>  $stats  Statistik cache AI
     */
    

/**
 * @param array<string, mixed> $stats
 */
public function __construct(array $stats)
    {
        $this->stats = $this->sanitizeStats($stats);
        $this->timestamp = now()->timezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s');
    }

    /**
     * Sanitize stats to only include allowed keys.
     *
     * @param  array<string, mixed>  $stats
     * @return array<string, mixed>
     */
    

/**
 * @return array<string, mixed>
 */
private function sanitizeStats(array $stats): array
    {
        $allowedKeys = [
            'hit_rate',
            'miss_rate',
            'total_hits',
            'total_misses',
            'cache_size_mb',
            'faq_cache_entries',
            'embedding_cache_entries',
            'avg_cache_age_seconds',
        ];

        return array_intersect_key($stats, array_flip($allowedKeys));
    }

    

/**
 * @return array<string, mixed>
 */
public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ai-performance'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'AICacheStatsUpdate';
    }

    

/**
 * @return array<string, mixed>
 */
public function broadcastWith(): array
    {
        return [
            'stats' => $this->stats,
            'timestamp' => $this->timestamp,
            'locale' => 'ms',
        ];
    }
}

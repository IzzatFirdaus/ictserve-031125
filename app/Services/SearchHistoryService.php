<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Search History Service
 *
 * Tracks search history with analytics (50 items max).
 *
 * @version 1.0.0
 *
 * @since 2025-01-06
 */
class SearchHistoryService
{
    private const MAX_HISTORY = 50;

    public function addSearch(string $query, string $userId): void
    {
        $history = $this->getHistory($userId);

        array_unshift($history, [
            'query' => $query,
            'timestamp' => now()->toIso8601String(),
        ]);

        $history = array_slice($history, 0, self::MAX_HISTORY);

        Cache::put("search_history:{$userId}", $history, 86400);
    }

    public function getHistory(string $userId): array
    {
        return Cache::get("search_history:{$userId}", []);
    }

    public function clearHistory(string $userId): void
    {
        Cache::forget("search_history:{$userId}");
    }

    public function getAnalytics(string $userId): array
    {
        $history = $this->getHistory($userId);

        return [
            'total_searches' => count($history),
            'top_queries' => array_slice(array_count_values(array_column($history, 'query')), 0, 10),
            'recent_searches' => array_slice($history, 0, 10),
        ];
    }
}

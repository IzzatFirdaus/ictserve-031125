<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * Search History Service
 *
 * Manages user search history, preferences, and quick access to recent searches.
 * Provides search result persistence and bookmarking capabilities.
 *
 * @trace Requirements 11.3
 */
class SearchHistoryService
{
    private const CACHE_TTL = 3600; // 1 hour

    private const MAX_HISTORY_ITEMS = 50;

    private const MAX_RECENT_SEARCHES = 10;

    public function recordSearch(User $user, string $query, string $resource = 'global', array $filters = [], int $resultCount = 0): void
    {
        $history = $this->getUserSearchHistory($user);

        $searchEntry = [
            'id' => uniqid(),
            'query' => $query,
            'resource' => $resource,
            'filters' => $filters,
            'result_count' => $resultCount,
            'timestamp' => now()->toISOString(),
        ];

        // Remove duplicate searches (same query and resource)
        $history = array_filter($history, function ($entry) use ($query, $resource) {
            return ! ($entry['query'] === $query && $entry['resource'] === $resource);
        });

        // Add new search to the beginning
        array_unshift($history, $searchEntry);

        // Limit history size
        $history = array_slice($history, 0, self::MAX_HISTORY_ITEMS);

        $this->storeUserSearchHistory($user, $history);
    }

    public function getUserSearchHistory(User $user): array
    {
        $cacheKey = "search_history:{$user->id}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user) {
            $preferences = $user->filament_preferences ?? [];

            return $preferences['search_history'] ?? [];
        });
    }

    public function getRecentSearches(User $user, ?string $resource = null): array
    {
        $history = $this->getUserSearchHistory($user);

        if ($resource) {
            $history = array_filter($history, fn ($entry) => $entry['resource'] === $resource);
        }

        return array_slice($history, 0, self::MAX_RECENT_SEARCHES);
    }

    public function getPopularSearches(User $user, ?string $resource = null): array
    {
        $history = $this->getUserSearchHistory($user);

        if ($resource) {
            $history = array_filter($history, fn ($entry) => $entry['resource'] === $resource);
        }

        // Count search frequency
        $searchCounts = [];
        foreach ($history as $entry) {
            $key = $entry['query'].'|'.$entry['resource'];
            $searchCounts[$key] = ($searchCounts[$key] ?? 0) + 1;
        }

        // Sort by frequency and get top searches
        arsort($searchCounts);
        $popularSearches = [];

        foreach (array_slice($searchCounts, 0, 10, true) as $key => $count) {
            [$query, $searchResource] = explode('|', $key);

            // Find the most recent entry for this search
            $recentEntry = null;
            foreach ($history as $entry) {
                if ($entry['query'] === $query && $entry['resource'] === $searchResource) {
                    $recentEntry = $entry;
                    break;
                }
            }

            if ($recentEntry) {
                $popularSearches[] = [
                    'query' => $query,
                    'resource' => $searchResource,
                    'count' => $count,
                    'last_searched' => $recentEntry['timestamp'],
                    'filters' => $recentEntry['filters'],
                ];
            }
        }

        return $popularSearches;
    }

    public function saveBookmark(User $user, string $name, string $url, array $filters = [], string $resource = 'global'): array
    {
        $bookmarks = $this->getUserBookmarks($user);

        $bookmark = [
            'id' => uniqid(),
            'name' => $name,
            'url' => $url,
            'filters' => $filters,
            'resource' => $resource,
            'created_at' => now()->toISOString(),
        ];

        $bookmarks[$bookmark['id']] = $bookmark;
        $this->storeUserBookmarks($user, $bookmarks);

        return $bookmark;
    }

    public function getUserBookmarks(User $user): array
    {
        $cacheKey = "search_bookmarks:{$user->id}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user) {
            $preferences = $user->filament_preferences ?? [];

            return $preferences['search_bookmarks'] ?? [];
        });
    }

    public function deleteBookmark(User $user, string $bookmarkId): bool
    {
        $bookmarks = $this->getUserBookmarks($user);

        if (! isset($bookmarks[$bookmarkId])) {
            return false;
        }

        unset($bookmarks[$bookmarkId]);
        $this->storeUserBookmarks($user, $bookmarks);

        return true;
    }

    public function getSearchSuggestions(User $user, string $query, ?string $resource = null): array
    {
        $history = $this->getUserSearchHistory($user);
        $suggestions = [];

        // Filter by resource if specified
        if ($resource) {
            $history = array_filter($history, fn ($entry) => $entry['resource'] === $resource);
        }

        // Find matching searches from history
        foreach ($history as $entry) {
            if (stripos($entry['query'], $query) !== false && $entry['query'] !== $query) {
                $suggestions[] = [
                    'query' => $entry['query'],
                    'resource' => $entry['resource'],
                    'result_count' => $entry['result_count'],
                    'type' => 'history',
                ];
            }
        }

        // Add common search patterns
        $commonPatterns = $this->getCommonSearchPatterns($query);
        foreach ($commonPatterns as $pattern) {
            $suggestions[] = [
                'query' => $pattern['query'],
                'resource' => $resource ?? 'global',
                'description' => $pattern['description'],
                'type' => 'pattern',
            ];
        }

        // Remove duplicates and limit results
        $suggestions = array_unique($suggestions, SORT_REGULAR);

        return array_slice($suggestions, 0, 8);
    }

    protected function getCommonSearchPatterns(string $query): array
    {
        $patterns = [];

        // Ticket patterns
        if (preg_match('/^TKT/i', $query) || stripos($query, 'ticket') !== false) {
            $patterns[] = [
                'query' => 'TKT-'.date('Y'),
                'description' => 'Tiket tahun ini',
            ];
        }

        // Loan patterns
        if (preg_match('/^LA/i', $query) || stripos($query, 'loan') !== false) {
            $patterns[] = [
                'query' => 'LA-'.date('Y'),
                'description' => 'Pinjaman tahun ini',
            ];
        }

        // Asset patterns
        if (preg_match('/^(LT|PC|PR)-/i', $query) || stripos($query, 'laptop') !== false) {
            $patterns[] = [
                'query' => 'laptop available',
                'description' => 'Laptop yang tersedia',
            ];
        }

        // Status patterns
        $statusKeywords = ['open', 'pending', 'approved', 'maintenance', 'available'];
        foreach ($statusKeywords as $status) {
            if (stripos($query, $status) !== false) {
                $patterns[] = [
                    'query' => $status,
                    'description' => 'Semua item dengan status '.$status,
                ];
            }
        }

        return $patterns;
    }

    public function getSearchAnalytics(User $user, int $days = 30): array
    {
        $history = $this->getUserSearchHistory($user);
        $cutoffDate = now()->subDays($days);

        // Filter recent searches
        $recentHistory = array_filter($history, function ($entry) use ($cutoffDate) {
            return \Carbon\Carbon::parse($entry['timestamp'])->isAfter($cutoffDate);
        });

        $analytics = [
            'total_searches' => count($recentHistory),
            'unique_queries' => count(array_unique(array_column($recentHistory, 'query'))),
            'avg_results_per_search' => 0,
            'most_searched_resource' => null,
            'search_frequency_by_day' => [],
            'top_queries' => [],
        ];

        if (empty($recentHistory)) {
            return $analytics;
        }

        // Calculate average results per search
        $totalResults = array_sum(array_column($recentHistory, 'result_count'));
        $analytics['avg_results_per_search'] = round($totalResults / count($recentHistory), 1);

        // Find most searched resource
        $resourceCounts = array_count_values(array_column($recentHistory, 'resource'));
        arsort($resourceCounts);
        $analytics['most_searched_resource'] = array_key_first($resourceCounts);

        // Search frequency by day
        $dailyCounts = [];
        foreach ($recentHistory as $entry) {
            $date = \Carbon\Carbon::parse($entry['timestamp'])->format('Y-m-d');
            $dailyCounts[$date] = ($dailyCounts[$date] ?? 0) + 1;
        }
        $analytics['search_frequency_by_day'] = $dailyCounts;

        // Top queries
        $queryCounts = array_count_values(array_column($recentHistory, 'query'));
        arsort($queryCounts);
        $analytics['top_queries'] = array_slice($queryCounts, 0, 10, true);

        return $analytics;
    }

    protected function storeUserSearchHistory(User $user, array $history): void
    {
        $preferences = $user->filament_preferences ?? [];
        $preferences['search_history'] = $history;

        $user->update(['filament_preferences' => $preferences]);

        // Clear cache
        $cacheKey = "search_history:{$user->id}";
        Cache::forget($cacheKey);
    }

    protected function storeUserBookmarks(User $user, array $bookmarks): void
    {
        $preferences = $user->filament_preferences ?? [];
        $preferences['search_bookmarks'] = $bookmarks;

        $user->update(['filament_preferences' => $preferences]);

        // Clear cache
        $cacheKey = "search_bookmarks:{$user->id}";
        Cache::forget($cacheKey);
    }

    public function clearUserHistory(User $user): void
    {
        $preferences = $user->filament_preferences ?? [];
        $preferences['search_history'] = [];

        $user->update(['filament_preferences' => $preferences]);

        // Clear cache
        $cacheKey = "search_history:{$user->id}";
        Cache::forget($cacheKey);
    }

    public function clearUserCache(User $user): void
    {
        $cacheKeys = [
            "search_history:{$user->id}",
            "search_bookmarks:{$user->id}",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }
}

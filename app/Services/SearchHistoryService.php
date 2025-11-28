<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SavedSearch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Search History Service
 *
 * Manages user search history and saved searches for cross-module search.
 * Works with existing saved_searches table schema.
 *
 * @see D03-FR-011.2 (Cross-module search functionality)
 * @see D04 §5.2 (Cross-Module Search System)
 */
class SearchHistoryService
{
    /**
     * Maximum number of history entries to keep per user.
     */
    private const MAX_HISTORY_ENTRIES = 50;

    /**
     * Maximum number of saved searches per user.
     */
    private const MAX_SAVED_SEARCHES = 20;

    /**
     * Record a search in history.
     *
     * @param  array<string, mixed>  $filters
     */
    public function recordSearch(string $query, array $filters = [], int $resultCount = 0): ?SavedSearch
    {
        $userId = Auth::id();
        if (! $userId || empty(trim($query))) {
            return null;
        }

        // Check if same search exists in recent history (last 24 hours)
        $existing = SavedSearch::forUser($userId)
            ->history()
            ->whereJsonContains('filters->query', $query)
            ->where('created_at', '>=', now()->subDay())
            ->first();

        if ($existing) {
            // Update existing entry
            $existingFilters = $existing->filters ?? [];
            $existingFilters['query'] = $query;
            $existingFilters['search_filters'] = $filters;
            $existingFilters['result_count'] = $resultCount;
            $existingFilters['last_used_at'] = now()->toIso8601String();

            $existing->update(['filters' => $existingFilters]);

            return $existing;
        }

        // Create new history entry
        $search = SavedSearch::create([
            'user_id' => $userId,
            'name' => null,
            'search_type' => SavedSearch::TYPE_HISTORY,
            'filters' => [
                'query' => $query,
                'search_filters' => $filters,
                'result_count' => $resultCount,
                'last_used_at' => now()->toIso8601String(),
            ],
        ]);

        // Cleanup old history entries
        $this->cleanupHistory($userId);

        return $search;
    }

    /**
     * Save a search for quick access.
     *
     * @param  array<string, mixed>  $filters
     */
    public function saveSearch(string $name, string $query, array $filters = []): ?SavedSearch
    {
        $userId = Auth::id();
        if (! $userId || empty(trim($query)) || empty(trim($name))) {
            return null;
        }

        // Check if user has reached max saved searches
        $savedCount = SavedSearch::forUser($userId)->saved()->count();
        if ($savedCount >= self::MAX_SAVED_SEARCHES) {
            return null;
        }

        // Check if same name already exists
        $existing = SavedSearch::forUser($userId)
            ->saved()
            ->where('name', $name)
            ->first();

        if ($existing) {
            // Update existing saved search
            $existing->update([
                'filters' => [
                    'query' => $query,
                    'search_filters' => $filters,
                    'last_used_at' => now()->toIso8601String(),
                ],
            ]);

            return $existing;
        }

        return SavedSearch::create([
            'user_id' => $userId,
            'name' => $name,
            'search_type' => SavedSearch::TYPE_SAVED,
            'filters' => [
                'query' => $query,
                'search_filters' => $filters,
                'last_used_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Get user's search history.
     *
     * @return Collection<int, SavedSearch>
     */
    public function getHistory(int $limit = 10): Collection
    {
        $userId = Auth::id();
        if (! $userId) {
            return collect();
        }

        return SavedSearch::forUser($userId)
            ->history()
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user's saved searches.
     *
     * @return Collection<int, SavedSearch>
     */
    public function getSavedSearches(): Collection
    {
        $userId = Auth::id();
        if (! $userId) {
            return collect();
        }

        return SavedSearch::forUser($userId)
            ->saved()
            ->orderBy('name')
            ->get();
    }

    /**
     * Delete a saved search.
     */
    public function deleteSavedSearch(int $searchId): bool
    {
        $userId = Auth::id();
        if (! $userId) {
            return false;
        }

        return SavedSearch::forUser($userId)
            ->where('id', $searchId)
            ->delete() > 0;
    }

    /**
     * Clear user's search history.
     */
    public function clearHistory(): int
    {
        $userId = Auth::id();
        if (! $userId) {
            return 0;
        }

        return SavedSearch::forUser($userId)
            ->history()
            ->delete();
    }

    /**
     * Update last used timestamp for a saved search.
     */
    public function markAsUsed(int $searchId): void
    {
        $userId = Auth::id();
        if (! $userId) {
            return;
        }

        $search = SavedSearch::forUser($userId)->where('id', $searchId)->first();
        if ($search) {
            $filters = $search->filters ?? [];
            $filters['last_used_at'] = now()->toIso8601String();
            $search->update(['filters' => $filters]);
        }
    }

    /**
     * Cleanup old history entries to maintain limit.
     */
    private function cleanupHistory(int $userId): void
    {
        $historyCount = SavedSearch::forUser($userId)->history()->count();

        if ($historyCount > self::MAX_HISTORY_ENTRIES) {
            $toDelete = $historyCount - self::MAX_HISTORY_ENTRIES;

            $oldestIds = SavedSearch::forUser($userId)
                ->history()
                ->orderBy('updated_at')
                ->limit($toDelete)
                ->pluck('id');

            SavedSearch::whereIn('id', $oldestIds)->delete();
        }
    }
}

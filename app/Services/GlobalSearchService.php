<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Global Search Service
 *
 * Provides unified search across all resources with caching,
 * filtering, and relevance scoring.
 *
 * @version 1.0.0
 *
 * @since 2025-01-06
 *
 * @author ICTServe Development Team
 * @copyright 2025 MOTAC BPM
 *
 * Requirements: D03-FR-012 (Global Search)
 * Traceability: Phase 11.1 - Global Search Service
 * WCAG 2.2 AA: N/A (Backend service)
 * Bilingual: N/A (Backend service)
 */
class GlobalSearchService
{
    /**
     * Cache duration in seconds (5 minutes)
     */
    private const CACHE_DURATION = 300;

    /**
     * Maximum results per resource type
     */
    private const MAX_RESULTS_PER_TYPE = 10;

    /**
     * Search across all resources
     *
     * @param  array<string>  $resourceTypes
     * @param  array<string, mixed>  $filters
     * @return array<string, array>
     */
    public function search(string $query, array $resourceTypes = [], array $filters = []): array
    {
        $cacheKey = $this->generateCacheKey($query, $resourceTypes, $filters);

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($query, $resourceTypes, $filters) {
            $results = [];

            if (empty($resourceTypes) || in_array('tickets', $resourceTypes)) {
                $results['tickets'] = $this->searchTickets($query, $filters);
            }

            if (empty($resourceTypes) || in_array('loans', $resourceTypes)) {
                $results['loans'] = $this->searchLoans($query, $filters);
            }

            if (empty($resourceTypes) || in_array('assets', $resourceTypes)) {
                $results['assets'] = $this->searchAssets($query, $filters);
            }

            if (empty($resourceTypes) || in_array('users', $resourceTypes)) {
                $results['users'] = $this->searchUsers($query, $filters);
            }

            return $this->sortByRelevance($results, $query);
        });
    }

    /**
     * Search helpdesk tickets
     *
     * @param  array<string, mixed>  $filters
     */
    private function searchTickets(string $query, array $filters): Collection
    {
        $queryBuilder = HelpdeskTicket::query()
            ->with(['user', 'assignedTo', 'division'])
            ->where(function ($q) use ($query) {
                $q->where('ticket_number', 'like', "%{$query}%")
                    ->orWhere('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('guest_name', 'like', "%{$query}%")
                    ->orWhere('guest_email', 'like', "%{$query}%");
            });

        // Apply filters
        if (! empty($filters['status'])) {
            $queryBuilder->where('status', $filters['status']);
        }

        if (! empty($filters['priority'])) {
            $queryBuilder->where('priority', $filters['priority']);
        }

        if (! empty($filters['category'])) {
            $queryBuilder->where('category', $filters['category']);
        }

        return $queryBuilder
            ->limit(self::MAX_RESULTS_PER_TYPE)
            ->get()
            ->map(function ($ticket) use ($query) {
                return [
                    'id' => $ticket->id,
                    'type' => 'ticket',
                    'title' => $ticket->title,
                    'subtitle' => $ticket->ticket_number,
                    'description' => $ticket->description,
                    'url' => route('filament.admin.resources.helpdesk.tickets.view', $ticket),
                    'relevance' => $this->calculateRelevance($query, [
                        $ticket->ticket_number,
                        $ticket->title,
                        $ticket->description,
                    ]),
                    'metadata' => [
                        'status' => $ticket->status,
                        'priority' => $ticket->priority,
                        'created_at' => $ticket->created_at->toIso8601String(),
                    ],
                ];
            });
    }

    /**
     * Search loan applications
     *
     * @param  array<string, mixed>  $filters
     */
    private function searchLoans(string $query, array $filters): Collection
    {
        $queryBuilder = LoanApplication::query()
            ->with(['applicant', 'loanItems.asset'])
            ->where(function ($q) use ($query) {
                $q->where('application_number', 'like', "%{$query}%")
                    ->orWhere('applicant_name', 'like', "%{$query}%")
                    ->orWhere('applicant_email', 'like', "%{$query}%")
                    ->orWhere('purpose', 'like', "%{$query}%");
            });

        // Apply filters
        if (! empty($filters['status'])) {
            $queryBuilder->where('status', $filters['status']);
        }

        if (! empty($filters['approval_status'])) {
            $queryBuilder->where('approval_status', $filters['approval_status']);
        }

        return $queryBuilder
            ->limit(self::MAX_RESULTS_PER_TYPE)
            ->get()
            ->map(function ($loan) use ($query) {
                return [
                    'id' => $loan->id,
                    'type' => 'loan',
                    'title' => $loan->applicant_name,
                    'subtitle' => $loan->application_number,
                    'description' => $loan->purpose,
                    'url' => route('filament.admin.resources.loans.applications.view', $loan),
                    'relevance' => $this->calculateRelevance($query, [
                        $loan->application_number,
                        $loan->applicant_name,
                        $loan->purpose,
                    ]),
                    'metadata' => [
                        'status' => $loan->status,
                        'approval_status' => $loan->approval_status,
                        'created_at' => $loan->created_at->toIso8601String(),
                    ],
                ];
            });
    }

    /**
     * Search assets
     *
     * @param  array<string, mixed>  $filters
     */
    private function searchAssets(string $query, array $filters): Collection
    {
        $queryBuilder = Asset::query()
            ->with(['category'])
            ->where(function ($q) use ($query) {
                $q->where('asset_tag', 'like', "%{$query}%")
                    ->orWhere('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('serial_number', 'like', "%{$query}%");
            });

        // Apply filters
        if (! empty($filters['category'])) {
            $queryBuilder->where('category_id', $filters['category']);
        }

        if (! empty($filters['status'])) {
            $queryBuilder->where('status', $filters['status']);
        }

        return $queryBuilder
            ->limit(self::MAX_RESULTS_PER_TYPE)
            ->get()
            ->map(function ($asset) use ($query) {
                return [
                    'id' => $asset->id,
                    'type' => 'asset',
                    'title' => $asset->name,
                    'subtitle' => $asset->asset_tag,
                    'description' => $asset->description,
                    'url' => route('filament.admin.resources.assets.view', $asset),
                    'relevance' => $this->calculateRelevance($query, [
                        $asset->asset_tag,
                        $asset->name,
                        $asset->description,
                    ]),
                    'metadata' => [
                        'status' => $asset->status,
                        'category' => $asset->category?->name,
                        'availability' => $asset->availability,
                    ],
                ];
            });
    }

    /**
     * Search users
     *
     * @param  array<string, mixed>  $filters
     */
    private function searchUsers(string $query, array $filters): Collection
    {
        $queryBuilder = User::query()
            ->with(['division', 'grade', 'roles'])
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('staff_id', 'like', "%{$query}%");
            });

        // Apply filters
        if (! empty($filters['role'])) {
            $queryBuilder->role($filters['role']);
        }

        if (! empty($filters['division'])) {
            $queryBuilder->where('division_id', $filters['division']);
        }

        return $queryBuilder
            ->limit(self::MAX_RESULTS_PER_TYPE)
            ->get()
            ->map(function ($user) use ($query) {
                return [
                    'id' => $user->id,
                    'type' => 'user',
                    'title' => $user->name,
                    'subtitle' => $user->email,
                    'description' => $user->division?->name,
                    'url' => route('filament.admin.resources.users.view', $user),
                    'relevance' => $this->calculateRelevance($query, [
                        $user->name,
                        $user->email,
                        $user->staff_id,
                    ]),
                    'metadata' => [
                        'role' => $user->roles->pluck('name')->first(),
                        'division' => $user->division?->name,
                        'grade' => $user->grade?->name,
                    ],
                ];
            });
    }

    /**
     * Calculate relevance score
     *
     * @param  array<string>  $fields
     */
    private function calculateRelevance(string $query, array $fields): float
    {
        $score = 0.0;
        $query = strtolower($query);

        foreach ($fields as $index => $field) {
            if (empty($field)) {
                continue;
            }

            $field = strtolower($field);

            // Exact match gets highest score
            if ($field === $query) {
                $score += 100.0 / ($index + 1);
            }
            // Starts with query gets high score
            elseif (str_starts_with($field, $query)) {
                $score += 50.0 / ($index + 1);
            }
            // Contains query gets medium score
            elseif (str_contains($field, $query)) {
                $score += 25.0 / ($index + 1);
            }
        }

        return $score;
    }

    /**
     * Sort results by relevance
     *
     * @param  array<string, Collection>  $results
     * @return array<string, array>
     */
    private function sortByRelevance(array $results, string $query): array
    {
        $sorted = [];

        foreach ($results as $type => $items) {
            $sorted[$type] = $items->sortByDesc('relevance')->values()->toArray();
        }

        return $sorted;
    }

    /**
     * Generate cache key
     *
     * @param  array<string>  $resourceTypes
     * @param  array<string, mixed>  $filters
     */
    private function generateCacheKey(string $query, array $resourceTypes, array $filters): string
    {
        return 'global_search:'.md5($query.json_encode($resourceTypes).json_encode($filters));
    }

    /**
     * Clear search cache
     */
    public function clearCache(): void
    {
        Cache::flush();
    }

    /**
     * Get search suggestions
     *
     * @return array<int, string>
     */
    public function getSuggestions(string $query, int $limit = 5): array
    {
        $suggestions = [];

        // Get recent searches from cache
        $recentSearches = Cache::get('recent_searches', []);

        foreach ($recentSearches as $search) {
            if (str_contains(strtolower($search), strtolower($query))) {
                $suggestions[] = $search;
            }

            if (count($suggestions) >= $limit) {
                break;
            }
        }

        return $suggestions;
    }

    /**
     * Save search query
     */
    public function saveSearch(string $query): void
    {
        $recentSearches = Cache::get('recent_searches', []);

        // Add to beginning of array
        array_unshift($recentSearches, $query);

        // Keep only last 50 searches
        $recentSearches = array_slice(array_unique($recentSearches), 0, 50);

        Cache::put('recent_searches', $recentSearches, 86400); // 24 hours
    }
}

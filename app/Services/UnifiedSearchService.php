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
 * Unified Search Service
 *
 * Multi-resource search with caching and relevance ranking for Filament admin panel.
 *
 * @see D03-FR-009.1 Global search requirements
 * @see D03-FR-012.1 Advanced search and filtering
 * @see D04 §9.1 Search architecture
 */
class UnifiedSearchService
{
    /**
     * Cache duration in seconds (5 minutes)
     */
    private const CACHE_DURATION = 300;

    /**
     * Search across all resources with caching and relevance ranking
     *
     * @param  string  $query  Search query
     * @param  array  $resources  Resources to search (default: all)
     * @param  int  $limit  Maximum results per resource
     * @return array Grouped search results with relevance scores
     */
    public function search(string $query, array $resources = [], int $limit = 10): array
    {
        if (empty($query) || strlen($query) < 2) {
            return [];
        }

        $cacheKey = $this->getCacheKey($query, $resources, $limit);

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($query, $resources, $limit) {
            $results = [];

            $searchableResources = empty($resources) ? ['tickets', 'loans', 'assets', 'users'] : $resources;

            if (in_array('tickets', $searchableResources)) {
                $results['tickets'] = $this->searchTickets($query, $limit);
            }

            if (in_array('loans', $searchableResources)) {
                $results['loans'] = $this->searchLoans($query, $limit);
            }

            if (in_array('assets', $searchableResources)) {
                $results['assets'] = $this->searchAssets($query, $limit);
            }

            if (in_array('users', $searchableResources)) {
                $results['users'] = $this->searchUsers($query, $limit);
            }

            return $results;
        });
    }

    /**
     * Search helpdesk tickets
     */
    private function searchTickets(string $query, int $limit): Collection
    {
        return HelpdeskTicket::query()
            ->where(function ($q) use ($query) {
                $q->where('ticket_number', 'like', "%{$query}%")
                    ->orWhere('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('guest_name', 'like', "%{$query}%")
                    ->orWhere('guest_email', 'like', "%{$query}%");
            })
            ->with(['user', 'assignedTo', 'asset'])
            ->limit($limit)
            ->get()
            ->map(function ($ticket) use ($query) {
                return [
                    'id' => $ticket->id,
                    'title' => $ticket->title,
                    'subtitle' => $ticket->ticket_number,
                    'description' => $ticket->description,
                    'url' => route('filament.admin.resources.helpdesk.helpdesk-tickets.view', $ticket->id),
                    'relevance' => $this->calculateRelevance($query, [
                        $ticket->ticket_number,
                        $ticket->title,
                        $ticket->description,
                    ]),
                    'metadata' => [
                        'status' => $ticket->status,
                        'priority' => $ticket->priority,
                        'created_at' => $ticket->created_at->format('d M Y'),
                    ],
                ];
            })
            ->sortByDesc('relevance')
            ->values();
    }

    /**
     * Search loan applications
     */
    private function searchLoans(string $query, int $limit): Collection
    {
        return LoanApplication::query()
            ->where(function ($q) use ($query) {
                $q->where('application_number', 'like', "%{$query}%")
                    ->orWhere('applicant_name', 'like', "%{$query}%")
                    ->orWhere('applicant_email', 'like', "%{$query}%")
                    ->orWhere('purpose', 'like', "%{$query}%");
            })
            ->with(['user', 'division', 'loanItems.asset'])
            ->limit($limit)
            ->get()
            ->map(function ($loan) use ($query) {
                return [
                    'id' => $loan->id,
                    'title' => $loan->applicant_name,
                    'subtitle' => $loan->application_number,
                    'description' => $loan->purpose,
                    'url' => route('filament.admin.resources.loans.loan-applications.view', $loan->id),
                    'relevance' => $this->calculateRelevance($query, [
                        $loan->application_number,
                        $loan->applicant_name,
                        $loan->purpose,
                    ]),
                    'metadata' => [
                        'status' => $loan->status->value ?? (string) $loan->status,
                        'loan_date' => $loan->loan_start_date?->format('d M Y'),
                        'assets_count' => $loan->loanItems->count(),
                    ],
                ];
            })
            ->sortByDesc('relevance')
            ->values();
    }

    /**
     * Search assets
     */
    private function searchAssets(string $query, int $limit): Collection
    {
        return Asset::query()
            ->where(function ($q) use ($query) {
                $q->where('asset_code', 'like', "%{$query}%")
                    ->orWhere('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('serial_number', 'like', "%{$query}%");
            })
            ->with(['category'])
            ->limit($limit)
            ->get()
            ->map(function ($asset) use ($query) {
                return [
                    'id' => $asset->id,
                    'title' => $asset->name,
                    'subtitle' => $asset->asset_code,
                    'description' => $asset->description,
                    'url' => route('filament.admin.resources.assets.assets.view', $asset->id),
                    'relevance' => $this->calculateRelevance($query, [
                        $asset->asset_code,
                        $asset->name,
                        $asset->description,
                    ]),
                    'metadata' => [
                        'status' => $asset->status,
                        'condition' => $asset->condition,
                        'category' => $asset->category?->name_en,
                    ],
                ];
            })
            ->sortByDesc('relevance')
            ->values();
    }

    /**
     * Search users
     */
    private function searchUsers(string $query, int $limit): Collection
    {
        return User::query()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('staff_id', 'like', "%{$query}%");
            })
            ->with(['division'])
            ->limit($limit)
            ->get()
            ->map(function ($user) use ($query) {
                return [
                    'id' => $user->id,
                    'title' => $user->name,
                    'subtitle' => $user->email,
                    'description' => $user->division?->name_en,
                    'url' => route('filament.admin.resources.users.users.view', $user->id),
                    'relevance' => $this->calculateRelevance($query, [
                        $user->name,
                        $user->email,
                        $user->staff_id,
                    ]),
                    'metadata' => [
                        'role' => $user->roles->first()?->name,
                        'division' => $user->division?->name_en,
                        'grade' => $user->grade,
                    ],
                ];
            })
            ->sortByDesc('relevance')
            ->values();
    }

    /**
     * Calculate relevance score based on query match
     *
     * @param  string  $query  Search query
     * @param  array  $fields  Fields to check
     * @return float Relevance score (0-100)
     */
    private function calculateRelevance(string $query, array $fields): float
    {
        $score = 0;
        $query = strtolower($query);

        foreach ($fields as $field) {
            if (! $field) {
                continue;
            }

            $field = strtolower((string) $field);

            // Exact match: highest score
            if ($field === $query) {
                $score += 100;
            }
            // Starts with query: high score
            elseif (str_starts_with($field, $query)) {
                $score += 75;
            }
            // Contains query: medium score
            elseif (str_contains($field, $query)) {
                $score += 50;
            }
            // Word match: lower score
            else {
                $words = explode(' ', $query);
                foreach ($words as $word) {
                    if (str_contains($field, $word)) {
                        $score += 25;
                    }
                }
            }
        }

        return $score;
    }

    /**
     * Generate cache key for search query
     */
    private function getCacheKey(string $query, array $resources, int $limit): string
    {
        return 'unified_search:'.md5($query.implode(',', $resources).$limit);
    }

    /**
     * Clear search cache
     */
    public function clearCache(): void
    {
        Cache::flush();
    }
}

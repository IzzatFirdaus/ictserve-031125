<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Cross-Module Search Service
 *
 * Provides unified search functionality across helpdesk tickets and loan applications
 * with advanced filtering, ranking, and performance optimization.
 *
 * Uses FULLTEXT indexes for MySQL/MariaDB when available for improved search performance.
 *
 * @see D03-FR-011.2 (Cross-module search functionality)
 * @see D04 §5.2 (Cross-Module Search System)
 */
class CrossModuleSearchService
{
    /**
     * Cache TTL in seconds (5 minutes)
     */
    private const CACHE_TTL = 300;

    /**
     * Whether FULLTEXT search is available
     */
    private ?bool $fulltextAvailable = null;

    /**
     * Search across helpdesk tickets and loan applications
     *
     * @param  array<string, mixed>  $filters  Additional filters
     * @return LengthAwarePaginator<int, array<string, mixed>>&iterable<array<string, mixed>>
     */
    

/**
 * @param array<string, mixed> $filters
 */
public function search(string $query, array $filters = [], int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $cacheKey = $this->buildCacheKey($query, $filters, $perPage, $page);

        /** @var LengthAwarePaginator<int, array<string, mixed>>&iterable<array<string, mixed>> */
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($query, $filters, $perPage, $page): LengthAwarePaginator {
            $tickets = $this->searchTickets($query, $filters);
            $loans = $this->searchLoanApplications($query, $filters);
            $combinedResults = $this->combineAndRankResults($tickets, $loans, $query);
            $filteredResults = $this->applyFilters($combinedResults, $filters);

            return $this->paginateResults($filteredResults, $perPage, $page);
        });
    }

    /**
     * Check if FULLTEXT search is available
     */
    private function isFulltextAvailable(): bool
    {
        if ($this->fulltextAvailable === null) {
            $driver = DB::connection()->getDriverName();
            $this->fulltextAvailable = \in_array($driver, ['mysql', 'mariadb'], true);
        }

        return $this->fulltextAvailable;
    }

    /**
     * Search helpdesk tickets
     *
     * Uses FULLTEXT search for MySQL/MariaDB, falls back to LIKE for other databases.
     *
     * @param  array<string, mixed>  $filters
     * @return Collection<int, array<string, mixed>>
     */
    

/**
 * @param array<string, mixed> $filters
 */
private function searchTickets(string $query, array $filters): Collection
    {
        $ticketQuery = HelpdeskTicket::query()
            ->with(['user', 'category', 'assignedUser', 'division']);

        // Use FULLTEXT search for MySQL/MariaDB (3+ char queries), LIKE for others
        if ($this->isFulltextAvailable() && \strlen($query) >= 3) {
            $ticketQuery->where(function (Builder $q) use ($query) {
                $q->whereRaw(
                    'MATCH(ticket_number, subject, description, guest_name, guest_email) AGAINST(? IN BOOLEAN MODE)',
                    ["*{$query}*"]
                )
                    ->orWhereHas('user', function (Builder $userQuery) use ($query) {
                        $userQuery->where('name', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%");
                    });
            });
        } else {
            $ticketQuery->where(function (Builder $q) use ($query) {
                $q->where('ticket_number', 'like', "%{$query}%")
                    ->orWhere('subject', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('guest_name', 'like', "%{$query}%")
                    ->orWhere('guest_email', 'like', "%{$query}%")
                    ->orWhereHas('user', function (Builder $userQuery) use ($query) {
                        $userQuery->where('name', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%");
                    });
            });
        }

        // Apply module filter
        if (isset($filters['module']) && $filters['module'] !== 'all' && $filters['module'] !== 'helpdesk') {
            return collect();
        }

        // Apply status filter
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $ticketQuery->where('status', $filters['status']);
        }

        // Apply date range filter
        if (isset($filters['date_from']) && is_string($filters['date_from']) && $filters['date_from'] !== '') {
            $ticketQuery->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to']) && is_string($filters['date_to']) && $filters['date_to'] !== '') {
            $ticketQuery->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Apply user filter
        if (isset($filters['user_id']) && $filters['user_id']) {
            $ticketQuery->where('user_id', $filters['user_id']);
        }

        return $ticketQuery->get()->map(function ($ticket) use ($query): array {
            /** @var array<string, mixed> */
            return [
                'id' => $ticket->id,
                'type' => 'ticket',
                'title' => $ticket->subject,
                'description' => $ticket->description,
                'number' => $ticket->ticket_number,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'category' => $ticket->category->name ?? __('staff.search.unknown'),
                'user_name' => $ticket->user->name ?? $ticket->guest_name,
                'user_email' => $ticket->user->email ?? $ticket->guest_email,
                'division' => $ticket->division->name ?? $ticket->guest_division,
                'created_at' => $ticket->created_at,
                'updated_at' => $ticket->updated_at,
                'url' => route('portal.helpdesk.show', $ticket),
                'relevance_score' => $this->calculateRelevanceScore($query, [
                    'title' => $ticket->subject,
                    'description' => $ticket->description,
                    'number' => $ticket->ticket_number,
                ]),
            ];
        });
    }

    /**
     * Search loan applications
     *
     * @param  array<string, mixed>  $filters
     * @return Collection<int, array<string, mixed>>
     */
    

/**
 * @param array<string, mixed> $filters
 */
private function searchLoanApplications(string $query, array $filters): Collection
    {
        $loanQuery = LoanApplication::query()
            ->with(['user', 'loanItems.asset', 'approver', 'division']);

        // Use FULLTEXT search for MySQL/MariaDB (3+ char queries), LIKE for others
        if ($this->isFulltextAvailable() && \strlen($query) >= 3) {
            $loanQuery->where(function (Builder $q) use ($query) {
                $q->whereRaw(
                    'MATCH(application_number, purpose, applicant_name, applicant_email) AGAINST(? IN BOOLEAN MODE)',
                    ["*{$query}*"]
                )
                    ->orWhereHas('user', function (Builder $userQuery) use ($query) {
                        $userQuery->where('name', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%");
                    })
                    ->orWhereHas('loanItems.asset', function (Builder $assetQuery) use ($query) {
                        $assetQuery->where('name', 'like', "%{$query}%")
                            ->orWhere('model', 'like', "%{$query}%")
                            ->orWhere('serial_number', 'like', "%{$query}%");
                    });
            });
        } else {
            $loanQuery->where(function (Builder $q) use ($query) {
                $q->where('application_number', 'like', "%{$query}%")
                    ->orWhere('purpose', 'like', "%{$query}%")
                    ->orWhere('applicant_name', 'like', "%{$query}%")
                    ->orWhere('applicant_email', 'like', "%{$query}%")
                    ->orWhereHas('user', function (Builder $userQuery) use ($query) {
                        $userQuery->where('name', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%");
                    })
                    ->orWhereHas('loanItems.asset', function (Builder $assetQuery) use ($query) {
                        $assetQuery->where('name', 'like', "%{$query}%")
                            ->orWhere('model', 'like', "%{$query}%")
                            ->orWhere('serial_number', 'like', "%{$query}%");
                    });
            });
        }

        // Apply module filter
        if (isset($filters['module']) && $filters['module'] !== 'all' && $filters['module'] !== 'loans') {
            return collect();
        }

        // Apply status filter
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $loanQuery->where('status', $filters['status']);
        }

        // Apply date range filter
        if (isset($filters['date_from']) && is_string($filters['date_from']) && $filters['date_from'] !== '') {
            $loanQuery->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to']) && is_string($filters['date_to']) && $filters['date_to'] !== '') {
            $loanQuery->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Apply user filter
        if (isset($filters['user_id']) && $filters['user_id']) {
            $loanQuery->where('user_id', $filters['user_id']);
        }

        return $loanQuery->get()->map(function ($loan) use ($query): array {
            $assetNames = $loan->loanItems->map(fn ($item) => $item->asset?->name)->filter()->join(', ');

            /** @var array<string, mixed> */
            return [
                'id' => $loan->id,
                'type' => 'loan',
                'title' => $loan->purpose ?: __('staff.search.loan_application'),
                'description' => $loan->purpose,
                'number' => $loan->application_number,
                'status' => $loan->status,
                'priority' => $loan->priority ?? 'normal',
                'category' => __('staff.search.asset_loan'),
                'user_name' => $loan->user->name ?? $loan->applicant_name,
                'user_email' => $loan->user->email ?? $loan->applicant_email,
                'division' => $loan->division->name,
                'assets' => $assetNames,
                'loan_start_date' => $loan->loan_start_date,
                'loan_end_date' => $loan->loan_end_date,
                'created_at' => $loan->created_at,
                'updated_at' => $loan->updated_at,
                'url' => route('portal.loans.show', $loan),
                'relevance_score' => $this->calculateRelevanceScore($query, [
                    'title' => $loan->purpose,
                    'description' => $loan->purpose,
                    'number' => $loan->application_number,
                    'assets' => $assetNames,
                ]),
            ];
        });
    }

    /**
     * Combine and rank search results
     *
     * @param  Collection<int, array<string, mixed>>  $tickets
     * @param  Collection<int, array<string, mixed>>  $loans
     * @return Collection<int, array<string, mixed>>
     */
    private function combineAndRankResults(Collection $tickets, Collection $loans, string $query): Collection
    {
        return $tickets->concat($loans)
            ->sortByDesc('relevance_score')
            ->values();
    }

    /**
     * Apply additional filters to combined results
     *
     * @param  Collection<int, array<string, mixed>>  $results
     * @param  array<string, mixed>  $filters
     * @return Collection<int, array<string, mixed>>
     */
    

/**
 * @param array<string, mixed> $filters
 */
private function applyFilters(Collection $results, array $filters): Collection
    {
        // Apply priority filter
        if (isset($filters['priority']) && $filters['priority'] !== 'all') {
            /** @var Collection<int, array<string, mixed>> */
            $results = $results->filter(function ($item) use ($filters) {
                return isset($item['priority']) && $item['priority'] === $filters['priority'];
            });
        }

        /** @var Collection<int, array<string, mixed>> */
        return $results->values();
    }

    /**
     * Calculate relevance score for search results
     *
     * @param  array<string, string|null>  $fields
     */
    

/**
 * @param array<string, mixed> $fields
 */
private function calculateRelevanceScore(string $query, array $fields): float
    {
        $score = 0.0;
        $queryLower = strtolower($query);

        foreach ($fields as $field => $value) {
            if (! $value) {
                continue;
            }

            $valueLower = strtolower($value);

            // Exact match gets highest score
            if ($valueLower === $queryLower) {
                $score += 100;

                continue;
            }

            // Starts with query gets high score
            if (str_starts_with($valueLower, $queryLower)) {
                $score += 50;

                continue;
            }

            // Contains query gets medium score
            if (str_contains($valueLower, $queryLower)) {
                $score += 25;

                continue;
            }

            // Word boundary matches get lower score
            if (preg_match('/\b'.preg_quote($queryLower, '/').'\b/', $valueLower)) {
                $score += 10;
            }
        }

        // Boost score for number field matches (ticket/application numbers)
        if (isset($fields['number']) && str_contains(strtolower($fields['number'] ?? ''), $queryLower)) {
            $score += 75;
        }

        return $score;
    }

    /**
     * Paginate search results
     *
     * @param  Collection<int, array<string, mixed>>  $results
     * @return LengthAwarePaginator<int, array<string, mixed>>&iterable<array<string, mixed>>
     */
    private function paginateResults(Collection $results, int $perPage, int $page): LengthAwarePaginator
    {
        $total = $results->count();
        $offset = ($page - 1) * $perPage;
        $items = $results->slice($offset, $perPage)->values();

        /** @var LengthAwarePaginator<int, array<string, mixed>>&iterable<array<string, mixed>> */
        return new LengthAwarePaginator(
            $items->toArray(),
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
    }

    /**
     * Build cache key for search results
     *
     * @param  array<string, mixed>  $filters
     */
    

/**
 * @param array<string, mixed> $filters
 */
private function buildCacheKey(string $query, array $filters, int $perPage, int $page): string
    {
        $filterHash = md5(serialize($filters));

        return "cross_module_search:{$query}:{$filterHash}:{$perPage}:{$page}";
    }

    /**
     * Get search suggestions based on query
     *
     * @return array<string>
     */
    public function getSuggestions(string $query): array
    {
        if (strlen($query) < 2) {
            return [];
        }

        $cacheKey = 'search_suggestions:'.md5($query);

        /** @var array<string> */
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($query): array {
            // Get ticket number suggestions
            $ticketNumbers = HelpdeskTicket::where('ticket_number', 'like', "%{$query}%")
                ->limit(5)
                ->pluck('ticket_number')
                ->toArray();

            // Get loan application number suggestions
            $loanNumbers = LoanApplication::where('application_number', 'like', "%{$query}%")
                ->limit(5)
                ->pluck('application_number')
                ->toArray();

            return array_merge($ticketNumbers, $loanNumbers);
        });
    }

    /**
     * Clear search cache
     */
    public function clearCache(): void
    {
        Cache::forget('cross_module_search:*');
        Cache::forget('search_suggestions:*');
    }
}

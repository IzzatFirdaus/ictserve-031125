<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Fuzzy Search Service
 *
 * Provides fuzzy search functionality with typo tolerance using Levenshtein distance.
 *
 * @author ICTServe Development Team
 *
 * @version 1.0.0
 *
 * @trace D12 §6.14, D13 §3.7
 *
 * @requirements 22.1, 22.2, 22.3, 22.4, 22.5
 */
class FuzzySearchService
{
    /**
     * Maximum Levenshtein distance for fuzzy matching
     */
    protected int $maxDistance = 2;

    /**
     * Minimum query length for fuzzy matching
     */
    protected int $minQueryLength = 3;

    /**
     * Cache TTL for search suggestions (5 minutes)
     */
    protected int $suggestionCacheTtl = 300;

    /**
     * Search across tickets and loans with fuzzy matching
     *
     * @param  string  $query  Search query
     * @param  array<string, mixed>  $options  Search options
     * @return array{tickets: Collection<int, HelpdeskTicket>, loans: Collection<int, LoanApplication>, suggestions: array<int, string>, total: int}
     */
    

/**
 * @param array<string, mixed> $options
 */
public function search(string $query, array $options = []): array
    {
        $query = trim($query);

        if ($query === '') {
            return [
                'tickets' => collect(),
                'loans' => collect(),
                'suggestions' => [],
                'total' => 0,
            ];
        }

        $limit = $options['limit'] ?? 20;
        $includeTickets = $options['include_tickets'] ?? true;
        $includeLoans = $options['include_loans'] ?? true;

        $tickets = collect();
        $loans = collect();

        if ($includeTickets) {
            $tickets = $this->searchTickets($query, (int) \ceil($limit / 2));
        }

        if ($includeLoans) {
            $loans = $this->searchLoans($query, (int) \ceil($limit / 2));
        }

        $suggestions = $this->getSuggestions($query);

        return [
            'tickets' => $tickets,
            'loans' => $loans,
            'suggestions' => $suggestions,
            'total' => $tickets->count() + $loans->count(),
        ];
    }

    /**
     * Search helpdesk tickets
     *
     * @return Collection<int, HelpdeskTicket>
     */
    public function searchTickets(string $query, int $limit = 10): Collection
    {
        $normalizedQuery = $this->normalizeQuery($query);
        $terms = $this->extractSearchTerms($normalizedQuery);

        /** @var Builder<HelpdeskTicket> $builder */
        $builder = HelpdeskTicket::query();

        // Apply user scope if authenticated
        if (Auth::check()) {
            $user = Auth::user();
            if ($user && ! $user->hasAdminAccess()) {
                $builder->where(function (Builder $q) use ($user): void {
                    $q->where('user_id', $user->id)
                        ->orWhere('email', $user->email);
                });
            }
        }

        // Build search query
        $builder->where(function (Builder $q) use ($terms): void {
            foreach ($terms as $term) {
                $q->where(function (Builder $subQ) use ($term): void {
                    $subQ->where('title', 'LIKE', "%{$term}%")
                        ->orWhere('description', 'LIKE', "%{$term}%")
                        ->orWhere('ticket_number', 'LIKE', "%{$term}%")
                        ->orWhere('email', 'LIKE', "%{$term}%")
                        ->orWhere('name', 'LIKE', "%{$term}%");
                });
            }
        });

        return $builder
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Search loan applications
     *
     * @return Collection<int, LoanApplication>
     */
    public function searchLoans(string $query, int $limit = 10): Collection
    {
        $normalizedQuery = $this->normalizeQuery($query);
        $terms = $this->extractSearchTerms($normalizedQuery);

        /** @var Builder<LoanApplication> $builder */
        $builder = LoanApplication::query();

        // Apply user scope if authenticated
        if (Auth::check()) {
            $user = Auth::user();
            if ($user && ! $user->hasAdminAccess()) {
                $builder->where('user_id', $user->id);
            }
        }

        // Build search query
        $builder->where(function (Builder $q) use ($terms): void {
            foreach ($terms as $term) {
                $q->where(function (Builder $subQ) use ($term): void {
                    $subQ->where('purpose', 'LIKE', "%{$term}%")
                        ->orWhere('application_number', 'LIKE', "%{$term}%")
                        ->orWhere('location', 'LIKE', "%{$term}%")
                        ->orWhere('notes', 'LIKE', "%{$term}%");
                });
            }
        });

        return $builder
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get search suggestions based on query
     *
     * @return array<int, string>
     */
    public function getSuggestions(string $query): array
    {
        if (\strlen($query) < $this->minQueryLength) {
            return [];
        }

        $cacheKey = 'search_suggestions_'.\md5($query);

        return Cache::remember($cacheKey, $this->suggestionCacheTtl, function () use ($query): array {
            $suggestions = [];

            // Get common search terms from recent tickets
            $ticketTerms = $this->getCommonTicketTerms();
            $loanTerms = $this->getCommonLoanTerms();

            $allTerms = \array_merge($ticketTerms, $loanTerms);
            $allTerms = \array_unique($allTerms);

            foreach ($allTerms as $term) {
                if ($this->isFuzzyMatch($query, $term)) {
                    $suggestions[] = $term;
                }
            }

            // Sort by relevance (exact matches first, then by distance)
            \usort($suggestions, function (string $a, string $b) use ($query): int {
                $distA = \levenshtein(\strtolower($query), \strtolower($a));
                $distB = \levenshtein(\strtolower($query), \strtolower($b));

                return $distA <=> $distB;
            });

            return \array_slice($suggestions, 0, 5);
        });
    }

    /**
     * Get autocomplete suggestions
     *
     * @return array<int, array{text: string, type: string, count: int}>
     */
    public function getAutocompleteSuggestions(string $query, int $limit = 8): array
    {
        if (\strlen($query) < 2) {
            return [];
        }

        $suggestions = [];
        $normalizedQuery = \strtolower(trim($query));

        // Search ticket titles
        $ticketTitles = HelpdeskTicket::query()
            ->where('title', 'LIKE', "%{$normalizedQuery}%")
            ->select('title')
            ->distinct()
            ->limit($limit)
            ->pluck('title');

        foreach ($ticketTitles as $title) {
            $suggestions[] = [
                'text' => $title,
                'type' => 'ticket',
                'count' => HelpdeskTicket::where('title', $title)->count(),
            ];
        }

        // Search loan purposes
        $loanPurposes = LoanApplication::query()
            ->where('purpose', 'LIKE', "%{$normalizedQuery}%")
            ->select('purpose')
            ->distinct()
            ->limit($limit)
            ->pluck('purpose');

        foreach ($loanPurposes as $purpose) {
            $suggestions[] = [
                'text' => $purpose,
                'type' => 'loan',
                'count' => LoanApplication::where('purpose', $purpose)->count(),
            ];
        }

        // Sort by count and limit
        \usort($suggestions, fn (array $a, array $b): int => $b['count'] <=> $a['count']);

        return \array_slice($suggestions, 0, $limit);
    }

    /**
     * Check if two strings are a fuzzy match using Levenshtein distance
     */
    public function isFuzzyMatch(string $query, string $target): bool
    {
        $query = \strtolower(trim($query));
        $target = \strtolower(trim($target));

        // Exact match
        if ($query === $target) {
            return true;
        }

        // Contains match
        if (Str::contains($target, $query)) {
            return true;
        }

        // Levenshtein distance match
        if (\strlen($query) >= $this->minQueryLength) {
            $distance = \levenshtein($query, $target);

            // Adjust max distance based on query length
            $adjustedMaxDistance = \min($this->maxDistance, (int) \floor(\strlen($query) / 3));

            return $distance <= $adjustedMaxDistance;
        }

        return false;
    }

    /**
     * Calculate similarity score between query and text
     *
     * @return float Score between 0 and 1
     */
    public function calculateSimilarity(string $query, string $text): float
    {
        $query = \strtolower(trim($query));
        $text = \strtolower(trim($text));

        if ($query === '' || $text === '') {
            return 0.0;
        }

        // Exact match
        if ($query === $text) {
            return 1.0;
        }

        // Contains match
        if (Str::contains($text, $query)) {
            return 0.9;
        }

        // Levenshtein-based similarity
        $maxLen = \max(\strlen($query), \strlen($text));
        $distance = \levenshtein($query, $text);

        return 1.0 - ($distance / $maxLen);
    }

    /**
     * Highlight matched terms in text
     */
    public function highlightMatches(string $text, string $query): string
    {
        $terms = $this->extractSearchTerms($query);

        foreach ($terms as $term) {
            $pattern = '/('.\preg_quote($term, '/').')/i';
            $text = \preg_replace(
                $pattern,
                '<mark class="bg-warning-100 text-warning-800 dark:bg-warning-900 dark:text-warning-200 px-0.5 rounded" role="mark">$1</mark>',
                $text
            ) ?? $text;
        }

        return $text;
    }

    /**
     * Normalize search query
     */
    protected function normalizeQuery(string $query): string
    {
        // Remove extra whitespace
        $query = \preg_replace('/\s+/', ' ', trim($query)) ?? trim($query);

        // Remove special characters except spaces and alphanumeric
        $query = \preg_replace('/[^\p{L}\p{N}\s]/u', '', $query) ?? $query;

        return \strtolower($query);
    }

    /**
     * Extract search terms from query
     *
     * @return array<int, string>
     */
    protected function extractSearchTerms(string $query): array
    {
        $terms = \explode(' ', $query);

        return \array_filter($terms, fn (string $term): bool => \strlen($term) >= 2);
    }

    /**
     * Get common terms from recent tickets
     *
     * @return array<int, string>
     */
    protected function getCommonTicketTerms(): array
    {
        return Cache::remember('common_ticket_terms', 3600, function (): array {
            $titles = HelpdeskTicket::query()
                ->select('title')
                ->orderByDesc('created_at')
                ->limit(100)
                ->pluck('title')
                ->toArray();

            return $this->extractCommonTerms($titles);
        });
    }

    /**
     * Get common terms from recent loans
     *
     * @return array<int, string>
     */
    protected function getCommonLoanTerms(): array
    {
        return Cache::remember('common_loan_terms', 3600, function (): array {
            $purposes = LoanApplication::query()
                ->select('purpose')
                ->orderByDesc('created_at')
                ->limit(100)
                ->pluck('purpose')
                ->toArray();

            return $this->extractCommonTerms($purposes);
        });
    }

    /**
     * Extract common terms from text array
     *
     * @param  array<int, string|null>  $texts
     * @return array<int, string>
     */
    

/**
 * @param array<string, mixed> $texts
 */
protected function extractCommonTerms(array $texts): array
    {
        $wordCounts = [];

        foreach ($texts as $text) {
            if ($text === null) {
                continue;
            }

            $words = \explode(' ', $this->normalizeQuery($text));

            foreach ($words as $word) {
                if (\strlen($word) >= 3) {
                    $wordCounts[$word] = ($wordCounts[$word] ?? 0) + 1;
                }
            }
        }

        // Sort by frequency
        \arsort($wordCounts);

        // Return top 50 terms
        return \array_slice(\array_keys($wordCounts), 0, 50);
    }

    /**
     * Set maximum Levenshtein distance
     */
    public function setMaxDistance(int $distance): self
    {
        $this->maxDistance = $distance;

        return $this;
    }

    /**
     * Set minimum query length for fuzzy matching
     */
    public function setMinQueryLength(int $length): self
    {
        $this->minQueryLength = $length;

        return $this;
    }
}

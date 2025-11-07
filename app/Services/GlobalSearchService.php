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
 * Enhanced search functionality across all resources with caching,
 * suggestions, and advanced ranking algorithms.
 *
 * @trace Requirements 11.1, 11.2
 */
class GlobalSearchService
{
    private const CACHE_TTL = 300; // 5 minutes

    private const MIN_SEARCH_LENGTH = 2;

    private const MAX_RESULTS_PER_TYPE = 15;

    public function search(string $query, array $filters = []): array
    {
        if (strlen($query) < self::MIN_SEARCH_LENGTH) {
            return [];
        }

        $cacheKey = 'global_search:'.md5($query.serialize($filters));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($query, $filters) {
            return [
                'tickets' => $this->searchTickets($query, $filters),
                'loans' => $this->searchLoans($query, $filters),
                'assets' => $this->searchAssets($query, $filters),
                'users' => $this->searchUsers($query, $filters),
                'suggestions' => $this->generateSuggestions($query),
            ];
        });
    }

    public function searchTickets(string $query, array $filters = []): Collection
    {
        $queryBuilder = HelpdeskTicket::query()
            ->where(function ($q) use ($query) {
                $q->where('ticket_number', 'like', "%{$query}%")
                    ->orWhere('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('guest_name', 'like', "%{$query}%")
                    ->orWhere('guest_email', 'like', "%{$query}%");
            })
            ->with(['user', 'assignedTo', 'asset', 'division']);

        // Apply filters
        if (! empty($filters['status'])) {
            $queryBuilder->whereIn('status', (array) $filters['status']);
        }

        if (! empty($filters['priority'])) {
            $queryBuilder->whereIn('priority', (array) $filters['priority']);
        }

        if (! empty($filters['date_from'])) {
            $queryBuilder->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $queryBuilder->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $queryBuilder
            ->limit(self::MAX_RESULTS_PER_TYPE)
            ->get()
            ->map(function ($ticket) use ($query) {
                return [
                    'id' => $ticket->id,
                    'type' => 'ticket',
                    'title' => $ticket->ticket_number.' - '.$ticket->title,
                    'subtitle' => 'Status: '.ucfirst(str_replace('_', ' ', $ticket->status)),
                    'description' => $ticket->description,
                    'url' => route('filament.admin.resources.helpdesk.helpdesk-tickets.view', $ticket),
                    'icon' => 'heroicon-o-ticket',
                    'color' => $this->getTicketColor($ticket->status),
                    'relevance' => $this->calculateRelevance($query, $ticket->ticket_number.' '.$ticket->title),
                    'metadata' => [
                        'created_at' => $ticket->created_at->format('d/m/Y H:i'),
                        'priority' => $ticket->priority,
                        'assigned_to' => $ticket->assignedTo?->name,
                    ],
                ];
            });
    }

    public function searchLoans(string $query, array $filters = []): Collection
    {
        $queryBuilder = LoanApplication::query()
            ->where(function ($q) use ($query) {
                $q->where('application_number', 'like', "%{$query}%")
                    ->orWhere('applicant_name', 'like', "%{$query}%")
                    ->orWhere('applicant_email', 'like', "%{$query}%")
                    ->orWhere('purpose', 'like', "%{$query}%");
            })
            ->with(['user', 'loanItems.asset']);

        // Apply filters
        if (! empty($filters['status'])) {
            $queryBuilder->whereIn('status', (array) $filters['status']);
        }

        if (! empty($filters['date_from'])) {
            $queryBuilder->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $queryBuilder->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $queryBuilder
            ->limit(self::MAX_RESULTS_PER_TYPE)
            ->get()
            ->map(function ($loan) use ($query) {
                return [
                    'id' => $loan->id,
                    'type' => 'loan',
                    'title' => $loan->application_number.' - '.$loan->applicant_name,
                    'subtitle' => 'Status: '.ucfirst(str_replace('_', ' ', $loan->status)),
                    'description' => $loan->purpose,
                    'url' => route('filament.admin.resources.loans.loan-applications.view', $loan),
                    'icon' => 'heroicon-o-cube',
                    'color' => $this->getLoanColor($loan->status),
                    'relevance' => $this->calculateRelevance($query, $loan->application_number.' '.$loan->applicant_name),
                    'metadata' => [
                        'created_at' => $loan->created_at->format('d/m/Y H:i'),
                        'loan_date' => $loan->loan_date?->format('d/m/Y'),
                        'return_date' => $loan->return_date?->format('d/m/Y'),
                    ],
                ];
            });
    }

    public function searchAssets(string $query, array $filters = []): Collection
    {
        $queryBuilder = Asset::query()
            ->where(function ($q) use ($query) {
                $q->where('asset_code', 'like', "%{$query}%")
                    ->orWhere('name', 'like', "%{$query}%")
                    ->orWhere('brand', 'like', "%{$query}%")
                    ->orWhere('model', 'like', "%{$query}%")
                    ->orWhere('serial_number', 'like', "%{$query}%");
            })
            ->with(['category']);

        // Apply filters
        if (! empty($filters['status'])) {
            $queryBuilder->whereIn('status', (array) $filters['status']);
        }

        if (! empty($filters['category'])) {
            $queryBuilder->whereIn('category_id', (array) $filters['category']);
        }

        return $queryBuilder
            ->limit(self::MAX_RESULTS_PER_TYPE)
            ->get()
            ->map(function ($asset) use ($query) {
                return [
                    'id' => $asset->id,
                    'type' => 'asset',
                    'title' => $asset->asset_code.' - '.$asset->name,
                    'subtitle' => 'Status: '.ucfirst(str_replace('_', ' ', $asset->status)),
                    'description' => $asset->category?->name_en.' | '.$asset->brand.' '.$asset->model,
                    'url' => route('filament.admin.resources.assets.assets.view', $asset),
                    'icon' => 'heroicon-o-server',
                    'color' => $this->getAssetColor($asset->status),
                    'relevance' => $this->calculateRelevance($query, $asset->asset_code.' '.$asset->name),
                    'metadata' => [
                        'category' => $asset->category?->name_en,
                        'brand' => $asset->brand,
                        'model' => $asset->model,
                    ],
                ];
            });
    }

    public function searchUsers(string $query, array $filters = []): Collection
    {
        if (! auth()->user()->hasRole('superuser')) {
            return collect();
        }

        $queryBuilder = User::query()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('staff_id', 'like', "%{$query}%");
            })
            ->with(['division', 'grade']);

        // Apply filters
        if (! empty($filters['role'])) {
            $queryBuilder->whereHas('roles', function ($q) use ($filters) {
                $q->whereIn('name', (array) $filters['role']);
            });
        }

        if (! empty($filters['division'])) {
            $queryBuilder->whereIn('division_id', (array) $filters['division']);
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
                    'description' => $user->division?->name_ms.' | '.$user->grade?->name,
                    'url' => route('filament.admin.resources.users.users.view', $user),
                    'icon' => 'heroicon-o-user',
                    'color' => $user->is_active ? 'success' : 'secondary',
                    'relevance' => $this->calculateRelevance($query, $user->name.' '.$user->email),
                    'metadata' => [
                        'staff_id' => $user->staff_id,
                        'division' => $user->division?->name_ms,
                        'role' => $user->role,
                    ],
                ];
            });
    }

    public function generateSuggestions(string $query): array
    {
        $suggestions = [];

        // Common search patterns
        $patterns = [
            'TKT-' => 'Tiket (contoh: TKT-2024-001)',
            'LA-' => 'Pinjaman (contoh: LA-2024-001)',
            'LT-' => 'Laptop (contoh: LT-001)',
            'PC-' => 'Komputer (contoh: PC-001)',
            'PR-' => 'Projektor (contoh: PR-001)',
        ];

        foreach ($patterns as $pattern => $description) {
            if (stripos($pattern, $query) !== false || stripos($query, $pattern) !== false) {
                $suggestions[] = [
                    'text' => $pattern,
                    'description' => $description,
                ];
            }
        }

        // Add recent popular searches (placeholder for future implementation)
        if (empty($suggestions)) {
            $suggestions = [
                ['text' => 'laptop', 'description' => 'Cari semua laptop'],
                ['text' => 'maintenance', 'description' => 'Cari tiket penyelenggaraan'],
                ['text' => 'overdue', 'description' => 'Cari pinjaman tertunggak'],
            ];
        }

        return array_slice($suggestions, 0, 5);
    }

    protected function calculateRelevance(string $query, string $text): int
    {
        $queryLower = strtolower($query);
        $textLower = strtolower($text);

        // Exact match gets highest score
        if ($textLower === $queryLower) {
            return 100;
        }

        // Starts with search term gets high score
        if (str_starts_with($textLower, $queryLower)) {
            return 90;
        }

        // Contains search term gets medium score
        if (str_contains($textLower, $queryLower)) {
            return 70;
        }

        // Word match gets lower score
        $words = explode(' ', $queryLower);
        $score = 0;
        foreach ($words as $word) {
            if (str_contains($textLower, $word)) {
                $score += 15;
            }
        }

        return min($score, 60);
    }

    protected function getTicketColor(string $status): string
    {
        return match ($status) {
            'open' => 'warning',
            'in_progress' => 'info',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'gray',
        };
    }

    protected function getLoanColor(string $status): string
    {
        return match ($status) {
            'pending_approval' => 'warning',
            'approved' => 'success',
            'in_use' => 'info',
            'completed' => 'secondary',
            'rejected' => 'danger',
            default => 'gray',
        };
    }

    protected function getAssetColor(string $status): string
    {
        return match ($status) {
            'available' => 'success',
            'on_loan' => 'warning',
            'maintenance' => 'danger',
            'retired' => 'secondary',
            default => 'gray',
        };
    }

    public function clearCache(): void
    {
        Cache::tags(['global_search'])->flush();
    }
}

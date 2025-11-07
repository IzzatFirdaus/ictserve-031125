<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Submission Service
 *
 * Handles submission queries, filtering, and search operations for authenticated portal users.
 * Provides unified interface for both helpdesk tickets and asset loan applications.
 *
 * @see .kiro/specs/staff-dashboard-profile/design.md - Submission Service Design
 * @see .kiro/specs/staff-dashboard-profile/requirements.md - Requirements 2.1, 2.2, 2.3, 8.1, 8.2
 */
class SubmissionService
{
    /**
     * Records per page for pagination
     */
    private const RECORDS_PER_PAGE = 25;

    /**
     * Get user submissions with filtering and pagination
     *
     * @param  string  $type  'tickets' or 'loans'
     * @param  array<string, mixed>  $filters
     */
    public function getUserSubmissions(User $user, string $type, array $filters = []): LengthAwarePaginator
    {
        $query = $type === 'tickets'
            ? HelpdeskTicket::where('user_id', $user->id)
            : LoanApplication::where('user_id', $user->id);

        $query = $this->applyFilters($query, $filters);

        return $query->with($this->getEagerLoadRelations($type))
            ->paginate(self::RECORDS_PER_PAGE);
    }

    /**
     * Search submissions across tickets and loans
     */
    public function searchSubmissions(User $user, string $searchTerm): Collection
    {
        $tickets = HelpdeskTicket::where('user_id', $user->id)
            ->where(function (Builder $query) use ($searchTerm) {
                $query->where('ticket_number', 'like', "%{$searchTerm}%")
                    ->orWhere('subject', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            })
            ->with(['division:id,name', 'category:id,name'])
            ->limit(10)
            ->get();

        $loans = LoanApplication::where('user_id', $user->id)
            ->where(function (Builder $query) use ($searchTerm) {
                $query->where('application_number', 'like', "%{$searchTerm}%")
                    ->orWhere('purpose', 'like', "%{$searchTerm}%")
                    ->orWhereHas('asset', function (Builder $q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%");
                    });
            })
            ->with(['asset:id,name,category'])
            ->limit(10)
            ->get();

        return $tickets->merge($loans);
    }

    /**
     * Apply filters to query
     *
     * @param  array<string, mixed>  $filters
     */
    private function applyFilters(Builder $query, array $filters): Builder
    {
        // Status filter (multi-select)
        if (isset($filters['status']) && is_array($filters['status'])) {
            $query->whereIn('status', $filters['status']);
        }

        // Date range filter
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Category filter (for tickets)
        if (isset($filters['category']) && is_array($filters['category'])) {
            $query->whereIn('category_id', $filters['category']);
        }

        // Asset type filter (for loans)
        if (isset($filters['asset_type']) && is_array($filters['asset_type'])) {
            $query->whereHas('asset', function (Builder $q) use ($filters) {
                $q->whereIn('category', $filters['asset_type']);
            });
        }

        // Priority filter
        if (isset($filters['priority']) && is_array($filters['priority'])) {
            $query->whereIn('priority', $filters['priority']);
        }

        // Sorting
        if (isset($filters['sort_by'])) {
            $direction = $filters['sort_direction'] ?? 'desc';
            $query->orderBy($filters['sort_by'], $direction);
        } else {
            $query->latest();
        }

        return $query;
    }

    /**
     * Get eager load relations for submission type
     *
     * @return array<string>
     */
    private function getEagerLoadRelations(string $type): array
    {
        if ($type === 'tickets') {
            return [
                'division:id,name',
                'category:id,name',
                'assignedUser:id,name',
                'latestComment' => function ($query) {
                    $query->latest()->limit(1);
                },
            ];
        }

        // For loans
        return [
            'asset:id,name,category,condition',
            'approver:id,name,grade',
        ];
    }

    /**
     * Get submission by ID and type
     */
    public function getSubmissionById(int $id, string $type): HelpdeskTicket|LoanApplication|null
    {
        if ($type === 'tickets') {
            return HelpdeskTicket::with([
                'user:id,name,email',
                'division',
                'category',
                'attachments',
                'comments.user:id,name',
                'activities.user:id,name',
            ])->find($id);
        }

        return LoanApplication::with([
            'user:id,name,email,grade,division_id',
            'asset',
            'approver:id,name,grade',
            'activities.user:id,name',
        ])->find($id);
    }

    /**
     * Get submission count by status for user
     *
     * @return array<string, int>
     */
    public function getSubmissionCountByStatus(User $user, string $type): array
    {
        $query = $type === 'tickets'
            ? HelpdeskTicket::where('user_id', $user->id)
            : LoanApplication::where('user_id', $user->id);

        return $query->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * Get submission history with advanced filtering
     *
     * @param  array<string, mixed>  $filters
     */
    public function getSubmissionHistory(User $user, array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $type = $filters['type'] ?? 'helpdesk';

        $query = $type === 'helpdesk'
            ? HelpdeskTicket::where('user_id', $user->id)
            : LoanApplication::where('user_id', $user->id);

        // Apply status filter
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply category filter
        if (! empty($filters['category'])) {
            if ($type === 'helpdesk') {
                $query->where('category_id', $filters['category']);
            } else {
                $query->whereHas('asset', function (Builder $q) use ($filters) {
                    $q->where('category', $filters['category']);
                });
            }
        }

        // Apply priority filter (helpdesk only)
        if (! empty($filters['priority']) && $type === 'helpdesk') {
            $query->where('priority', $filters['priority']);
        }

        // Apply date range filters
        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Apply search filter
        if (! empty($filters['search'])) {
            $searchTerm = $filters['search'];
            if ($type === 'helpdesk') {
                $query->where(function (Builder $q) use ($searchTerm) {
                    $q->where('ticket_number', 'like', "%{$searchTerm}%")
                        ->orWhere('subject', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%");
                });
            } else {
                $query->where(function (Builder $q) use ($searchTerm) {
                    $q->where('application_number', 'like', "%{$searchTerm}%")
                        ->orWhere('purpose', 'like', "%{$searchTerm}%")
                        ->orWhereHas('asset', function (Builder $subQ) use ($searchTerm) {
                            $subQ->where('name', 'like', "%{$searchTerm}%");
                        });
                });
            }
        }

        // Apply sorting
        $query->orderBy('created_at', 'desc');

        // Eager load relationships
        $query->with($this->getEagerLoadRelations($type === 'helpdesk' ? 'tickets' : 'loans'));

        return $query->paginate($perPage);
    }

    /**
     * Save search filters for user
     *
     * @param  array<string, mixed>  $filters
     */
    public function saveSearch(User $user, string $name, string $type, array $filters): \App\Models\SavedSearch
    {
        return \App\Models\SavedSearch::create([
            'user_id' => $user->id,
            'name' => $name,
            'search_type' => $type,
            'filters' => $filters,
        ]);
    }

    /**
     * Get saved searches for user
     */
    public function getSavedSearches(User $user, string $type): Collection
    {
        return \App\Models\SavedSearch::where('user_id', $user->id)
            ->where('search_type', $type)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

<?php

declare(strict_types=1);

// name: SavedSearch
// description: User-saved search filters for quick access to submission queries
// author: dev-team@motac.gov.my
// trace: SRS-FR-003; D04 §4.3; D11 §8; Requirements 8.4
// last-updated: 2025-11-06

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'search_type',
        'filters',
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user who owns the saved search
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get searches for a specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get searches of a specific type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('search_type', $type);
    }

    /**
     * Get filter value for a specific key
     */
    public function getFilter(string $key, $default = null)
    {
        $filters = $this->filters ?? [];

        return $filters[$key] ?? $default;
    }

    /**
     * Check if filter has a specific key
     */
    public function hasFilter(string $key): bool
    {
        $filters = $this->filters ?? [];

        return isset($filters[$key]);
    }
}

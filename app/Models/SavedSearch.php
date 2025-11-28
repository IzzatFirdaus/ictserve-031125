<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SavedSearch Model
 *
 * Stores user search history and saved searches for cross-module search.
 * Uses existing table schema with search_type for categorization.
 *
 * @see D03-FR-011.2 (Cross-module search functionality)
 * @see D04 §5.2 (Cross-Module Search System)
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string $search_type
 * @property array<string, mixed>|null $filters
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User $user
 */
class SavedSearch extends Model
{
    use HasFactory;

    public const TYPE_HISTORY = 'history';

    public const TYPE_SAVED = 'saved';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'search_type',
        'filters',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'filters' => 'array',
        ];
    }

    /**
     * Get the user that owns the saved search.
     *
     * @return BelongsTo<User, SavedSearch>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only search history (not saved).
     *
     * @param  \Illuminate\Database\Eloquent\Builder<SavedSearch>  $query
     * @return \Illuminate\Database\Eloquent\Builder<SavedSearch>
     */
    public function scopeHistory($query)
    {
        return $query->where('search_type', self::TYPE_HISTORY);
    }

    /**
     * Scope to get only saved searches.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<SavedSearch>  $query
     * @return \Illuminate\Database\Eloquent\Builder<SavedSearch>
     */
    public function scopeSaved($query)
    {
        return $query->where('search_type', self::TYPE_SAVED);
    }

    /**
     * Scope to get searches for a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<SavedSearch>  $query
     * @return \Illuminate\Database\Eloquent\Builder<SavedSearch>
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the search query from filters.
     */
    public function getQueryAttribute(): ?string
    {
        return $this->filters['query'] ?? null;
    }

    /**
     * Get the result count from filters.
     */
    public function getResultCountAttribute(): int
    {
        return (int) ($this->filters['result_count'] ?? 0);
    }

    /**
     * Get the last used timestamp from filters.
     */
    public function getLastUsedAtAttribute(): ?\Carbon\Carbon
    {
        $timestamp = $this->filters['last_used_at'] ?? null;

        return $timestamp ? \Carbon\Carbon::parse($timestamp) : null;
    }
}

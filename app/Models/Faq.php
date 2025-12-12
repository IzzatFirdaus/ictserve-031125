<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * Model FAQ untuk sistem AI Ollama
 *
 * Per Requirements 1.1, 1.5, 4.1: FAQ management dengan True Hybrid Architecture
 * Selaras dengan D09 Database Documentation v3.6.0 (Dual Audit System)
 *
 * @property int $id
 * @property string $question
 * @property string $answer
 * @property array|null $tags
 * @property float|null $match_score
 * @property int|null $created_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 */
class Faq extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'question',
        'answer',
        'tags',
        'match_score',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'match_score' => 'float',
        ];
    }

    /**
     * Hubungan dengan User yang mencipta FAQ
     * True Hybrid Architecture: nullable untuk sokongan guest/authenticated
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope untuk carian FAQ berdasarkan query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $searchQuery
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $searchQuery)
    {
        // Use LIKE search for SQLite compatibility in testing
        return $query->where(function ($q) use ($searchQuery) {
            $q->where('question', 'like', "%{$searchQuery}%")
                ->orWhere('answer', 'like', "%{$searchQuery}%");
        });
    }

    /**
     * Scope untuk FAQ dengan skor persamaan minimum
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float $minScore
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithMinScore($query, float $minScore = 0.3)
    {
        return $query->where('match_score', '>=', $minScore);
    }
}

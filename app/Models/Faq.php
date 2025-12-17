<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

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
 * @property string|null $preferred_model
 * @property float|null $complexity_score
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Database\Factories\FaqFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq search(string $searchQuery)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereComplexityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereMatchScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq wherePreferredModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq withMinScore(float $minScore = 0.3)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @mixin \Eloquent
 */
class Faq extends Model implements AuditableContract
{
    use Auditable;
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

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
        'preferred_model',
        'complexity_score',
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
            'complexity_score' => 'float',
        ];
    }

    /**
     * Spatie Activity Log configuration
     *
     * @see D09 §4.7 - Activity Log Requirements
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'question',
                'answer',
                'tags',
                'preferred_model',
                'created_by',
            ])
            ->logOnlyDirty()
            ->useLogName('faq')
            ->setDescriptionForEvent(fn (string $eventName) => "FAQ {$eventName}");
    }

    /**
     * Hubungan dengan User yang mencipta FAQ
     * True Hybrid Architecture: nullable untuk sokongan guest/authenticated
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope untuk carian FAQ berdasarkan query
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
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
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithMinScore($query, float $minScore = 0.3)
    {
        return $query->where('match_score', '>=', $minScore);
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $request_id
 * @property string $search_query
 * @property string $provider
 * @property int $results_count
 * @property array<array-key, mixed>|null $sources_used
 * @property numeric|null $cost
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\WebSearchLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebSearchLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebSearchLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebSearchLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebSearchLog whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebSearchLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebSearchLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebSearchLog whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebSearchLog whereRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebSearchLog whereResultsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebSearchLog whereSearchQuery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebSearchLog whereSourcesUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebSearchLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebSearchLog whereUserId($value)
 * @mixin \Eloquent
 */
class WebSearchLog extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'request_id',
        'search_query',
        'provider',
        'results_count',
        'sources_used',
        'cost',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'request_id' => 'string',
            'results_count' => 'integer',
            'sources_used' => 'array',
            'cost' => 'decimal:6',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}

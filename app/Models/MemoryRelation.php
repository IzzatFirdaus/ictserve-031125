<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $from_entity_id
 * @property string $to_entity_id
 * @property string $relation_type
 * @property array<array-key, mixed>|null $metadata
 * @property float|null $confidence
 * @property \Illuminate\Support\Carbon|null $discovered_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\MemoryEntity $from
 * @property-read \App\Models\MemoryEntity $to
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereConfidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereDiscoveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereFromEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereRelationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereToEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation withoutTrashed()
 * @mixin \Eloquent
 */
class MemoryRelation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'from_entity_id',
        'to_entity_id',
        'relation_type',
        'metadata',
        'confidence',
        'discovered_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'discovered_at' => 'datetime',
            'confidence' => 'float',
        ];
    }

    public function from(): BelongsTo
    {
        return $this->belongsTo(MemoryEntity::class, 'from_entity_id');
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(MemoryEntity::class, 'to_entity_id');
    }
}

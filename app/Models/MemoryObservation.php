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
 * @property string $memory_entity_id
 * @property string|null $memory_adapter_id
 * @property string|null $content_hash
 * @property string $content Large content support for imported markdown files
 * @property array<array-key, mixed>|null $metadata
 * @property float|null $confidence
 * @property \Illuminate\Support\Carbon|null $recorded_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\MemoryAdapter|null $adapter
 * @property-read \App\Models\MemoryEntity $entity
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereConfidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereContentHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereMemoryAdapterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereMemoryEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereRecordedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation withoutTrashed()
 * @mixin \Eloquent
 */
class MemoryObservation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'memory_entity_id',
        'memory_adapter_id',
        'content_hash',
        'content',
        'metadata',
        'confidence',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'recorded_at' => 'datetime',
            'confidence' => 'float',
        ];
    }

    public function entity(): BelongsTo
    {
        return $this->belongsTo(MemoryEntity::class, 'memory_entity_id');
    }

    public function adapter(): BelongsTo
    {
        return $this->belongsTo(MemoryAdapter::class, 'memory_adapter_id');
    }
}

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
 * @property string $memory_adapter_id
 * @property string $status
 * @property array<array-key, mixed>|null $payload
 * @property array<array-key, mixed>|null $error
 * @property int $synced_entities
 * @property int $synced_relations
 * @property int $synced_observations
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $finished_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\MemoryAdapter $adapter
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereError($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereMemoryAdapterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereSyncedEntities($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereSyncedObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereSyncedRelations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync withoutTrashed()
 * @mixin \Eloquent
 */
class MemoryAdapterSync extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'memory_adapter_id',
        'status',
        'payload',
        'error',
        'synced_entities',
        'synced_relations',
        'synced_observations',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'error' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function adapter(): BelongsTo
    {
        return $this->belongsTo(MemoryAdapter::class, 'memory_adapter_id');
    }
}

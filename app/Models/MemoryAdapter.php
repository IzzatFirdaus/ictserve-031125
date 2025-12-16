<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $provider
 * @property string $name
 * @property string|null $description
 * @property array<array-key, mixed>|null $config
 * @property array<array-key, mixed>|null $capabilities
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_synced_at
 * @property string|null $sync_cursor
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MemoryEntity> $entities
 * @property-read int|null $entities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MemoryAdapterSync> $syncs
 * @property-read int|null $syncs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereCapabilities($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereLastSyncedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereSyncCursor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter withoutTrashed()
 * @mixin \Eloquent
 */
class MemoryAdapter extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'provider',
        'name',
        'description',
        'config',
        'capabilities',
        'is_active',
        'last_synced_at',
        'sync_cursor',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'capabilities' => 'array',
            'is_active' => 'boolean',
            'last_synced_at' => 'datetime',
        ];
    }

    public function syncs(): HasMany
    {
        return $this->hasMany(MemoryAdapterSync::class, 'memory_adapter_id');
    }

    public function entities(): BelongsToMany
    {
        return $this->belongsToMany(MemoryEntity::class, 'memory_adapter_entity', 'memory_adapter_id', 'memory_entity_id')
            ->withTimestamps();
    }
}

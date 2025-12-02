<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemoryAdapter extends Model
{
    /** @use HasFactory<\Database\Factories\MemoryAdapterFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'provider',
        'config',
        'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'last_synced_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<MemoryAdapterSync, MemoryAdapter>
     */
    public function syncs(): HasMany
    {
        return $this->hasMany(MemoryAdapterSync::class);
    }

    /**
     * @return BelongsToMany<MemoryEntity, MemoryAdapter>
     */
    public function entities(): BelongsToMany
    {
        return $this->belongsToMany(MemoryEntity::class, 'memory_adapter_entity')
            ->withPivot(['metadata'])
            ->withTimestamps();
    }
}

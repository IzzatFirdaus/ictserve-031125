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

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemoryAdapterSync extends Model
{
    /** @use HasFactory<\Database\Factories\MemoryAdapterSyncFactory> */
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'memory_adapter_id',
        'status',
        'synced_entities',
        'synced_relations',
        'synced_observations',
        'payload',
        'error',
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

    /**
     * @return BelongsTo<MemoryAdapter, MemoryAdapterSync>
     */
    public function adapter(): BelongsTo
    {
        return $this->belongsTo(MemoryAdapter::class, 'memory_adapter_id');
    }
}

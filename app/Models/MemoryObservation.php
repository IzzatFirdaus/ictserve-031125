<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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

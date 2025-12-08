<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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

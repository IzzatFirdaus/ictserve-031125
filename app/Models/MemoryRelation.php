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
    /** @use HasFactory<\Database\Factories\MemoryRelationFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

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
            'confidence' => 'float',
            'discovered_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<MemoryEntity, MemoryRelation>
     */
    public function fromEntity(): BelongsTo
    {
        return $this->belongsTo(MemoryEntity::class, 'from_entity_id');
    }

    /**
     * @return BelongsTo<MemoryEntity, MemoryRelation>
     */
    public function toEntity(): BelongsTo
    {
        return $this->belongsTo(MemoryEntity::class, 'to_entity_id');
    }
}

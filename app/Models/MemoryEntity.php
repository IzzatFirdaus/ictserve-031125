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
 * Represents a node stored inside the cross-extension memory graph.
 *
 * @see D03-FR-020 Memory persistence requirements
 */
class MemoryEntity extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'entity_type',
        'labels',
        'summary',
        'metadata',
        'source',
        'source_identifier',
        'confidence',
        'discovered_at',
    ];

    protected function casts(): array
    {
        return [
            'labels' => 'array',
            'metadata' => 'array',
            'confidence' => 'float',
            'discovered_at' => 'datetime',
        ];
    }

    public function observations(): HasMany
    {
        return $this->hasMany(MemoryObservation::class);
    }

    public function outgoingRelations(): HasMany
    {
        return $this->hasMany(MemoryRelation::class, 'from_entity_id');
    }

    public function incomingRelations(): HasMany
    {
        return $this->hasMany(MemoryRelation::class, 'to_entity_id');
    }

    public function relatedEntities(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'memory_relations', 'from_entity_id', 'to_entity_id')
            ->withPivot(['relation_type', 'metadata', 'confidence'])
            ->withTimestamps();
    }
}

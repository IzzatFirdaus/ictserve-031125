<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemoryEntity extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

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
            'discovered_at' => 'datetime',
            'confidence' => 'float',
        ];
    }

    public function observations(): HasMany
    {
        return $this->hasMany(MemoryObservation::class, 'memory_entity_id');
    }

    public function relationsFrom(): HasMany
    {
        return $this->hasMany(MemoryRelation::class, 'from_entity_id');
    }

    public function relationsTo(): HasMany
    {
        return $this->hasMany(MemoryRelation::class, 'to_entity_id');
    }
}

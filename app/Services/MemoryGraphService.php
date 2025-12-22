<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MemoryAdapter;
use App\Models\MemoryAdapterSync;
use App\Models\MemoryEntity;
use App\Models\MemoryObservation;
use App\Models\MemoryRelation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * MemoryGraphService centralizes write operations for the agentic memory domain.
 */
class MemoryGraphService
{
    

/**
 * @param array<string, mixed> $data
 */
public function createEntity(array $data): MemoryEntity
    {
        return MemoryEntity::create($data);
    }

    

/**
 * @param array<string, mixed> $data
 */
public function updateEntity(MemoryEntity $entity, array $data): MemoryEntity
    {
        $entity->fill($data);
        $entity->save();

        return $entity;
    }

    public function deleteEntity(MemoryEntity $entity): void
    {
        $entity->delete();
    }

    

/**
 * @param array<string, mixed> $data
 */
public function recordObservation(MemoryEntity $entity, array $data): MemoryObservation
    {
        $hash = $data['content_hash'] ?? sha1($data['content']);

        return $entity->observations()->updateOrCreate(
            [
                'content_hash' => $hash,
            ],
            [
                'memory_adapter_id' => $data['memory_adapter_id'] ?? null,
                'content' => $data['content'],
                'metadata' => $data['metadata'] ?? null,
                'confidence' => $data['confidence'] ?? null,
                'recorded_at' => $data['recorded_at'] ?? Carbon::now(),
            ]
        );
    }

    

/**
 * @param array<string, mixed> $data
 */
public function createRelation(MemoryEntity $from, MemoryEntity $to, array $data): MemoryRelation
    {
        return MemoryRelation::updateOrCreate(
            [
                'from_entity_id' => $from->id,
                'to_entity_id' => $to->id,
                'relation_type' => $data['relation_type'],
            ],
            [
                'metadata' => $data['metadata'] ?? null,
                'confidence' => $data['confidence'] ?? null,
                'discovered_at' => $data['discovered_at'] ?? Carbon::now(),
            ]
        );
    }

    

/**
 * @param array<string, mixed> $data
 */
public function registerAdapter(array $data): MemoryAdapter
    {
        $adapter = MemoryAdapter::updateOrCreate(
            [
                'provider' => $data['provider'],
                'name' => $data['name'],
            ],
            [
                'description' => $data['description'] ?? null,
                'config' => $data['config'] ?? null,
                'capabilities' => $data['capabilities'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'last_synced_at' => $data['last_synced_at'] ?? null,
                'sync_cursor' => $data['sync_cursor'] ?? null,
            ]
        );

        return $adapter;
    }

    

/**
 * @param array<string, mixed> $data
 */
public function recordAdapterSync(MemoryAdapter $adapter, array $data): MemoryAdapterSync
    {
        $sync = $adapter->syncs()->create([
            'status' => $data['status'] ?? 'completed',
            'payload' => $data['payload'] ?? null,
            'error' => $data['error'] ?? null,
            'synced_entities' => $data['synced_entities'] ?? 0,
            'synced_relations' => $data['synced_relations'] ?? 0,
            'synced_observations' => $data['synced_observations'] ?? 0,
            'started_at' => $data['started_at'] ?? Carbon::now(),
            'finished_at' => $data['finished_at'] ?? Carbon::now(),
        ]);

        $adapter->forceFill([
            'last_synced_at' => $data['finished_at'] ?? Carbon::now(),
            'sync_cursor' => $data['sync_cursor'] ?? $adapter->sync_cursor,
        ])->save();

        return $sync;
    }

    

/**
 * @param array<string, mixed> $metadata
 */
public function attachAdapterEntities(MemoryAdapter $adapter, Collection $entities, ?array $metadata = null): void
    {
        $payload = $entities->mapWithKeys(function (MemoryEntity $entity) use ($metadata) {
            return [
                $entity->id => [
                    'metadata' => $metadata,
                ],
            ];
        })->toArray();

        $adapter->entities()->syncWithoutDetaching($payload);
    }
}

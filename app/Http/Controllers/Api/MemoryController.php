<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemoryEntity;
use App\Models\MemoryObservation;
use App\Services\MemoryGraphService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemoryController extends Controller
{
    public function import(Request $request, MemoryGraphService $memory)
    {
        $this->authorizeRequest($request);

        $title = $request->input('title') ?? Str::limit($request->input('path', 'memory-push'), 120);
        $content = $request->input('content');
        $path = $request->input('path');

        if (! $content && $path && file_exists(base_path($path))) {
            $content = file_get_contents(base_path($path));
        }

        if (! $content) {
            return response()->json(['message' => 'No content provided'], 422);
        }

        $entity = $memory->createEntity([
            'name' => $title,
            'entity_type' => $request->input('entity_type', 'analysis_work'),
            'summary' => $request->input('summary'),
            'source' => 'agent',
            'source_identifier' => $path ?? null,
            'discovered_at' => now(),
        ]);

        $memory->recordObservation($entity, [
            'content' => $content,
            'content_hash' => sha1($content),
            'metadata' => $request->input('metadata', []),
            'confidence' => $request->input('confidence', 0.9),
        ]);

        return response()->json(['message' => 'Imported', 'entity' => $entity->name], 201);
    }

    public function search(Request $request)
    {
        $this->authorizeRequest($request);

        $q = $request->query('q', '');
        $limit = intval($request->input('limit', 10));

        $entities = MemoryEntity::query()
            ->where('name', 'like', "%$q%")
            ->orWhere('summary', 'like', "%$q%")
            ->limit($limit)
            ->get();

        $observations = MemoryObservation::query()
            ->where('content', 'like', "%$q%")
            ->limit($limit)
            ->get();

        return response()->json(['entities' => $entities, 'observations' => $observations]);
    }

    protected function authorizeRequest(Request $request): void
    {
        // Support both a bearer token (MEMORY_API_TOKEN) or auth
        if ($request->bearerToken() && $request->bearerToken() === env('MEMORY_API_TOKEN')) {
            return;
        }

        // Fallback to Laravel auth (sanctum)
        if ($request->user() && $request->user()->can('create', MemoryEntity::class)) {
            return;
        }

        abort(401, 'Unauthenticated');
    }
}

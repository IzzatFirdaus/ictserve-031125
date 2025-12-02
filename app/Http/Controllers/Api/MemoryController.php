<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemoryEntity;
use App\Models\MemoryObservation;
use App\Services\MemoryGraphService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemoryController extends Controller
{
    public function import(Request $request, MemoryGraphService $memory): JsonResponse
    {
        $this->authorizeRequest($request);

        $pathInput = $request->string('path', 'memory-push')->toString();
        $titleInput = $request->input('title');
        $title = is_string($titleInput) ? $titleInput : Str::limit($pathInput, 120);
        $contentInput = $request->input('content');
        $content = is_string($contentInput) ? $contentInput : null;
        $path = $request->input('path');

        if (! $content && $path && is_string($path) && file_exists(base_path($path))) {
            $fileContent = file_get_contents(base_path($path));
            $content = $fileContent === false ? null : $fileContent;
        }

        if (! is_string($content) || $content === '') {
            return response()->json(['message' => 'No content provided'], 422);
        }

        $entityType = $request->input('entity_type');
        $summary = $request->input('summary');

        $entity = $memory->createEntity([
            'name' => $title,
            'entity_type' => is_string($entityType) ? $entityType : 'analysis_work',
            'summary' => is_string($summary) ? $summary : null,
            'source' => 'agent',
            'source_identifier' => is_string($path) ? $path : null,
            'discovered_at' => now(),
        ]);

        $metadata = $request->input('metadata', []);

        $memory->recordObservation($entity, [
            'content' => $content,
            'content_hash' => sha1($content),
            'metadata' => is_array($metadata) ? $metadata : [],
            'confidence' => is_numeric($request->input('confidence')) ? (float) $request->input('confidence') : 0.9,
        ]);

        return response()->json(['message' => 'Imported', 'entity' => $entity->name], 201);
    }

    public function search(Request $request): JsonResponse
    {
        $this->authorizeRequest($request);

        $q = $request->string('q')->toString();
        $limit = max(1, $request->integer('limit', 10));

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
        $token = config('app.memory_api_token');
        if ($request->bearerToken() && $token && $request->bearerToken() === $token) {
            return;
        }

        // Fallback to Laravel auth (sanctum)
        if ($request->user() && $request->user()->can('create', MemoryEntity::class)) {
            return;
        }

        abort(401, 'Unauthenticated');
    }
}

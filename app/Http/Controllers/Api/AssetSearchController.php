<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Asset Search API Controller
 *
 * Provides JSON API endpoints for asset search.
 */
class AssetSearchController extends Controller
{
    /**
     * Search assets by name, code, or description
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');

        $assets = Asset::where('name', 'like', "%{$query}%")
            ->orWhere('asset_code', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->with(['category'])
            ->limit(50)
            ->get();

        return response()->json($assets);
    }
}

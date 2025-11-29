<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Asset;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class CheckAssetStatusTool extends Tool
{
    protected string $description = 'Check asset availability and status by asset tag or ID';

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'asset_tag' => 'required_without:asset_id|string',
            'asset_id' => 'required_without:asset_tag|integer',
        ]);

        $asset = isset($validated['asset_id'])
            ? Asset::find($validated['asset_id'])
            : Asset::where('asset_tag', $validated['asset_tag'])->first();

        if (!$asset) {
            return Response::error('Asset not found');
        }

        $data = [
            'id' => $asset->id,
            'asset_tag' => $asset->asset_tag,
            'name' => $asset->name,
            'status' => $asset->status,
            'category' => $asset->category?->name_en,
            'available' => $asset->status === 'available',
        ];

        return Response::text(json_encode($data, JSON_PRETTY_PRINT));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'asset_tag' => $schema->string()
                ->description('Asset tag (e.g., LT-001)'),
            'asset_id' => $schema->integer()
                ->description('Asset ID'),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Filter Preset Service
 *
 * Manages saved filter configurations for resources.
 *
 * @version 1.0.0
 *
 * @since 2025-01-06
 */
class FilterPresetService
{
    public function savePreset(string $resource, string $name, array $filters): void
    {
        $presets = $this->getPresets($resource);
        $presets[$name] = $filters;
        Cache::put("filter_presets:{$resource}", $presets, 86400);
    }

    public function getPresets(string $resource): array
    {
        return Cache::get("filter_presets:{$resource}", []);
    }

    public function deletePreset(string $resource, string $name): void
    {
        $presets = $this->getPresets($resource);
        unset($presets[$name]);
        Cache::put("filter_presets:{$resource}", $presets, 86400);
    }

    public function generateUrl(string $resource, array $filters): string
    {
        return route("filament.admin.resources.{$resource}.index", $filters);
    }
}

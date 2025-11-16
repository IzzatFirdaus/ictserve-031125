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

    /**
     * @return array<string, mixed>
     */
    public function saveFilterPreset(mixed $user, string $resource, string $name, array $filters, bool $isDefault = false): array
    {
        $payload = [
            'filters' => $filters,
            'is_default' => $isDefault,
        ];

        $this->savePreset($resource, $name, $payload);

        return $payload;
    }

    public function getPresets(string $resource): array
    {
        return Cache::get("filter_presets:{$resource}", []);
    }

    public function getUserPresets(mixed $user, string $resource): array
    {
        return $this->getPresets($resource);
    }

    public function deletePreset(mixed $user, string $resource, string $name): void
    {
        $presets = $this->getPresets($resource);
        unset($presets[$name]);
        Cache::put("filter_presets:{$resource}", $presets, 86400);
    }

    public function updatePreset(mixed $user, string $resource, string $name, array $data): void
    {
        $presets = $this->getPresets($resource);
        $existing = $presets[$name] ?? [];
        $presets[$name] = array_merge($existing, $data);

        Cache::put("filter_presets:{$resource}", $presets, 86400);
    }

    /**
     * @return array<string, mixed>
     */
    public function generateQuickFilter(string $label, array $filters): array
    {
        return [
            'label' => $label,
            'filters' => $filters,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getQuickFilters(string $resource): array
    {
        return [
            $this->generateQuickFilter('Open High Priority Tickets', [
                'status' => ['open', 'assigned'],
                'priority' => ['high', 'urgent'],
            ]),
        ];
    }

    public function generateFilterUrl(string $baseUrl, array $filters): string
    {
        if (empty($filters)) {
            return $baseUrl;
        }

        return $baseUrl.'?'.http_build_query($filters);
    }

    public function generateUrl(string $resource, array $filters): string
    {
        return route("filament.admin.resources.{$resource}.index", $filters);
    }
}

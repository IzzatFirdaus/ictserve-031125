<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Filter Preset Service
 *
 * Manages saved filter configurations for resources.
 * Supports user-specific preset storage with cache-based persistence.
 *
 * @version 2.0.0
 *
 * @since 2025-01-06
 */
class FilterPresetService
{
    /**
     * Generate a user-specific cache key for filter presets.
     */
    public function getUserCacheKey(mixed $user, string $resource): string
    {
        $userId = is_object($user) && method_exists($user, 'getKey') ? $user->getKey() : (string) $user;

        return "filter_presets:user:{$userId}:{$resource}";
    }

    /**
     * Save a preset for a specific resource (legacy method for backward compatibility).
     *
     * @param  array<string, mixed>  $filters
     */
    public function savePreset(string $resource, string $name, array $filters): void
    {
        $presets = $this->getPresets($resource);
        $presets[$name] = $filters;
        Cache::put("filter_presets:{$resource}", $presets, 86400);
    }

    /**
     * Save a filter preset for a specific user.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function saveFilterPreset(mixed $user, string $resource, string $name, array $filters, bool $isDefault = false): array
    {
        $cacheKey = $this->getUserCacheKey($user, $resource);
        $presets = Cache::get($cacheKey, []);

        // If setting as default, unset existing defaults for this resource
        if ($isDefault) {
            foreach ($presets as $presetName => $preset) {
                if (isset($preset['is_default']) && $preset['is_default']) {
                    $presets[$presetName]['is_default'] = false;
                }
            }
        }

        $payload = [
            'name' => $name,
            'filters' => $filters,
            'is_default' => $isDefault,
            'created_at' => now()->toIso8601String(),
        ];

        $presets[$name] = $payload;
        Cache::put($cacheKey, $presets, 86400);

        return $payload;
    }

    /**
     * Get presets for a resource (legacy method for backward compatibility).
     *
     * @return array<string, mixed>
     */
    public function getPresets(string $resource): array
    {
        return Cache::get("filter_presets:{$resource}", []);
    }

    /**
     * Get presets for a specific user and resource.
     *
     * @return array<string, mixed>
     */
    public function getUserPresets(mixed $user, string $resource): array
    {
        $cacheKey = $this->getUserCacheKey($user, $resource);

        return Cache::get($cacheKey, []);
    }

    /**
     * Delete a preset for a specific user.
     */
    public function deletePreset(mixed $user, string $resource, string $name): void
    {
        $cacheKey = $this->getUserCacheKey($user, $resource);
        $presets = Cache::get($cacheKey, []);
        unset($presets[$name]);
        Cache::put($cacheKey, $presets, 86400);
    }

    /**
     * Update a preset for a specific user.
     *
     * @param  array<string, mixed>  $data
     */
    public function updatePreset(mixed $user, string $resource, string $name, array $data): void
    {
        $cacheKey = $this->getUserCacheKey($user, $resource);
        $presets = Cache::get($cacheKey, []);

        // If setting as default, unset existing defaults for this resource
        if (isset($data['is_default']) && $data['is_default']) {
            foreach ($presets as $presetName => $preset) {
                if ($presetName !== $name && isset($preset['is_default']) && $preset['is_default']) {
                    $presets[$presetName]['is_default'] = false;
                }
            }
        }

        $existing = $presets[$name] ?? [];
        $presets[$name] = array_merge($existing, $data);

        Cache::put($cacheKey, $presets, 86400);
    }

    /**
     * Generate a quick filter configuration.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function generateQuickFilter(string $labelKey, array $filters): array
    {
        return [
            'label_key' => $labelKey,
            'label' => __($labelKey),
            'filters' => $filters,
        ];
    }

    /**
     * Get quick filters for a specific resource.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getQuickFilters(string $resource): array
    {
        return match ($resource) {
            'helpdesk-tickets' => [
                $this->generateQuickFilter(
                    'admin_pages.filter_presets.quick_filters.helpdesk.open_high_priority',
                    [
                        'status' => ['open', 'assigned'],
                        'priority' => ['high', 'urgent'],
                    ]
                ),
            ],
            'loan-applications' => [
                $this->generateQuickFilter(
                    'admin_pages.filter_presets.quick_filters.loans.pending_approval',
                    [
                        'status' => ['pending_approval'],
                    ]
                ),
            ],
            'assets' => [
                $this->generateQuickFilter(
                    'admin_pages.filter_presets.quick_filters.assets.available',
                    [
                        'status' => ['available'],
                    ]
                ),
            ],
            'users' => [
                $this->generateQuickFilter(
                    'admin_pages.filter_presets.quick_filters.users.active',
                    [
                        'is_active' => '1',
                    ]
                ),
            ],
            default => [],
        };
    }

    /**
     * Generate a filter URL from base URL and filters.
     *
     * @param  array<string, mixed>  $filters
     */
    public function generateFilterUrl(string $baseUrl, array $filters): string
    {
        if (empty($filters)) {
            return $baseUrl;
        }

        return $baseUrl.'?'.http_build_query($filters);
    }

    /**
     * Generate a URL for a resource with filters.
     *
     * @param  array<string, mixed>  $filters
     */
    public function generateUrl(string $resource, array $filters): string
    {
        return route("filament.admin.resources.{$resource}.index", $filters);
    }
}

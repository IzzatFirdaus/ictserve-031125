<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * Filter Preset Service
 *
 * Manages user filter presets and saved searches across all resources.
 * Provides URL persistence and bookmarkable filtered views.
 *
 * @trace Requirements 11.2, 11.3
 */
class FilterPresetService
{
    private const CACHE_TTL = 3600; // 1 hour

    public function saveFilterPreset(User $user, string $resource, string $name, array $filters, bool $isDefault = false): array
    {
        $presets = $this->getUserPresets($user, $resource);

        // If setting as default, remove default flag from others
        if ($isDefault) {
            foreach ($presets as &$preset) {
                $preset['is_default'] = false;
            }
        }

        $preset = [
            'id' => uniqid(),
            'name' => $name,
            'filters' => $filters,
            'is_default' => $isDefault,
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
        ];

        $presets[$preset['id']] = $preset;

        $this->storeUserPresets($user, $resource, $presets);

        return $preset;
    }

    public function getUserPresets(User $user, string $resource): array
    {
        $cacheKey = "filter_presets:{$user->id}:{$resource}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user, $resource) {
            $preferences = $user->filament_preferences ?? [];

            return $preferences['filter_presets'][$resource] ?? [];
        });
    }

    public function getDefaultPreset(User $user, string $resource): ?array
    {
        $presets = $this->getUserPresets($user, $resource);

        foreach ($presets as $preset) {
            if ($preset['is_default'] ?? false) {
                return $preset;
            }
        }

        return null;
    }

    public function deletePreset(User $user, string $resource, string $presetId): bool
    {
        $presets = $this->getUserPresets($user, $resource);

        if (! isset($presets[$presetId])) {
            return false;
        }

        unset($presets[$presetId]);
        $this->storeUserPresets($user, $resource, $presets);

        return true;
    }

    public function updatePreset(User $user, string $resource, string $presetId, array $data): ?array
    {
        $presets = $this->getUserPresets($user, $resource);

        if (! isset($presets[$presetId])) {
            return null;
        }

        $preset = $presets[$presetId];

        if (isset($data['name'])) {
            $preset['name'] = $data['name'];
        }

        if (isset($data['filters'])) {
            $preset['filters'] = $data['filters'];
        }

        if (isset($data['is_default'])) {
            // If setting as default, remove default flag from others
            if ($data['is_default']) {
                foreach ($presets as &$p) {
                    $p['is_default'] = false;
                }
            }
            $preset['is_default'] = $data['is_default'];
        }

        $preset['updated_at'] = now()->toISOString();
        $presets[$presetId] = $preset;

        $this->storeUserPresets($user, $resource, $presets);

        return $preset;
    }

    protected function storeUserPresets(User $user, string $resource, array $presets): void
    {
        $preferences = $user->filament_preferences ?? [];
        $preferences['filter_presets'][$resource] = $presets;

        $user->update(['filament_preferences' => $preferences]);

        // Clear cache
        $cacheKey = "filter_presets:{$user->id}:{$resource}";
        Cache::forget($cacheKey);
    }

    public function generateFilterUrl(string $baseUrl, array $filters): string
    {
        $queryParams = [];

        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $queryParams[] = urlencode("filters[{$key}][]").'='.urlencode($v);
                }
            } else {
                $queryParams[] = urlencode("filters[{$key}]").'='.urlencode($value);
            }
        }

        return $baseUrl.(empty($queryParams) ? '' : '?'.implode('&', $queryParams));
    }

    public function parseFiltersFromUrl(array $queryParams): array
    {
        $filters = [];

        if (isset($queryParams['filters']) && is_array($queryParams['filters'])) {
            foreach ($queryParams['filters'] as $key => $value) {
                $filters[$key] = $value;
            }
        }

        return $filters;
    }

    public function getCommonFilters(string $resource): array
    {
        return match ($resource) {
            'helpdesk-tickets' => [
                'status' => [
                    'label' => 'Status',
                    'type' => 'select',
                    'multiple' => true,
                    'options' => [
                        'open' => 'Terbuka',
                        'assigned' => 'Ditugaskan',
                        'in_progress' => 'Dalam Proses',
                        'pending_user' => 'Menunggu Pengguna',
                        'resolved' => 'Diselesaikan',
                        'closed' => 'Ditutup',
                    ],
                ],
                'priority' => [
                    'label' => 'Keutamaan',
                    'type' => 'select',
                    'multiple' => true,
                    'options' => [
                        'low' => 'Rendah',
                        'normal' => 'Biasa',
                        'high' => 'Tinggi',
                        'urgent' => 'Segera',
                    ],
                ],
                'date_range' => [
                    'label' => 'Julat Tarikh',
                    'type' => 'date_range',
                ],
            ],
            'loan-applications' => [
                'status' => [
                    'label' => 'Status',
                    'type' => 'select',
                    'multiple' => true,
                    'options' => [
                        'pending_approval' => 'Menunggu Kelulusan',
                        'approved' => 'Diluluskan',
                        'in_use' => 'Sedang Digunakan',
                        'completed' => 'Selesai',
                        'rejected' => 'Ditolak',
                    ],
                ],
                'date_range' => [
                    'label' => 'Julat Tarikh',
                    'type' => 'date_range',
                ],
            ],
            'assets' => [
                'status' => [
                    'label' => 'Status',
                    'type' => 'select',
                    'multiple' => true,
                    'options' => [
                        'available' => 'Tersedia',
                        'on_loan' => 'Dipinjam',
                        'maintenance' => 'Penyelenggaraan',
                        'retired' => 'Dilupuskan',
                    ],
                ],
                'category' => [
                    'label' => 'Kategori',
                    'type' => 'select',
                    'multiple' => true,
                    'options' => [], // Will be populated dynamically
                ],
            ],
            'users' => [
                'role' => [
                    'label' => 'Peranan',
                    'type' => 'select',
                    'multiple' => true,
                    'options' => [
                        'staff' => 'Staf',
                        'approver' => 'Pelulus',
                        'admin' => 'Pentadbir',
                        'superuser' => 'Superuser',
                    ],
                ],
                'is_active' => [
                    'label' => 'Status Aktif',
                    'type' => 'select',
                    'options' => [
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ],
                ],
            ],
            default => [],
        };
    }

    public function getQuickFilters(string $resource): array
    {
        return match ($resource) {
            'helpdesk-tickets' => [
                [
                    'name' => 'Tiket Terbuka',
                    'filters' => ['status' => ['open', 'assigned']],
                    'icon' => 'heroicon-o-exclamation-circle',
                    'color' => 'warning',
                ],
                [
                    'name' => 'Keutamaan Tinggi',
                    'filters' => ['priority' => ['high', 'urgent']],
                    'icon' => 'heroicon-o-fire',
                    'color' => 'danger',
                ],
                [
                    'name' => 'Tiket Saya',
                    'filters' => ['assigned_to' => [auth()->id()]],
                    'icon' => 'heroicon-o-user',
                    'color' => 'info',
                ],
            ],
            'loan-applications' => [
                [
                    'name' => 'Menunggu Kelulusan',
                    'filters' => ['status' => ['pending_approval']],
                    'icon' => 'heroicon-o-clock',
                    'color' => 'warning',
                ],
                [
                    'name' => 'Tertunggak',
                    'filters' => ['overdue' => true],
                    'icon' => 'heroicon-o-exclamation-triangle',
                    'color' => 'danger',
                ],
            ],
            'assets' => [
                [
                    'name' => 'Tersedia',
                    'filters' => ['status' => ['available']],
                    'icon' => 'heroicon-o-check-circle',
                    'color' => 'success',
                ],
                [
                    'name' => 'Penyelenggaraan',
                    'filters' => ['status' => ['maintenance']],
                    'icon' => 'heroicon-o-wrench-screwdriver',
                    'color' => 'warning',
                ],
            ],
            default => [],
        };
    }

    public function clearUserCache(User $user): void
    {
        $resources = ['helpdesk-tickets', 'loan-applications', 'assets', 'users'];

        foreach ($resources as $resource) {
            $cacheKey = "filter_presets:{$user->id}:{$resource}";
            Cache::forget($cacheKey);
        }
    }
}

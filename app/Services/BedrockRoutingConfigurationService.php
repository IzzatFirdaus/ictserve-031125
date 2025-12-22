<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BedrockRoutingConfigurationService
{
    private const CACHE_KEY = 'bedrock_routing_config';

    /**
     * @return array<string, mixed>
     */
    public function getConfiguration(): array
    {
        $cached = Cache::get(self::CACHE_KEY);
        if (is_array($cached)) {
            return $this->mergeWithDefaults($cached);
        }

        $defaults = $this->getDefaultConfiguration();
        Cache::put(self::CACHE_KEY, $defaults, now()->addDay());

        return $defaults;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    

/**
 * @param array<string, mixed> $config
 */
public function updateConfiguration(array $config): void
    {
        $merged = $this->mergeWithDefaults($config);

        Cache::put(self::CACHE_KEY, $merged, now()->addDay());

        Log::info('Konfigurasi penghalaan Bedrock dikemas kini.', [
            'enabled' => (bool) ($merged['enabled'] ?? false),
            'prevent_cloud_pii' => (bool) ($merged['prevent_cloud_pii'] ?? true),
        ]);
    }

    public function resetToDefaults(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::put(self::CACHE_KEY, $this->getDefaultConfiguration(), now()->addDay());

        Log::info('Konfigurasi penghalaan Bedrock ditetapkan semula ke lalai.');
    }

    /**
     * @return array<string, mixed>
     */
    private function getDefaultConfiguration(): array
    {
        return [
            'enabled' => (bool) config('bedrock.enabled', false),
            'prevent_cloud_pii' => (bool) config('bedrock.prevent_cloud_pii', true),
            'enforce_malaysia_residency' => (bool) config('bedrock.enforce_malaysia_residency', false),
            'model_id' => (string) config('bedrock.model_id', ''),
            'models' => (array) config('bedrock.models', []),
            'rate_limits' => (array) config('bedrock.rate_limits', []),
            'routing' => (array) config('bedrock.routing', []),
            'classification' => (array) config('bedrock.classification', []),
            'budgets' => (array) config('bedrock.budgets', []),
        ];
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    

/**
 * @param array<string, mixed> $config
 */
private function mergeWithDefaults(array $config): array
    {
        $defaults = $this->getDefaultConfiguration();

        $merged = array_merge($defaults, $config);

        $merged['models'] = array_merge(
            is_array($defaults['models'] ?? null) ? $defaults['models'] : [],
            is_array($config['models'] ?? null) ? $config['models'] : [],
        );

        $merged['rate_limits'] = array_merge(
            is_array($defaults['rate_limits'] ?? null) ? $defaults['rate_limits'] : [],
            is_array($config['rate_limits'] ?? null) ? $config['rate_limits'] : [],
        );

        $merged['routing'] = array_merge(
            is_array($defaults['routing'] ?? null) ? $defaults['routing'] : [],
            is_array($config['routing'] ?? null) ? $config['routing'] : [],
        );

        $merged['classification'] = array_merge(
            is_array($defaults['classification'] ?? null) ? $defaults['classification'] : [],
            is_array($config['classification'] ?? null) ? $config['classification'] : [],
        );

        $merged['budgets'] = array_merge(
            is_array($defaults['budgets'] ?? null) ? $defaults['budgets'] : [],
            is_array($config['budgets'] ?? null) ? $config['budgets'] : [],
        );

        return $merged;
    }
}

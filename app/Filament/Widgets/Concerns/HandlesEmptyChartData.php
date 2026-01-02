<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Concerns;

/**
 * Trait for chart widgets to handle empty data gracefully.
 *
 * Provides caching of getData() result to avoid double calls and
 * implements hasData() method to check if chart has meaningful data.
 *
 * @see Requirements 22.1-22.5
 */
trait HandlesEmptyChartData
{
    /**
     * Cached chart data to avoid double getData() calls.
     *
     * @var array<string, mixed>|null
     */
    protected ?array $cachedChartData = null;

    /**
     * Get the empty state view path.
     */
    protected function getEmptyStateView(): ?string
    {
        return 'filament.widgets.chart-empty-state';
    }

    /**
     * Get the empty state message.
     */
    protected function getEmptyStateMessage(): string
    {
        return 'Tiada data tersedia';
    }

    /**
     * Get the empty state icon.
     */
    protected function getEmptyStateIcon(): string
    {
        return 'heroicon-o-chart-bar';
    }

    /**
     * Check if the chart has meaningful data to display.
     *
     * Caches the getData() result to avoid calling it twice.
     */
    protected function hasData(): bool
    {
        if ($this->cachedChartData === null) {
            $this->cachedChartData = $this->getData();
        }

        // Check if datasets exist and have non-zero values
        if (empty($this->cachedChartData['datasets'])) {
            return false;
        }

        return collect($this->cachedChartData['datasets'])->some(function ($dataset) {
            if (empty($dataset['data'])) {
                return false;
            }

            return collect($dataset['data'])->some(fn ($value) => $value > 0);
        });
    }

    /**
     * Get cached chart data or fetch if not cached.
     *
     * @return array<string, mixed>
     */
    protected function getCachedData(): array
    {
        if ($this->cachedChartData === null) {
            $this->cachedChartData = $this->getData();
        }

        return $this->cachedChartData;
    }

    /**
     * Clear the cached chart data.
     */
    protected function clearCachedData(): void
    {
        $this->cachedChartData = null;
    }

    /**
     * Check if chart should show empty state.
     */
    protected function shouldShowEmptyState(): bool
    {
        return ! $this->hasData();
    }
}

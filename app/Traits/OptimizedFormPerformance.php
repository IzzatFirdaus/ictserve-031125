<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;

/**
 * Trait: OptimizedFormPerformance
 * Description: Performance optimizations for Livewire form components
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D11 §8 (Performance Optimization)
 * @trace Performance Requirement 7.2 (Core Web Vitals)
 *
 * @version 1.0.0
 *
 * @created 2025-11-05
 */
trait OptimizedFormPerformance
{
    /**
     * Debounce time for search inputs (in milliseconds)
     */
    public int $searchDebounce = 500;

    /**
     * Maximum results for dropdown/select queries
     */
    public int $maxDropdownResults = 50;

    /**
     * Cache duration for computed properties (in seconds)
     */
    public int $computedCacheDuration = 300; // 5 minutes

    /**
     * Lazy load flag - prevents loading data until step is active
     */
    protected bool $enableLazyLoading = true;

    /**
     * Check if lazy loading should be applied for a specific step
     */
    protected function shouldLoadForStep(int $requiredStep): bool
    {
        return ! $this->enableLazyLoading || $this->currentStep >= $requiredStep;
    }

    /**
     * Get optimized query builder with common performance enhancements
     *
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $model
     * @return Builder<TModel>
     */
    protected function optimizedQuery(string $model): Builder
    {
        /** @var Builder<Model> $query */
        $query = $model::query()
            ->select($this->getSelectColumns($model))
            ->limit($this->maxDropdownResults);

        /** @var Builder<TModel> $query */
        return $query;
    }

    /**
     * Get default select columns for a model (override in component if needed)
     *
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $model
     * @return array<int, string>
     */
    protected function getSelectColumns(string $model): array
    {
        // Default: return id and common name fields
        return ['id'];
    }

    /**
     * Optimize wire:model bindings by switching to blur/lazy where appropriate
     *
     * Performance Impact:
     * - wire:model.live: Sends request on every keystroke (high network usage)
     * - wire:model.blur: Sends request only when field loses focus (optimal for most inputs)
     * - wire:model.lazy: Sends request on form submit (optimal for non-critical fields)
     */
    public function getWireModelStrategy(string $fieldType): string
    {
        return match ($fieldType) {
            'search' => 'live.debounce.'.$this->searchDebounce.'ms',
            'text', 'email', 'tel', 'number' => 'blur',
            'select', 'radio' => 'live',
            'textarea', 'password' => 'blur',
            default => 'lazy',
        };
    }

    /**
     * Clear computed property caches when step changes
     * Call this in your nextStep() or previousStep() methods
     */
    public function clearStepCaches(): void
    {
        // Livewire 3 will automatically handle #[Computed(persist: true)] caching
        // This method is available for manual cache clearing if needed
        $this->dispatch('step-caches-cleared');
    }
}

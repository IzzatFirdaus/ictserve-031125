<?php
/**
 * Search Filter Volt Component v3.6.0
 *
 * Real-time search and filter component using Livewire Volt 1.10.
 * Implements wire:model.live.debounce.300ms for optimized performance.
 *
 * Features:
 * - Real-time search with 300ms debounce
 * - Multiple filter types (select, date range, status)
 * - Saved filters support
 * - WCAG 2.2 AA compliant with ARIA attributes
 * - Bahasa Melayu exclusive interface
 *
 * @see D12 UI/UX Design Guide - Search Components
 * @see D13 Frontend Framework - Livewire Volt Patterns
 * @see Requirements 6.2, 6.3, 7.4 - Real-time validation and accessibility
 */

use function Livewire\Volt\{state, computed, on, mount};
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

// Component state
state([
    'search' => '',
    'filters' => [],
    'activeFilters' => [],
    'savedFilters' => [],
    'showFilters' => false,
    'isLoading' => false,
]);

// Props passed from parent
state([
    'placeholder' => 'Cari...',
    'filterOptions' => [],
    'enableSavedFilters' => true,
    'debounceMs' => 300,
]);

// Mount lifecycle hook
mount(function () {
    if ($this->enableSavedFilters && Auth::check()) {
        $this->savedFilters = $this->loadSavedFilters();
    }
});

// Computed property for active filter count
$activeFilterCount = computed(function () {
    return count(array_filter($this->activeFilters, fn($value) => !empty($value)));
});

// Computed property for has active filters
$hasActiveFilters = computed(function () {
    return $this->activeFilterCount > 0 || !empty($this->search);
});

// Load saved filters from cache
$loadSavedFilters = function (): array {
    if (!Auth::check()) {
        return [];
    }

    $cacheKey = 'user.' . Auth::id() . '.saved_filters.' . class_basename($this);
    return Cache::get($cacheKey, []);
};

// Save current filters
$saveCurrentFilters = function (string $name): void {
    if (!Auth::check() || empty($name)) {
        return;
    }

    $filterData = [
        'name' => $name,
        'search' => $this->search,
        'filters' => $this->activeFilters,
        'created_at' => now()->toISOString(),
    ];

    $this->savedFilters[] = $filterData;

    $cacheKey = 'user.' . Auth::id() . '.saved_filters.' . class_basename($this);
    Cache::put($cacheKey, $this->savedFilters, now()->addDays(30));

    $this->dispatch('notify', [
        'type' => 'success',
        'message' => 'Penapis berjaya disimpan',
    ]);
};

// Apply saved filter
$applySavedFilter = function (int $index): void {
    if (!isset($this->savedFilters[$index])) {
        return;
    }

    $saved = $this->savedFilters[$index];
    $this->search = $saved['search'] ?? '';
    $this->activeFilters = $saved['filters'] ?? [];

    $this->emitFiltersChanged();
};

// Delete saved filter
$deleteSavedFilter = function (int $index): void {
    if (!isset($this->savedFilters[$index])) {
        return;
    }

    unset($this->savedFilters[$index]);
    $this->savedFilters = array_values($this->savedFilters);

    if (Auth::check()) {
        $cacheKey = 'user.' . Auth::id() . '.saved_filters.' . class_basename($this);
        Cache::put($cacheKey, $this->savedFilters, now()->addDays(30));
    }

    $this->dispatch('notify', [
        'type' => 'info',
        'message' => 'Penapis berjaya dipadam',
    ]);
};

// Clear all filters
$clearFilters = function (): void {
    $this->search = '';
    $this->activeFilters = [];
    $this->emitFiltersChanged();
};

// Toggle filter panel
$toggleFilters = function (): void {
    $this->showFilters = !$this->showFilters;
};

// Update filter value
$updateFilter = function (string $key, $value): void {
    $this->activeFilters[$key] = $value;
    $this->emitFiltersChanged();
};

// Emit filters changed event to parent
$emitFiltersChanged = function (): void {
    $this->dispatch('filters-changed', [
        'search' => $this->search,
        'filters' => $this->activeFilters,
    ]);
};

// Listen for search input changes (debounced)
on([
    'search-updated' => function () {
        $this->emitFiltersChanged();
    },
]);

?>

<div class="search-filter-component" x-data="{ showSaveModal: false, filterName: '' }" role="search" aria-label="Carian dan penapis">

    {{-- Search Input --}}
    <div class="relative">
        <div class="flex items-center gap-2">
            {{-- Search Field --}}
            <div class="relative flex-1">
                <label for="search-input" class="sr-only">Cari</label>
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="search" id="search-input" wire:model.live.debounce.{{ $debounceMs }}ms="search"
                    placeholder="{{ $placeholder }}"
                    class="block w-full pl-10 pr-10 py-2.5 min-h-11 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                    aria-describedby="search-help">
                {{-- Clear search button --}}
                @if (!empty($search))
                    <button type="button" wire:click="$set('search', '')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center min-w-11 min-h-11 justify-center"
                        aria-label="Kosongkan carian">
                        <svg class="h-5 w-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </button>
                @endif
            </div>

            {{-- Filter Toggle Button --}}
            @if (count($filterOptions) > 0)
                <button type="button" wire:click="toggleFilters"
                    class="inline-flex items-center gap-2 px-4 py-2.5 min-h-11 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors"
                    aria-expanded="{{ $showFilters ? 'true' : 'false' }}" aria-controls="filter-panel">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.591L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Penapis</span>
                    @if ($this->activeFilterCount > 0)
                        <span
                            class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200 rounded-full">
                            {{ $this->activeFilterCount }}
                        </span>
                    @endif
                </button>
            @endif
        </div>

        {{-- Search Help Text --}}
        <p id="search-help" class="sr-only">
            Masukkan kata kunci untuk mencari. Hasil akan dikemas kini secara automatik.
        </p>
    </div>

    {{-- Filter Panel --}}
    @if ($showFilters && count($filterOptions) > 0)
        <div id="filter-panel"
            class="mt-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700"
            role="region" aria-label="Panel penapis">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($filterOptions as $key => $option)
                    <div>
                        <label for="filter-{{ $key }}"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $option['label'] ?? ucfirst($key) }}
                        </label>

                        @if (($option['type'] ?? 'select') === 'select')
                            <select id="filter-{{ $key }}" wire:model.live="activeFilters.{{ $key }}"
                                class="block w-full px-3 py-2 min-h-11 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">{{ $option['placeholder'] ?? 'Semua' }}</option>
                                @foreach ($option['options'] ?? [] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        @elseif(($option['type'] ?? 'select') === 'date')
                            <input type="date" id="filter-{{ $key }}"
                                wire:model.live="activeFilters.{{ $key }}"
                                class="block w-full px-3 py-2 min-h-11 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Filter Actions --}}
            <div class="mt-4 flex flex-wrap items-center gap-3">
                @if ($this->hasActiveFilters)
                    <button type="button" wire:click="clearFilters"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-md">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                        Kosongkan semua
                    </button>
                @endif

                @if ($enableSavedFilters && Auth::check())
                    <button type="button" x-on:click="showSaveModal = true"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-md"
                        :disabled="!{{ $this->hasActiveFilters ? 'true' : 'false' }}">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                        </svg>
                        Simpan penapis
                    </button>
                @endif
            </div>

            {{-- Saved Filters --}}
            @if ($enableSavedFilters && count($savedFilters) > 0)
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Penapis tersimpan
                    </h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($savedFilters as $index => $saved)
                            <div
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-full text-sm">
                                <button type="button" wire:click="applySavedFilter({{ $index }})"
                                    class="text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400">
                                    {{ $saved['name'] }}
                                </button>
                                <button type="button" wire:click="deleteSavedFilter({{ $index }})"
                                    class="ml-1 text-gray-400 hover:text-danger-600 dark:hover:text-danger-400"
                                    aria-label="Padam penapis {{ $saved['name'] }}">
                                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Save Filter Modal --}}
    <div x-show="showSaveModal" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true"
        aria-labelledby="save-filter-title">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50" x-on:click="showSaveModal = false"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6"
                x-on:keydown.escape.window="showSaveModal = false">
                <h3 id="save-filter-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    Simpan Penapis
                </h3>

                <div class="mb-4">
                    <label for="filter-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nama penapis
                    </label>
                    <input type="text" id="filter-name" x-model="filterName"
                        placeholder="Contoh: Tiket terbuka minggu ini"
                        class="block w-full px-3 py-2 min-h-11 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="showSaveModal = false"
                        class="px-4 py-2 min-h-11 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        Batal
                    </button>
                    <button type="button"
                        x-on:click="$wire.saveCurrentFilters(filterName); showSaveModal = false; filterName = ''"
                        class="px-4 py-2 min-h-11 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading Indicator --}}
    <div wire:loading.delay class="mt-2" role="status" aria-live="polite">
        <span class="text-sm text-gray-500 dark:text-gray-400">
            Mencari...
        </span>
    </div>
</div>

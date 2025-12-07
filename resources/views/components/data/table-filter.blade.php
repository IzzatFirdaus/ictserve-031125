{{--
/**
 * Table Filter Component
 *
 * Accessible table filtering with dropdown filters, search input, and clear button.
 * Follows D14 §10.5 ARIA guidelines and D12 §4.1 touch target requirements.
 *
 * Features:
 * - Dropdown filters with aria-expanded per D14 §10.5
 * - Search input with aria-describedby per D13 §3.7.1
 * - Clear filters button with 44×44px touch target per D12 §4.1
 * - Keyboard accessible (Tab, Enter, Escape)
 *
 * @component
 * @name Table Filter
 * @description Accessible table filtering controls
 * @author Pasukan BPM MOTAC
 * @version 1.0.0
 * @since 2025-12-05
 *
 * Requirements Traceability: D12 §4.1, D13 §3.7.1, D14 §10.5
 * WCAG Level: AA (SC 1.3.1, 2.1.1, 4.1.2)
 *
 * Usage:
 * <x-data.table-filter
 *     search-placeholder="Search tickets..."
 *     :filters="[
 *         ['name' => 'status', 'label' => 'Status', 'options' => [...]],
 *         ['name' => 'priority', 'label' => 'Priority', 'options' => [...]]
 *     ]"
 * />
 */
--}}

@props([
    'searchPlaceholder' => null,
    'searchValue' => '',
    'filters' => [],
    'activeFilters' => [],
])

@php
    $filterId = 'table-filter-' . uniqid();
    $searchId = $filterId . '-search';
    $searchHintId = $filterId . '-search-hint';
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row gap-3 mb-4']) }} x-data="{
    search: @js($searchValue),
    filters: @js($activeFilters),
    openDropdown: null,
    hasActiveFilters() {
        return this.search.length > 0 || Object.values(this.filters).some(v => v && v.length > 0);
    },
    clearAll() {
        this.search = '';
        this.filters = {};
        this.$dispatch('table-filter-clear');
    },
    updateFilter(name, value) {
        this.filters[name] = value;
        this.$dispatch('table-filter-change', { name, value });
    },
    updateSearch() {
        this.$dispatch('table-search', { value: this.search });
    }
}">
    {{-- Search Input with aria-describedby per D13 §3.7.1 --}}
    @if ($searchPlaceholder)
        <div class="relative flex-1 max-w-md">
            <label for="{{ $searchId }}" class="sr-only">{{ __('Search') }}</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="search" id="{{ $searchId }}" x-model.debounce.300ms="search" @input="updateSearch()"
                    class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 py-2 pl-10 pr-3 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500 focus:ring-offset-0"
                    placeholder="{{ $searchPlaceholder }}" aria-describedby="{{ $searchHintId }}" />
            </div>
            <p id="{{ $searchHintId }}" class="sr-only">{{ __('Type to search. Results will update automatically.') }}
            </p>
        </div>
    @endif

    {{-- Filter Dropdowns with aria-expanded per D14 §10.5 --}}
    @foreach ($filters as $filter)
        @php
            $dropdownId = $filterId . '-' . $filter['name'];
        @endphp
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <button type="button" id="{{ $dropdownId }}-button" @click="open = !open" :aria-expanded="open"
                aria-haspopup="listbox" aria-controls="{{ $dropdownId }}-listbox"
                class="inline-flex items-center justify-between gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 min-h-11 min-w-[120px]">
                <span>{{ $filter['label'] }}</span>
                <svg class="h-4 w-4 text-gray-400 transition-transform" :class="open && 'rotate-180'"
                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1"
                @keydown.escape.window="open = false"
                class="absolute left-0 z-20 mt-2 w-56 origin-top-left rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                id="{{ $dropdownId }}-listbox" role="listbox" aria-labelledby="{{ $dropdownId }}-button" x-cloak>
                <div class="py-1 max-h-60 overflow-auto">
                    @foreach ($filter['options'] ?? [] as $option)
                        <button type="button" role="option"
                            @click="updateFilter('{{ $filter['name'] }}', '{{ $option['value'] }}'); open = false"
                            :aria-selected="filters['{{ $filter['name'] }}'] === '{{ $option['value'] }}'"
                            class="flex w-full items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:bg-gray-100 dark:focus:bg-gray-700 focus:outline-none min-h-11">
                            <span
                                class="mr-2 h-4 w-4 rounded border border-gray-300 dark:border-gray-600 flex items-center justify-center"
                                :class="filters['{{ $filter['name'] }}'] === '{{ $option['value'] }}' &&
                                    'bg-primary-600 border-primary-600'">
                                <svg x-show="filters['{{ $filter['name'] }}'] === '{{ $option['value'] }}'"
                                    class="h-3 w-3 text-white" viewBox="0 0 20 20" fill="currentColor"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                            {{ $option['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    {{-- Clear Filters Button - 44×44px touch target per D12 §4.1 --}}
    <button type="button" x-show="hasActiveFilters()" @click="clearAll()"
        class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 min-h-11 min-w-11"
        aria-label="{{ __('Clear all filters') }}" x-cloak>
        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                clip-rule="evenodd" />
        </svg>
        <span class="hidden sm:inline">{{ __('Clear') }}</span>
    </button>
</div>

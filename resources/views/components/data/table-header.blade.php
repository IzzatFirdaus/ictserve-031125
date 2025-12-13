{{--
/**
 * Table Header Component
 *
 * Sortable table header cell with proper ARIA attributes per D14 §10.5.
 *
 * Features:
 * - Semantic <th scope="col"> per D12 §6.14
 * - aria-sort attribute for sortable columns per D14 §10.5
 * - Keyboard accessible sorting (Enter/Space)
 * - Visual sort indicators with Heroicons
 *
 * @component
 * @name Table Header
 * @description Accessible sortable table header cell
 * @author Pasukan BPM MOTAC
 * @version 1.0.0
 * @since 2025-12-05
 *
 * Requirements Traceability: D12 §6.14, D14 §6.6, D14 §10.5
 * WCAG Level: AA (SC 1.3.1, 4.1.2)
 *
 * Usage:
 * <x-data.table-header column="name" :sortable="true">Name</x-data.table-header>
 */
--}}

@props([
    'column' => null,
    'sortable' => false,
    'align' => 'left',
    'width' => null,
])

@php
    $alignClasses = match ($align) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };

    $baseClasses = 'px-3 py-3.5 text-sm font-semibold text-gray-900 dark:text-gray-100 ' . $alignClasses;

    if ($width) {
        $baseClasses .= ' ' . $width;
    }
@endphp

<th scope="col" {{ $attributes->merge(['class' => $baseClasses]) }}
    @if ($sortable && $column) x-bind:aria-sort="getSortAriaLabel('{{ $column }}')"
        role="columnheader" @endif>
    @if ($sortable && $column)
        <button type="button"
            class="group inline-flex items-center gap-1 hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1 rounded"
            @click="sort('{{ $column }}')" @keydown.enter.prevent="sort('{{ $column }}')"
            @keydown.space.prevent="sort('{{ $column }}')" aria-label="{{ __('Sort by') }} {{ $slot }}">
            <span>{{ $slot }}</span>
            {{-- Sort indicator icons --}}
            <span class="relative flex flex-col" aria-hidden="true">
                {{-- Ascending arrow --}}
                <svg class="h-3 w-3 transition-colors"
                    :class="sortColumn === '{{ $column }}' && sortDirection === 'asc' ?
                        'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-500'"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                {{-- Descending arrow --}}
                <svg class="h-3 w-3 -mt-1 transition-colors"
                    :class="sortColumn === '{{ $column }}' && sortDirection === 'desc' ?
                        'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-500'"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </span>
        </button>
    @else
        {{ $slot }}
    @endif
</th>

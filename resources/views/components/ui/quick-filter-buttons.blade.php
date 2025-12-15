{{--
    Quick Filter Buttons Component
    @component QuickFilterButtons
    @description Displays quick-apply buttons for saved filters
    @trace D14 §6.5
    @requirements 23.5
--}}
@props([
'filters' => [],
'appliedFilterId' => null,
'context' => '',
])

@if (count($filters) > 0)
<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2']) }} role="group"
    aria-label="{{ __('Quick filter buttons') }}">

    @foreach ($filters as $filter)
    <button type="button"
        wire:click="$dispatch('apply-saved-filter', { filterId: '{{ $filter['id'] }}', context: '{{ $context }}' })"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-md min-h-11
                           transition-all duration-150 ease-out
                           {{ $appliedFilterId === $filter['id']
                               ? 'bg-primary-100 text-primary-700 border-2 border-primary-400 shadow-sm dark:bg-primary-900/50 dark:text-primary-300 dark:border-primary-600'
                               : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 hover:border-gray-400 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700' }}
                           focus:outline-none
                           touch-target"
        title="{{ $filter['description'] ?? __('Apply filter: :name', ['name' => $filter['name']]) }}"
        aria-pressed="{{ $appliedFilterId === $filter['id'] ? 'true' : 'false' }}">

        {{-- Filter Icon --}}
        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
            aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>

        <span class="truncate max-w-[120px]">{{ $filter['name'] }}</span>

        {{-- Active indicator --}}
        @if ($appliedFilterId === $filter['id'])
        <svg class="w-3.5 h-3.5 text-primary-600 dark:text-primary-400 shrink-0" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        @endif
    </button>
    @endforeach

    {{-- Clear Filter Button (shown when a filter is applied) --}}
    @if ($appliedFilterId)
    <button type="button" wire:click="$dispatch('clear-saved-filter', { context: '{{ $context }}' })"
        class="inline-flex items-center gap-1 px-2 py-1.5 text-xs font-medium min-h-11
                           text-gray-500 hover:text-gray-700 hover:bg-gray-100
                           dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700
                           rounded-md transition-colors duration-150
                           focus:outline-none"
        aria-label="{{ __('Clear applied filter') }}">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
            aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
        {{ __('Clear') }}
    </button>
    @endif
</div>
@endif
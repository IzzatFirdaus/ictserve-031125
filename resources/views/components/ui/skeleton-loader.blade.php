{{--
    Skeleton Loader Component v3.6.0

    Provides visual placeholder during content loading to prevent CLS.
    Supports multiple variants for different content types.

    @props
    - type: string - Skeleton type: 'card', 'list', 'table', 'stats', 'text', 'avatar'
    - count: int - Number of skeleton items (default: 1)
    - class: string - Additional CSS classes

    @see D12 §9 Performance optimization patterns
    @see Requirements 13.1 - CLS <0.1 target

    @example
    <x-ui.skeleton-loader type="stats" :count="4" />
    <x-ui.skeleton-loader type="card" :count="3" />

    @version 3.6.0
    @author Frontend Engineering Team
--}}

@props([
    'type' => 'card',
    'count' => 1,
])

<div role="status" aria-label="Memuatkan..." {{ $attributes->merge(['class' => 'space-y-4']) }}>
    @for ($i = 0; $i < $count; $i++)
        @switch($type)
            @case('stats')
                {{-- Statistics card skeleton --}}
                <div class="animate-pulse bg-white dark:bg-gray-800 rounded-lg p-6 shadow-card min-h-[140px]">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
                        <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                    </div>
                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-16 mb-2"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-32"></div>
                </div>
            @break

            @case('card')
                {{-- Content card skeleton --}}
                <div class="animate-pulse bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-3"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-full mb-2"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-5/6"></div>
                </div>
            @break

            @case('list')
                {{-- List item skeleton --}}
                <div class="animate-pulse flex items-center space-x-4 p-3">
                    <div class="h-10 w-10 bg-gray-200 dark:bg-gray-700 rounded-full shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mb-2"></div>
                        <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-1/2 mb-2"></div>
                        <div class="h-2 bg-slate-200 dark:bg-slate-700 rounded w-3/4"></div>
                    </div>
                </div>
            @break

            @case('table')
                {{-- Table row skeleton --}}
                <div class="animate-pulse flex items-center space-x-4 p-3 border-b border-slate-200 dark:border-slate-700">
                    <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-20"></div>
                    <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-32 flex-1"></div>
                    <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-24"></div>
                    <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-16"></div>
                </div>
            @break

            @case('text')
                {{-- Text paragraph skeleton --}}
                <div class="animate-pulse space-y-2">
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-4/6"></div>
                </div>
            @break

            @case('avatar')
                {{-- Avatar skeleton --}}
                <div class="animate-pulse flex items-center space-x-3">
                    <div class="h-12 w-12 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                    <div class="space-y-2">
                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
                        <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
                    </div>
                </div>
            @break

            @default
                {{-- Default card skeleton --}}
                <div class="animate-pulse bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-3"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-full mb-2"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-5/6"></div>
                </div>
        @endswitch
    @endfor

    <span class="sr-only">Memuatkan...</span>
</div>

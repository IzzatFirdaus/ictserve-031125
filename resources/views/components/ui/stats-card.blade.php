{{--
    Dynamic Stats Card Component (x-ui.stats-card)

    Dashboard statistics card with conditional icon colors:
    - Green/neutral for count = 0
    - Type color (e.g., red for danger) for count > 0

    @props
    - title: Card title text
    - count: Numeric count value
    - type: Color type (primary, success, warning, danger, gray)
    - icon: Named slot for SVG icon
    - href: Optional link for clickable card
    - description: Optional description text

    @usage
    <x-ui.stats-card
        :title="__('dashboard.overdue_items')"
        :count="$overdueCount"
        type="danger"
    >
        <x-slot:icon>
            <path d="M10 2L3 7v11a2 2 0 002 2h14a2 2 0 002-2V7l-7-5z"/>
        </x-slot:icon>
    </x-ui.stats-card>

    @trace SRS-FR-002.1; D04 §3.1; Task 2.2.15
    @see design.md Portal-Specific Components
--}}

@props([
    'title' => '',
    'count' => 0,
    'type' => 'primary',
    'href' => null,
    'description' => null,
])

@php
    // Determine if count is zero (neutral state)
    $isZero = (int) $count === 0;

    // Color mapping for background and text
    $typeColors = [
        'primary' => [
            'bg' => 'bg-primary-100 dark:bg-primary-900/30',
            'text' => 'text-primary-500 dark:text-primary-400',
        ],
        'success' => [
            'bg' => 'bg-success-100 dark:bg-success-900/30',
            'text' => 'text-success-500 dark:text-success-400',
        ],
        'warning' => [
            'bg' => 'bg-warning-100 dark:bg-warning-900/30',
            'text' => 'text-warning-500 dark:text-warning-400',
        ],
        'danger' => [
            'bg' => 'bg-danger-100 dark:bg-danger-900/30',
            'text' => 'text-danger-500 dark:text-danger-400',
        ],
        'gray' => [
            'bg' => 'bg-gray-100 dark:bg-gray-700',
            'text' => 'text-gray-500 dark:text-gray-400',
        ],
    ];

    // Neutral colors for zero count
    $neutralColors = [
        'bg' => 'bg-gray-100 dark:bg-gray-700',
        'text' => 'text-gray-500 dark:text-gray-400',
    ];

    // Success colors for zero count on danger type (good state - no overdue items)
    $zeroSuccessColors = [
        'bg' => 'bg-green-100 dark:bg-green-900/30',
        'text' => 'text-green-500 dark:text-green-400',
    ];

    // Determine which colors to use
    if ($isZero) {
        // For danger type at zero, use green to indicate "good" state
        $colors = $type === 'danger' ? $zeroSuccessColors : $neutralColors;
    } else {
        $colors = $typeColors[$type] ?? $typeColors['primary'];
    }
@endphp

@if($href)
<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'block bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all duration-200 hover:shadow-md hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900',
    ]) }}
    aria-label="{{ $title }}: {{ $count }}"
>
@else
<div
    {{ $attributes->merge([
        'class' => 'bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6',
        'role' => 'status',
        'aria-label' => $title . ': ' . $count,
    ]) }}
>
@endif

    <div class="flex items-center justify-between">
        {{-- Text Content --}}
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 truncate">
                {{ $title }}
            </p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">
                {{ number_format((int) $count) }}
            </p>
            @if($description)
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $description }}
            </p>
            @endif
        </div>

        {{-- Icon Container --}}
        <div class="flex-shrink-0 ml-4">
            <div class="p-3 rounded-full {{ $colors['bg'] }}" aria-hidden="true">
                @if(isset($icon) && $icon->isNotEmpty())
                <svg class="h-8 w-8 {{ $colors['text'] }}" fill="currentColor" viewBox="0 0 20 20">
                    {{ $icon }}
                </svg>
                @else
                {{-- Default chart icon --}}
                <svg class="h-8 w-8 {{ $colors['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                @endif
            </div>
        </div>
    </div>

    {{-- Optional trend indicator slot --}}
    @if(isset($trend) && $trend->isNotEmpty())
    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
        {{ $trend }}
    </div>
    @endif

@if($href)
</a>
@else
</div>
@endif

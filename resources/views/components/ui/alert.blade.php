{{-- 
/**
 * Component name: UI Alert
 * Description: Accessible alert notification with severity variants and ARIA live regions.
 * Author: Pasukan BPM MOTAC
 * References: D03-FR-006.1, D03-FR-006.2, D04 section 6.1, D10 section 7, D12 section 9, D14 section 8
 * WCAG: 2.2 Level AA
 * Version: 1.0.0 (2025-11-03)
 */
--}}

@props([
    'type' => 'info',
    'dismissible' => false,
    'title' => null,
])

@php
    $typeConfig = [
        'success' => [
            'bg' => 'bg-green-50',
            'border' => 'border-green-700',
            'text' => 'text-green-900',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
            'role' => 'status',
            'live' => 'polite',
        ],
        'error' => [
            'bg' => 'bg-red-50',
            'border' => 'border-danger-dark',
            'text' => 'text-red-900',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />',
            'role' => 'alert',
            'live' => 'assertive',
        ],
        'warning' => [
            'bg' => 'bg-orange-50',
            'border' => 'border-orange-600',
            'text' => 'text-orange-900',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
            'role' => 'alert',
            'live' => 'polite',
        ],
        'info' => [
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-600',
            'text' => 'text-blue-900',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
            'role' => 'status',
            'live' => 'polite',
        ],
    ];

    $config = $typeConfig[$type] ?? $typeConfig['info'];
@endphp

<div class="rounded-md border-l-4 {{ $config['bg'] }} {{ $config['border'] }} p-4 mb-4" role="{{ $config['role'] }}"
    aria-live="{{ $config['live'] }}" x-data="{ show: true }" x-show="show" {{ $attributes }}>
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 {{ $config['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                aria-hidden="true">
                {!! $config['icon'] !!}
            </svg>
        </div>
        <div class="ml-3 flex-1">
            @if ($title)
                <h3 class="text-sm font-medium {{ $config['text'] }}">{{ $title }}</h3>
                <div class="mt-2 text-sm {{ $config['text'] }}">{{ $slot }}</div>
            @else
                <p class="text-sm {{ $config['text'] }}">{{ $slot }}</p>
            @endif
        </div>
        @if ($dismissible)
            <div class="ml-auto pl-3">
                <button type="button" @click="show = false"
                    class="inline-flex rounded-md p-1.5 {{ $config['text'] }} hover:bg-opacity-20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $type === 'error' ? 'red' : ($type === 'warning' ? 'orange' : ($type === 'success' ? 'green' : 'blue')) }}-600 min-h-[44px] min-w-[44px] items-center justify-center"
                    aria-label="{{ __('Dismiss alert') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif
    </div>
</div>

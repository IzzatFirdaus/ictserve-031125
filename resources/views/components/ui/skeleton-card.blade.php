{{--
/**
 * Skeleton Card Component
 *
 * Loading placeholder for dashboard statistics cards to improve perceived performance
 * and prevent Cumulative Layout Shift (CLS) during data loading.
 *
 * Features:
 * - aria-busy="true" for accessibility per D12 §6.4
 * - Hidden text for screen readers per WCAG 2.2 AA
 * - Pulse animation respecting prefers-reduced-motion per D12 §6.10
 * - Uses --bg-washed (#F7F7F7) for skeleton background per D14 §4.1.1
 * - Matches statistics card dimensions
 * - Prevents layout shift
 *
 * @props variant: card|compact - Card style variant
 *
 * @see D12 §6.4 Loading states
 * @see D12 §6.10 Motion and animation
 * @see D14 §4.1.1 Background tokens
 * @see D14 §9.2 Loading states guidelines
 *
 * @requirements 8.5, 9.4, 26.1, 26.5 Skeleton loading states
 *
 * @wcag-level AA
 *
 * @version 2.0.0
 *
 * @updated 2025-12-05
 */
--}}

@props([
'variant' => 'card',
])

@php
$baseClasses = match ($variant) {
'compact' => 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-(--radius-l) p-4',
default
=> 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm rounded-(--radius-l)',
};
@endphp

<div {{ $attributes->merge(['class' => $baseClasses]) }} aria-busy="true" aria-label="{{ __('Loading content') }}"
    role="status">

    @if ($variant === 'compact')
    {{-- Compact variant for smaller cards --}}
    <div class="flex items-center gap-3">
        <div class="shrink-0">
            <div class="h-10 w-10 bg-gray-200 dark:bg-gray-700 rounded-(--radius-l) skeleton-pulse"></div>
        </div>
        <div class="flex-1 space-y-2">
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded-sm w-2/3 skeleton-pulse"></div>
            <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded-sm w-1/3 skeleton-pulse"></div>
        </div>
    </div>
    @else
    {{-- Default card variant matching stats-card dimensions --}}
    <div class="p-5">
        <div class="flex items-center">
            <div class="shrink-0">
                <div class="h-12 w-12 bg-gray-200 dark:bg-gray-700 rounded-(--radius-l) skeleton-pulse"></div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <div class="space-y-3">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded-sm w-3/4 skeleton-pulse"></div>
                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded-sm w-1/2 skeleton-pulse"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-gray-50 dark:bg-gray-900 px-5 py-3">
        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded-sm w-1/3 skeleton-pulse"></div>
    </div>
    @endif

    {{-- Screen reader text --}}
    <span class="sr-only">{{ __('Loading content, please wait...') }}</span>
</div>

<style>
    .skeleton-pulse {
        animation: skeleton-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes skeleton-pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    /* Respect prefers-reduced-motion per D12 §6.10 */
    @media (prefers-reduced-motion: reduce) {
        .skeleton-pulse {
            animation: none;
            opacity: 0.7;
        }
    }
</style>
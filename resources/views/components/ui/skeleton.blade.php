{{--
/**
 * Skeleton Loader Component
 *
 * Versatile loading placeholder with multiple variants for different content types.
 *
 * Features:
 * - aria-busy="true" for accessibility per D12 §6.4
 * - Hidden text for screen readers per WCAG 2.2 AA
 * - Pulse animation respecting prefers-reduced-motion per D12 §6.10
 * - Uses --bg-washed for skeleton background per D14 §4.1.1
 * - Multiple variants: text, avatar, table-row, button, image
 *
 * @props type: text|avatar|table-row|button|image - Skeleton variant
 * @props lines: int - Number of text lines (for text type)
 * @props width: string - Width class (full, 3/4, 1/2, 1/3, 1/4)
 * @props size: sm|md|lg - Size variant for avatar/button
 *
 * @see D12 §6.4 Loading states
 * @see D12 §6.10 Motion and animation
 * @see D14 §4.1.1 Background tokens
 * @see D14 §9.2 Loading states guidelines
 *
 * @requirements 26.1 Skeleton variants
 *
 * @wcag-level AA
 *
 * @version 1.0.0
 *
 * @created 2025-12-05
 */
--}}

@props([
'type' => 'text',
'lines' => 3,
'width' => 'full',
'size' => 'md',
])

@php
$widthClass = match ($width) {
'full' => 'w-full',
'3/4' => 'w-3/4',
'1/2' => 'w-1/2',
'1/3' => 'w-1/3',
'1/4' => 'w-1/4',
default => 'w-full',
};

$avatarSize = match ($size) {
'sm' => 'h-8 w-8',
'lg' => 'h-14 w-14',
default => 'h-10 w-10',
};

$buttonSize = match ($size) {
'sm' => 'h-8 w-20',
'lg' => 'h-12 w-32',
default => 'h-10 w-24',
};
@endphp

<div {{ $attributes->merge(['class' => 'skeleton-container']) }} aria-busy="true" aria-label="{{ __('Loading') }}"
    role="status">

    @switch($type)
    @case('avatar')
    {{-- Avatar skeleton --}}
    <div class="{{ $avatarSize }} bg-gray-200 dark:bg-gray-700 rounded-full skeleton-pulse"></div>
    @break

    @case('table-row')
    {{-- Table row skeleton --}}
    <div class="flex items-center gap-4 py-3">
        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded-xs w-1/6 skeleton-pulse"></div>
        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded-xs w-1/4 skeleton-pulse"></div>
        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded-xs w-1/3 skeleton-pulse"></div>
        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded-xs w-1/6 skeleton-pulse"></div>
    </div>
    @break

    @case('button')
    {{-- Button skeleton --}}
    <div class="{{ $buttonSize }} bg-gray-200 dark:bg-gray-700 rounded-m skeleton-pulse"></div>
    @break

    @case('image')
    {{-- Image skeleton --}}
    <div
        class="aspect-video {{ $widthClass }} bg-gray-200 dark:bg-gray-700 rounded-(--radius-l) skeleton-pulse flex items-center justify-center">
        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20"
            aria-hidden="true">
            <path fill-rule="evenodd"
                d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                clip-rule="evenodd" />
        </svg>
    </div>
    @break

    @case('text')

    @default
    {{-- Text lines skeleton --}}
    <div class="space-y-2">
        @for ($i = 0; $i < $lines; $i++)
            @php
            // Vary line widths for natural appearance
            $lineWidth=match (true) {
            $i===$lines - 1=> 'w-2/3', // Last line shorter
            $i % 3 === 0 => 'w-full',
            $i % 3 === 1 => 'w-11/12',
            default => 'w-5/6',
            };
            @endphp
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded-xs {{ $lineWidth }} skeleton-pulse"></div>
            @endfor
    </div>
    @break

    @endswitch

    {{-- Screen reader text --}}
    <span class="sr-only">{{ __('Loading content...') }}</span>
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
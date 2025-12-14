{{--
/**
 * Component: Loading Indicator
 * Description: WCAG 2.2 AA compliant loading indicator with multiple variants
 * @author Pasukan BPM MOTAC
 * @trace D12 §6.8 (Performance Optimization)
 * @trace D13 §3.7.2 (Loading States)
 * @trace D14 §10.3 (Accessibility Standards)
 * @wcag WCAG 2.2 Level AA (SC 4.1.3 Status Messages)
 * @version 1.0.0
 * @created 2025-12-14
 *
 * Features:
 * - Multiple variants: spinner, dots, pulse, skeleton
 * - ARIA live region for screen reader announcements
 * - Respects prefers-reduced-motion
 * - Customizable size and color
 *
 * Usage:
 * <x-ui.loading />
 * <x-ui.loading variant="dots" size="lg" />
 * <x-ui.loading variant="skeleton" class="h-20 w-full" />
 */
--}}

@props([
    'variant' => 'spinner', // spinner, dots, pulse, skeleton
    'size' => 'md', // sm, md, lg, xl
    'color' => 'primary', // primary, secondary, white, current
    'label' => null, // Screen reader label
    'inline' => false, // Display inline with text
])

@php
    // Size classes
    $sizeClasses = match ($size) {
        'sm' => 'h-4 w-4',
        'lg' => 'h-8 w-8',
        'xl' => 'h-12 w-12',
        default => 'h-6 w-6',
    };

    // Color classes
    $colorClasses = match ($color) {
        'secondary' => 'text-gray-500 dark:text-gray-400',
        'white' => 'text-white',
        'current' => 'text-current',
        default => 'text-primary-600 dark:text-primary-400',
    };

    // Default label
    $ariaLabel = $label ?? __('Memuatkan...');
@endphp

@if ($variant === 'spinner')
    {{-- Spinner variant --}}
    <div {{ $attributes->merge(['class' => ($inline ? 'inline-flex' : 'flex') . ' items-center justify-center']) }}
        role="status" aria-live="polite" aria-label="{{ $ariaLabel }}">
        <svg class="animate-spin {{ $sizeClasses }} {{ $colorClasses }}" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
        <span class="sr-only">{{ $ariaLabel }}</span>
    </div>
@elseif($variant === 'dots')
    {{-- Dots variant --}}
    <div {{ $attributes->merge(['class' => ($inline ? 'inline-flex' : 'flex') . ' items-center justify-center gap-1']) }}
        role="status" aria-live="polite" aria-label="{{ $ariaLabel }}">
        @php
            $dotSize = match ($size) {
                'sm' => 'h-1.5 w-1.5',
                'lg' => 'h-3 w-3',
                'xl' => 'h-4 w-4',
                default => 'h-2 w-2',
            };
        @endphp
        <span class="{{ $dotSize }} {{ $colorClasses }} rounded-full animate-bounce"
            style="animation-delay: 0ms"></span>
        <span class="{{ $dotSize }} {{ $colorClasses }} rounded-full animate-bounce"
            style="animation-delay: 150ms"></span>
        <span class="{{ $dotSize }} {{ $colorClasses }} rounded-full animate-bounce"
            style="animation-delay: 300ms"></span>
        <span class="sr-only">{{ $ariaLabel }}</span>
    </div>
@elseif($variant === 'pulse')
    {{-- Pulse variant --}}
    <div {{ $attributes->merge(['class' => ($inline ? 'inline-flex' : 'flex') . ' items-center justify-center']) }}
        role="status" aria-live="polite" aria-label="{{ $ariaLabel }}">
        <span class="{{ $sizeClasses }} {{ $colorClasses }} rounded-full animate-pulse bg-current opacity-75"></span>
        <span class="sr-only">{{ $ariaLabel }}</span>
    </div>
@elseif($variant === 'skeleton')
    {{-- Skeleton variant --}}
    <div {{ $attributes->merge(['class' => 'animate-pulse bg-gray-200 dark:bg-gray-700 rounded-md']) }} role="status"
        aria-live="polite" aria-label="{{ $ariaLabel }}">
        <span class="sr-only">{{ $ariaLabel }}</span>
    </div>
@endif

{{-- Reduced motion support --}}
@once
    @push('styles')
        <style>
            @media (prefers-reduced-motion: reduce) {

                .animate-spin,
                .animate-bounce,
                .animate-pulse {
                    animation: none !important;
                }

                /* Provide alternative indication for reduced motion */
                .animate-spin {
                    opacity: 0.7;
                }

                .animate-bounce {
                    opacity: 0.5;
                }
            }
        </style>
    @endpush
@endonce

{{--
/**
 * UI Button Component - MyDS Design System
 *
 * @component ui.button
 * @description WCAG 2.2 AA compliant button with MyDS design tokens
 * @author Pasukan BPM MOTAC
 * @trace D13 §2.2-2.7 (MyDS Design Tokens)
 * @trace D12 §4.1 (44px Touch Targets)
 * @trace D14 §6.5 (Button Styling)
 * @version 2.0.0
 * @updated 2025-12-06
 */
--}}
@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'loading' => null,
    'icon' => null,
    'disabled' => false,
])

@php
    // Base classes with MyDS tokens: shadow-button, rounded-lg, transition-colors duration-200
    $baseClasses =
        'inline-flex items-center justify-center font-medium rounded-lg shadow-button transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    // Variants using MyDS semantic colors (D13 §2.2)
    $variants = [
        'primary' => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500 dark:bg-primary-600 dark:hover:bg-primary-500',
        'secondary' =>
            'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-primary-500 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700',
        'success' => 'bg-success-500 text-white hover:bg-success-600 focus:ring-success-500',
        'warning' => 'bg-warning-500 text-white hover:bg-warning-600 focus:ring-warning-500',
        'danger' => 'bg-danger-500 text-white hover:bg-danger-600 focus:ring-danger-500',
        'ghost' =>
            'bg-transparent text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:ring-primary-500 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-gray-100',
    ];

    // Sizes with 44px minimum touch target (D12 §4.1, WCAG 2.5.8)
    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm min-h-9', // 36px - for dense UIs only
        'md' => 'px-4 py-2 text-base min-h-44 min-w-44', // 44px touch target
        'lg' => 'px-6 py-3 text-lg min-h-13', // 52px
    ];

    $classes =
        $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button {{ $attributes->merge(['class' => $classes, 'type' => $type, 'disabled' => $disabled || $loading]) }}>
    @if ($loading)
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
        <span class="sr-only">{{ __('common.loading') }}</span>
    @elseif($icon)
        <x-icon :name="$icon" class="w-5 h-5 mr-2 -ml-1" aria-hidden="true" />
    @endif

    {{ $slot }}
</button>

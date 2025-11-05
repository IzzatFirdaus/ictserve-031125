{{-- 
/**
 * Component name: UI Button
 * Description: Accessible button with multiple variants and loading state support.
 * Author: Pasukan BPM MOTAC
 * References: D03-FR-006.1, D03-FR-006.2, D04 section 6.1, D10 section 7, D12 section 9, D14 section 8
 * WCAG: 2.2 Level AA
 * Version: 1.0.0 (2025-11-03)
 */
--}}

@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'loading' => false,
])

@php
    $baseClasses =
        'inline-flex items-center justify-center px-4 py-2 rounded-md font-medium text-sm transition-colors duration-200 focus:outline-none focus:ring-[3px] focus:ring-offset-2 min-h-[44px] disabled:opacity-50 disabled:cursor-not-allowed';

    $variantClasses = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-600',
        'secondary' => 'bg-gray-200 text-gray-900 hover:bg-gray-300 focus:ring-gray-500',
        'success' => 'bg-green-700 text-white hover:bg-green-800 focus:ring-green-700',
        'danger' => 'bg-danger-dark text-white hover:bg-red-800 focus:ring-red-700',
        'warning' => 'bg-orange-600 text-white hover:bg-orange-700 focus:ring-orange-600',
        'ghost' => 'bg-transparent text-blue-600 hover:bg-blue-50 focus:ring-blue-600',
    ];

    $classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" class="{{ $classes }}" {{ $attributes }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" class="{{ $classes }}" @if ($disabled || $loading) disabled @endif
        {{ $attributes }}>
        @if ($loading)
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
        @endif
        {{ $slot }}
    </button>
@endif

{{--
/**
 * Component: Accessible Button
 * Description: WCAG 2.2 AA compliant button with 44px touch target and 3px focus indicator
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-006.2 (Keyboard Navigation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @wcag WCAG 2.2 Level AA (SC 2.4.7 Focus Visible, SC 2.5.8 Target Size)
 * @version 1.0.0
 * @created 2025-12-04
 *
 * Requirements:
 * - 9.1: Color contrast 4.5:1 for text
 * - 9.2: 3px focus indicators
 * - 9.4: 44px minimum touch target
 */
--}}

@props([
    'variant' => 'primary', // primary, secondary, success, warning, danger, ghost
    'size' => 'md', // sm, md, lg
    'type' => 'button',
    'loading' => false,
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'left', // left, right
    'ariaLabel' => null,
    'ariaDescribedby' => null,
    'ariaExpanded' => null,
    'ariaControls' => null,
    'ariaPressed' => null,
])

@php
    // Base classes with 44px minimum touch target
    $baseClasses =
        'inline-flex items-center justify-center font-medium rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    // Variant styles with WCAG AA compliant contrast ratios
    $variants = [
        'primary' => 'bg-primary-500 text-white hover:bg-primary-600 focus-visible:ring-3 focus-visible:ring-primary-500',
        'secondary' =>
            'bg-white text-gray-700 border-2 border-gray-300 hover:bg-gray-50 focus-visible:ring-3 focus-visible:ring-primary-500',
        'success' => 'bg-success-500 text-white hover:bg-success-600 focus-visible:ring-3 focus-visible:ring-success-500',
        'warning' => 'bg-warning-600 text-white hover:bg-warning-700 focus-visible:ring-3 focus-visible:ring-warning-600',
        'danger' => 'bg-danger-500 text-white hover:bg-danger-600 focus-visible:ring-3 focus-visible:ring-danger-500',
        'ghost' => 'bg-transparent text-gray-700 hover:bg-gray-100 focus-visible:ring-3 focus-visible:ring-primary-500',
    ];

    // Size styles - all meet 44px minimum touch target
    $sizes = [
        'sm' => 'min-h-11 min-w-11 px-4 py-2 text-sm',
        'md' => 'min-h-11 min-w-11 px-5 py-2.5 text-base',
        'lg' => 'min-h-[52px] min-w-[52px] px-6 py-3 text-lg',
    ];

    $classes =
        $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}
    @if ($disabled || $loading) disabled @endif
    @if ($ariaLabel) aria-label="{{ $ariaLabel }}" @endif
    @if ($ariaDescribedby) aria-describedby="{{ $ariaDescribedby }}" @endif
    @if ($ariaExpanded !== null) aria-expanded="{{ $ariaExpanded ? 'true' : 'false' }}" @endif
    @if ($ariaControls) aria-controls="{{ $ariaControls }}" @endif
    @if ($ariaPressed !== null) aria-pressed="{{ $ariaPressed ? 'true' : 'false' }}" @endif
    @if ($loading) aria-busy="true" @endif>
    {{-- Loading spinner --}}
    @if ($loading)
        <svg class="animate-spin -ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
        <span class="sr-only">{{ __('common.loading') }}</span>
    @endif

    {{-- Left icon --}}
    @if ($icon && $iconPosition === 'left' && !$loading)
        <span class="mr-2 -ml-1" aria-hidden="true">
            @if (is_string($icon))
                <x-dynamic-component :component="$icon" class="h-5 w-5" />
            @else
                {{ $icon }}
            @endif
        </span>
    @endif

    {{-- Button content --}}
    <span>{{ $slot }}</span>

    {{-- Right icon --}}
    @if ($icon && $iconPosition === 'right' && !$loading)
        <span class="ml-2 -mr-1" aria-hidden="true">
            @if (is_string($icon))
                <x-dynamic-component :component="$icon" class="h-5 w-5" />
            @else
                {{ $icon }}
            @endif
        </span>
    @endif
</button>

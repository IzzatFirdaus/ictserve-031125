{{--
    Component name: Badge
    Description: WCAG 2.2 AA compliant badge component for labels, counts, and status indicators with semantic color variants
    Author: dev-team@motac.gov.my
    Version: 1.0.0
    Last Updated: 2025-11-03
    WCAG Level: AA
    Requirements Traceability: D03-FR-006.1, D03-FR-006.3, D04 §6.1, D10 §7, D12 §7, D12 §9, D14 §5, D14 §8, D14 §9
    Browser Support: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
    Standards Compliance: ISO/IEC 40500 (WCAG 2.2 Level AA), D12 (UI/UX), D14 (Style Guide)
--}}

@props([
    'variant' => 'default', // default, primary, success, warning, danger, info
    'size' => 'md', // sm, md, lg
    'rounded' => 'full', // full, lg, md
    'removable' => false,
])

@php
    // WCAG 2.2 AA compliant color classes
    $variantClasses = [
        'default' => 'bg-gray-100 text-gray-800 border-gray-300',
        'primary' => 'bg-blue-100 text-blue-800 border-blue-300',
        'success' => 'bg-green-100 text-green-800 border-green-300',
        'warning' => 'bg-orange-100 text-orange-900 border-orange-300',
        'danger' => 'bg-red-100 text-red-900 border-red-300',
        'info' => 'bg-cyan-100 text-cyan-900 border-cyan-300',
    ];

    // Size classes
    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-1 text-sm',
        'lg' => 'px-3 py-1.5 text-base',
    ];

    // Rounded classes
    $roundedClasses = [
        'full' => 'rounded-full',
        'lg' => 'rounded-lg',
        'md' => 'rounded-md',
    ];

    $baseClasses = 'inline-flex items-center gap-1 font-medium border';
    $classes = implode(' ', [
        $baseClasses,
        $variantClasses[$variant] ?? $variantClasses['default'],
        $sizeClasses[$size] ?? $sizeClasses['md'],
        $roundedClasses[$rounded] ?? $roundedClasses['full'],
    ]);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}

    @if($removable)
        <button
            type="button"
            class="flex-shrink-0 ml-0.5 h-4 w-4 rounded-full inline-flex items-center justify-center hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600"
            aria-label="{{ __('Remove') }}"
        >
            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    @endif
</span>

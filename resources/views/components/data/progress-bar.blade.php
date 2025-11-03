{{--
/**
 * Component name: Progress Bar
 * Description: WCAG 2.2 AA compliant progress bar with ARIA attributes, percentage display, and color variants.
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-006.1 (Accessibility Requirements)
 * @trace D03-FR-006.3 (Screen Reader Support)
 * @trace D04 §6.1 (Accessibility Compliance)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §7 (UI Components)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 * @trace D14 §9 (Accessibility Standards)
 * @version 1.0.0
 * @created 2025-11-03
 */
--}}

@props([
    'value' => 0,
    'max' => 100,
    'label' => '',
    'showLabel' => true,
    'showPercentage' => true,
    'color' => 'primary', // primary, success, warning, danger
    'size' => 'md', // sm, md, lg
    'striped' => false,
    'animated' => false,
])

@php
    $percentage = $max > 0 ? min(100, ($value / $max) * 100) : 0;

    // WCAG 2.2 AA compliant color classes
    $colorClasses = [
        'primary' => 'bg-blue-600',
        'success' => 'bg-green-600',
        'warning' => 'bg-orange-500',
        'danger' => 'bg-red-700',
    ];

    // Size classes
    $sizeClasses = [
        'sm' => 'h-2',
        'md' => 'h-4',
        'lg' => 'h-6',
    ];

    $barColor = $colorClasses[$color] ?? $colorClasses['primary'];
    $barHeight = $sizeClasses[$size] ?? $sizeClasses['md'];

    $stripedClass = $striped ? 'bg-stripes' : '';
    $animatedClass = $animated ? 'progress-bar-animated' : '';
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    @if($showLabel && $label)
        <div class="flex justify-between items-center mb-2">
            <label class="text-sm font-medium text-gray-700">{{ $label }}</label>
            @if($showPercentage)
                <span class="text-sm font-medium text-gray-700">{{ number_format($percentage, 0) }}%</span>
            @endif
        </div>
    @endif

    <div
        class="w-full bg-gray-200 rounded-full overflow-hidden {{ $barHeight }}"
        role="progressbar"
        aria-valuenow="{{ $value }}"
        aria-valuemin="0"
        aria-valuemax="{{ $max }}"
        aria-label="{{ $label ?: __('Progress') }}"
    >
        <div
            class="h-full {{ $barColor }} {{ $stripedClass }} {{ $animatedClass }} transition-all duration-300 ease-out rounded-full"
            style="width: {{ $percentage }}%"
        >
            <span class="sr-only">{{ number_format($percentage, 0) }}% {{ __('complete') }}</span>
        </div>
    </div>
</div>

@pushOnce('styles')
<style>
    .bg-stripes {
        background-image: linear-gradient(
            45deg,
            rgba(255, 255, 255, 0.15) 25%,
            transparent 25%,
            transparent 50%,
            rgba(255, 255, 255, 0.15) 50%,
            rgba(255, 255, 255, 0.15) 75%,
            transparent 75%,
            transparent
        );
        background-size: 1rem 1rem;
    }

    .progress-bar-animated {
        animation: progress-bar-stripes 1s linear infinite;
    }

    @keyframes progress-bar-stripes {
        0% { background-position: 1rem 0; }
        100% { background-position: 0 0; }
    }

    @media (prefers-reduced-motion: reduce) {
        .progress-bar-animated {
            animation: none;
        }
    }
</style>
@endPushOnce

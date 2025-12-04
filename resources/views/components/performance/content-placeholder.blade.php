{{--
/**
 * Content Placeholder Component
 *
 * Generic placeholder for dynamic content to prevent CLS.
 * Uses content-visibility CSS for rendering optimization.
 *
 * @component performance.content-placeholder
 * @trace Requirements: 10.3 (CLS <0.1)
 * @see D12 §9 Performance optimization patterns
 */
--}}
@props([
    'height' => '200',
    'width' => 'full',
    'type' => 'block', // block, inline, text
    'class' => '',
])

@php
    $widthClass = $width === 'full' ? 'w-full' : "w-{$width}";
    $typeClasses = match ($type) {
        'text' => 'h-4 rounded',
        'inline' => 'inline-block rounded',
        default => 'rounded-lg',
    };
@endphp

<div {{ $attributes->merge(['class' => "content-placeholder bg-gray-200 dark:bg-gray-700 skeleton-pulse {$widthClass} {$typeClasses} {$class}"]) }}
    style="min-height: {{ $height }}px; contain: layout style paint;" aria-hidden="true" role="presentation">
    {{-- Slot for custom placeholder content --}}
    @if(isset($slot) && !$slot->isEmpty())
        {{ $slot }}
    @endif
</div>

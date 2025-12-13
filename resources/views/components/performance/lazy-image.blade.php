{{--
/**
 * Lazy Image Component
 *
 * Optimized image component for LCP and CLS optimization.
 *
 * @component performance.lazy-image
 * @trace Requirements: 10.1 (LCP), 10.3 (CLS)
 * @see D12 §9 Performance optimization patterns
 */
--}}
@props([
    'src',
    'alt',
    'width' => null,
    'height' => null,
    'loading' => 'lazy',
    'decoding' => 'async',
    'class' => '',
    'priority' => false,
    'placeholder' => null,
])

@php
    $aspectRatioStyle = $width && $height ? "aspect-ratio: {$width} / {$height};" : '';
    $placeholderSrc =
        $placeholder ??
        'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"%3E%3Crect fill="%23f3f4f6" width="1" height="1"/%3E%3C/svg%3E';
    $loadingAttr = $priority ? 'eager' : $loading;
    $fetchPriority = $priority ? 'high' : 'auto';
@endphp

<div class="lazy-image-wrapper relative overflow-hidden {{ $class }}"
    style="{{ $aspectRatioStyle }} min-height: 1px;">
    {{-- Placeholder for CLS prevention --}}
    @if (!$priority)
        <div class="lazy-image-placeholder absolute inset-0 bg-gray-100 skeleton-pulse" aria-hidden="true"></div>
    @endif

    {{-- Actual image --}}
    <img src="{{ $src }}" alt="{{ $alt }}"
        @if ($width) width="{{ $width }}" @endif
        @if ($height) height="{{ $height }}" @endif loading="{{ $loadingAttr }}"
        decoding="{{ $decoding }}" fetchpriority="{{ $fetchPriority }}"
        class="lazy-image w-full h-full object-cover transition-opacity duration-300" style="{{ $aspectRatioStyle }}"
        onload="this.parentElement.querySelector('.lazy-image-placeholder')?.remove(); this.classList.add('loaded');"
        onerror="this.parentElement.querySelector('.lazy-image-placeholder')?.classList.add('bg-gray-200');">
</div>

<style>
    .lazy-image:not(.loaded) {
        opacity: 0;
    }

    .lazy-image.loaded {
        opacity: 1;
    }
</style>

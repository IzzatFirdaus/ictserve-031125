{{--
/**
 * Responsive Image Component with WebP Support
 *
 * Optimized image component with:
 * - WebP format with JPEG/PNG fallbacks
 * - Explicit width/height for CLS prevention
 * - Lazy loading for below-the-fold images
 * - Priority loading for LCP candidates
 * - Responsive srcset for different viewport sizes
 *
 * @component performance.responsive-image
 * @trace Requirements: 7.5 (Image optimization), 10.1 (LCP), 10.3 (CLS)
 * @see D12 §9 Performance optimization patterns
 * @see D14 §8.2 Image guidelines
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
    'sizes' => '100vw',
    'srcset' => null,
    'webpSrc' => null,
    'placeholder' => null,
])

@php
    // Determine loading strategy
    $loadingAttr = $priority ? 'eager' : $loading;
    $fetchPriority = $priority ? 'high' : 'auto';

    // Calculate aspect ratio for CLS prevention
    $aspectRatioStyle = $width && $height ? "aspect-ratio: {$width} / {$height};" : '';

    // Generate WebP source if not provided (assumes same path with .webp extension)
    $webpSource = $webpSrc;
    if (!$webpSource && $src) {
        $pathInfo = pathinfo($src);
        $webpSource = ($pathInfo['dirname'] ?? '') . '/' . ($pathInfo['filename'] ?? '') . '.webp';
    }

    // Default placeholder for CLS prevention
    $placeholderSrc =
        $placeholder ??
        'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' .
            ($width ?? 1) .
            ' ' .
            ($height ?? 1) .
            '"%3E%3Crect fill="%23f3f4f6" width="100%25" height="100%25"/%3E%3C/svg%3E';
@endphp

<div class="responsive-image-wrapper relative overflow-hidden {{ $class }}"
    style="{{ $aspectRatioStyle }} min-height: 1px;" role="img" aria-label="{{ $alt }}">
    {{-- Placeholder for CLS prevention (only for lazy-loaded images) --}}
    @if (!$priority)
        <div class="responsive-image-placeholder absolute inset-0 bg-gray-100 skeleton-pulse" aria-hidden="true"></div>
    @endif

    {{-- Picture element with WebP support and fallback --}}
    <picture>
        {{-- WebP source (modern browsers) --}}
        @if ($webpSource)
            <source type="image/webp" srcset="{{ $webpSource }}"
                @if ($sizes) sizes="{{ $sizes }}" @endif>
        @endif

        {{-- Original format fallback --}}
        <img src="{{ $src }}" alt="{{ $alt }}"
            @if ($width) width="{{ $width }}" @endif
            @if ($height) height="{{ $height }}" @endif loading="{{ $loadingAttr }}"
            decoding="{{ $decoding }}" fetchpriority="{{ $fetchPriority }}"
            @if ($srcset) srcset="{{ $srcset }}" @endif
            @if ($sizes) sizes="{{ $sizes }}" @endif
            class="responsive-image w-full h-full object-cover transition-opacity duration-300"
            style="{{ $aspectRatioStyle }}"
            onload="this.closest('.responsive-image-wrapper').querySelector('.responsive-image-placeholder')?.remove(); this.classList.add('loaded');"
            onerror="this.closest('.responsive-image-wrapper').querySelector('.responsive-image-placeholder')?.classList.add('bg-gray-200');">
    </picture>
</div>

@once
    <style>
        /* Responsive image loading states */
        .responsive-image:not(.loaded) {
            opacity: 0;
        }

        .responsive-image.loaded {
            opacity: 1;
        }

        /* Respect reduced motion preference */
        @media (prefers-reduced-motion: reduce) {
            .responsive-image {
                transition: none;
            }
        }
    </style>
@endonce

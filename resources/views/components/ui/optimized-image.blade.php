{{--
    Optimized Image Component v3.6.0

    Performance-optimized image component with:
    - Lazy loading for below-the-fold images
    - WebP format with JPEG fallback
    - Explicit dimensions to prevent CLS
    - Blur placeholder for loading state
    - WCAG 2.2 AA compliant alt text

    @props
    - src: string - Image source URL (required)
    - alt: string - Alt text for accessibility (required)
    - width: int|null - Image width in pixels
    - height: int|null - Image height in pixels
    - critical: bool - Whether image is above the fold (default: false)
    - class: string - Additional CSS classes
    - aspectRatio: string - Aspect ratio (e.g., '16/9', '4/3', '1/1')

    @see D12 §9 Performance optimization patterns
    @see D13 §6 Performance monitoring
    @see Requirements 13.1, 13.4 - Core Web Vitals optimization

    @example
    <x-ui.optimized-image
        src="/images/hero.jpg"
        alt="Imej utama"
        :width="800"
        :height="450"
        :critical="true"
    />

    @version 3.6.0
    @author Frontend Engineering Team
--}}

@props(['src', 'alt', 'width' => null, 'height' => null, 'critical' => false, 'aspectRatio' => null])

@php
    use App\Services\PerformanceOptimizationService;

    $performanceService = app(PerformanceOptimizationService::class);
    $imageAttrs = $performanceService->getImageAttributes($src, $alt, $width, $height, $critical);

    // Generate WebP source if applicable
    $sources = $performanceService->getOptimizedImageSources($src);

    // Build aspect ratio style
    $aspectStyle = $aspectRatio ? "aspect-ratio: {$aspectRatio};" : '';
@endphp

<picture>
    {{-- WebP source for modern browsers --}}
    <source srcset="{{ $sources['webp'] }}" type="image/webp">

    {{-- Fallback image with all optimizations --}}
    <img src="{{ $imageAttrs['src'] }}" alt="{{ $imageAttrs['alt'] }}"
        @if ($width) width="{{ $width }}" @endif
        @if ($height) height="{{ $height }}" @endif loading="{{ $imageAttrs['loading'] }}"
        decoding="{{ $imageAttrs['decoding'] }}" fetchpriority="{{ $imageAttrs['fetchpriority'] }}"
        @if ($aspectStyle) style="{{ $aspectStyle }}" @endif
        {{ $attributes->merge([
            'class' => 'max-w-full h-auto object-cover ' . ($critical ? '' : 'transition-opacity duration-300'),
        ]) }}>
</picture>

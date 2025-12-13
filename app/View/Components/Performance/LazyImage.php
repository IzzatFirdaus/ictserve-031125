<?php

declare(strict_types=1);

namespace App\View\Components\Performance;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Lazy Image Component
 *
 * Optimized image component for LCP and CLS optimization.
 * Features:
 * - Native lazy loading
 * - Aspect ratio preservation (CLS prevention)
 * - WebP fallback support
 * - Blur placeholder
 *
 * @trace Requirements: 10.1 (LCP), 10.3 (CLS)
 *
 * @see D12 §9 Performance optimization patterns
 */
class LazyImage extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $src,
        public string $alt,
        public ?int $width = null,
        public ?int $height = null,
        public string $loading = 'lazy',
        public string $decoding = 'async',
        public ?string $class = null,
        public bool $priority = false,
        public ?string $placeholder = null,
    ) {
        // Priority images should load eagerly
        if ($this->priority) {
            $this->loading = 'eager';
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.performance.lazy-image');
    }

    /**
     * Get aspect ratio style for CLS prevention
     */
    public function getAspectRatioStyle(): string
    {
        if ($this->width && $this->height) {
            return "aspect-ratio: {$this->width} / {$this->height};";
        }

        return '';
    }

    /**
     * Get placeholder data URI for blur effect
     */
    public function getPlaceholder(): string
    {
        if ($this->placeholder) {
            return $this->placeholder;
        }

        // Default gray pceholder
        return 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"%3E%3Crect fill="%23f3f4f6" width="1" height="1"/%3E%3C/svg%3E';
    }
}

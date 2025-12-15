{{--
    Accessible Loading Indicator Component
    WCAG 2.2 AA Compliant (Req 5.7)

    Usage:
    <x-ollama.loading-indicator />
    <x-ollama.loading-indicator :show="$isLoading" text="Sedang memproses..." />
--}}
@props([
    'show' => true,
    'text' => 'Sedang memproses...',
    'size' => 'md', // sm, md, lg
    'variant' => 'default', // default, inline, overlay
])

@php
    $sizeClasses = match ($size) {
        'sm' => 'w-4 h-4',
        'lg' => 'w-8 h-8',
        default => 'w-6 h-6',
    };

    $containerClasses = match ($variant) {
        'inline' => 'inline-flex items-center gap-2',
        'overlay' => 'fixed inset-0 z-50 flex items-center justify-center bg-black/50',
        default => 'flex items-center gap-3 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg',
    };
@endphp

@if ($show)
    <div {{ $attributes->merge(['class' => $containerClasses]) }} role="status" aria-live="polite" aria-busy="true"
        aria-label="{{ $text }}">
        {{-- Spinner (hidden from screen readers) --}}
        <svg class="animate-spin {{ $sizeClasses }} text-primary-600 dark:text-primary-400"
            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>

        {{-- Visible text --}}
        <span class="text-sm text-gray-600 dark:text-gray-300">
            {{ $text }}
        </span>

        {{-- Screen reader announcement --}}
        <span class="sr-only">
            {{ $text }} Sila tunggu.
        </span>
    </div>
@endif

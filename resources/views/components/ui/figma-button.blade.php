{{--
    Figma Component: Button/Primary
    Figma URL: https://figma.com/design/abc123def/ICTServe-Components?node-id=123-456
    Last synced: 2025-12-11

    ICTServe Button Component - Generated from Figma Design
    Supports: WCAG 2.2 AA, Bahasa Melayu, Hybrid Architecture
--}}

@props([
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'wire:click' => null,
    'type' => 'button'
])

@php
$classes = [
    'inline-flex items-center justify-center font-medium transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed',

    // Size variants
    'text-sm px-4 py-2 min-h-9' => $size === 'sm',
    'text-base px-6 py-3 min-h-11' => $size === 'md', // 44px touch target (WCAG)
    'text-lg px-8 py-4 min-h-12' => $size === 'lg',

    // Variant styles using ICTServe design tokens
    'bg-primary-600 hover:bg-primary-700 text-white focus-visible:ring-primary-500 rounded-lg shadow-button' => $variant === 'primary',
    'bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 focus-visible:ring-primary-500 rounded-lg shadow-sm' => $variant === 'secondary',
    'bg-success-600 hover:bg-success-700 text-white focus-visible:ring-success-500 rounded-lg shadow-button' => $variant === 'success',
    'bg-danger-600 hover:bg-danger-700 text-white focus-visible:ring-danger-500 rounded-lg shadow-button' => $variant === 'danger',
];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->class($classes) }}
    @if($disabled) disabled @endif
    @if($attributes->get('wire:click')) wire:click="{{ $attributes->get('wire:click') }}" @endif
>
    {{ $slot }}
</button>

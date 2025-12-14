{{--
/**
 * Component: Screen Reader Text
 * Description: WCAG 2.2 AA compliant visually hidden text for screen readers
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-006.3 (Screen Reader Support)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §10.3 (Accessibility Standards)
 * @wcag WCAG 2.2 Level AA (SC 1.3.1 Info and Relationships, SC 4.1.2 Name, Role, Value)
 * @version 1.0.0
 * @created 2025-12-14
 *
 * Purpose:
 * - Provides text content that is hidden visually but accessible to screen readers
 * - Used for additional context, instructions, or labels that sighted users don't need
 * - Maintains accessibility without cluttering the visual interface
 *
 * Usage:
 * <x-accessibility.screen-reader-text>
 *     Additional context for screen reader users
 * </x-accessibility.screen-reader-text>
 *
 * @example
 * <button>
 *     <svg>...</svg>
 *     <x-accessibility.screen-reader-text>{{ __('Tutup modal') }}</x-accessibility.screen-reader-text>
 * </button>
 *
 * @example
 * <x-accessibility.screen-reader-text tag="h2">
 *     {{ __('Bahagian navigasi utama') }}
 * </x-accessibility.screen-reader-text>
 */
--}}

@props([
    'tag' => 'span',
    'focusable' => false,
])

@php
    // Base classes for visually hidden but screen reader accessible content
    // Uses the sr-only pattern which is widely supported
    $baseClasses = 'sr-only';

    // If focusable, allow the element to become visible when focused
    // Useful for skip links and other keyboard-accessible hidden content
    if ($focusable) {
        $baseClasses .= ' focus:not-sr-only focus:absolute focus:z-50';
    }
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $baseClasses]) }}>
    {{ $slot }}
    </{{ $tag }}>

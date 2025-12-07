{{--
/**
 * Typography Component
 *
 * Implements MyDS typography scale per D13 §2.4.
 *
 * Features:
 * - Poppins for headings, Inter for body per D13 §2.4
 * - Heading sizes: H1 36px, H2 30px, H3 24px, H4 20px per D13 §2.4.2
 * - Body sizes: Large 18px, Medium 16px, Small 14px per D13 §2.4.3
 * - Fluid typography scaling for responsive design
 *
 * @component
 * @name Typography
 * @description MyDS typography scale component
 * @author Pasukan BPM MOTAC
 * @version 1.0.0
 * @since 2025-12-05
 *
 * Requirements Traceability: D13 §2.4, D14 §5.2
 * WCAG Level: AA (SC 1.4.4)
 *
 * Usage:
 * <x-layout.typography as="h1">Page Title</x-layout.typography>
 * <x-layout.typography as="h2" class="mb-4">Section Title</x-layout.typography>
 * <x-layout.typography as="p" size="lg">Large body text</x-layout.typography>
 */
--}}

@props([
    'as' => 'p',
    'size' => null,
    'weight' => null,
    'color' => null,
])

@php
    // Typography scale per D13 §2.4
    $headingSizes = [
        'h1' => 'text-4xl leading-tight', // 36px, 44px line-height
        'h2' => 'text-3xl leading-snug', // 30px, 38px line-height
        'h3' => 'text-2xl leading-snug', // 24px, 32px line-height
        'h4' => 'text-xl leading-normal', // 20px, 28px line-height
        'h5' => 'text-base leading-normal', // 16px, 24px line-height
        'h6' => 'text-sm leading-normal', // 14px, 20px line-height
    ];

    $bodySizes = [
        'xl' => 'text-xl leading-relaxed', // 20px
        'lg' => 'text-lg leading-relaxed', // 18px
        'base' => 'text-base leading-normal', // 16px
        'sm' => 'text-sm leading-normal', // 14px
        'xs' => 'text-xs leading-normal', // 12px
    ];

    // Determine if heading or body
    $isHeading = in_array($as, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']);

    // Get size classes
    if ($size) {
        $sizeClasses = $bodySizes[$size] ?? $bodySizes['base'];
    } elseif ($isHeading) {
        $sizeClasses = $headingSizes[$as] ?? $headingSizes['h4'];
    } else {
        $sizeClasses = $bodySizes['base'];
    }

    // Font family per D13 §2.4
    // Poppins for headings, Inter for body
    $fontClasses = $isHeading ? 'font-heading' : 'font-sans';

    // Font weight
    $weightClasses = match ($weight) {
        'thin' => 'font-thin',
        'light' => 'font-light',
        'normal' => 'font-normal',
        'medium' => 'font-medium',
        'semibold' => 'font-semibold',
        'bold' => 'font-bold',
        'extrabold' => 'font-extrabold',
        default => $isHeading ? 'font-semibold' : 'font-normal',
    };

    // Color
    $colorClasses = match ($color) {
        'primary' => 'text-primary-600 dark:text-primary-400',
        'secondary' => 'text-gray-600 dark:text-gray-400',
        'muted' => 'text-gray-500 dark:text-gray-500',
        'success' => 'text-success-600 dark:text-success-400',
        'warning' => 'text-warning-600 dark:text-warning-400',
        'danger' => 'text-danger-600 dark:text-danger-400',
        'white' => 'text-white',
        default => $isHeading ? 'text-gray-900 dark:text-gray-100' : 'text-gray-700 dark:text-gray-300',
    };

    $classes = trim("{$fontClasses} {$sizeClasses} {$weightClasses} {$colorClasses}");
@endphp

<{{ $as }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
    </{{ $as }}>

{{--
/**
 * 12-8-4 Responsive Grid Component
 *
 * Implements the MyDS responsive grid system per D14 §7.4.
 *
 * Features:
 * - Desktop (≥1024px): 12 columns, 24px gap, 24px edge padding, max-width 1280px
 * - Tablet (768-1023px): 8 columns, 24px gap, 24px edge padding
 * - Mobile (≤767px): 4 columns, 18px gap, 18px edge padding
 *
 * @component
 * @name Responsive Grid
 * @description 12-8-4 responsive grid system per MyDS
 * @author Pasukan BPM MOTAC
 * @version 1.0.0
 * @since 2025-12-05
 *
 * Requirements Traceability: D12 §6.8, D13 §4.3, D14 §7.4
 * WCAG Level: AA
 *
 * Usage:
 * <x-layout.responsive-grid>
 *   <div class="col-span-4 lg:col-span-6">Content</div>
 *   <div class="col-span-4 lg:col-span-6">Content</div>
 * </x-layout.responsive-grid>
 *
 * Or with explicit columns:
 * <x-layout.responsive-grid :cols="['default' => 1, 'sm' => 2, 'lg' => 3]">
 *   ...
 * </x-layout.responsive-grid>
 */
--}}

@props([
    'cols' => null,
    'gap' => null,
    'maxWidth' => '7xl',
    'padding' => true,
    'centered' => true,
])

@php
    // Default 12-8-4 grid per D14 §7.4
    $defaultCols = 'grid-cols-4 md:grid-cols-8 lg:grid-cols-12';

    // Custom column configuration
    if ($cols) {
        $colClasses = collect($cols)
            ->map(function ($value, $breakpoint) {
                $prefix = $breakpoint === 'default' ? '' : $breakpoint . ':';
                return "{$prefix}grid-cols-{$value}";
            })
            ->implode(' ');
    } else {
        $colClasses = $defaultCols;
    }

    // Gap configuration per D14 §7.4
    // Mobile: 18px (gap-4.5 ≈ gap-4), Tablet/Desktop: 24px (gap-6)
    $gapClasses = $gap ?? 'gap-4 md:gap-6';

    // Edge padding per D14 §7.4
    // Mobile: 18px (px-4.5 ≈ px-4), Tablet/Desktop: 24px (px-6)
    $paddingClasses = $padding ? 'px-4 md:px-6' : '';

    // Max width per D14 §7.4 (1280px = max-w-7xl)
    $maxWidthClasses = match ($maxWidth) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
        'full' => 'max-w-full',
        'none' => '',
        default => 'max-w-7xl',
    };

    // Centering
    $centerClasses = $centered ? 'mx-auto' : '';

    // Combine all classes
    $containerClasses = trim("{$maxWidthClasses} {$centerClasses} {$paddingClasses}");
    $gridClasses = trim("grid {$colClasses} {$gapClasses}");
@endphp

<div {{ $attributes->merge(['class' => $containerClasses]) }}>
    <div class="{{ $gridClasses }}">
        {{ $slot }}
    </div>
</div>

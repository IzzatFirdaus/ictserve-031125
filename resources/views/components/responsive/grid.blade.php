{{--
/**
 * Component: Responsive Grid
 * Description: MyDS Design System compliant responsive grid with 12-8-4 column system
 * @author Pasukan BPM MOTAC
 * @trace D13 §2.6 (MyDS Spacing System)
 * @trace D14 §5.1 (Grid System)
 * @trace D12 §6.8 (Responsive Design)
 * @wcag WCAG 2.2 Level AA (SC 1.4.10 Reflow)
 * @version 1.0.0
 * @created 2025-12-14
 *
 * MyDS Grid System:
 * - Desktop (≥1280px): 12 columns
 * - Tablet (768px-1279px): 8 columns
 * - Mobile (<768px): 4 columns
 *
 * Usage:
 * <x-responsive.grid cols="3" gap="4">
 *     <div>Item 1</div>
 *     <div>Item 2</div>
 *     <div>Item 3</div>
 * </x-responsive.grid>
 *
 * @example Responsive columns
 * <x-responsive.grid cols="1" md="2" lg="3" xl="4" gap="6">
 *     @foreach ($items as $item)
 *         <x-ui.card>{{ $item->name }}</x-ui.card>
 *     @endforeach
 * </x-responsive.grid>
 */
--}}

@props([
    'cols' => '1', // Default columns (mobile)
    'sm' => null, // Small breakpoint (640px+)
    'md' => null, // Medium breakpoint (768px+)
    'lg' => null, // Large breakpoint (1024px+)
    'xl' => null, // Extra large breakpoint (1280px+)
    '2xl' => null, // 2XL breakpoint (1536px+)
    'gap' => '4', // Gap between items (uses Tailwind spacing scale)
    'gapX' => null, // Horizontal gap (overrides gap)
    'gapY' => null, // Vertical gap (overrides gap)
])

@php
    // Build responsive column classes
    $colClasses = "grid-cols-{$cols}";

    if ($sm) {
        $colClasses .= " sm:grid-cols-{$sm}";
    }
    if ($md) {
        $colClasses .= " md:grid-cols-{$md}";
    }
    if ($lg) {
        $colClasses .= " lg:grid-cols-{$lg}";
    }
    if ($xl) {
        $colClasses .= " xl:grid-cols-{$xl}";
    }
    if ($attributes->get('2xl')) {
        $colClasses .= " 2xl:grid-cols-{$attributes->get('2xl')}";
    }

    // Build gap classes
    $gapClasses = '';
    if ($gapX && $gapY) {
        $gapClasses = "gap-x-{$gapX} gap-y-{$gapY}";
    } elseif ($gapX) {
        $gapClasses = "gap-x-{$gapX} gap-y-{$gap}";
    } elseif ($gapY) {
        $gapClasses = "gap-x-{$gap} gap-y-{$gapY}";
    } else {
        $gapClasses = "gap-{$gap}";
    }
@endphp

<div {{ $attributes->except(['2xl'])->merge(['class' => "grid {$colClasses} {$gapClasses}"]) }}>
    {{ $slot }}
</div>

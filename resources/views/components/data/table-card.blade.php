{{--
/**
 * Table Card Component (Mobile View)
 *
 * Card representation of a table row for mobile viewports (<768px) per D12 §6.14.
 * Transforms table data into a stacked card layout for better mobile UX.
 *
 * Features:
 * - Stacked card layout per D12 §6.14
 * - shadow-card styling per D14 §7.5
 * - Touch-friendly 44×44px action targets per D12 §4.1
 * - Semantic structure with role="listitem"
 *
 * @component
 * @name Table Card
 * @description Mobile card view for table rows
 * @author Pasukan BPM MOTAC
 * @version 1.0.0
 * @since 2025-12-05
 *
 * Requirements Traceability: D12 §6.8, D12 §6.14, D14 §7.4, D14 §7.5
 * WCAG Level: AA (SC 1.3.1, 2.5.5)
 *
 * Usage:
 * <x-data.table-card>
 *   <x-slot:header>
 *     <span class="font-semibold">John Doe</span>
 *     <x-ui.badge type="success">Active</x-ui.badge>
 *   </x-slot:header>
 *   <x-slot:content>
 *     <div class="grid grid-cols-2 gap-2">
 *       <div><span class="text-gray-500">Email:</span> john@example.com</div>
 *     </div>
 *   </x-slot:content>
 *   <x-slot:actions>
 *     <button>Edit</button>
 *   </x-slot:actions>
 * </x-data.table-card>
 */
--}}

@props([
    'href' => null,
])

@php
    $cardClasses = 'bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4';
    // shadow-card per D14 §7.5
    $cardClasses .= ' shadow-[0_1px_3px_0_rgb(0_0_0/0.1),0_1px_2px_-1px_rgb(0_0_0/0.1)]';
@endphp

<div {{ $attributes->merge(['class' => $cardClasses]) }} role="listitem">
    {{-- Card Header --}}
    @if (isset($header))
        <div class="flex items-center justify-between gap-3 mb-3">
            {{ $header }}
        </div>
    @endif

    {{-- Card Content --}}
    @if (isset($content))
        <div class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
            {{ $content }}
        </div>
    @else
        <div class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
            {{ $slot }}
        </div>
    @endif

    {{-- Card Actions - 44×44px touch targets per D12 §4.1 --}}
    @if (isset($actions))
        <div class="flex items-center justify-end gap-2 mt-4 pt-3 border-t border-gray-100 dark:border-gray-700">
            {{ $actions }}
        </div>
    @endif
</div>

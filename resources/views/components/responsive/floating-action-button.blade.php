{{--
/**
 * Component: Floating Action Button (FAB)
 * Description: WCAG 2.2 AA compliant floating action button for primary mobile actions
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-015.5 (Mobile FAB)
 * @trace D12 §6.11 (Touch Interactions)
 * @trace D14 §10.5 (ARIA Attributes)
 * @trace D15 §3.1 (Mobile Optimization)
 * @wcag WCAG 2.2 Level AA (SC 2.5.8 Target Size, SC 4.1.2 Name, Role, Value)
 * @version 1.0.0
 * @created 2025-12-14
 *
 * Features:
 * - 56×56px touch target (exceeds 44×44px minimum)
 * - Fixed position with safe area support
 * - Optional speed dial menu
 * - Keyboard accessible
 * - Reduced motion support
 * - Hidden on desktop by default
 *
 * Usage:
 * <x-responsive.floating-action-button 
 *     href="/helpdesk/create"
 *     label="Hantar Tiket Baharu"
 *     icon="plus" />
 *
 * With speed dial:
 * <x-responsive.floating-action-button 
 *     :actions="[
 *         ['label' => 'Tiket Baharu', 'href' => '/helpdesk/create', 'icon' => 'ticket'],
 *         ['label' => 'Pinjaman Baharu', 'href' => '/loans/create', 'icon' => 'clipboard'],
 *     ]" />
 */
--}}

@props([
    'href' => null,
    'label' => __('Tindakan utama'),
    'icon' => 'plus',
    'color' => 'primary',
    'position' => 'bottom-right',
    'actions' => [],
    'showOnDesktop' => false,
])

@php
    $positionClasses = match ($position) {
        'bottom-left' => 'bottom-20 left-4',
        'bottom-center' => 'bottom-20 left-1/2 -translate-x-1/2',
        default => 'bottom-20 right-4',
    };

    $colorClasses = match ($color) {
        'secondary' => 'bg-secondary-600 hover:bg-secondary-700 focus-visible:ring-secondary-500 text-white',
        'success' => 'bg-success-600 hover:bg-success-700 focus-visible:ring-3 focus-visible:ring-success-500 text-white',
        'warning' => 'bg-warning-600 hover:bg-warning-700 focus-visible:ring-3 focus-visible:ring-warning-500 text-white',
        'danger' => 'bg-danger-600 hover:bg-danger-700 focus-visible:ring-3 focus-visible:ring-danger-500 text-white',
        default => 'bg-primary-600 hover:bg-primary-700 focus-visible:ring-3 focus-visible:ring-primary-500 text-white',
    };

    $visibilityClass = $showOnDesktop ? '' : 'md:hidden';
    $hasSpeedDial = count($actions) > 0;
@endphp

@if ($hasSpeedDial)
    {{-- FAB with Speed Dial --}}
    <div x-data="{ open: false }" class="fixed {{ $positionClasses }} z-50 {{ $visibilityClass }}"
        style="bottom: calc(5rem + env(safe-area-inset-bottom, 0px));">

        {{-- Speed Dial Actions --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute bottom-16 right-0 flex flex-col-reverse items-end gap-3 mb-2" x-cloak>
            @foreach ($actions as $action)
                <a href="{{ $action['href'] ?? '#' }}" class="flex items-center gap-3 group" @click="open = false">
                    <span
                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white rounded-lg shadow-lg dark:bg-gray-700 dark:text-gray-200 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        {{ $action['label'] ?? '' }}
                    </span>
                    <span
                        class="flex items-center justify-center w-12 h-12 rounded-full shadow-lg {{ $colorClasses }} transition-transform duration-200 hover:scale-110">
                        @switch($action['icon'] ?? 'plus')
                            @case('ticket')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                            @break

                            @case('clipboard')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            @break

                            @default
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                        @endswitch
                    </span>
                </a>
            @endforeach
        </div>

        {{-- Main FAB Button --}}
        <button type="button" @click="open = !open" :aria-expanded="open"
            class="flex items-center justify-center w-14 h-14 rounded-full shadow-lg {{ $colorClasses }} focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 transition-all duration-200 motion-safe:hover:scale-110"
            aria-label="{{ $label }}">
            <svg x-show="!open" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <svg x-show="open" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@else
    {{-- Simple FAB --}}
    <a href="{{ $href ?? '#' }}"
        {{ $attributes->merge([
            'class' => "fixed {$positionClasses} z-50 flex items-center justify-center w-14 h-14 rounded-full shadow-lg {$colorClasses} focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 transition-all duration-200 motion-safe:hover:scale-110 {$visibilityClass}",
            'style' => 'bottom: calc(5rem + env(safe-area-inset-bottom, 0px));',
        ]) }}
        aria-label="{{ $label }}">
        @switch($icon)
            @case('ticket')
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
            @break

            @case('clipboard')
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            @break

            @default
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
        @endswitch
    </a>
@endif

{{-- Reduced motion support --}}
<style>
    @media (prefers-reduced-motion: reduce) {
        [class*="motion-safe"] {
            transform: none !important;
            transition: none !important;
        }
    }
</style>

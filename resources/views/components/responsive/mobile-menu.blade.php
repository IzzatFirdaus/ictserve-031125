{{--
/**
 * Component: Mobile Menu
 * Description: WCAG 2.2 AA compliant mobile navigation menu with hamburger toggle
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-006.2 (Keyboard Navigation)
 * @trace D12 §6.11 (Keyboard Navigation)
 * @trace D14 §10.5 (ARIA Attributes)
 * @trace D15 §3.1 (Mobile Optimization)
 * @wcag WCAG 2.2 Level AA (SC 2.4.1 Bypass Blocks, SC 4.1.2 Name, Role, Value)
 * @version 1.0.0
 * @created 2025-12-14
 *
 * Features:
 * - Hamburger menu toggle with 44×44px touch target
 * - Slide-in animation with reduced motion support
 * - Focus trap when open
 * - Escape key to close
 * - ARIA attributes for accessibility
 *
 * Usage:
 * <x-responsive.mobile-menu>
 *     <x-slot name="trigger">
 *         <span class="sr-only">Buka menu</span>
 *         <svg>...</svg>
 *     </x-slot>
 *     
 *     <nav>
 *         <a href="/">Laman Utama</a>
 *         <a href="/helpdesk">Helpdesk</a>
 *     </nav>
 * </x-responsive.mobile-menu>
 */
--}}

@props([
    'id' => 'mobile-menu-' . uniqid(),
    'position' => 'left', // left or right
    'width' => 'w-80',
])

@php
    $slideDirection = $position === 'left' ? '-translate-x-full' : 'translate-x-full';
    $positionClass = $position === 'left' ? 'left-0' : 'right-0';
@endphp

<div x-data="{
    open: false,
    previousFocus: null,

    toggle() {
        this.open = !this.open;
    },

    openMenu() {
        this.previousFocus = document.activeElement;
        this.open = true;
        this.$nextTick(() => {
                    const firstFocusable = this.$refs.menu.querySelector('a, button, input, [tabindex]:not([tabindex=\"-1\"])'); if (firstFocusable) firstFocusable.focus(); }); }, closeMenu() { this.open=false;
    this.$nextTick(()=> {
    if (this.previousFocus) this.previousFocus.focus();
    });
    }
    }"
    @keydown.escape.window="if (open) closeMenu()"
    {{ $attributes }}
    >
    {{-- Hamburger Toggle Button --}}
    <button type="button" @click="toggle()" :aria-expanded="open" aria-controls="{{ $id }}" aria-haspopup="true"
        class="inline-flex items-center justify-center min-h-11 min-w-11 p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-800 dark:focus-visible:ring-offset-gray-900 transition-colors duration-200 md:hidden">
        {{ $trigger ?? '' }}

        @if (!isset($trigger))
            <span class="sr-only" x-text="open ? '{{ __('Tutup menu') }}' : '{{ __('Buka menu') }}'"></span>
            {{-- Hamburger icon --}}
            <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            {{-- Close icon --}}
            <svg x-show="open" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        @endif
    </button>

    {{-- Backdrop --}}
    <div x-show="open" x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="closeMenu()"
        class="fixed inset-0 z-40 bg-gray-600/75 dark:bg-gray-900/75 md:hidden" aria-hidden="true" x-cloak></div>

    {{-- Slide-out Menu Panel --}}
    <div x-ref="menu" x-show="open" x-trap.noscroll.inert="open"
        x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="{{ $slideDirection }}"
        x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="{{ $slideDirection }}" id="{{ $id }}"
        class="fixed inset-y-0 {{ $positionClass }} z-50 {{ $width }} max-w-full bg-white dark:bg-gray-800 shadow-xl overflow-y-auto md:hidden"
        role="dialog" aria-modal="true" aria-label="{{ __('Menu navigasi') }}" x-cloak>
        {{-- Close button inside menu --}}
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <span class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ __('Menu') }}
            </span>
            <button type="button" @click="closeMenu()"
                class="inline-flex items-center justify-center min-h-11 min-w-11 p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 transition-colors duration-200"
                aria-label="{{ __('Tutup menu') }}">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Menu Content --}}
        <div class="p-4">
            {{ $slot }}
        </div>
    </div>
</div>

{{-- Reduced motion support --}}
<style>
    @media (prefers-reduced-motion: reduce) {

        [x-transition\:enter],
        [x-transition\:leave] {
            transition-duration: 0.01ms !important;
        }
    }
</style>

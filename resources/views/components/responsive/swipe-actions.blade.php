{{--
/**
 * Component: Swipe Actions
 * Description: WCAG 2.2 AA compliant swipe gesture actions for list items
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-015.3 (Touch Interactions)
 * @trace D12 §6.11 (Touch Interactions)
 * @trace D14 §10.5 (ARIA Attributes)
 * @trace D15 §3.1 (Mobile Optimization)
 * @wcag WCAG 2.2 Level AA (SC 2.5.1 Pointer Gestures, SC 2.1.1 Keyboard)
 * @version 1.0.0
 * @created 2025-12-14
 *
 * Features:
 * - Left/right swipe gesture detection
 * - Reveal hidden action buttons
 * - Keyboard accessible alternatives
 * - Visual feedback during swipe
 * - Auto-close on action or outside tap
 * - Reduced motion support
 *
 * Usage:
 * <x-responsive.swipe-actions>
 *     <x-slot name="leftActions">
 *         <button class="bg-success-500 text-white px-4">Selesai</button>
 *     </x-slot>
 *     
 *     <div>List item content</div>
 *     
 *     <x-slot name="rightActions">
 *         <button class="bg-danger-500 text-white px-4">Padam</button>
 *     </x-slot>
 * </x-responsive.swipe-actions>
 */
--}}

@props([
    'threshold' => 50,
    'maxSwipe' => 100,
    'leftActionsWidth' => 80,
    'rightActionsWidth' => 80,
])

<div x-data="{
    startX: 0,
    currentX: 0,
    swiping: false,
    offset: 0,
    threshold: {{ $threshold }},
    maxSwipe: {{ $maxSwipe }},
    leftWidth: {{ $leftActionsWidth }},
    rightWidth: {{ $rightActionsWidth }},
    revealed: null,

    handleTouchStart(e) {
        this.startX = e.touches[0].clientX;
        this.swiping = true;
    },

    handleTouchMove(e) {
        if (!this.swiping) return;

        this.currentX = e.touches[0].clientX;
        const diff = this.currentX - this.startX;

        // Limit swipe distance
        if (diff > 0) {
            this.offset = Math.min(diff, this.leftWidth);
        } else {
            this.offset = Math.max(diff, -this.rightWidth);
        }
    },

    handleTouchEnd() {
        if (!this.swiping) return;

        // Determine if we should reveal actions
        if (this.offset > this.threshold) {
            this.offset = this.leftWidth;
            this.revealed = 'left';
        } else if (this.offset < -this.threshold) {
            this.offset = -this.rightWidth;
            this.revealed = 'right';
        } else {
            this.offset = 0;
            this.revealed = null;
        }

        this.swiping = false;
    },

    close() {
        this.offset = 0;
        this.revealed = null;
    },

    handleAction(callback) {
        if (typeof callback === 'function') {
            callback();
        }
        this.close();
    }
}" @click.outside="close()" {{ $attributes->merge(['class' => 'relative overflow-hidden']) }}>

    {{-- Left Actions (revealed on swipe right) --}}
    @if (isset($leftActions))
        <div class="absolute inset-y-0 left-0 flex items-stretch" :style="{ width: leftWidth + 'px' }"
            style="transform: translateX(-100%);">
            {{ $leftActions }}
        </div>
    @endif

    {{-- Right Actions (revealed on swipe left) --}}
    @if (isset($rightActions))
        <div class="absolute inset-y-0 right-0 flex items-stretch" :style="{ width: rightWidth + 'px' }"
            style="transform: translateX(100%);">
            {{ $rightActions }}
        </div>
    @endif

    {{-- Main Content --}}
    <div @touchstart.passive="handleTouchStart" @touchmove="handleTouchMove" @touchend="handleTouchEnd"
        :style="{ transform: 'translateX(' + offset + 'px)' }"
        class="relative bg-white dark:bg-gray-800 transition-transform duration-200 ease-out"
        :class="{ 'transition-none': swiping }">
        {{ $slot }}

        {{-- Keyboard accessible action menu --}}
        <div class="absolute top-2 right-2 md:hidden">
            <div x-data="{ menuOpen: false }" class="relative">
                <button type="button" @click="menuOpen = !menuOpen"
                    class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 rounded-lg"
                    aria-label="{{ __('Tindakan') }}" :aria-expanded="menuOpen">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                    </svg>
                </button>

                {{-- Dropdown menu for keyboard users --}}
                <div x-show="menuOpen" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95" @click.outside="menuOpen = false"
                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg ring-1 ring-black/5 z-10"
                    role="menu" x-cloak>
                    <div class="py-1">
                        {{ $menuActions ?? '' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reduced motion support --}}
<style>
    @media (prefers-reduced-motion: reduce) {
        [class*="transition"] {
            transition: none !important;
        }
    }
</style>


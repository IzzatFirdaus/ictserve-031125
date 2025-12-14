{{--
/**
 * Component: Pull to Refresh
 * Description: WCAG 2.2 AA compliant pull-to-refresh functionality for mobile
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-015.3 (Touch Interactions)
 * @trace D12 §6.11 (Touch Interactions)
 * @trace D14 §10.5 (ARIA Attributes)
 * @trace D15 §3.1 (Mobile Optimization)
 * @wcag WCAG 2.2 Level AA (SC 2.5.1 Pointer Gestures)
 * @version 1.0.0
 * @created 2025-12-14
 *
 * Features:
 * - Touch-based pull gesture detection
 * - Visual feedback during pull
 * - Loading spinner animation
 * - Livewire integration for refresh action
 * - Reduced motion support
 * - Screen reader announcements
 *
 * Usage:
 * <x-responsive.pull-to-refresh wire:refresh="refreshData">
 *     <div>Content to refresh</div>
 * </x-responsive.pull-to-refresh>
 */
--}}

@props([
    'threshold' => 80,
    'maxPull' => 120,
    'refreshText' => __('Tarik untuk muat semula'),
    'releaseText' => __('Lepaskan untuk muat semula'),
    'loadingText' => __('Memuat semula...'),
])

<div x-data="{
    startY: 0,
    currentY: 0,
    pulling: false,
    refreshing: false,
    pullDistance: 0,
    threshold: {{ $threshold }},
    maxPull: {{ $maxPull }},

    get canRefresh() {
        return this.pullDistance >= this.threshold;
    },

    get pullProgress() {
        return Math.min(this.pullDistance / this.threshold, 1);
    },

    get statusText() {
        if (this.refreshing) return '{{ $loadingText }}';
        if (this.canRefresh) return '{{ $releaseText }}';
        return '{{ $refreshText }}';
    },

    handleTouchStart(e) {
        if (window.scrollY === 0 && !this.refreshing) {
            this.startY = e.touches[0].clientY;
            this.pulling = true;
        }
    },

    handleTouchMove(e) {
        if (!this.pulling || this.refreshing) return;

        this.currentY = e.touches[0].clientY;
        const diff = this.currentY - this.startY;

        if (diff > 0 && window.scrollY === 0) {
            e.preventDefault();
            this.pullDistance = Math.min(diff * 0.5, this.maxPull);
        }
    },

    handleTouchEnd() {
        if (!this.pulling) return;

        if (this.canRefresh && !this.refreshing) {
            this.refreshing = true;
            this.pullDistance = 60;

            // Trigger Livewire refresh
            this.$wire.call('$refresh').then(() => {
                this.refreshing = false;
                this.pullDistance = 0;
            }).catch(() => {
                this.refreshing = false;
                this.pullDistance = 0;
            });
        } else {
            this.pullDistance = 0;
        }

        this.pulling = false;
    }
}" @touchstart.passive="handleTouchStart" @touchmove="handleTouchMove" @touchend="handleTouchEnd"
    {{ $attributes->merge(['class' => 'relative']) }}>

    {{-- Pull indicator --}}
    <div class="absolute inset-x-0 top-0 flex items-center justify-center overflow-hidden pointer-events-none md:hidden"
        :style="{ height: pullDistance + 'px', opacity: pullProgress }" role="status"
        :aria-live="refreshing ? 'polite' : 'off'">
        <div class="flex flex-col items-center gap-2 text-gray-500 dark:text-gray-400">
            {{-- Spinner/Arrow --}}
            <div class="relative w-8 h-8">
                {{-- Loading spinner --}}
                <svg x-show="refreshing" class="w-8 h-8 animate-spin text-primary-500" fill="none" viewBox="0 0 24 24"
                    aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>

                {{-- Pull arrow --}}
                <svg x-show="!refreshing" class="w-8 h-8 transition-transform duration-200"
                    :class="{ 'rotate-180': canRefresh }" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
            </div>

            {{-- Status text --}}
            <span class="text-sm font-medium" x-text="statusText"></span>
        </div>
    </div>

    {{-- Content --}}
    <div :style="{ transform: 'translateY(' + pullDistance + 'px)' }" class="transition-transform duration-200 ease-out"
        :class="{ 'transition-none': pulling }">
        {{ $slot }}
    </div>

    {{-- Screen reader announcement --}}
    <div class="sr-only" aria-live="polite" aria-atomic="true">
        <span x-show="refreshing">{{ $loadingText }}</span>
    </div>
</div>

{{-- Reduced motion support --}}
<style>
    @media (prefers-reduced-motion: reduce) {
        .animate-spin {
            animation: none !important;
        }

        [class*="transition"] {
            transition: none !important;
        }
    }
</style>

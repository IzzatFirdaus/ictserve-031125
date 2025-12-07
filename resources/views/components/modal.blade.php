{{--
/**
 * Modal Component - MyDS Design System
 *
 * @component modal
 * @description Modal dialog with focus trap and keyboard navigation
 * @author Pasukan BPM MOTAC
 * @trace D13 §2.2-2.7 (MyDS Design Tokens)
 * @trace D12 §6.11 (Focus Trap, Keyboard Navigation)
 * @trace D14 §7.5 (Shadow System - shadow-dropdown)
 * @trace D14 §10.5 (ARIA Modal)
 * @version 2.0.0
 * @updated 2025-12-06
 */
--}}

@props(['name', 'show' => false, 'maxWidth' => '2xl'])

<div x-data="{
    show: @js($show),
    focusables() {
        let selector = 'a, button, input, textarea, select, [tabindex]:not([tabindex=\'-1\'])'
        return [...this.$el.querySelectorAll(selector)]
            .filter(el => !el.hasAttribute('disabled'))
    },
    firstFocusable() { return this.focusables()[0] },
    lastFocusable() { return this.focusables().slice(-1)[0] },
    nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
    prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
    nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
    prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1 },
}" x-init="$watch('show', value => {
    if (value) {
        document.body.classList.add('overflow-hidden');
        setTimeout(() => firstFocusable()?.focus(), 100);
    } else {
        document.body.classList.remove('overflow-hidden');
    }
})"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null" x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false" x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()" x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" style="display: none;" role="dialog" aria-modal="true"
    aria-labelledby="modal-title-{{ $name }}">
    {{-- Backdrop with 400ms transition per D12 §6.10 --}}
    <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false"
        x-transition:enter="ease-out duration-400" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-900/75 dark:bg-gray-950/80"></div>
    </div>

    {{-- Modal panel with shadow-dropdown per D14 §7.5 --}}
    <div x-show="show"
        class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-dropdown transform transition-all sm:w-full sm:mx-auto {{ 'sm:max-w-' . $maxWidth }} max-h-screen sm:max-h-[calc(100vh-4rem)] overflow-y-auto focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
        x-transition:enter="ease-out duration-400"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-on:click.stop>
        {{ $slot }}
    </div>
</div>

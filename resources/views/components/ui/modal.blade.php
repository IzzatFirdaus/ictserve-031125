{{--
/**
 * Modal Component
 *
 * Accessible modal dialog with focus trap, keyboard navigation, and animations.
 *
 * Features:
 * - Focus trap using x-trap.noscroll per D12 §6.11
 * - Escape key close with @keydown.escape.window per D12 §6.11
 * - aria-modal="true", aria-labelledby per D14 §10.5
 * - Return focus to trigger on close per D12 §6.11
 * - 400ms easeout animations using --motion-easeout per D12 §6.10
 * - shadow-dropdown for modal panel per D14 §7.5
 * - Respects prefers-reduced-motion per D12 §6.10
 *
 * @props name: string - Unique modal identifier
 * @props title: string - Modal title (optional)
 * @props maxWidth: sm|md|lg|xl|2xl|3xl|4xl|5xl|6xl|7xl|full - Maximum width
 * @props closeable: bool - Whether modal can be closed by clicking backdrop/escape
 * @props focusFirst: string - Selector for element to focus on open (optional)
 *
 * @see D12 §6.11 Keyboard navigation
 * @see D12 §6.10 Motion and animation
 * @see D14 §7.5 Shadow tokens
 * @see D14 §10.5 ARIA attributes
 *
 * @requirements 28.1-28.5 Modal accessibility
 *
 * @wcag-level AA
 *
 * @version 2.0.0
 *
 * @updated 2025-12-05
 */
--}}

@props([
'name' => 'default',
'title' => null,
'maxWidth' => '2xl',
'closeable' => true,
'focusFirst' => null,
])

@php
$maxWidthClass = match ($maxWidth) {
'sm' => 'sm:max-w-sm',
'md' => 'sm:max-w-md',
'lg' => 'sm:max-w-lg',
'xl' => 'sm:max-w-xl',
'2xl' => 'sm:max-w-2xl',
'3xl' => 'sm:max-w-3xl',
'4xl' => 'sm:max-w-4xl',
'5xl' => 'sm:max-w-5xl',
'6xl' => 'sm:max-w-6xl',
'7xl' => 'sm:max-w-7xl',
'full' => 'sm:max-w-full',
default => 'sm:max-w-2xl',
};
@endphp

<div x-data="{
    show: false,
    name: '{{ $name }}',
    triggerElement: null,
    focusableElements: [],
    init() {
        this.$watch('show', (value) => {
                    if (value) {
                        document.body.classList.add('overflow-hidden');
                        this.$nextTick(() => {
                                    @if($focusFirst)
                                    const firstFocus = this.$el.querySelector('{{ $focusFirst }}');
                                    if (firstFocus) firstFocus.focus();
                                    @else
                                    const focusable = this.$el.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex=\" -1\"])'); if (focusable.length) focusable[0].focus(); @endif
    });
    } else {
    document.body.classList.remove('overflow-hidden');
    if (this.triggerElement) {
    this.$nextTick(()=> this.triggerElement.focus());
    }
    }
    });
    },
    open(trigger = null) {
    this.triggerElement = trigger || document.activeElement;
    this.show = true;
    },
    close() {
    @if ($closeable)
    this.show = false;
    @endif
    }
    }"
    x-show="show"
    x-on:open-modal.window="if ($event.detail === name || $event.detail?.name === name) { triggerElement = $event.detail?.trigger || document.activeElement; show = true; }"
    x-on:close-modal.window="if ($event.detail === name || $event.detail?.name === name) show = false"
    @if ($closeable)
    x-on:keydown.escape.window="if (show) close()"
    @endif
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    role="dialog"
    aria-modal="true"
    @if ($title)
    aria-labelledby="modal-title-{{ $name }}"
    @else
    aria-label="{{ __('common.dialog') }}"
    @endif
    {{ $attributes }}>

    {{-- Backdrop --}}
    <div x-show="show" x-transition:enter="ease-out duration-400" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75 transition-opacity"
        @if ($closeable) @click="close()" @endif aria-hidden="true"></div>

    {{-- Modal Panel Container --}}
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            {{-- Modal Panel --}}
            <div x-show="show" x-trap.noscroll.inert="show" x-transition:enter="ease-out duration-400"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-(--radius-l) bg-white dark:bg-gray-800 text-left shadow-dropdown transition-all sm:my-8 sm:w-full {{ $maxWidthClass }}">

                {{-- Close Button --}}
                @if ($closeable)
                <button type="button" @click="close()"
                    class="absolute top-4 right-4 z-10 inline-flex items-center justify-center min-w-11 min-h-11 -m-2 rounded-(--radius-s) text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 focus:outline-none transition-colors"
                    aria-label="{{ __('common.close_modal') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                @endif

                {{-- Header --}}
                @if ($title)
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 id="modal-title-{{ $name }}"
                        class="text-lg font-semibold text-gray-900 dark:text-white pr-8">
                        {{ $title }}
                    </h2>
                </div>
                @endif

                {{-- Content --}}
                <div class="px-6 py-4">
                    {{ $slot }}
                </div>

                {{-- Footer (optional slot) --}}
                @if (isset($footer))
                <div
                    class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    {{ $footer }}
                </div>
                @endif
            </div>
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

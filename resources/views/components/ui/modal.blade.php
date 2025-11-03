{{--
    Component name: Modal
    Description: WCAG 2.2 AA compliant modal dialog with focus trap, ARIA attributes, and keyboard navigation support
    Author: dev-team@motac.gov.my
    Version: 2.0.0
    Last Updated: 2025-11-03
    WCAG Level: AA
    Requirements Traceability: D03-FR-006.1, D03-FR-006.2, D03-FR-006.3, D04 §6.1, D10 §7, D12 §7.6, D12 §9, D14 §9
    Browser Support: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
    Standards Compliance: ISO/IEC 40500 (WCAG 2.2 Level AA), D12 (UI/UX), D14 (Style Guide)
--}}

@props([
    'id' => 'modal-' . uniqid(),
    'name' => null,
    'show' => false,
    'maxWidth' => '2xl',
    'title' => '',
    'closeable' => true,
])

@php
    $maxWidthClasses = [
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
    ];

    $maxWidth = $maxWidthClasses[$maxWidth] ?? $maxWidthClasses['2xl'];
    $nameAttr = $name ?? $id;
@endphp

<div
    x-data="{
        show: @js($show),
        previousFocus: null,

        open() {
            this.show = true;
            this.previousFocus = document.activeElement;
            document.body.classList.add('overflow-y-hidden');
            this.\$nextTick(() => {
                const firstFocusable = this.\$el.querySelector('[autofocus]') ||
                                     this.\$el.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex=\"-1\"])');
                if (firstFocusable) {
                    firstFocusable.focus();
                }
            });
        },

        close() {
            if (@js($closeable)) {
                this.show = false;
                document.body.classList.remove('overflow-y-hidden');
                if (this.previousFocus) {
                    this.previousFocus.focus();
                }
            }
        },

        focusableElements() {
            const selectors = [
                'a[href]:not([disabled])',
                'button:not([disabled])',
                'textarea:not([disabled])',
                'input:not([disabled])',
                'select:not([disabled])',
                '[tabindex]:not([tabindex=\"-1\"]):not([disabled])'
            ];
            return Array.from(this.\$el.querySelectorAll(selectors.join(','))).filter(el => {
                return el.offsetParent !== null;
            });
        },

        handleTabKey(event) {
            const focusables = this.focusableElements();
            if (focusables.length === 0) {
                event.preventDefault();
                return;
            }

            const firstFocusable = focusables[0];
            const lastFocusable = focusables[focusables.length - 1];

            if (event.shiftKey) {
                if (document.activeElement === firstFocusable) {
                    event.preventDefault();
                    lastFocusable.focus();
                }
            } else {
                if (document.activeElement === lastFocusable) {
                    event.preventDefault();
                    firstFocusable.focus();
                }
            }
        }
    }"
    x-init="
        $watch('show', value => {
            if (value) { open(); } else { close(); }
        });
    "
    @if($name)
    x-on:open-modal.window="if ($event.detail === '{{ $nameAttr }}') { open(); }"
    x-on:close-modal.window="if ($event.detail === '{{ $nameAttr }}') { close(); }"
    @endif
    @keydown.escape.window="if (show && @js($closeable)) { close(); }"
    @keydown.tab="if (show) { handleTabKey($event); }"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
    role="dialog"
    aria-modal="true"
    :aria-hidden="!show"
    @if($title)
    aria-labelledby="{{ $id }}-title"
    @endif
    id="{{ $id }}"
    {{ $attributes }}
>
    {{-- Backdrop --}}
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @if($closeable)
        @click="close()"
        @endif
        class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
        aria-hidden="true"
    ></div>

    {{-- Modal Container --}}
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative bg-white rounded-lg shadow-xl transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto my-8"
    >
        {{-- Header --}}
        @if($title || $closeable)
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            @if($title)
            <h2 id="{{ $id }}-title" class="text-xl font-semibold text-gray-900">
                {{ $title }}
            </h2>
            @endif

            @if($closeable)
            <button
                type="button"
                @click="close()"
                class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 rounded-md p-2 min-h-[44px] min-w-[44px] flex items-center justify-center"
                aria-label="{{ __('Close modal') }}"
            >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            @endif
        </div>
        @endif

        {{-- Content --}}
        <div class="px-6 py-4">
            {{ $slot }}
        </div>

        {{-- Footer (optional) --}}
        @isset($footer)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3">
            {{ $footer }}
        </div>
        @endisset
    </div>
</div>

@push('scripts')
<script>
    // Global helpers for modal control
    window.openModal = function(name) {
        window.dispatchEvent(new CustomEvent('open-modal', { detail: name }));
    };

    window.closeModal = function(name) {
        window.dispatchEvent(new CustomEvent('close-modal', { detail: name }));
    };
</script>
@endpush

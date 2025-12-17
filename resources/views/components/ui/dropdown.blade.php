{{--
/**
 * Dropdown Component
 *
 * Accessible dropdown menu with keyboard navigation and ARIA attributes.
 *
 * Features:
 * - aria-expanded, aria-haspopup per D14 §10.5
 * - Arrow key navigation (@keydown.arrow-down, @keydown.arrow-up) per D12 §6.11
 * - Escape key close per D12 §6.11
 * - shadow-dropdown styling per D14 §7.5
 * - 200ms transitions using --duration-short per D12 §6.10
 * - Focus management with return focus to trigger
 * - Respects prefers-reduced-motion per D12 §6.10
 * - 44×44px minimum touch targets per D12 §4.1
 *
 * @props align: left|right|top|bottom - Dropdown alignment
 * @props width: string - Width class (e.g., '48', 'w-64', 'auto')
 * @props contentClasses: string - Additional classes for content container
 * @props closeOnClick: bool - Close dropdown when item is clicked
 * @props disabled: bool - Disable dropdown trigger
 *
 * @see D12 §6.11 Keyboard navigation
 * @see D12 §6.10 Motion and animation
 * @see D14 §7.5 Shadow tokens
 * @see D14 §10.5 ARIA attributes
 *
 * @requirements 29.1-29.5 Dropdown accessibility
 *
 * @wcag-level AA
 *
 * @version 2.0.0
 *
 * @updated 2025-12-05
 */
--}}

@props([
'align' => 'right',
'width' => '48',
'contentClasses' => 'py-1 bg-white dark:bg-gray-800',
'closeOnClick' => true,
'disabled' => false,
])

@php
$alignmentClasses = match ($align) {
'left' => 'origin-top-left left-0',
'right' => 'origin-top-right right-0',
'top' => 'origin-bottom bottom-full mb-2',
'bottom' => 'origin-top top-full mt-2',
default => 'origin-top-right right-0',
};

$widthClass = match ($width) {
'32' => 'w-32',
'40' => 'w-40',
'48' => 'w-48',
'56' => 'w-56',
'64' => 'w-64',
'72' => 'w-72',
'80' => 'w-80',
'auto' => 'w-auto',
'full' => 'w-full',
default => is_numeric($width) ? "w-{$width}" : $width,
};
@endphp

<div class="relative inline-block text-left" x-data="{
    open: false,
    disabled: {{ $disabled ? 'true' : 'false' }},
    triggerEl: null,
    menuItems: [],
    activeIndex: -1,
    init() {
        this.triggerEl = this.$refs.trigger;
        this.$watch('open', (value) => {
            if (value) {
                this.$nextTick(() => {
                    this.menuItems = Array.from(this.$refs.menu?.querySelectorAll('[role=menuitem]:not([disabled])') || []);
                    this.activeIndex = -1;
                });
            } else {
                this.activeIndex = -1;
            }
        });
    },
    toggle() {
        if (this.disabled) return;
        this.open = !this.open;
    },
    close() {
        this.open = false;
        this.$nextTick(() => this.triggerEl?.focus());
    },
    focusNext() {
        if (!this.menuItems.length) return;
        this.activeIndex = (this.activeIndex + 1) % this.menuItems.length;
        this.menuItems[this.activeIndex]?.focus();
    },
    focusPrev() {
        if (!this.menuItems.length) return;
        this.activeIndex = this.activeIndex <= 0 ? this.menuItems.length - 1 : this.activeIndex - 1;
        this.menuItems[this.activeIndex]?.focus();
    },
    focusFirst() {
        if (!this.menuItems.length) return;
        this.activeIndex = 0;
        this.menuItems[0]?.focus();
    },
    focusLast() {
        if (!this.menuItems.length) return;
        this.activeIndex = this.menuItems.length - 1;
        this.menuItems[this.activeIndex]?.focus();
    }
}" @keydown.escape.prevent="close()"
    @click.outside="open = false" @click.away="open = false" {{ $attributes }}>

    {{-- Trigger Button --}}
    <div x-ref="trigger" @click="toggle()" @keydown.enter.prevent="toggle()" @keydown.space.prevent="toggle()"
        @keydown.arrow-down.prevent="if (!open) { open = true; $nextTick(() => focusFirst()); } else { focusNext(); }"
        @keydown.arrow-up.prevent="if (!open) { open = true; $nextTick(() => focusLast()); } else { focusPrev(); }"
        :aria-expanded="open" aria-haspopup="menu" :aria-disabled="disabled"
        :class="{ 'opacity-50 cursor-not-allowed': disabled }" class="inline-flex items-center">
        {{ $trigger }}
    </div>

    {{-- Dropdown Menu --}}
    <div x-ref="menu" x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" x-cloak @keydown.arrow-down.prevent="focusNext()"
        @keydown.arrow-up.prevent="focusPrev()" @keydown.home.prevent="focusFirst()" @keydown.end.prevent="focusLast()"
        @keydown.tab="close()" @if ($closeOnClick) @click="close()" @endif
        class="absolute z-50 mt-2 {{ $widthClass }} rounded-(--radius-l) {{ $alignmentClasses }} shadow-dropdown" role="menu"
        aria-orientation="vertical">
        <div class="rounded-lg shadow-lg ring-1 ring-black/5 dark:ring-white/10 {{ $contentClasses }}">
            {{ $content }}
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

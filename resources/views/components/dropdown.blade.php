{{--
/**
 * Dropdown Component - MyDS Design System
 *
 * @component dropdown
 * @description Dropdown menu with keyboard navigation and ARIA support
 * @author Pasukan BPM MOTAC
 * @trace D13 §2.2-2.7 (MyDS Design Tokens)
 * @trace D12 §6.11 (Keyboard Navigation)
 * @trace D14 §7.5 (Shadow System - shadow-dropdown)
 * @version 2.0.0
 * @updated 2025-12-06
 */
--}}
@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white dark:bg-gray-800'])

@php
    $alignmentClasses = match ($align) {
        'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
        'top' => 'origin-top',
        default => 'ltr:origin-top-right rtl:origin-top-left end-0',
    };

    $width = match ($width) {
        '48' => 'w-48',
        default => $width,
    };
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    {{-- Dropdown panel with shadow-dropdown per D14 §7.5 --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-2 {{ $width }} rounded-lg shadow-dropdown {{ $alignmentClasses }}"
        style="display: none;" @click="open = false" @keydown.escape.window="open = false"
        @keydown.arrow-down.prevent="$event.target.nextElementSibling?.focus()"
        @keydown.arrow-up.prevent="$event.target.previousElementSibling?.focus()" role="menu"
        aria-orientation="vertical">
        <div class="rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>

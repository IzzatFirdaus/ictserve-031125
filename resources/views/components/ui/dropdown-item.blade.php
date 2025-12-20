{{--
/**
 * Dropdown Item Component
 *
 * Accessible dropdown menu item with proper ARIA role and keyboard support.
 *
 * Features:
 * - role="menuitem" for proper ARIA semantics
 * - 44×44px minimum touch targets per D12 §4.1
 * - Focus styles with visible indicator per D14 §10.2
 * - Support for icons, descriptions, and disabled state
 * - Keyboard activation with Enter/Space
 *
 * @props href: string|null - Link URL (renders as <a>)
 * @props disabled: bool - Disable the item
 * @props icon: string|null - Heroicon component name
 * @props danger: bool - Destructive action styling
 * @props active: bool - Currently active/selected state
 *
 * @see D12 §4.1 Touch targets
 * @see D14 §10.2 Focus indicators
 *
 * @requirements 29.1-29.5 Dropdown accessibility
 *
 * @wcag-level AA
 *
 * @version 1.0.0
 *
 * @created 2025-12-05
 */
--}}

@props([
'href' => null,
'disabled' => false,
'icon' => null,
'danger' => false,
'active' => false,
])

@php
$baseClasses = 'group flex w-full items-center gap-3 px-4 py-2.5 min-h-11 text-sm transition-colors';
$baseClasses .= ' focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-primary-500';

$stateClasses = match (true) {
$disabled => 'text-gray-400 dark:text-gray-500 cursor-not-allowed',
$danger
=> 'text-danger-600 dark:text-danger-400 hover:bg-danger-50 dark:hover:bg-danger-900/50 focus:bg-danger-50 dark:focus:bg-danger-900/50',
$active => 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/50',
default
=> 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 focus:bg-slate-100 dark:focus:bg-slate-700',
};

$iconClasses = match (true) {
$disabled => 'text-slate-400 dark:text-slate-500',
$danger => 'text-danger-500 dark:text-danger-400',
$active => 'text-primary-500 dark:text-primary-400',
default => 'text-slate-400 dark:text-slate-500 group-hover:text-slate-500 dark:group-hover:text-slate-400',
};

$tag = $href && !$disabled ? 'a' : 'button';
@endphp

<{{ $tag }} @if ($href && !$disabled) href="{{ $href }}" @endif
    @if ($tag==='button' ) type="button" @endif
    @if ($disabled) disabled aria-disabled="true" @endif role="menuitem"
    tabindex="{{ $disabled ? '-1' : '0' }}" {{ $attributes->merge(['class' => "{$baseClasses} {$stateClasses}"]) }}>
    @if ($icon)
    <x-dynamic-component :component="$icon" class="w-5 h-5 shrink-0 {{ $iconClasses }}" aria-hidden="true" />
    @endif

    <span class="flex-1 text-left">{{ $slot }}</span>

    {{-- Active indicator --}}
    @if ($active)
    <svg class="w-5 h-5 shrink-0 text-primary-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
        <path fill-rule="evenodd"
            d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
            clip-rule="evenodd" />
    </svg>
    @endif
</{{ $tag }}>

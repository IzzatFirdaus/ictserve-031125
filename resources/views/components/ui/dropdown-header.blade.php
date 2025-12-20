{{--
/**
 * Dropdown Header Component
 *
 * Section header/label for dropdown menu groups.
 *
 * @see D14 §5.2 Typography hierarchy
 *
 * @version 1.0.0
 *
 * @created 2025-12-05
 */
--}}

@props([])

<div role="presentation"
    {{ $attributes->merge(['class' => 'px-4 py-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500']) }}>
    {{ $slot }}
</div>

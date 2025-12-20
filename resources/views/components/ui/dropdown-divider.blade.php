{{--
/**
 * Dropdown Divider Component
 *
 * Visual separator for dropdown menu sections.
 *
 * @see D14 §7.5 Visual hierarchy
 *
 * @version 1.0.0
 *
 * @created 2025-12-05
 */
--}}

@props([])

<div role="separator" aria-orientation="horizontal"
    {{ $attributes->merge(['class' => 'border-t border-slate-100 dark:border-slate-700 my-1']) }}></div>

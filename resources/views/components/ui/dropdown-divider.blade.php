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
    {{ $attributes->merge(['class' => 'my-1 h-px bg-gray-200 dark:bg-gray-700']) }}></div>

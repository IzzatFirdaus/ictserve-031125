{{--
/**
 * Nav Link Component - MyDS Design System
 *
 * @component nav-link
 * @description Navigation link with active state and WCAG 2.2 AA compliance
 * @author Pasukan BPM MOTAC
 * @trace D13 §2.2-2.7 (MyDS Design Tokens)
 * @trace D12 §4.1 (44px Touch Targets)
 * @trace D14 §6.2 (Navigation Styling)
 * @version 2.0.0
 * @updated 2025-12-06
 */
--}}
@props(['active'])

@php
    // WCAG 2.5.8: Touch target minimum 44x44px (min-h-11)
    // MyDS tokens: primary-500 for active state, transition-colors duration-200
    $classes =
        $active ?? false
            ? 'inline-flex items-center px-4 min-h-11 border-b-2 border-primary-500 dark:border-primary-400 text-sm font-medium leading-5 text-gray-900 dark:text-gray-100 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 transition-colors duration-200'
            : 'inline-flex items-center px-4 min-h-11 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus:text-gray-700 dark:focus:text-gray-300 transition-colors duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

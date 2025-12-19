{{--
/**
 * Uncategorized - Responsive Nav Link Blade Component
 *
 * Legacy component - consider categorization
 *
 * @component
 * @name Responsive Nav Link
 * @description Legacy component - consider categorization
 * @author Pasukan BPM MOTAC
 * @version 1.0.0
 * @since 2025-11-03
 *
 * Requirements: 6.1, 14.1
 * WCAG Level: AA (SC 1.4.3, 2.1.1)
 * Standards: D04 §6.1, D10 §7, D12 §9, D14 §8
 * Browsers: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 *
 * Usage:
 * <x-uncategorized.responsive-nav-link.blade />
 */
--}}

{{--
/**
 * Component name: Responsive Navigation Link
 * Description: Mobile-responsive navigation link component with active state styling for hamburger menus
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.1 (Authentication)
 * @trace D04 §6.1 (Layout Components)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 * @version 1.0.0
 * @created 2025-11-03
 */
--}}
@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full min-h-11 ps-3 pe-4 py-2 border-l-4 border-primary-400 dark:border-primary-600 text-start text-base font-medium text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-900/50 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded-lg transition duration-150 ease-in-out'
            : 'block w-full min-h-11 ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded-lg transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

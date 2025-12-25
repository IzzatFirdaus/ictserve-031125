{{--
/**
 * Dropdown Link Component - MyDS Design System
 *
 * @component dropdown-link
 * @description Dropdown menu item link with WCAG 2.2 AA compliance
 * @author Pasukan BPM MOTAC
 * @trace D13 §2.2-2.7 (MyDS Design Tokens)
 * @trace D12 §4.1 (44px Touch Targets)
 * @trace D14 §6.2 (Navigation Styling)
 * @version 2.0.0
 * @updated 2025-12-06
 */
--}}
<a
    {{ $attributes->merge(['class' => 'block w-full px-4 py-2 min-h-11 text-start text-sm leading-5 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-3 focus-visible:ring-primary-500 focus:bg-gray-100 dark:focus:bg-gray-700 transition-colors duration-200', 'role' => 'menuitem']) }}>{{ $slot }}</a>

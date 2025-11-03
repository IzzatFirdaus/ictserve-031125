{{--
/**
 * Uncategorized - Secondary Button Blade Component
 *
 * Legacy component - consider categorization
 *
 * @component
 * @name Secondary Button
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
 * <x-uncategorized.secondary-button.blade />
 */
--}}

{{--
/**
 * Component name: Secondary Button
 * Description: Secondary action button component with outline styling and accessible interaction states
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
<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

{{--
/**
 * Uncategorized - Danger Button Blade Component
 *
 * Legacy component - consider categorization
 *
 * @component
 * @name Danger Button
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
 * <x-uncategorized.danger-button.blade />
 */
--}}

{{--
/**
 * Danger Button Blade Component
 *
 * Reusable Blade component for consistent UI patterns
 *
 * @trace D04 §6.1
 * @trace D10 §7
 * @trace D12 §9
 * @trace D14 §8
 * @wcag WCAG 2.2 Level AA
 * @browsers Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 * @version 1.0.0
 * @author Pasukan BPM MOTAC
 * @created 2025-11-03
 * @updated 2025-11-03
 */
--}}
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-danger border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-danger active:bg-danger-dark focus:outline-none focus:ring-2 focus:ring-danger focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

{{--
/**
 * Uncategorized - Text Input Blade Component
 *
 * Legacy component - consider categorization
 *
 * @component
 * @name Text Input
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
 * <x-uncategorized.text-input.blade />
 */
--}}

{{--
/**
 * Component name: Text Input
 * Description: WCAG 2.2 AA compliant text input component with focus indicators and semantic HTML
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-016.1, D03-FR-017.1
 * @trace D04 §8.1 (Form Components)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 * @version 1.0.0
 * @created 2025-11-03
 */
--}}

@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm']) }}>

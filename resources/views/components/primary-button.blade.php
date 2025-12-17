{{--
/**
 * Primary Button Component - MyDS Design System
 *
 * @component primary-button
 * @description Main call-to-action button with MOTAC branding and WCAG 2.2 AA compliance
 * @author Pasukan BPM MOTAC
 * @trace D13 §2.2-2.7 (MyDS Design Tokens)
 * @trace D12 §4.1 (44px Touch Targets)
 * @trace D14 §6.5 (Button Styling)
 * @version 2.0.0
 * @updated 2025-12-06
 */
--}}
<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 min-h-11 min-w-11 bg-primary-500 dark:bg-primary-600 border border-transparent rounded-md font-semibold text-sm text-white shadow-button hover:bg-primary-600 dark:hover:bg-primary-500 focus:bg-primary-600 dark:focus:bg-primary-500 active:bg-primary-700 dark:active:bg-primary-400 focus:outline-none transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>

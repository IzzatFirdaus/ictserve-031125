{{--
/**
 * Danger Button Component - MyDS Design System
 *
 * @component danger-button
 * @description Destructive action button with danger styling and WCAG 2.2 AA compliance
 * @author Pasukan BPM MOTAC
 * @trace D13 §2.2-2.7 (MyDS Design Tokens)
 * @trace D12 §4.1 (44px Touch Targets)
 * @trace D14 §6.5 (Button Styling)
 * @version 2.0.0
 * @updated 2025-12-06
 */
--}}
<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 min-h-11 min-w-11 bg-danger-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-button hover:bg-danger-700 active:bg-danger-800 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-danger-500 dark:focus-visible:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200']) }}>
    {{ $slot }}
</button>

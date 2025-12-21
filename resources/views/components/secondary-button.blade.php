{{--
/**
 * Secondary Button Component - MyDS Design System
 *
 * @component secondary-button
 * @description Secondary action button with outline styling and WCAG 2.2 AA compliance
 * @author Pasukan BPM MOTAC
 * @trace D13 §2.2-2.7 (MyDS Design Tokens)
 * @trace D12 §4.1 (44px Touch Targets)
 * @trace D14 §6.5 (Button Styling)
 * @version 2.0.0
 * @updated 2025-12-06
 */
--}}
<button
    {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center min-h-11 min-w-11 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg font-semibold text-sm text-slate-700 dark:text-slate-200 shadow-button hover:bg-slate-50 dark:hover:bg-slate-700 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200']) }}>
    {{ $slot }}
</button>

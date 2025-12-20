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
    {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-500 rounded-lg font-semibold text-xs text-slate-700 dark:text-slate-300 uppercase tracking-widest shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

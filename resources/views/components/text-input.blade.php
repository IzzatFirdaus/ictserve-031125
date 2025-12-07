{{--
/**
 * Text Input Component - MyDS Design System
 *
 * @component text-input
 * @description WCAG 2.2 AA compliant text input with MyDS design tokens
 * @author Pasukan BPM MOTAC
 * @trace D13 §2.2-2.7 (MyDS Design Tokens)
 * @trace D13 §3.7.1 (Form Input Guidelines)
 * @trace D12 §6.2 (Form Design)
 * @version 2.0.0
 * @updated 2025-12-06
 */
--}}

@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge(['class' => 'w-full min-h-11 px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-3 focus:ring-primary-500/20 dark:focus:ring-primary-400/20 rounded-lg shadow-sm transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed']) }}>

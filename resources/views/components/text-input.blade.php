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

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' =>
        'border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-lg shadow-sm',
]) !!}>

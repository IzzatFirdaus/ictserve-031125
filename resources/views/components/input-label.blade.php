{{--
/**
 * Input Label Component - MyDS Design System
 *
 * @component input-label
 * @description Form label with MyDS typography and WCAG 2.2 AA compliance
 * @author Pasukan BPM MOTAC
 * @trace D13 §2.4 (Typography System)
 * @trace D12 §6.2 (Form Design - Label above field)
 * @trace D14 §10.3 (Accessible Labels)
 * @version 2.0.0
 * @updated 2025-12-06
 */
--}}
@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700 dark:text-gray-300']) }}>
    {{ $value ?? $slot }}
    @if ($required)
        <abbr title="{{ __('common.required') }}" class="text-danger-500 no-underline ml-0.5">*</abbr>
    @endif
</label>

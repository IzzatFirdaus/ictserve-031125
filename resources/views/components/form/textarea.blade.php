{{--
/**
 * Component name: Form Textarea
 * Description: Accessible multi-line text input form component with validation support, character counting, and WCAG 2.2 AA compliance.
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-006.3 (Form inputs)
 * @trace D03-FR-006.5 (Validation)
 * @trace D04 §6.1 (Forms)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 * @version 1.0.0
 * @created 2025-11-03
 */
--}}
 *
 * Reusable Blade component for consistent UI patterns
 *
 * @trace D03-FR-011.5
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
{{--
/**
 * Component: WCAG Compliant Textarea Field
 * Description: Accessible textarea with character count, proper labels, and ARIA attributes
 * Author: Pasukan BPM MOTAC
 * Requirements: 6.1, 6.2, 6.3, 6.5, 11.5, 21.3, 21.4
 * WCAG Level: AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.5, 3.3.1, 3.3.2)
 * Version: 1.0.0
 * Created: 2025-11-03
 */
--}}

@props([
    'name',
    'label',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'placeholder' => '',
    'helpText' => '',
    'rows' => 4,
    'minlength' => null,
    'maxlength' => null,
    'showCharCount' => false,
])

@php
    $inputId = $attributes->get('id', $name);
    $helpId = $helpText ? "{$inputId}-help" : null;
    $errorId = "{$inputId}-error";
    $charCountId = $showCharCount ? "{$inputId}-char-count" : null;
    $hasError = $errors->has($name);

    $textareaClasses =
        'block w-full rounded-md shadow-sm transition-colors duration-200 px-4 py-2 text-base ' .
        ($hasError
            ? 'border-danger text-red-900 placeholder-red-300 focus:outline-none focus:ring-2 focus:ring-danger focus:border-danger'
            : 'border-gray-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600 focus:outline-none');
@endphp

<div class="mb-4" x-data="{ charCount: {{ strlen(old($name, $value)) }} }">
    <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }}
        @if ($required)
            <span class="text-danger" aria-label="{{ __('required') }}">*</span>
        @endif
    </label>

    @if ($helpText)
        <p id="{{ $helpId }}" class="text-sm text-gray-600 mb-2">{{ $helpText }}</p>
    @endif

    <textarea name="{{ $name }}" id="{{ $inputId }}" rows="{{ $rows }}" class="{{ $textareaClasses }}"
        @if ($required) required aria-required="true" @endif
        @if ($disabled) disabled @endif @if ($readonly) readonly @endif
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($minlength) minlength="{{ $minlength }}" @endif
        @if ($maxlength) maxlength="{{ $maxlength }}" @endif
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $errorId }}" @endif
        @if ($helpId && !$hasError) aria-describedby="{{ $helpId }}" @endif
        @if ($showCharCount) x-on:input="charCount = $event.target.value.length" @endif
        {{ $attributes->except(['id', 'class']) }}>{{ old($name, $value) }}</textarea>

    @if ($showCharCount && $maxlength)
        <p id="{{ $charCountId }}" class="mt-1 text-sm text-gray-600" aria-live="polite">
            <span x-text="charCount"></span> / {{ $maxlength }} {{ __('characters') }}
        </p>
    @endif

    @error($name)
        <p id="{{ $errorId }}" class="mt-2 text-sm text-danger" role="alert">
            <span class="font-medium">{{ __('Error:') }}</span> {{ $message }}
        </p>
    @enderror
</div>

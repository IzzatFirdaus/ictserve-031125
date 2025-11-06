{{--
/**
 * Component name: Form Textarea
 * Description: Accessible multiline input with validation feedback and optional character counting.
 * Author: Pasukan BPM MOTAC
 * References: D03-FR-006.3, D03-FR-006.5, D04 section 6.1, D10 section 7, D12 section 9, D14 section 8
 * WCAG: 2.2 Level AA
 * Version: 1.0.0 (2025-11-03)
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
        'block w-full rounded-md shadow-sm transition-colors duration-200 px-4 py-2 text-base text-gray-900 placeholder-gray-600 ' .
        ($hasError
            ? 'border-danger text-red-900 placeholder-red-700 focus:outline-none focus:ring-2 focus:ring-danger focus:border-danger'
            : 'border-gray-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600 focus:outline-none');
@endphp

<div class="mb-4" x-data="{ charCount: {{ strlen(old($name, $value)) }} }">
    @if (isset($label))
    <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }}
        @if ($required)
            <span class="text-danger" aria-label="{{ __('required') }}">*</span>
        @endif
    </label>
    @endif

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

{{--
/**
 * Component: WCAG Compliant Select Dropdown
 * Description: Accessible select field with proper labels, error handling, and ARIA attributes
 * Author: Pasukan BPM MOTAC
 * Requirements: 6.1, 6.2, 6.3, 6.5, 11.5, 21.3
 * WCAG Level: AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.5, 3.3.1, 3.3.2)
 * Version: 1.0.0
 * Created: 2025-11-03
 */
--}}

@props([
    'name',
    'label',
    'options' => [],
    'value' => '',
    'required' => false,
    'disabled' => false,
    'placeholder' => null,
    'helpText' => '',
])

@php
    $inputId = $attributes->get('id', $name);
    $helpId = $helpText ? "{$inputId}-help" : null;
    $errorId = "{$inputId}-error";
    $hasError = $errors->has($name);

    $selectClasses =
        'block w-full rounded-md shadow-sm transition-colors duration-200 min-h-[44px] px-4 py-2 text-base ' .
        ($hasError
            ? 'border-red-600 text-red-900 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600'
            : 'border-gray-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600 focus:outline-none');
@endphp

<div class="mb-4">
    <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }}
        @if ($required)
            <span class="text-red-600" aria-label="{{ __('required') }}">*</span>
        @endif
    </label>

    @if ($helpText)
        <p id="{{ $helpId }}" class="text-sm text-gray-600 mb-2">{{ $helpText }}</p>
    @endif

    <select name="{{ $name }}" id="{{ $inputId }}" class="{{ $selectClasses }}"
        @if ($required) required aria-required="true" @endif
        @if ($disabled) disabled @endif
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $errorId }}" @endif
        @if ($helpId && !$hasError) aria-describedby="{{ $helpId }}" @endif
        {{ $attributes->except(['id', 'class']) }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @error($name)
        <p id="{{ $errorId }}" class="mt-2 text-sm text-red-600" role="alert">
            <span class="font-medium">{{ __('Error:') }}</span> {{ $message }}
        </p>
    @enderror
</div>

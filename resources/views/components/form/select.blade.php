{{-- 
/**
 * Component name: Form Select
 * Description: Accessible select dropdown with validation states and optional help text.
 * Author: Pasukan BPM MOTAC
 * References: D03-FR-006.3, D03-FR-006.5, D04 section 6.1, D10 section 7, D12 section 9, D14 section 8
 * WCAG: 2.2 Level AA
 * Version: 1.0.0 (2025-11-03)
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
        'block w-full rounded-md shadow-sm transition-colors duration-200 min-h-[44px] px-4 py-2 text-base text-gray-900 ' .
        ($hasError
            ? 'border-danger text-red-900 focus:outline-none focus:ring-2 focus:ring-danger focus:border-danger'
            : 'border-gray-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600 focus:outline-none');
@endphp

<div class="mb-4">
    @if (isset($label) && !($attributes->has('hide-label') && $attributes->get('hide-label') == true))
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
        <p id="{{ $errorId }}" class="mt-2 text-sm text-danger" role="alert">
            <span class="font-medium">{{ __('Error:') }}</span> {{ $message }}
        </p>
    @enderror
</div>

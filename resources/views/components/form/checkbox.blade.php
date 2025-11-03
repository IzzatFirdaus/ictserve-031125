{{--
/**
 * Component: WCAG Compliant Checkbox Field
 * Description: Accessible checkbox with proper labeling and ARIA attributes
 * Author: Pasukan BPM MOTAC
 * Requirements: 6.1, 6.2, 6.3, 6.5, 11.5, 21.3
 * WCAG Level: AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.5, 3.3.1)
 * Version: 1.0.0
 * Created: 2025-11-03
 */
--}}

@props([
    'name',
    'label',
    'value' => '1',
    'checked' => false,
    'required' => false,
    'disabled' => false,
    'helpText' => '',
])

@php
    $inputId = $attributes->get('id', $name);
    $helpId = $helpText ? "{$inputId}-help" : null;
    $errorId = "{$inputId}-error";
    $hasError = $errors->has($name);

    $isChecked = old($name, $checked) ? true : false;
@endphp

<div class="mb-4">
    <div class="flex items-start">
        <div class="flex items-center h-5 min-h-[44px]">
            <input type="checkbox" name="{{ $name }}" id="{{ $inputId }}" value="{{ $value }}"
                class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors duration-200"
                @if ($isChecked) checked @endif
                @if ($required) required aria-required="true" @endif
                @if ($disabled) disabled @endif
                @if ($hasError) aria-invalid="true" aria-describedby="{{ $errorId }}" @endif
                @if ($helpId && !$hasError) aria-describedby="{{ $helpId }}" @endif
                {{ $attributes->except(['id', 'class']) }} />
        </div>
        <div class="ml-3 text-sm">
            <label for="{{ $inputId }}" class="font-medium text-gray-700 cursor-pointer">
                {{ $label }}
                @if ($required)
                    <span class="text-red-600" aria-label="{{ __('required') }}">*</span>
                @endif
            </label>
            @if ($helpText)
                <p id="{{ $helpId }}" class="text-gray-600 mt-1">{{ $helpText }}</p>
            @endif
        </div>
    </div>

    @error($name)
        <p id="{{ $errorId }}" class="mt-2 text-sm text-red-600" role="alert">
            <span class="font-medium">{{ __('Error:') }}</span> {{ $message }}
        </p>
    @enderror
</div>

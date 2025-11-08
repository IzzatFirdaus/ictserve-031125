{{--
/**
 * Component name: Form Input
 * Description: Accessible text input with validation states and optional help text.
 * Author: Pasukan BPM MOTAC
 * References: D03-FR-006.3, D03-FR-006.5, D04 section 6.1, D10 section 7, D12 section 9, D14 section 8
 * WCAG: 2.2 Level AA
 * Version: 1.0.0 (2025-11-03)
 */
--}}

@props([
    'name',
    'label',
    'type' => 'text',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'placeholder' => '',
    'helpText' => '',
    'autocomplete' => '',
    'minlength' => null,
    'maxlength' => null,
    'pattern' => null,
    'hideLabel' => false,
])

@php
    $inputId = $attributes->get('id', $name);
    $helpId = $helpText ? "{$inputId}-help" : null;
    $errorId = "{$inputId}-error";
    $hasError = $errors->has($name);

    $baseClasses = 'block w-full rounded-md shadow-sm transition-colors duration-200 min-h-[44px] px-4 py-2 text-base text-gray-900 placeholder-gray-600 dark:bg-slate-900 dark:text-slate-100 dark:placeholder-slate-500';

    $errorClasses = 'border-danger text-red-900 placeholder-red-700 focus:outline-none focus:ring-2 focus:ring-danger focus:border-danger dark:text-slate-100 dark:placeholder-red-300';
    $normalClasses = 'border-gray-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600 focus:outline-none dark:border-slate-700 dark:focus:border-blue-400 dark:focus:ring-blue-400';

    $inputClasses = $baseClasses . ' ' . ($hasError ? $errorClasses : $normalClasses);
@endphp

<div class="mb-4">
    {{-- Label --}}
    @if (isset($label))
    <label for="{{ $inputId }}"
        class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-2 @if($hideLabel) sr-only @endif">
        {{ $label }}
        @if ($required)
            <span class="text-danger" aria-label="{{ __('required') }}">*</span>
        @endif
    </label>
    @endif

    {{-- Help Text --}}
    @if ($helpText)
        <p id="{{ $helpId }}" class="text-sm text-gray-600 dark:text-slate-400 mb-2">
            {{ $helpText }}
        </p>
    @endif

    {{-- Input Field --}}
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $inputId }}" value="{{ old($name, $value) }}"
        class="{{ $inputClasses }}" @if ($required) required aria-required="true" @endif
        @if ($disabled) disabled @endif @if ($readonly) readonly @endif
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        @if ($minlength) minlength="{{ $minlength }}" @endif
        @if ($maxlength) maxlength="{{ $maxlength }}" @endif
        @if ($pattern) pattern="{{ $pattern }}" @endif
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $errorId }}" @endif
        @if ($helpId && !$hasError) aria-describedby="{{ $helpId }}" @endif
        {{ $attributes->except(['id', 'class']) }} />

    {{-- Error Message --}}
    @error($name)
        <p id="{{ $errorId }}" class="mt-2 text-sm text-danger" role="alert">
            <span class="font-medium">{{ __('Error:') }}</span> {{ $message }}
        </p>
    @enderror
</div>

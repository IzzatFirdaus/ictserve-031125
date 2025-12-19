{{--
/**
 * Component: Touch Input
 * Description: WCAG 2.2 AA compliant touch-friendly form input for mobile
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-015.3 (Touch Interactions)
 * @trace D12 §6.11 (Touch Interactions)
 * @trace D14 §10.5 (ARIA Attributes)
 * @trace D15 §3.1 (Mobile Optimization)
 * @wcag WCAG 2.2 Level AA (SC 2.5.8 Target Size, SC 1.3.5 Identify Input Purpose)
 * @version 1.0.0
 * @created 2025-12-14
 *
 * Features:
 * - 44×44px minimum touch target
 * - Large text for mobile readability
 * - Autocomplete support for common fields
 * - Input mode optimization for virtual keyboards
 * - Clear button for easy text clearing
 * - Error state with accessible messaging
 *
 * Usage:
 * <x-responsive.touch-input 
 *     name="email"
 *     type="email"
 *     label="Alamat E-mel"
 *     autocomplete="email"
 *     inputmode="email" />
 */
--}}

@props([
    'name' => '',
    'id' => null,
    'type' => 'text',
    'label' => '',
    'placeholder' => '',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'hint' => null,
    'autocomplete' => null,
    'inputmode' => null,
    'pattern' => null,
    'showClear' => true,
])

@php
    $inputId = $id ?? 'touch-input-' . ($name ?: uniqid());
    $errorId = $inputId . '-error';
    $hintId = $inputId . '-hint';
    $hasError = !empty($error);

    // Determine inputmode based on type if not specified
    $computedInputmode =
        $inputmode ??
        match ($type) {
            'email' => 'email',
            'tel' => 'tel',
            'url' => 'url',
            'number' => 'numeric',
            'search' => 'search',
            default => 'text',
        };

    // Determine autocomplete based on name if not specified
    $computedAutocomplete =
        $autocomplete ??
        match ($name) {
            'email' => 'email',
            'phone', 'tel', 'telephone' => 'tel',
            'name', 'full_name', 'fullname' => 'name',
            'first_name', 'firstname' => 'given-name',
            'last_name', 'lastname' => 'family-name',
            'address' => 'street-address',
            'city' => 'address-level2',
            'state' => 'address-level1',
            'zip', 'postal_code', 'postcode' => 'postal-code',
            'country' => 'country-name',
            'organization', 'company' => 'organization',
            'username' => 'username',
            'password' => 'current-password',
            'new_password' => 'new-password',
            default => null,
        };
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'space-y-2']) }}>
    {{-- Label --}}
    @if ($label)
        <label for="{{ $inputId }}" class="block text-base font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if ($required)
                <span class="text-danger-500" aria-hidden="true">*</span>
                <span class="sr-only">{{ __('(wajib)') }}</span>
            @endif
        </label>
    @endif

    {{-- Input wrapper --}}
    <div x-data="{ value: '{{ $value }}', focused: false }" class="relative">
        <input type="{{ $type }}" id="{{ $inputId }}" name="{{ $name }}" x-model="value"
            @focus="focused = true" @blur="focused = false"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($required) required aria-required="true" @endif
            @if ($disabled) disabled @endif @if ($readonly) readonly @endif
            @if ($computedAutocomplete) autocomplete="{{ $computedAutocomplete }}" @endif
            @if ($computedInputmode) inputmode="{{ $computedInputmode }}" @endif
            @if ($pattern) pattern="{{ $pattern }}" @endif
            @if ($hasError) aria-invalid="true" aria-describedby="{{ $errorId }}" @endif
            @if ($hint && !$hasError) aria-describedby="{{ $hintId }}" @endif
            {{ $attributes->except('class') }}
            class="block w-full min-h-12 px-4 py-3 text-base text-gray-900 dark:text-white bg-white dark:bg-gray-800 border rounded-lg transition-colors duration-200
                {{ $hasError
                    ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500'
                    : 'border-gray-300 dark:border-gray-600 focus:border-primary-500 focus:ring-primary-500' }}
                {{ $disabled ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-60' : '' }}
                {{ $showClear ? 'pr-12' : '' }}
                focus:outline-none focus:ring-2 focus:ring-offset-0">

        {{-- Clear button --}}
        @if ($showClear && !$disabled && !$readonly)
            <button type="button" x-show="value.length > 0" @click="value = ''; $refs.input?.focus()"
                class="absolute right-3 top-1/2 -translate-y-1/2 p-2 min-w-44 min-h-44 flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-md"
                aria-label="{{ __('Kosongkan medan') }}" x-cloak>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        @endif
    </div>

    {{-- Hint text --}}
    @if ($hint && !$hasError)
        <p id="{{ $hintId }}" class="text-sm text-gray-500 dark:text-gray-400">
            {{ $hint }}
        </p>
    @endif

    {{-- Error message --}}
    @if ($hasError)
        <p id="{{ $errorId }}" class="text-sm text-danger-600 dark:text-danger-400" role="alert">
            <span class="sr-only">{{ __('Ralat:') }}</span>
            {{ $error }}
        </p>
    @endif
</div>

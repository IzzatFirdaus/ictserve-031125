 {{--
/**
 * Component: Accessible Form Input
 * Description: WCAG 2.2 AA compliant form input with proper labeling, error handling, and focus indicators
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-006.2 (Keyboard Navigation)
 * @trace D03-FR-006.3 (Screen Reader Support)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @wcag WCAG 2.2 Level AA (SC 1.3.1, 1.3.5, 2.4.6, 3.3.2, 4.1.2)
 * @version 1.0.0
 * @created 2025-12-04
 *
 * Requirements:
 * - 9.1: Color contrast 4.5:1 for text
 * - 9.2: 3px focus indicators
 * - 9.3: ARIA labels
 * - 9.4: 44px minimum touch target
 */
--}}

@props([
    'type' => 'text',
    'label' => null,
    'id' => null,
    'name' => null,
    'error' => null,
    'helper' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'autocomplete' => null,
    'placeholder' => null,
    'prefix' => null,
    'suffix' => null,
])

@php
    $inputId = $id ?? ($name ?? 'input-' . uniqid());
    $errorId = $inputId . '-error';
    $helperId = $inputId . '-helper';
    $hasError = !empty($error);

    // Base input classes with 44px minimum height
    $inputClasses =
        'block w-full min-h-[44px] px-4 py-2.5 text-base rounded-lg border shadow-sm transition-colors duration-200';

    // State-specific classes
    if ($hasError) {
        $inputClasses .=
            ' border-danger-500 text-gray-900 placeholder-danger-300 focus:border-danger-500 focus:ring-danger-500 bg-danger-50';
    } else {
        $inputClasses .=
            ' border-gray-300 text-gray-900 placeholder-gray-500 focus:border-primary-500 focus:ring-primary-500';
    }

    // Focus indicator - 3px outline
    $inputClasses .= ' focus-visible:outline-3 focus-visible:outline-offset-0';

    if ($disabled) {
        $inputClasses .= ' bg-gray-100 cursor-not-allowed opacity-60';
    }

    if ($readonly) {
        $inputClasses .= ' bg-gray-50';
    }
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'w-full']) }}>
    {{-- Label --}}
    @if ($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 mb-1.5">
            {{ $label }}
            @if ($required)
                <span class="text-danger-500 ml-0.5" aria-hidden="true">*</span>
                <span class="sr-only">{{ __('common.required') }}</span>
            @endif
        </label>
    @endif

    {{-- Input wrapper for prefix/suffix --}}
    <div class="relative">
        {{-- Prefix --}}
        @if ($prefix)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm" aria-hidden="true">{{ $prefix }}</span>
            </div>
        @endif

        {{-- Input element --}}
        <input type="{{ $type }}" id="{{ $inputId }}"
            @if ($name) name="{{ $name }}" @endif
            {{ $attributes->except(['class', 'type', 'id', 'name'])->merge([
                'class' => $inputClasses . ($prefix ? ' pl-10' : '') . ($suffix ? ' pr-10' : ''),
            ]) }}
            @if ($required) required aria-required="true" @endif
            @if ($disabled) disabled aria-disabled="true" @endif
            @if ($readonly) readonly @endif
            @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($hasError) aria-invalid="true"
                aria-describedby="{{ $errorId }}"
            @elseif($helper)
                aria-describedby="{{ $helperId }}" @endif
            style="outline-width: 3px;">

        {{-- Suffix or error icon --}}
        @if ($hasError)
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-danger-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
            </div>
        @elseif($suffix)
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm" aria-hidden="true">{{ $suffix }}</span>
            </div>
        @endif
    </div>

    {{-- Error message --}}
    @if ($hasError)
        <p id="{{ $errorId }}" class="mt-1.5 text-sm text-danger-600 flex items-center gap-1" role="alert">
            <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
            {{ $error }}
        </p>
    @elseif($helper)
        <p id="{{ $helperId }}" class="mt-1.5 text-sm text-gray-500">
            {{ $helper }}
        </p>
    @endif
</div>

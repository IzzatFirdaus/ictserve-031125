@props([
    'disabled' => false,
    'label' => null,
    'error' => null,
    'helper' => null,
    'id' => null,
])

@php
    $id = $id ?? $attributes->get('wire:model') ?? $attributes->get('name') ?? md5($attributes->get('label'));
    $wireModel = $attributes->get('wire:model');
    $hasError = $error || ($wireModel && isset($errors) && is_object($errors) && method_exists($errors, 'has') && $errors->has($wireModel));
    $errorMessage = $error ?? ($wireModel && isset($errors) && is_object($errors) && method_exists($errors, 'first') ? $errors->first($wireModel) : null);
@endphp

<div class="{{ $attributes->get('class') }}">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $label }}
            @if($attributes->has('required'))
                <span class="text-danger-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative rounded-md shadow-sm">
        <input
            {{ $disabled ? 'disabled' : '' }}
            id="{{ $id }}"
            {{ $attributes->merge([
                'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white min-h-44' . 
                ($hasError ? ' border-danger-300 text-danger-900 placeholder-danger-300 focus:border-danger-500 focus:ring-danger-500' : '')
            ]) }}
        >
        
        @if($hasError)
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="h-5 w-5 text-danger-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
        @endif
    </div>

    @if($errorMessage)
        <p class="mt-1 text-sm text-danger-600 dark:text-danger-400" id="{{ $id }}-error">{{ $errorMessage }}</p>
    @elseif($helper)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" id="{{ $id }}-helper">{{ $helper }}</p>
    @endif
</div>

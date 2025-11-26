@props([
    'disabled' => false,
    'label' => null,
    'error' => null,
    'helper' => null,
    'id' => null,
    'options' => [],
    'placeholder' => 'Select an option',
])

@php
    $id = $id ?? $attributes->get('wire:model') ?? $attributes->get('name') ?? md5($attributes->get('label'));
    $hasError = $error || ($attributes->has('wire:model') && $errors->has($attributes->get('wire:model')));
    $errorMessage = $error ?? ($attributes->has('wire:model') ? $errors->first($attributes->get('wire:model')) : null);
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
        <select
            {{ $disabled ? 'disabled' : '' }}
            id="{{ $id }}"
            {{ $attributes->merge([
                'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white min-h-44' . 
                ($hasError ? ' border-danger-300 text-danger-900 focus:border-danger-500 focus:ring-danger-500' : '')
            ]) }}
        >
            @if($placeholder)
                <option value="" disabled selected>{{ $placeholder }}</option>
            @endif
            
            @foreach($options as $value => $text)
                <option value="{{ $value }}">{{ $text }}</option>
            @endforeach
            
            {{ $slot }}
        </select>
    </div>

    @if($errorMessage)
        <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $errorMessage }}</p>
    @elseif($helper)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $helper }}</p>
    @endif
</div>

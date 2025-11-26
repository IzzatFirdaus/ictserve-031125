@props([
    'disabled' => false,
    'label' => null,
    'error' => null,
    'id' => null,
])

@php
    $id = $id ?? $attributes->get('wire:model') ?? $attributes->get('name') ?? md5($attributes->get('label'));
    $hasError = $error || ($attributes->has('wire:model') && $errors->has($attributes->get('wire:model')));
    $errorMessage = $error ?? ($attributes->has('wire:model') ? $errors->first($attributes->get('wire:model')) : null);
@endphp

<div class="flex items-start {{ $attributes->get('class') }}">
    <div class="flex h-5 items-center">
        <input
            {{ $disabled ? 'disabled' : '' }}
            id="{{ $id }}"
            type="checkbox"
            {{ $attributes->merge([
                'class' => 'h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:ring-offset-gray-900' . 
                ($hasError ? ' border-danger-300 text-danger-900 focus:ring-danger-500' : '')
            ]) }}
        >
    </div>
    @if($label)
        <div class="ml-3 text-sm">
            <label for="{{ $id }}" class="font-medium text-gray-700 dark:text-gray-300">
                {{ $label }}
                @if($attributes->has('required'))
                    <span class="text-danger-500">*</span>
                @endif
            </label>
            @if($errorMessage)
                <p class="text-danger-600 dark:text-danger-400">{{ $errorMessage }}</p>
            @endif
        </div>
    @endif
</div>

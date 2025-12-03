@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'required' => false,
    'error' => null,
    'helper' => null,
    'value' => '',
    'placeholder' => '',
])

<div class="form-field">
    @if($label)
        <label for="{{ $name }}" class="form-label {{ $required ? 'form-label-required' : '' }}">
            {{ $label }}
            @if($required)
                <span class="sr-only">{{ __('common.required') }}</span>
            @endif
        </label>
    @endif

    @if($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            {{ $attributes->merge(['class' => 'form-textarea' . ($error ? ' form-input-error' : '')]) }}
            @if($required) required @endif
            @if($error) aria-describedby="{{ $name }}-error" aria-invalid="true" @endif
            placeholder="{{ $placeholder }}"
        >{{ old($name, $value) }}</textarea>
    @elseif($type === 'select')
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            {{ $attributes->merge(['class' => 'form-select' . ($error ? ' form-input-error' : '')]) }}
            @if($required) required @endif
            @if($error) aria-describedby="{{ $name }}-error" aria-invalid="true" @endif
        >
            {{ $slot }}
        </select>
    @else
        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            {{ $attributes->merge(['class' => 'form-input' . ($error ? ' form-input-error' : '')]) }}
            @if($required) required @endif
            @if($error) aria-describedby="{{ $name }}-error" aria-invalid="true" @endif
            placeholder="{{ $placeholder }}"
        />
    @endif

    @if($helper)
        <p class="form-helper">{{ $helper }}</p>
    @endif

    @if($error)
        <p id="{{ $name }}-error" class="form-error" role="alert">
            {{ $error }}
        </p>
    @endif
</div>

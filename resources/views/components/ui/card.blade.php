{{--
/**
 * Component: WCAG Compliant Card Container
 * Description: Accessible card component with proper semantic structure
 * Author: Pasukan BPM MOTAC
 * Requirements: 6.1, 6.2, 14.1, 22.2
 * WCAG Level: AA (SC 1.4.3, 2.1.1)
 * Version: 1.0.0
 * Created: 2025-11-03
 */
--}}

@props([
    'title' => null,
    'footer' => null,
    'padding' => true,
])

<div class="bg-white rounded-lg shadow-md overflow-hidden {{ $attributes->get('class', '') }}"
    {{ $attributes->except('class') }}>
    @if ($title)
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
        </div>
    @endif

    <div class="{{ $padding ? 'p-6' : '' }}">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $footer }}
        </div>
    @endif
</div>

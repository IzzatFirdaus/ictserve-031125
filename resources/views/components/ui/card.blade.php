{{-- 
/**
 * Component name: UI Card
 * Description: Accessible content container with optional header and footer slots.
 * Author: Pasukan BPM MOTAC
 * References: D03-FR-006.1, D03-FR-006.2, D04 section 6.1, D10 section 7, D12 section 9, D14 section 8
 * WCAG: 2.2 Level AA
 * Version: 1.0.0 (2025-11-03)
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

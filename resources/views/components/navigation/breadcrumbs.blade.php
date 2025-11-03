{{--
    Component name: Breadcrumbs
    Description: WCAG 2.2 AA compliant breadcrumb navigation component with ARIA landmarks and screen reader support
    Author: dev-team@motac.gov.my
    Version: 1.0.0
    Last Updated: 2025-11-03
    WCAG Level: AA
    Requirements Traceability: D03-FR-006.1, D03-FR-006.2, D03-FR-006.3, D04 §6.1, D10 §7, D12 §4.1, D12 §9, D14 §9
    Browser Support: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
    Standards Compliance: ISO/IEC 40500 (WCAG 2.2 Level AA), D12 (UI/UX), D14 (Style Guide)
--}}

@props([
    'items' => [],
    'separator' => '/',
])

@php
    $hasItems = !empty($items);
@endphp

@if($hasItems)
<nav aria-label="{{ __('Breadcrumb') }}" {{ $attributes->merge(['class' => 'flex']) }}>
    <ol class="inline-flex items-center space-x-1 md:space-x-3" role="list">
        @foreach($items as $index => $item)
            @php
                $isLast = $index === count($items) - 1;
                $hasUrl = isset($item['url']) && !empty($item['url']);
                $label = $item['label'] ?? $item['name'] ?? '';
            @endphp

            <li class="inline-flex items-center">
                @if(!$isLast && $index > 0)
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                @endif

                @if($isLast)
                    <span class="text-sm font-medium text-gray-700" aria-current="page">
                        {{ $label }}
                    </span>
                @elseif($hasUrl)
                    <a
                        href="{{ $item['url'] }}"
                        class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 rounded px-2 py-1 min-h-[44px]"
                    >
                        @if($index === 0 && isset($item['icon']))
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                {!! $item['icon'] !!}
                            </svg>
                        @endif
                        {{ $label }}
                    </a>
                @else
                    <span class="text-sm font-medium text-gray-500">{{ $label }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif

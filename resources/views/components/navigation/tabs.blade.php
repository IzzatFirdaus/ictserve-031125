{{--
/**
 * Navigation Tabs Component
 * Description: Accessible tabbed navigation component with badges and icons
 *
 * @author Pasukan BPM MOTAC
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (UI Components)
 * @trace D14 §8 (Style Guide)
 *
 * @wcag WCAG 2.2 Level AA
 * @version 1.0.0
 * @created 2025-01-15
 */
--}}

@props([
    'tabs' => [],
    'activeTab' => null,
])

<div {{ $attributes->merge(['class' => 'border-b border-gray-200 dark:border-gray-700']) }} role="tablist">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        @foreach($tabs as $tab)
            <button
                type="button"
                role="tab"
                id="tab-{{ $tab['id'] }}"
                aria-selected="{{ $activeTab === $tab['id'] ? 'true' : 'false' }}"
                aria-controls="panel-{{ $tab['id'] }}"
                wire:click="$set('activeTab', '{{ $tab['id'] }}')"
                @class([
                    'group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 min-h-[44px]',
                    'border-blue-500 text-blue-600 dark:text-blue-400' => $activeTab === $tab['id'],
                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' => $activeTab !== $tab['id']
                ])
            >
                @if(isset($tab['icon']))
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        @switch($tab['icon'])
                            @case('home')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                @break
                            @case('check-circle')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                @break
                            @case('clock')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                @break
                            @case('document-text')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                @break
                            @default
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @endswitch
                    </svg>
                @endif

                <span>{{ $tab['label'] }}</span>

                @if(isset($tab['badge']) && $tab['badge'] > 0)
                    <span @class([
                        'ml-2 py-0.5 px-2 rounded-full text-xs font-medium',
                        'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300' => $activeTab === $tab['id'],
                        'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' => $activeTab !== $tab['id']
                    ])>
                        {{ $tab['badge'] }}
                    </span>
                @endif
            </button>
        @endforeach
    </nav>
</div>

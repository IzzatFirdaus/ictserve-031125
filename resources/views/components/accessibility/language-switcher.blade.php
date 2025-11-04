{{-- 
/**
 * Component name: Language Switcher
 * Description: Accessible bilingual language selector with session/cookie persistence.
 * Author: Pasukan BPM MOTAC
 * References: D03-FR-020, D04 section 7.3, D10 section 7, D12 section 9, D14 section 9.5
 * WCAG: 2.2 Level AA
 * Version: 1.0.0 (2025-11-03)
 */
--}}

@props(['variant' => 'light'])

@php
    $currentLocale = App::currentLocale();
    $languages = [
        'en' => [
            'label' => 'English',
            'abbr' => 'EN',
        ],
        'ms' => [
            'label' => 'Bahasa Melayu',
            'abbr' => 'MS',
        ],
    ];

    $buttonClasses = match ($variant) {
        'dark' => 'inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-4 py-2 text-sm font-medium text-slate-200 bg-slate-800 border border-slate-700 rounded-md hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-colors duration-150',
        default => 'inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors duration-150',
    };

    $menuClasses = match ($variant) {
        'dark' => 'absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-md bg-slate-900 shadow-xl ring-1 ring-slate-700 focus:outline-none',
        default => 'absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none',
    };

    $baseOptionClasses = match ($variant) {
        'dark' => 'group flex items-center min-h-[44px] px-4 py-2 text-sm text-slate-200 hover:bg-slate-800 hover:text-white focus:bg-slate-800 focus:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors duration-150',
        default => 'group flex items-center min-h-[44px] px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:bg-gray-100 focus:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-600 transition-colors duration-150',
    };

    $activeOptionClasses = 'bg-blue-600 text-white hover:bg-blue-600 hover:text-white';
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
    {{-- Language Switcher Button --}}
    <button type="button" @click="open = !open"
        class="{{ $buttonClasses }}"
        aria-haspopup="menu"
        :aria-expanded="open.toString()"
        aria-controls="language-menu"
        aria-label="{{ __('common.language_switcher') }}">
        {{-- Globe Icon --}}
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 5h12M9 3v2m1 14h9m-9 0a7 7 0 110-14 7 7 0 010 14zm0 0c1.657 0 3-3.134 3-7s-1.343-7-3-7-3 3.134-3 7 1.343 7 3 7z" />
        </svg>

        {{-- Desktop: Full Language Name --}}
        <span class="hidden sm:inline">
            {{ $languages[$currentLocale]['label'] ?? 'English' }}
        </span>

        {{-- Mobile: Abbreviated Language Code --}}
        <span class="inline sm:hidden font-semibold">
            {{ $languages[$currentLocale]['abbr'] ?? 'EN' }}
        </span>

        {{-- Dropdown Arrow --}}
        <svg class="w-4 h-4 ml-2 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none"
            stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="{{ $menuClasses }}"
        role="menu" id="language-menu" aria-orientation="vertical" tabindex="-1"
        style="display: none;">
        <div class="py-1" role="none">
            @foreach ($languages as $locale => $language)
                <a href="{{ route('change-locale', $locale) }}"
                    class="{{ trim($baseOptionClasses.' '.($currentLocale === $locale ? $activeOptionClasses : '')) }}"
                    role="menuitem" tabindex="-1" lang="{{ $locale }}" @click="open = false"
                    @if ($currentLocale === $locale) aria-current="page" @endif>
                    <span class="mr-3 font-semibold" aria-hidden="true">{{ $language['abbr'] }}</span>
                    <span class="flex-1">{{ $language['label'] }}</span>

                    @if ($currentLocale === $locale)
                        <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>

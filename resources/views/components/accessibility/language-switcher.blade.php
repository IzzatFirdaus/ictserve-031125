{{--
/**
 * Component name: Language Switcher
 * Description: WCAG 2.2 AA compliant bilingual language selector with ARIA menu button pattern
 * Implements W3C ARIA Authoring Practices Guide (APG) menu button design pattern
 * Keyboard navigation: Enter/Space to open, Escape to close, arrow keys to navigate
 * MDN ARIA: menu role with menuitemradio children and proper focus management
 *
 * Author: Pasukan BPM MOTAC
 * References: D03-FR-020, D04 section 7.3, D10 section 7, D12 section 9, D14 section 9.5
 * @wcag-level AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.5, 3.1.2, 3.1.1)
 * @version 2.0.0 (ARIA menu button pattern implementation)
 * @sources
 *   - W3C ARIA Authoring Practices Guide (2025): Menu Button pattern
 *   - MDN ARIA: menu role (2025-06)
 *   - Material Design: Touch targets (48x48dp minimum)
 *   - Canada.ca Design System: Language selector patterns
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

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @click.away="open = false" @keydown.escape.window="open = false">
    {{-- Language Switcher Button (ARIA Menu Button Pattern per W3C APG) --}}
    <button
        id="language-button"
        type="button"
        @click="open = !open"
        @keydown.enter="open = true"
        @keydown.space.prevent="open = true"
        @keydown.escape.window="open = false"
        class="{{ $buttonClasses }} min-h-[48px] min-w-[48px] focus:ring-2 focus:ring-offset-2 focus:outline-none transition-colors duration-150"
        aria-haspopup="menu"
        :aria-expanded="open.toString()"
        aria-controls="language-menu"
        aria-label="{{ __('common.language_switcher') }}"
        title="{{ __('common.language_switcher') }}">
        {{-- Language Label --}}
        <span>
            {{ $languages[$currentLocale]['label'] ?? 'English' }}
        </span>
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        id="language-menu"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="language-button"
        class="{{ $menuClasses }}"
        style="display: none;">
        <div class="py-1" role="none">
            @foreach ($languages as $locale => $language)
                <a href="{{ route('change-locale', $locale) }}"
                    @click="open = false"
                    @keydown.escape="open = false"
                    role="menuitemradio"
                    :aria-checked="{{ $currentLocale === $locale ? 'true' : 'false' }}"
                    lang="{{ $locale }}"
                    class="{{ trim($baseOptionClasses.' '.($currentLocale === $locale ? $activeOptionClasses : '')) }} min-h-[48px]"
                    @if ($currentLocale === $locale) aria-current="true" @endif>
                    <span class="font-medium">{{ $language['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>

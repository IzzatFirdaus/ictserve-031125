{{--
/**
 * Language Switcher Volt Component
 *
 * WCAG 2.2 AA compliant bilingual language selector
 * Single-file Volt component for simplified development
 *
 * @component LanguageSwitcher
 * @requirements 5.4, 6.1, 14.4, 15.1, 15.3, 20.5
 * @wcag-level AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.5, 3.1.2)
 * @version 1.0.0
 * @author Pasukan BPM MOTAC
 * @created 2025-11-03
 */
--}}

<?php

use function Livewire\Volt\{state};

// Current locale state
state(['currentLocale' => fn() => app()->getLocale()]);

// Change language method
$changeLanguage = function (string $locale) {
    if (!in_array($locale, ['en', 'ms'])) {
        return;
    }

    // Store in session
    session(['locale' => $locale]);

    // Store in cookie (1 year)
    cookie()->queue('locale', $locale, 60 * 24 * 365);

    // Apply to current request
    app()->setLocale($locale);

    // Update state
    $this->currentLocale = $locale;

    // Refresh page to apply language change
    $this->redirect(request()->header('Referer') ?? route('welcome'), navigate: true);
};

?>

<div x-data="{ open: false }" class="relative">
    {{-- Language Switcher Button --}}
    <button @click="open = !open" @keydown.escape.window="open = false" type="button"
        class="min-h-[44px] flex items-center space-x-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors"
        aria-haspopup="menu" :aria-expanded="open" aria-controls="language-menu"
        aria-label="{{ __('common.language_switcher') }}">
        {{-- Globe Icon --}}
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
        </svg>

        {{-- Current Language Label --}}
        <span class="hidden sm:inline">
            {{ $currentLocale === 'en' ? __('common.english') : __('common.malay') }}
        </span>
        <span class="sm:hidden">
            {{ $currentLocale === 'en' ? 'EN' : 'MS' }}
        </span>

        {{-- Dropdown Arrow --}}
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
            <path fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd" />
        </svg>
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95" id="language-menu" role="menu"
        aria-orientation="vertical" aria-labelledby="language-button"
        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
        style="display: none;">
        <div class="py-1">
            {{-- English Option --}}
            <button wire:click="changeLanguage('en')" @click="open = false" type="button" role="menuitem"
                lang="en"
                class="w-full text-left min-h-[44px] px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none {{ $currentLocale === 'en' ? 'bg-blue-50 font-semibold' : '' }}"
                @if ($currentLocale === 'en') aria-current="page" @endif>
                <span class="flex items-center">
                    <span class="mr-2">🇬🇧</span>
                    English
                    @if ($currentLocale === 'en')
                        <svg class="ml-auto w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    @endif
                </span>
            </button>

            {{-- Bahasa Melayu Option --}}
            <button wire:click="changeLanguage('ms')" @click="open = false" type="button" role="menuitem"
                lang="ms"
                class="w-full text-left min-h-[44px] px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none {{ $currentLocale === 'ms' ? 'bg-blue-50 font-semibold' : '' }}"
                @if ($currentLocale === 'ms') aria-current="page" @endif>
                <span class="flex items-center">
                    <span class="mr-2">🇲🇾</span>
                    Bahasa Melayu
                    @if ($currentLocale === 'ms')
                        <svg class="ml-auto w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    @endif
                </span>
            </button>
        </div>
    </div>
</div>

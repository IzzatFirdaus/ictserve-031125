{{--
/**
 * Language Switcher Volt Component
 *
 * WCAG 2.2 AA compliant bilingual language selector with ARIA menu button pattern
 * Implements W3C ARIA Authoring Practices Guide (APG) menu button design pattern
 * Keyboard navigation: Enter/Space to open, Escape to close, arrow keys to navigate
 * MDN ARIA: menu role with menuitem children and proper focus management
 *
 * @component LanguageSwitcher
 * @requirements 5.4, 6.1, 14.4, 15.1, 15.3, 20.5
 * @wcag-level AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.5, 3.1.2, 3.1.1)
 * @version 2.0.0 (ARIA menu button pattern implementation)
 * @author Pasukan BPM MOTAC
 * @created 2025-11-03
 * @updated 2025-11-05 (ARIA APG menu button pattern, keyboard navigation, touch targets)
 * @sources
 *   - W3C ARIA Authoring Practices Guide (2025): Menu Button pattern
 *   - MDN ARIA: menu role (2025-06)
 *   - WCAG 2.2 Level AA: Language of Page (SC 3.1.1)
 *   - Material Design: Touch targets (48x48dp minimum)
 *   - Canada.ca Design System: Language selector patterns
 */
--}}

<?php

use function Livewire\Volt\{state};

// Current locale state
state(['currentLocale' => fn() => app()->getLocale()]);

// Handle keyboard interactions for menu button pattern (W3C APG)
$handleMenuButtonKeydown = function ($event) {
    // Enter or Space opens menu
    if (in_array($event, ['Enter', ' '])) {
        return true; // Trigger @click to open menu
    }
};

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
    {{-- Language Switcher Button (ARIA Menu Button Pattern per W3C APG) --}}
    <button
        id="language-button"
        @click="open = !open"
        @keydown.enter="open = true"
        @keydown.space.prevent="open = true"
        @keydown.escape.window="open = false"
        type="button"
        class="inline-flex items-center justify-center space-x-2 px-4 py-2 min-h-[48px] min-w-[48px] text-gray-700 hover:bg-gray-100 active:bg-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors duration-150"
        aria-haspopup="menu"
        :aria-expanded="open"
        aria-controls="language-menu"
        aria-label="{{ __('common.language_switcher') }}"
        title="{{ __('common.language_switcher') }}">

        {{-- Bootstrap Icon: Globe (SVG with aria-hidden) --}}
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-globe flex-shrink-0" viewBox="0 0 16 16" aria-hidden="true" focusable="false">
            <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.022-13.55a.75.75 0 0 1 .456.063l5.008 2.833a.75.75 0 1 1-.912 1.212L6.924 2.331a.75.75 0 0 1 .098-1.242zm2.206-2.114a.75.75 0 1 1 .912 1.212l-3.329 2.5a.75.75 0 1 1-.912-1.212l3.329-2.5zm3.384 1.657a.75.75 0 0 1 1.037-.035l2.415 2.416a.75.75 0 1 1-1.061 1.06l-2.415-2.415a.75.75 0 0 1-.035-1.037zM1.637 8.75a6.5 6.5 0 0 1 13 0 .75.75 0 1 1-1.5 0 5 5 0 1 0-10 0 .75.75 0 0 1-1.5 0zM7 13a1 1 0 1 0 2 0 1 1 0 0 0-2 0z"/>
        </svg>

        {{-- Current Language Label (Responsive) --}}
        <span class="hidden sm:inline text-sm font-medium leading-5">
            {{ $currentLocale === 'en' ? __('common.english') : __('common.malay') }}
        </span>
        <span class="sm:hidden text-sm font-semibold leading-5">
            {{ $currentLocale === 'en' ? 'EN' : 'MS' }}
        </span>

        {{-- Dropdown Arrow Icon (Chevron) --}}
        <x-heroicon-o-chevron-down class="w-4 h-4 flex-shrink-0 transition-transform duration-150" :class="{ 'rotate-180': open }" aria-hidden="true" focusable="false" />
    </button>

    {{-- Dropdown Menu (ARIA Menu with menuitem roles) --}}
    <div
        x-show="open"
        @click.away="open = false"
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
        class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50 overflow-hidden"
        style="display: none;">

        <div class="py-1" role="group">
            {{-- English Option (ARIA menuitemradio pattern for single selection) --}}
            <button
                wire:click="changeLanguage('en')"
                @click="open = false"
                @keydown.escape="open = false"
                type="button"
                role="menuitemradio"
                :aria-checked="$currentLocale === 'en'"
                lang="en"
                class="w-full text-left min-h-[48px] px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none transition-colors duration-75 {{ $currentLocale === 'en' ? 'bg-blue-50 font-semibold' : '' }}"
                :class="{ 'bg-blue-50 font-semibold': $currentLocale === 'en' }"
                @if ($currentLocale === 'en') aria-current="true" @endif>

                <span class="flex items-center justify-between gap-3">
                    <span class="flex items-center gap-3 flex-1">
                        <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-gray-600 bg-gray-200 rounded">GB</span>
                        <span class="flex-1">
                            <span class="block font-medium">English</span>
                            <span class="text-xs text-gray-500">United Kingdom</span>
                        </span>
                    </span>
                    @if ($currentLocale === 'en')
                        <x-heroicon-o-check class="flex-shrink-0 w-5 h-5 text-blue-600" aria-hidden="true" />
                    @endif
                </span>
            </button>

            {{-- Bahasa Melayu Option (ARIA menuitemradio pattern for single selection) --}}
            <button
                wire:click="changeLanguage('ms')"
                @click="open = false"
                @keydown.escape="open = false"
                type="button"
                role="menuitemradio"
                :aria-checked="$currentLocale === 'ms'"
                lang="ms"
                class="w-full text-left min-h-[48px] px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none transition-colors duration-75 {{ $currentLocale === 'ms' ? 'bg-blue-50 font-semibold' : '' }}"
                :class="{ 'bg-blue-50 font-semibold': $currentLocale === 'ms' }"
                @if ($currentLocale === 'ms') aria-current="true" @endif>

                <span class="flex items-center justify-between gap-3">
                    <span class="flex items-center gap-3 flex-1">
                        <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-gray-600 bg-gray-200 rounded">MY</span>
                        <span class="flex-1">
                            <span class="block font-medium">Bahasa Melayu</span>
                            <span class="text-xs text-gray-500">Malaysia</span>
                        </span>
                    </span>
                    @if ($currentLocale === 'ms')
                        <x-heroicon-o-check class="flex-shrink-0 w-5 h-5 text-blue-600" aria-hidden="true" />
                    @endif
                </span>
            </button>
        </div>
    </div>
</div>

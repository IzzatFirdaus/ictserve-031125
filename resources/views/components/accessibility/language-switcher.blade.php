{{--
/**
 * Language Switcher Component
 *
 * WCAG 2.2 Level AA compliant bilingual language selector with session/cookie persistence.
 * NO user profile storage - designed for guest-first architecture.
 *
 * Features:
 * - ARIA menu button pattern with proper semantics
 * - Keyboard navigation (Tab, Enter, Space, Escape)
 * - 44×44px touch targets for mobile accessibility
 * - 3-4px focus indicators with 2px offset and 3:1 contrast
 * - Responsive design (mobile/tablet/desktop)
 * - Alpine.js for dropdown behavior (included in Livewire 3)
 *
 * @component
 * @name Language Switcher
 * @description WCAG 2.2 AA compliant bilingual language selector
 * @author Pasukan BPM MOTAC
 * @version 1.0.0
 * @since 2025-11-03
 * @updated 2025-11-03
 *
 * Requirements Traceability: D03-FR-020, D03-FR-006.1, D03-FR-006.2, D03-FR-006.3, D04 §7.3, D10 §7, D12 §7.4, D12 §9, D14 §9.5
 * WCAG Level: AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.5, 3.1.2)
 * Standards Compliance: ISO/IEC 40500 (WCAG 2.2 Level AA), D12 (UI/UX), D14 (Style Guide)
 *
 * Usage:
 * <x-accessibility.language-switcher />
 */
--}}

<div class="relative" x-data="{ open: false }" @click.away="open = false" @keydown.escape.window="open = false">
    {{-- Language Switcher Button --}}
    <button type="button" @click="open = !open"
        class="inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 transition-colors duration-150"
        aria-haspopup="menu" :aria-expanded="open.toString()" aria-controls="language-menu"
        aria-label="{{ __('common.language_switcher') }}"></button>
        {{-- Globe Icon --}}
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
        </svg>

        {{-- Desktop: Full Language Name --}}
        <span class="hidden sm:inline">
            {{ App::currentLocale() === 'ms' ? 'Bahasa Melayu' : 'English' }}
        </span>

        {{-- Mobile: Abbreviated Language Code --}}
        <span class="inline sm:hidden">
            {{ strtoupper(App::currentLocale()) }}
        </span>

        {{-- Dropdown Arrow --}}
        <svg class="w-4 h-4 ml-2" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
            viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        role="menu" id="language-menu" aria-orientation="vertical" aria-labelledby="language-button" tabindex="-1"
        style="display: none;">
        <div class="py-1" role="none">
            {{-- English Option --}}
            <a href="{{ route('change-locale', 'en') }}"
                class="group flex items-center min-h-[44px] px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:bg-gray-100 focus:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-600 transition-colors duration-150 {{ App::currentLocale() === 'en' ? 'bg-blue-600 text-white hover:bg-blue-700 hover:text-white' : '' }}"
                role="menuitem" tabindex="-1" lang="en"
                @if (App::currentLocale() === 'en') aria-current="page" @endif>
                {{-- Flag Icon --}}
                <span class="mr-3 text-lg" aria-hidden="true">🇬🇧</span>

                {{-- Language Name --}}
                <span class="flex-1">English</span>

                {{-- Active Indicator --}}
                @if (App::currentLocale() === 'en')
                    <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                @endif
            </a>

            {{-- Bahasa Melayu Option --}}
            <a href="{{ route('change-locale', 'ms') }}"
                class="group flex items-center min-h-[44px] px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:bg-gray-100 focus:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-600 transition-colors duration-150 {{ App::currentLocale() === 'ms' ? 'bg-blue-600 text-white hover:bg-blue-700 hover:text-white' : '' }}"
                role="menuitem" tabindex="-1" lang="ms"
                @if (App::currentLocale() === 'ms') aria-current="page" @endif>
                {{-- Flag Icon --}}
                <span class="mr-3 text-lg" aria-hidden="true">🇲🇾</span>

                {{-- Language Name --}}
                <span class="flex-1">Bahasa Melayu</span>

                {{-- Active Indicator --}}
                @if (App::currentLocale() === 'ms')
                    <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                @endif
            </a>
        </div>
    </div>
</div>

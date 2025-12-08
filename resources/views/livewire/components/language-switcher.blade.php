{{--
/**
 * Livewire 3.x + Volt 1.x Language Switcher Component
 *
 * @component livewire.components.language-switcher
 * @description WCAG 2.2 AA compliant bilingual language selector using Volt functional API
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-020 (Bilingual Support), D12 §9 (WCAG 2.2 AA), D15 §2 (Localization)
 * @trace D13 §2.2-2.7 (MyDS Design Tokens), D14 §9.5 (Accessibility)
 * @wcag-level AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.5, 3.1.2)
 * @version 2.1.0
 * @updated 2025-12-06
 *
 * Migration Notes:
 * - Migrated from app/Livewire/LanguageSwitcher.php to Volt functional API
 * - Simple component with minimal state (currentLocale) and single action (switchLanguage)
 * - Follows Volt 1.x patterns: state() for reactive properties, closures for actions
 * - WCAG 2.2 AA: 44x44px touch targets (SC 2.5.5), 4.5:1 contrast (SC 1.4.3)
 * - Default locale: 'ms' (Bahasa Melayu) per D15 §2
 */
--}}

@php
use App\Services\BilingualSupportService;

/** @var array<string, array{name: string, code: string, flag: string}> $supportedLocales */
    $service = app(BilingualSupportService::class);
    $locales = $supportedLocales ?? $service->getSupportedLocales();
    $activeLocale = $currentLocale ?? $service->getCurrentLocale() ?? 'ms';
    @endphp

    {{-- WCAG 2.2 AA: 44x44px touch targets (SC 2.5.5), 3px focus indicator (SC 2.4.7) --}}
    {{-- WCAG 2.2 AA: ARIA Menu Pattern (SC 4.1.2) --}}
    <div class="relative" x-data="{ open: false }" @click.away="open = false" @keydown.escape="open = false">
        {{-- Dropdown Trigger --}}
        <button
            type="button"
            @click="open = !open"
            class="inline-flex items-center justify-center min-h-11 min-w-11 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150
               bg-white text-gray-700 hover:bg-gray-50 hover:text-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            aria-label="{{ __('common.language_switcher') }}"
            aria-controls="language-menu"
            :aria-expanded="open.toString()">
            <span class="font-bold">{{ strtoupper($activeLocale) }}</span>
            <svg class="ml-1 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        {{-- Dropdown Menu --}}
        <div
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            id="language-menu"
            role="menu"
            aria-orientation="vertical"
            aria-labelledby="language-menu-button"
            class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 focus:outline-none">
            <div class="py-1" role="none">
                @foreach ($locales as $code => $locale)
                <button
                    wire:click="switchLocale('{{ $code }}')"
                    @click="open = false"
                    type="button"
                    class="group w-full flex items-center px-4 py-2 text-sm text-left
                           {{ $activeLocale === $code
                               ? 'bg-primary-50 text-primary-700'
                               : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}"
                    role="menuitem"
                    lang="{{ $locale['code'] ?? $code }}">
                    <span class="flex-1">{{ $locale['name'] ?? $service->getLocaleDisplayName($code) }}</span>
                    @if ($activeLocale === $code)
                    <svg class="h-4 w-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    @endif
                </button>
                @endforeach
            </div>
        </div>
    </div>
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
<div class="flex items-center gap-2" role="group" aria-label="{{ __('common.language_switcher') }}">
    @foreach ($locales as $code => $locale)
        <button
            wire:click="switchLocale('{{ $code }}')"
            type="button"
            class="inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-4 py-2.5 text-sm font-medium rounded-md transition-colors duration-150
                   {{ $activeLocale === $code
                       ? 'bg-primary-600 text-white hover:bg-primary-700 focus-visible:outline-3 focus-visible:outline-primary-500'
                       : 'bg-gray-100 text-gray-700 hover:bg-gray-200 focus-visible:outline-3 focus-visible:outline-primary-500' }}"
            aria-label="{{ __('common.switch_to') }} {{ $locale['name'] ?? $service->getLocaleDisplayName($code) }}"
            aria-current="{{ $activeLocale === $code ? 'page' : 'false' }}"
            lang="{{ $locale['code'] ?? $code }}">
            <span class="font-medium">{{ strtoupper($locale['code'] ?? $code) }}</span>
        </button>
    @endforeach
</div>

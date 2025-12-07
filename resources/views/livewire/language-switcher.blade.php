{{--
/**
 * Livewire 3.x Language Switcher Component
 *
 * @component livewire.language-switcher
 * @description WCAG 2.2 AA compliant bilingual language selector (Class-based Livewire)
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-020 (Bilingual Support), D12 §9 (WCAG 2.2 AA), D15 §2 (Localization)
 * @trace D13 §2.2-2.7 (MyDS Design Tokens), D14 §10.5 (ARIA Patterns)
 * @wcag-level AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.5, 3.1.2, 4.1.2)
 * @version 3.0.0
 * @updated 2025-12-08
 *
 * Compliance Notes:
 * - D13 §1.1: Server-first architecture (Livewire, not Alpine.js)
 * - SC 2.5.5: 44×44px minimum touch targets
 * - SC 2.4.7: 3px focus visible outline (MyDS tokens)
 * - SC 4.1.2: aria-current="page" (not "true") per ARIA 1.2 spec
 * - SC 3.1.2: lang attribute on buttons for correct pronunciation
 * - D15 §2.1: Bahasa Melayu (ms) as default/primary language
 */
--}}

@php
    use App\Services\BilingualSupportService;

    /** @var array<string, array{name: string, code: string, flag: string}> $supportedLocales */
    /** @var string $currentLocale */
    $service = app(BilingualSupportService::class);
    $locales = $supportedLocales ?? $service->getSupportedLocales();
    $activeLocale = $currentLocale ?? 'ms';
@endphp

{{-- WCAG 2.2 AA Compliant Language Switcher --}}
<div class="flex items-center gap-2" role="group" aria-label="{{ __('common.language_switcher') }}">
    @foreach ($locales as $code => $locale)
        <button
            wire:click="switchLocale('{{ $code }}')"
            type="button"
            class="inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-4 py-2.5 text-sm font-medium rounded-md transition-colors duration-150 focus-visible:outline focus-visible:outline-3 focus-visible:outline-offset-2
                   {{ $activeLocale === $code
                       ? 'bg-primary-600 text-white hover:bg-primary-700 focus-visible:outline-primary-500'
                       : 'bg-gray-100 text-gray-700 hover:bg-gray-200 focus-visible:outline-gray-500' }}"
            aria-label="{{ __('common.switch_to', ['language' => $locale['name'] ?? $service->getLocaleDisplayName($code)]) }}"
            aria-current="{{ $activeLocale === $code ? 'page' : 'false' }}"
            lang="{{ $locale['code'] ?? $code }}">
            <span class="font-medium">{{ strtoupper($locale['code'] ?? $code) }}</span>
        </button>
    @endforeach

    {{-- ARIA live region for screen reader announcements (SC 4.1.3) --}}
    <span class="sr-only" role="status" aria-live="polite" aria-atomic="true">
        {{ __('common.current_language') }}: {{ $locales[$activeLocale]['name'] ?? 'Unknown' }}
    </span>
</div>


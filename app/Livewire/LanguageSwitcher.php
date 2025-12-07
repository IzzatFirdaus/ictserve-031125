<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\BilingualSupportService;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Contracts\View\View;

/**
 * Language Switcher Component
 *
 * @trace D03-FR-020 (Bilingual Support)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D15 §2 (Localization Standards)
 * @version 3.0.0
 */
class LanguageSwitcher extends Component
{
    /**
     * Switch application locale with proper state management
     *
     * @param string $locale Target locale code ('ms' or 'en')
     */
    public function switchLocale(string $locale, BilingualSupportService $bilingualService): void
    {
        if (! \in_array($locale, ['en', 'ms'], true)) {
            return;
        }

        // Use service layer for consistent locale management
        $bilingualService->setLocale($locale);

        // Dispatch event for other components
        $this->dispatch('localeChanged', locale: $locale);

        // Redirect with cache-busting timestamp to force fresh content
        $currentUrl = request()->header('Referer') ?: '/';
        $separator = str_contains($currentUrl, '?') ? '&' : '?';
        $redirectUrl = $currentUrl . $separator . '_locale=' . time();

        $this->redirect($redirectUrl, navigate: false);
    }

    #[Computed]
    public function currentLocale(): string
    {
        return App::getLocale();
    }

    #[Computed]
    public function supportedLocales(): array
    {
        return app(BilingualSupportService::class)->getSupportedLocales();
    }

    public function render(): View
    {
        return view('livewire.language-switcher', [
            'supportedLocales' => $this->supportedLocales(),
            'currentLocale' => $this->currentLocale(),
        ]);
    }
}

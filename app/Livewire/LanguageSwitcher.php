<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\BilingualSupportService;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public string $currentLocale;

    public function mount(BilingualSupportService $service): void
    {
        $this->currentLocale = $service->getCurrentLocale();
    }

    public function switchLanguage(string $locale, BilingualSupportService $service): void
    {
        $service->switchLocale($locale);
        $this->currentLocale = $locale;

        // Refresh the page to apply new locale
        $this->redirect(request()->header('Referer') ?? '/');
    }

    public function render(BilingualSupportService $service)
    {
        return view('livewire.language-switcher', [
            'locales' => $service->getSupportedLocales(),
            'getDisplayName' => fn ($locale) => $service->getLocaleDisplayName($locale),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public string $currentLocale;

    public function mount(): void
    {
        $this->currentLocale = App::getLocale();
    }

    public function switchLocale(string $locale): void
    {
        if (! \in_array($locale, ['en', 'ms'], true)) {
            return;
        }

        // Store in session (immediate)
        Session::put('locale', $locale);

        // Store in cookie (12 months persistence)
        Cookie::queue('locale', $locale, 525600);

        // Apply to current request
        App::setLocale($locale);
        $this->currentLocale = $locale;

        $this->dispatch('localeChanged', locale: $locale);
        $this->redirect(request()->header('Referer') ?: '/', navigate: true);
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}

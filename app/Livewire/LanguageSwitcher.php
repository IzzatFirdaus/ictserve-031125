<?php

namespace App\Livewire;

use Illuminate\Support\Facades\App;
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
        if (! in_array($locale, ['en', 'ms'])) {
            return;
        }

        Session::put('locale', $locale);
        App::setLocale($locale);
        $this->currentLocale = $locale;
        
        $this->dispatch('localeChanged', $locale);
        $this->redirect(request()->header('Referer'), navigate: true);
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}

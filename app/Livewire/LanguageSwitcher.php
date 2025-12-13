<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

final class LanguageSwitcher extends Component
{
    public string $locale;

    public function mount(): void
    {
        $this->locale = config('app.locale', 'ms');
        App::setLocale($this->locale);
        Session::put('locale', $this->locale);
    }

    public function switchLocale(?string $locale = null): void
    {
        // v3.6.0: Language switching is deprecated; force Bahasa Melayu.
        $this->locale = config('app.locale', 'ms');

        App::setLocale($this->locale);
        Session::put('locale', $this->locale);
    }

    public function render(): View
    {
        return view('livewire.language-switcher');
    }
}

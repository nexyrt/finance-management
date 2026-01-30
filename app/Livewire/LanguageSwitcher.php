<?php

namespace App\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public function switchLanguage($locale)
    {
        // Validate locale
        if (!in_array($locale, config('app.available_locales', ['id', 'zh']))) {
            return;
        }

        // Set locale in session
        Session::put('locale', $locale);
        App::setLocale($locale);

        // If user is authenticated, save preference to database
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }

        // Refresh the page to apply new locale
        return redirect()->to(request()->header('Referer') ?? route('dashboard'));
    }

    public function render()
    {
        return view('livewire.language-switcher', [
            'currentLocale' => App::getLocale(),
            'availableLocales' => [
                'id' => [
                    'name' => 'Indonesia',
                    'flag' => '🇮🇩',
                    'code' => 'id',
                ],
                'zh' => [
                    'name' => '中文',
                    'flag' => '🇨🇳',
                    'code' => 'zh',
                ],
            ],
        ]);
    }
}

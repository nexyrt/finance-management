<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use TallStackUi\Facades\TallStackUi;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Prevent lazy loading in non-production to catch N+1 queries
        Model::preventLazyLoading(!app()->isProduction());

        // TallStackUI Component Personalization - Professional Blue Theme
        TallStackUi::personalize()
            ->modal()
            ->block('wrapper.first', 'fixed inset-0 bg-black/30 transform transition-opacity')
            ->and()
            ->modal()
            ->block('wrapper.third')
            ->replace('p-4', 'p-4 pt-16 sm:pt-4')
            ->and()
            ->card()
            ->block('wrapper.second')
            ->replace([
                'shadow-md' => 'border border-zinc-200 dark:border-dark-600 shadow-sm hover:shadow-md transition-shadow duration-150',
                'rounded-lg' => 'rounded-xl',
            ])
            ->and()
            ->floating()
            ->block('wrapper')
            ->replace('z-40', 'z-[55]')
            ->and()
            ->dropdown()
            ->block('floating.class')
            ->replace('w-56', 'w-80 sm:w-96')
            ->and()
            ->dropdown()
            ->block('floating.default')
            ->replace('z-40', 'z-[9999]');

        // Set locale from session or user preference with fallback
        $availableLocales = config('app.available_locales', ['id', 'zh']);
        $locale = session('locale');

        // If locale is not available, fallback to 'id'
        if (!$locale || !in_array($locale, $availableLocales)) {
            $locale = 'id';
        }

        // If user is authenticated, use their preference
        if (auth()->check() && auth()->user()->locale) {
            $userLocale = auth()->user()->locale;
            // Only use user locale if it's available
            if (in_array($userLocale, $availableLocales)) {
                $locale = $userLocale;
            } else {
                // Update user's locale to fallback
                auth()->user()->update(['locale' => 'id']);
            }
        }

        App::setLocale($locale);

        // Set Carbon locale for date translations
        // Map locale to Carbon locale codes
        $carbonLocale = match ($locale) {
            'zh' => 'zh_CN', // Chinese Simplified
            'id' => 'id',    // Indonesian
            default => 'id',
        };
        Carbon::setLocale($carbonLocale);
    }
}

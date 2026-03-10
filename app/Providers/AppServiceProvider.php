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

        // TallStackUI Component Personalization
        TallStackUi::personalize()
            // Modal
            ->modal()
            ->block('wrapper.first', 'fixed inset-0 bg-black/30 transform transition-opacity')
            ->and()
            ->modal()
            ->block('wrapper.third')
            ->replace('p-4', 'p-4 pt-16 sm:pt-4')
            ->and()
            ->modal()
            ->block('wrapper.fourth')
            ->replace('dark:bg-dark-700', 'dark:bg-[#1e1e1e]')
            ->and()
            ->modal()
            ->block('footer')
            ->replace('dark:bg-dark-700', 'dark:bg-[#1e1e1e]')
            ->and()
            // Card
            ->card()
            ->block('wrapper.second')
            ->replace([
                'shadow-md'        => 'border border-zinc-200 dark:border-white/8 shadow-sm hover:shadow-md transition-shadow duration-150',
                'rounded-lg'       => 'rounded-xl',
                'dark:bg-dark-700' => 'dark:bg-[#1e1e1e]',
            ])
            ->and()
            // Slide
            ->slide()
            ->block('wrapper.fifth')
            ->replace('dark:bg-dark-700', 'dark:bg-[#1e1e1e]')
            ->and()
            ->slide()
            ->block('footer')
            ->replace('dark:bg-dark-700', 'dark:bg-[#1e1e1e]')
            ->and()
            // Table
            ->table()
            ->block('wrapper')
            ->replace('dark:ring-dark-600', 'dark:ring-white/8')
            ->and()
            ->table()
            ->block('table.tbody')
            ->replace('dark:bg-dark-700', 'dark:bg-[#1e1e1e]')
            ->and()
            ->table()
            ->block('table.thead.normal')
            ->replace('dark:bg-dark-600', 'dark:bg-[#161618]')
            ->and()
            // Form inputs - background
            ->form('input')
            ->block('input.color.background')
            ->replace('dark:bg-dark-800', 'dark:bg-[#27272a]')
            ->and()
            ->form('textarea')
            ->block('input.color.background')
            ->replace('dark:bg-dark-800', 'dark:bg-[#27272a]')
            ->and()
            ->select('styled')
            ->block('input.wrapper.base')
            ->replace([
                'dark:bg-dark-800'          => 'dark:bg-[#27272a]',
                'dark:ring-dark-600'        => 'dark:ring-white/10',
                'dark:disabled:bg-dark-600' => 'dark:disabled:bg-[#1e1e1e]',
            ])
            ->and()
            ->select('styled')
            ->block('floating.default')
            ->replace('dark:bg-dark-700', 'dark:bg-[#1e1e1e]')
            ->and()
            ->select('native')
            ->block('input.color.background')
            ->replace('dark:bg-dark-800', 'dark:bg-[#27272a]')
            ->and()
            ->form('date')
            ->block('floating.default')
            ->replace('dark:bg-dark-700', 'dark:bg-[#1e1e1e]')
            ->and()
            // Floating (dropdown, tooltip, etc)
            ->floating()
            ->block('wrapper')
            ->replace([
                'z-40'             => 'z-55',
                'dark:bg-dark-700' => 'dark:bg-[#1e1e1e]',
            ])
            ->and()
            ->dropdown()
            ->block('floating.class')
            ->replace('w-56', 'w-80 sm:w-96')
            ->and()
            ->dropdown()
            ->block('floating.default')
            ->replace([
                'z-40'             => 'z-9999',
                'dark:bg-dark-700' => 'dark:bg-[#1e1e1e]',
            ]);

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

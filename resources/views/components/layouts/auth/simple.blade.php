<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-dark-800 antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center p-6 md:p-10">
            <div class="w-full max-w-md">
                {{-- Logo Section --}}
                <div class="flex flex-col items-center mb-8">
                    <a href="{{ route('home') }}" class="flex flex-col items-center gap-3" wire:navigate>
                        <div class="h-16 w-16 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                            <x-app-logo-icon class="h-10 w-10 text-primary-600 dark:text-primary-400" />
                        </div>
                        <span class="text-xl font-bold text-dark-900 dark:text-dark-50">
                            {{ config('app.name', 'Laravel') }}
                        </span>
                    </a>
                </div>

                {{-- Auth Card --}}
                <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl shadow-sm p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <tallstack-ui:script />
    </body>
</html>

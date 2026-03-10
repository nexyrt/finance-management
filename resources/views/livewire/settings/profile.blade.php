<section class="w-full">
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-linear-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('common.settings') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                {{ __('pages.settings_description') }}
            </p>
        </div>

        {{-- Settings Layout --}}
        <div class="flex flex-col md:flex-row gap-8">
            {{-- Left Nav --}}
            <nav class="flex flex-row md:flex-col gap-1 md:w-48 shrink-0">
                <a href="{{ route('settings.profile') }}" wire:navigate
                   class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('settings.profile') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-dark-700 dark:text-dark-300 hover:bg-gray-100 dark:hover:bg-dark-700' }}">
                    {{ __('common.profile') }}
                </a>
                <a href="{{ route('settings.password') }}" wire:navigate
                   class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('settings.password') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-dark-700 dark:text-dark-300 hover:bg-gray-100 dark:hover:bg-dark-700' }}">
                    {{ __('common.password') }}
                </a>
                <a href="{{ route('settings.company') }}" wire:navigate
                   class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('settings.company') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-dark-700 dark:text-dark-300 hover:bg-gray-100 dark:hover:bg-dark-700' }}">
                    {{ __('common.company') }}
                </a>
            </nav>

            {{-- Content --}}
            <div class="flex-1 max-w-lg">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4 mb-5">
                    <h2 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('common.profile') }}</h2>
                    <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.settings_profile_description') }}</p>
                </div>

                <form wire:submit="updateProfileInformation" class="space-y-5">
                    <x-input wire:model="name" :label="__('common.name')" required autofocus autocomplete="name" />
                    <x-input wire:model="email" :label="__('common.email')" type="email" required autocomplete="email" />

                    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                        <div class="rounded-xl p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800">
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                {{ __('pages.email_unverified') }}
                                <button wire:click.prevent="resendVerificationNotification"
                                    class="font-medium underline hover:no-underline cursor-pointer">
                                    {{ __('pages.resend_verification') }}
                                </button>
                            </p>

                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 text-sm font-medium text-green-600 dark:text-green-400">
                                    {{ __('pages.verification_link_sent') }}
                                </p>
                            @endif
                        </div>
                    @endif

                    <div class="flex items-center gap-4 pt-2">
                        <x-button type="submit" color="primary" loading="updateProfileInformation">
                            {{ __('common.save') }}
                        </x-button>
                        <x-action-message on="profile-updated">{{ __('common.saved_successfully') }}</x-action-message>
                    </div>
                </form>

                {{-- Delete Account Section --}}
                <div class="mt-10">
                    <livewire:settings.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</section>

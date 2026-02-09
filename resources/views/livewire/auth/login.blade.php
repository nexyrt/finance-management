<div>
    <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-4">
        <!-- Email Address -->
        <x-input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-dark-900 dark:text-dark-50">
                    {{ __('Password') }}
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors"
                       wire:navigate>
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>
            <x-password
                wire:model="password"
                required
                autocomplete="current-password"
                :placeholder="__('Password')"
            />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <x-checkbox wire:model="remember" id="remember" />
            <label for="remember" class="ml-2 text-sm text-dark-600 dark:text-dark-400 cursor-pointer">
                {{ __('Remember me') }}
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <x-button type="submit" color="primary" class="w-full" loading="login">
                {{ __('Log in') }}
            </x-button>
        </div>
    </form>

    {{-- @if (Route::has('register'))
        <div class="mt-6 text-center text-sm text-dark-600 dark:text-dark-400">
            {{ __('Don\'t have an account?') }}
            <a href="{{ route('register') }}"
               class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium transition-colors"
               wire:navigate>
                {{ __('Sign up') }}
            </a>
        </div>
    @endif --}}
</div>

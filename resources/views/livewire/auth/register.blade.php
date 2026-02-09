<div>
    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center mb-4" :status="session('status')" />

    <form wire:submit="register" class="space-y-4">
        <!-- Name -->
        <x-input
            wire:model="name"
            :label="__('Name')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('Full name')"
        />

        <!-- Email Address -->
        <x-input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <x-password
            wire:model="password"
            :label="__('Password')"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
        />

        <!-- Confirm Password -->
        <x-password
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
        />

        <!-- Submit Button -->
        <div class="pt-2">
            <x-button type="submit" color="primary" class="w-full" loading="register">
                {{ __('Create account') }}
            </x-button>
        </div>
    </form>

    <div class="mt-6 text-center text-sm text-dark-600 dark:text-dark-400">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}"
           class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium transition-colors"
           wire:navigate>
            {{ __('Log in') }}
        </a>
    </div>
</div>

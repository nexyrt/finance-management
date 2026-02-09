<div>
    <x-auth-header :title="__('Forgot password')" :description="__('Enter your email to receive a password reset link')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="space-y-4">
        <!-- Email Address -->
        <x-input
            wire:model="email"
            :label="__('Email Address')"
            type="email"
            required
            autofocus
            placeholder="email@example.com"
        />

        <!-- Submit Button -->
        <div class="pt-2">
            <x-button type="submit" color="primary" class="w-full" loading="sendPasswordResetLink">
                {{ __('Email password reset link') }}
            </x-button>
        </div>
    </form>

    <div class="mt-6 text-center text-sm text-dark-600 dark:text-dark-400">
        {{ __('Or, return to') }}
        <a href="{{ route('login') }}"
           class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium transition-colors"
           wire:navigate>
            {{ __('log in') }}
        </a>
    </div>
</div>

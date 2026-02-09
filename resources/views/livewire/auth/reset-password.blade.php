<div>
    <x-auth-header :title="__('Reset password')" :description="__('Please enter your new password below')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center mb-4" :status="session('status')" />

    <form wire:submit="resetPassword" class="space-y-4">
        <!-- Email Address -->
        <x-input
            wire:model="email"
            :label="__('Email')"
            type="email"
            required
            autocomplete="email"
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
            <x-button type="submit" color="primary" class="w-full" loading="resetPassword">
                {{ __('Reset password') }}
            </x-button>
        </div>
    </form>
</div>

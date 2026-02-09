<div>
    <x-auth-header
        :title="__('Confirm password')"
        :description="__('This is a secure area of the application. Please confirm your password before continuing.')"
    />

    <!-- Session Status -->
    <x-auth-session-status class="text-center mb-4" :status="session('status')" />

    <form wire:submit="confirmPassword" class="space-y-4">
        <!-- Password -->
        <x-password
            wire:model="password"
            :label="__('Password')"
            required
            autocomplete="current-password"
            :placeholder="__('Password')"
        />

        <!-- Submit Button -->
        <div class="pt-2">
            <x-button type="submit" color="primary" class="w-full" loading="confirmPassword">
                {{ __('Confirm') }}
            </x-button>
        </div>
    </form>
</div>

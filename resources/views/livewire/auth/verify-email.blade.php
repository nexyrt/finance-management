<div class="space-y-6">
    <p class="text-center text-dark-600 dark:text-dark-400">
        {{ __('Please verify your email address by clicking on the link we just emailed to you.') }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
            <p class="text-center text-sm font-medium text-green-600 dark:text-green-400">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </p>
        </div>
    @endif

    <div class="space-y-3">
        <x-button wire:click="sendVerification" color="primary" class="w-full" loading="sendVerification">
            {{ __('Resend verification email') }}
        </x-button>

        <div class="text-center">
            <button wire:click="logout"
                    class="text-sm text-dark-600 dark:text-dark-400 hover:text-dark-900 dark:hover:text-dark-50 transition-colors cursor-pointer">
                {{ __('Log out') }}
            </button>
        </div>
    </div>
</div>

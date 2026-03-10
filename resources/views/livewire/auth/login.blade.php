<div>
    <x-auth-header :title="__('Selamat Datang')" :description="__('Masuk ke akun Anda untuk melanjutkan')" />

    <!-- Session Status -->
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <!-- Email Address -->
        <x-input
            wire:model="email"
            :label="__('Alamat Email')"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@perusahaan.com"
        />

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-sm font-medium text-dark-700 dark:text-dark-200">
                    {{ __('Password') }}
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-xs text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors font-medium"
                       wire:navigate>
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>
            <x-password
                wire:model="password"
                required
                autocomplete="current-password"
                :placeholder="__('Masukkan password')"
            />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center gap-2.5">
            <x-checkbox wire:model="remember" id="remember" />
            <label for="remember" class="text-sm text-dark-600 dark:text-dark-400 cursor-pointer select-none">
                {{ __('Ingat saya selama 30 hari') }}
            </label>
        </div>

        <!-- Divider -->
        <div class="h-px bg-linear-to-r from-transparent via-slate-200 dark:via-dark-600 to-transparent"></div>

        <!-- Submit Button -->
        <x-button type="submit" color="primary" class="w-full" loading="login">
            {{ __('Masuk ke Dasbor') }}
        </x-button>
    </form>

    {{-- @if (Route::has('register'))
        <div class="mt-7 text-center text-sm text-dark-500 dark:text-dark-400">
            {{ __('Belum punya akun?') }}
            <a href="{{ route('register') }}"
               class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium transition-colors"
               wire:navigate>
                {{ __('Daftar sekarang') }}
            </a>
        </div>
    @endif --}}
</div>

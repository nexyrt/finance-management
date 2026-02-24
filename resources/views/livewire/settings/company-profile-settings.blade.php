<section class="w-full" x-data x-on:keydown.ctrl.enter.window="$wire.call('updateCompanyProfile')">
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('common.settings') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                {{ __('pages.settings_description') }}
            </p>
        </div>

        {{-- Settings Layout --}}
        <div class="flex flex-col md:flex-row gap-8">
            {{-- Left Nav --}}
            <nav class="flex flex-row md:flex-col gap-1 md:w-48 flex-shrink-0">
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
                    <h2 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('common.company_profile') }}</h2>
                    <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.settings_company_description') }}</p>
                </div>

                <form wire:submit="updateCompanyProfile" class="space-y-6">
                    <x-input wire:model="name" :label="__('pages.company_name')" required />
                    <x-textarea wire:model="address" :label="__('common.address')" rows="3" required />

                    <div class="grid grid-cols-2 gap-4">
                        <x-input wire:model="email" :label="__('common.email')" type="email" required />
                        <x-input wire:model="phone" :label="__('common.phone')" required />
                    </div>

                    <hr class="border-gray-200 dark:border-dark-600" />

                    <div class="grid grid-cols-2 gap-4">
                        <x-input wire:model="finance_manager_name" :label="__('pages.finance_manager')" required />
                        <x-input wire:model="finance_manager_position" :label="__('pages.position')" required />
                    </div>

                    <hr class="border-gray-200 dark:border-dark-600" />

                    <x-checkbox wire:model.boolean="is_pkp" :label="__('pages.pkp_label')" />

                    @if ($is_pkp)
                        <div class="grid grid-cols-2 gap-4">
                            <x-input wire:model="npwp" :label="__('pages.npwp')" />
                            <x-input wire:model="ppn_rate" :label="__('pages.ppn_rate')" type="number" step="0.01" />
                        </div>
                    @endif

                    <hr class="border-gray-200 dark:border-dark-600" />

                    {{-- Logo --}}
                    <div class="space-y-2">
                        @if ($currentLogo)
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3 cursor-pointer"
                                        wire:click="$set('showLogoModal', true)">
                                        <div>
                                            <p class="text-sm font-medium text-blue-900 dark:text-blue-100">logo.png</p>
                                            <p class="text-xs text-blue-700 dark:text-blue-300">{{ __('pages.click_to_preview') }}</p>
                                        </div>
                                    </div>
                                    <x-button color="red" sm wire:click="deleteExistingLogo">{{ __('common.delete') }}</x-button>
                                </div>
                            </div>
                        @endif
                        <x-file-upload wire:model="logo" :label="$currentLogo ? __('pages.replace_logo') : __('pages.logo')" accept="image/jpeg,image/jpg,image/png" />
                    </div>

                    {{-- Letter Head --}}
                    <div class="space-y-2">
                        @if ($currentLetterHead)
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3 cursor-pointer"
                                        wire:click="$set('showLetterHeadModal', true)">
                                        <div>
                                            <p class="text-sm font-medium text-blue-900 dark:text-blue-100">letter-head.png</p>
                                            <p class="text-xs text-blue-700 dark:text-blue-300">{{ __('pages.click_to_preview') }}</p>
                                        </div>
                                    </div>
                                    <x-button color="red" sm wire:click="deleteExistingLetterHead">{{ __('common.delete') }}</x-button>
                                </div>
                            </div>
                        @endif
                        <x-file-upload wire:model="letterHead" :label="$currentLetterHead ? __('pages.replace_letter_head') : __('pages.letter_head')" accept="image/jpeg,image/jpg,image/png" />
                    </div>

                    {{-- Signature --}}
                    <div class="space-y-2">
                        @if ($currentSignature)
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3 cursor-pointer"
                                        wire:click="$set('showSignatureModal', true)">
                                        <div>
                                            <p class="text-sm font-medium text-blue-900 dark:text-blue-100">pdf-signature.png</p>
                                            <p class="text-xs text-blue-700 dark:text-blue-300">{{ __('pages.click_to_preview') }}</p>
                                        </div>
                                    </div>
                                    <x-button color="red" sm wire:click="deleteExistingSignature">{{ __('common.delete') }}</x-button>
                                </div>
                            </div>
                        @endif
                        <x-file-upload wire:model="signature" :label="$currentSignature ? __('pages.replace_signature') : __('pages.signature')" accept="image/jpeg,image/jpg,image/png" />
                    </div>

                    {{-- Stamp --}}
                    <div class="space-y-2">
                        @if ($currentStamp)
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3 cursor-pointer"
                                        wire:click="$set('showStampModal', true)">
                                        <div>
                                            <p class="text-sm font-medium text-blue-900 dark:text-blue-100">kisantra-stamp.png</p>
                                            <p class="text-xs text-blue-700 dark:text-blue-300">{{ __('pages.click_to_preview') }}</p>
                                        </div>
                                    </div>
                                    <x-button color="red" sm wire:click="deleteExistingStamp">{{ __('common.delete') }}</x-button>
                                </div>
                            </div>
                        @endif
                        <x-file-upload wire:model="stamp" :label="$currentStamp ? __('pages.replace_stamp') : __('pages.stamp')" accept="image/jpeg,image/jpg,image/png" />
                    </div>

                    {{-- Favicon info --}}
                    @if ($currentLogo)
                        <div class="rounded-xl p-4 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-primary-900 dark:text-primary-100 mb-1">
                                        {{ __('pages.generate_favicons') }}
                                    </p>
                                    <p class="text-xs text-primary-700 dark:text-primary-300 mb-2">
                                        {{ __('pages.generate_favicons_hint') }}
                                    </p>
                                    <code class="block px-3 py-2 bg-white dark:bg-dark-800 rounded-lg text-xs font-mono text-primary-600 dark:text-primary-400 border border-primary-200 dark:border-primary-700">
                                        php artisan favicon:generate
                                    </code>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-3 pt-2">
                        <x-button color="primary" type="submit">{{ __('common.save') }}</x-button>
                        <span class="text-xs text-dark-400 dark:text-dark-500">
                            <kbd class="px-1.5 py-0.5 text-[10px] font-mono bg-dark-100 dark:bg-dark-700 border border-dark-200 dark:border-dark-600 rounded">Ctrl</kbd>
                            +
                            <kbd class="px-1.5 py-0.5 text-[10px] font-mono bg-dark-100 dark:bg-dark-700 border border-dark-200 dark:border-dark-600 rounded">Enter</kbd>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Preview Modals --}}
    <x-modal title="{{ __('pages.logo_preview') }}" wire="showLogoModal" size="lg" center>
        @if ($currentLogo)
            <img src="{{ asset('storage/' . $currentLogo) }}?v={{ filemtime(storage_path('app/public/' . $currentLogo)) }}" class="w-full" alt="Logo Preview">
        @endif
    </x-modal>

    <x-modal title="{{ __('pages.letter_head_preview') }}" wire="showLetterHeadModal" size="lg" center>
        @if ($currentLetterHead)
            <img src="{{ asset('storage/' . $currentLetterHead) }}?v={{ filemtime(storage_path('app/public/' . $currentLetterHead)) }}" class="w-full" alt="Letter Head Preview">
        @endif
    </x-modal>

    <x-modal title="{{ __('pages.signature_preview') }}" wire="showSignatureModal" size="lg" center>
        @if ($currentSignature)
            <img src="{{ asset('storage/' . $currentSignature) }}?v={{ filemtime(storage_path('app/public/' . $currentSignature)) }}" class="w-full" alt="Signature Preview">
        @endif
    </x-modal>

    <x-modal title="{{ __('pages.stamp_preview') }}" wire="showStampModal" size="lg" center>
        @if ($currentStamp)
            <img src="{{ asset('storage/' . $currentStamp) }}?v={{ filemtime(storage_path('app/public/' . $currentStamp)) }}" class="w-full" alt="Stamp Preview">
        @endif
    </x-modal>
</section>

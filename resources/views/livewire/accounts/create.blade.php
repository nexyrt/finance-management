{{-- resources/views/livewire/accounts/create.blade.php --}}

<x-modal wire="showModal" title="{{ __('common.bank_accounts') }}" size="lg" center>
    <x-slot:title>
        <div class="flex items-center gap-4 my-3">
            <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                <x-icon name="building-library" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
            </div>
            <div>
                <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('common.create') }} {{ __('common.bank_accounts') }}</h3>
                <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.add_new_client_to_system') }}</p>
            </div>
        </div>
    </x-slot:title>

    <form wire:submit.prevent="save" class="space-y-6">
        {{-- Basic Information --}}
        <div class="space-y-4">
            <div class="border-b border-zinc-200 dark:border-dark-600 pb-4">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.basic_information') }}</h4>
                <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.client_basic_details') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input wire:model.live="account_name"
                         :label="__('pages.account_name')"
                         :placeholder="__('pages.account_name')"
                         :hint="__('pages.account_name')" />

                <x-input wire:model.live="bank_name"
                         :label="__('pages.bank_name')"
                         :placeholder="__('pages.bank_name')"
                         :hint="__('pages.bank_name')" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input wire:model.live="account_number"
                         :label="__('pages.account_number')"
                         :placeholder="__('pages.account_number')"
                         :hint="__('pages.account_number')" />

                <x-input wire:model.live="branch"
                         :label="__('pages.branch')"
                         :placeholder="__('pages.branch')"
                         :hint="__('pages.branch')" />
            </div>
        </div>

        {{-- Financial Information --}}
        <div class="space-y-4">
            <div class="border-b border-zinc-200 dark:border-dark-600 pb-4">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.financial') }}</h4>
                <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.initial_balance') }}</p>
            </div>

            {{-- Use WireUI Currency component --}}
            <x-wireui-currency prefix="Rp "
                               wire:model.live="initial_balance"
                               :label="__('pages.initial_balance')"
                               placeholder="0"
                               color="dark:dark"
                               :hint="__('pages.initial_balance')" />
        </div>

        {{-- Preview Section --}}
        @if($account_name || $bank_name || $account_number)
        <div class="bg-zinc-50 dark:bg-dark-700 rounded-xl p-4 border border-zinc-200 dark:border-dark-600">
            <div class="flex items-center gap-3 mb-3">
                <div class="h-8 w-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <x-icon name="eye" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h5 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.preview') }}</h5>
                    <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.preview') }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-800 rounded-lg p-4 border border-zinc-200 dark:border-dark-600">
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-8 w-8 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center">
                        <x-icon name="building-library" class="w-4 h-4 text-white" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-dark-900 dark:text-dark-50 truncate">
                            {{ $account_name ?: __('pages.account_name') }}
                        </p>
                        <p class="text-sm text-dark-500 dark:text-dark-400">
                            {{ $bank_name ?: __('pages.bank_name') }}
                        </p>
                    </div>
                </div>
                @if($account_number)
                <div class="bg-zinc-50 dark:bg-dark-700 rounded-lg px-3 py-2 mt-2">
                    <p class="text-xs text-dark-500 dark:text-dark-400 mb-1">{{ __('pages.account_number') }}</p>
                    <p class="font-mono text-sm font-medium text-dark-900 dark:text-dark-50">
                        {{ $account_number }}
                    </p>
                </div>
                @endif
                @if($initial_balance)
                <div class="mt-3 pt-3 border-t border-zinc-200 dark:border-dark-600">
                    <p class="text-xs text-dark-500 dark:text-dark-400 mb-1">{{ __('pages.initial_balance') }}</p>
                    <p class="text-lg font-bold text-green-600 dark:text-green-400">
                        Rp {{ number_format($initial_balance, 0, ',', '.') }}
                    </p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </form>

    <x-slot:footer>
        <div class="flex flex-col sm:flex-row justify-end gap-3">
            <x-button wire:click="closeModal" color="zinc" outline class="w-full sm:w-auto order-2 sm:order-1">
                {{ __('common.cancel') }}
            </x-button>

            <x-button wire:click="save" color="primary" icon="check" loading="save"
                      class="w-full sm:w-auto order-1 sm:order-2">
                <span wire:loading.remove wire:target="save">{{ __('common.save') }}</span>
                <span wire:loading wire:target="save">{{ __('common.loading') }}</span>
            </x-button>
        </div>
    </x-slot:footer>
</x-modal>
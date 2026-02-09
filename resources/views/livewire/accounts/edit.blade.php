{{-- resources/views/livewire/accounts/edit.blade.php --}}

<x-modal wire="showModal" title="{{ __('common.edit') }}" size="lg" center persistent>
    <x-slot:title>
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 bg-amber-50 dark:bg-amber-900/20 rounded-xl flex items-center justify-center">
                <x-icon name="pencil" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
            </div>
            <div>
                <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.edit_bank_account') }}</h3>
                <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.update_bank_account_info') }}</p>
            </div>
        </div>
    </x-slot:title>

    <form wire:submit.prevent="save" class="space-y-6">
        {{-- Basic Information --}}
        <div class="space-y-4">
            <div class="border-b border-zinc-200 dark:border-dark-600 pb-4">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.basic_information') }}</h4>
                <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.account_basic_details') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input wire:model.live="account_name"
                         :label="__('pages.account_name')"
                         :placeholder="__('pages.enter_account_name')"
                         :hint="__('pages.account_name_description')" />

                <x-input wire:model.live="bank_name"
                         :label="__('pages.bank_name')"
                         :placeholder="__('pages.enter_bank_name')"
                         :hint="__('pages.bank_name_hint')" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input wire:model.live="account_number"
                         :label="__('pages.account_number')"
                         :placeholder="__('pages.enter_account_number')"
                         :hint="__('pages.account_number_hint')" />

                <x-input wire:model.live="branch"
                         :label="__('pages.branch')"
                         :placeholder="__('pages.enter_branch')"
                         :hint="__('pages.branch_hint')" />
            </div>
        </div>

        {{-- Financial Information --}}
        <div class="space-y-4">
            <div class="border-b border-zinc-200 dark:border-dark-600 pb-4">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.financial') }}</h4>
                <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.initial_balance_section_desc') }}</p>
            </div>

            {{-- Use WireUI Currency component --}}
            <x-wireui-currency prefix="Rp "
                               wire:model.live="initial_balance"
                               :label="__('pages.initial_balance')"
                               :placeholder="__('pages.enter_initial_balance')"
                               color="dark:dark"
                               :hint="__('pages.initial_balance_hint')" />
        </div>

        {{-- Warning for balance changes --}}
        @if($accountId)
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <div class="h-6 w-6 bg-amber-100 dark:bg-amber-900/40 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                    <x-icon name="exclamation-triangle" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <h5 class="text-sm font-semibold text-amber-900 dark:text-amber-100 mb-1">{{ __('common.warning') }}</h5>
                    <p class="text-xs text-amber-800 dark:text-amber-200">
                        {{ __('pages.balance_change_warning') }}
                    </p>
                </div>
            </div>
        </div>
        @endif
    </form>

    <x-slot:footer>
        <div class="flex flex-col sm:flex-row justify-end gap-3">
            <x-button wire:click="closeModal" class="w-full sm:w-auto order-2 sm:order-1">
                {{ __('common.cancel') }}
            </x-button>

            <x-button wire:click="save" color="amber" icon="check" loading="save"
                      class="w-full sm:w-auto order-1 sm:order-2">
                <span wire:loading.remove wire:target="save">{{ __('pages.update') }}</span>
                <span wire:loading wire:target="save">{{ __('common.updating') }}</span>
            </x-button>
        </div>
    </x-slot:footer>
</x-modal>
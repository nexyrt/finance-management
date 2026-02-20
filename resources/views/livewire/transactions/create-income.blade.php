<div>
    {{-- Trigger Button --}}
    <x-button wire:click="$toggle('modal')" color="green" icon="plus">
        {{ __('pages.add_income_btn') }}
    </x-button>

    {{-- Modal --}}
    <x-modal :title="__('pages.add_income_title')" wire="modal" size="xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="arrow-down-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.add_income_title') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.add_income_desc') }}</p>
                </div>
            </div>
        </x-slot:title>

        <form id="income-create" wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Left Column: Detail Transaksi --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.transaction_details_section') }}</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.income_details_desc') }}</p>
                    </div>

                    <x-select.styled
                        wire:model.live="bank_account_id"
                        :request="route('api.bank-accounts')"
                        :label="__('pages.bank_account_label')"
                        :placeholder="__('pages.bank_account_placeholder')"
                        searchable
                    />

                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <x-select.styled
                                wire:model.live="category_id"
                                :request="[
                                    'url' => route('api.transaction-categories'),
                                    'method' => 'get',
                                    'params' => ['type' => 'credit'],
                                ]"
                                :label="__('pages.category_label')"
                                :placeholder="__('pages.transaction_category_placeholder')"
                                searchable
                            />
                        </div>
                        <div class="flex-shrink-0">
                            <livewire:transactions-categories.create />
                        </div>
                    </div>

                    <x-currency-input wire:model="amount" :label="__('pages.amount_label')" prefix="Rp" placeholder="0" />

                    <x-date wire:model.live="transaction_date" :label="__('pages.transaction_date_label')" :placeholder="__('pages.select_range_placeholder')" helpers />
                </div>

                {{-- Right Column: Keterangan --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.description_section') }}</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.transaction_details_desc2') }}</p>
                    </div>

                    <x-input wire:model="description" :label="__('pages.description_label')" :placeholder="__('pages.income_description_placeholder')" />

                    <x-input wire:model="reference_number" :label="__('pages.reference_number_optional_label')" :placeholder="__('pages.reference_number_optional_placeholder')" />

                    <x-file-upload wire:model="attachment" :label="__('pages.attachment_optional_label')" />
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="zinc" class="w-full sm:w-auto order-2 sm:order-1">
                    {{ __('common.cancel') }}
                </x-button>
                <x-button type="submit" form="income-create" color="green" icon="check" loading="save" class="w-full sm:w-auto order-1 sm:order-2">
                    {{ __('pages.save_income_btn') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>

<div>
    <x-modal :title="__('pages.add_transaction_title')" wire="modal" size="xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="plus" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.add_transaction_title') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.add_transaction_desc') }}</p>
                </div>
            </div>
        </x-slot:title>

        <form id="transaction-form" wire:submit="save" class="space-y-6">
            {{-- Transaction Type Selection --}}
            @if (count($allowedTypes) > 1)
                <div class="rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">{{ __('pages.transaction_type_section') }}</h4>
                    <div class="grid grid-cols-2 gap-3">
                        @if (in_array('credit', $allowedTypes))
                            <label class="relative">
                                <input type="radio" wire:model.live="transaction_type" value="credit"
                                    class="sr-only peer">
                                <div
                                    class="p-4 rounded-lg border-2 border-secondary-200 dark:border-dark-600 cursor-pointer transition-all peer-checked:border-green-600 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                            <x-icon name="arrow-down"
                                                class="w-5 h-5 text-green-600 dark:text-green-400" />
                                        </div>
                                        <div>
                                            <p class="font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.income_type_label') }}</p>
                                            <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.income_type_desc') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endif

                        @if (in_array('debit', $allowedTypes))
                            <label class="relative">
                                <input type="radio" wire:model.live="transaction_type" value="debit"
                                    class="sr-only peer">
                                <div
                                    class="p-4 rounded-lg border-2 border-secondary-200 dark:border-dark-600 cursor-pointer transition-all peer-checked:border-red-600 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                                            <x-icon name="arrow-up" class="w-5 h-5 text-red-600 dark:text-red-400" />
                                        </div>
                                        <div>
                                            <p class="font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.expense_type_label') }}</p>
                                            <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.expense_type_desc') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endif
                    </div>
                </div>
            @else
                <div
                    class="bg-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-50 dark:bg-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-900/20 rounded-xl p-4 border border-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-200 dark:border-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-800">
                    <div class="flex items-center gap-3">
                        <div
                            class="h-10 w-10 bg-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-100 dark:bg-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-900/40 rounded-lg flex items-center justify-center">
                            <x-icon name="arrow-{{ $transaction_type === 'credit' ? 'down' : 'up' }}"
                                class="w-5 h-5 text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-600 dark:text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-400" />
                        </div>
                        <div>
                            <p
                                class="font-semibold text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-900 dark:text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-100">
                                {{ $transaction_type === 'credit' ? __('pages.income_transaction_banner') : __('pages.expense_transaction_banner') }}
                            </p>
                            <p
                                class="text-xs text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-700 dark:text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-300">
                                {{ $transaction_type === 'credit' ? __('pages.income_banner_desc') : __('pages.expense_banner_desc') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Transaction Details --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Left Column --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.transaction_details_section') }}</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.transaction_details_desc') }}</p>
                    </div>

                    <x-select.styled wire:model.live="bank_account_id" :options="$this->accounts
                        ->map(
                            fn($account) => [
                                'label' => $account->account_name . ' (' . $account->bank_name . ')',
                                'value' => $account->id,
                            ],
                        )
                        ->toArray()" :label="__('pages.bank_account_label')"
                        :placeholder="__('pages.bank_account_placeholder')" searchable />

                    <div>
                        <div class="flex items-center justify-between mb-1.5">

                                <span class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('pages.transaction_category_label') }}</span>
                            <button type="button"
                                wire:click="$dispatch('open-inline-category-modal', { transactionType: '{{ $transaction_type }}' })"
                                class="flex items-center gap-1 px-2 py-1 text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                <x-icon name="plus" class="w-4 h-4" />
                                <span>{{ __('pages.add_category_btn') }}</span>
                            </button>
                        </div>
                        <x-select.styled wire:model.live="category_id" :options="$this->categoriesOptions"
                            :placeholder="__('pages.transaction_category_placeholder')" searchable />
                    </div>

                    <x-currency-input wire:model="amount" :label="__('pages.amount_label')" prefix="Rp"
                        placeholder="0" :hint="__('pages.amount_hint')" />

                    <x-date wire:model.live="transaction_date" helpers :label="__('pages.transaction_date_label')">
                        <x-slot name="hint">
                            {{ __('pages.transaction_date_hint') }}
                        </x-slot>
                    </x-date>
                </div>

                {{-- Right Column --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.description_section') }}</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.description_section_desc') }}</p>
                    </div>

                    <x-input wire:model.live="description" :label="__('pages.description_label')"
                        :placeholder="__('pages.description_placeholder')" />

                    <x-input wire:model.live="reference_number" :label="__('pages.reference_number_optional_label')"
                        :placeholder="__('pages.reference_number_optional_placeholder')" />

                    <x-upload wire:model="attachment" :label="__('pages.attachment_optional_label')"
                        :tip="__('pages.attachment_optional_tip')"
                        accept="application/pdf,image/jpeg,image/jpg,image/png" delete />
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="zinc"
                    class="w-full sm:w-auto order-2 sm:order-1">
                    {{ __('common.cancel') }}
                </x-button>

                <x-button type="submit" form="transaction-form" :color="$transaction_type === 'credit' ? 'green' : 'red'" icon="check" loading="save"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    {{ __('pages.save_transaction_btn') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>

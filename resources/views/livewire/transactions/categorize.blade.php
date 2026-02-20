<div>
    <x-modal wire title="{{ __('pages.categorize_transaction_title') }}" size="lg" center>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-amber-50 dark:bg-amber-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="tag" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">
                        {{ $isBulk ? __('pages.categorize_bulk_title', ['count' => count($transactionIds)]) : __('pages.categorize_transaction_title') }}
                    </h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">
                        {{ $isBulk ? __('pages.categorize_bulk_desc') : __('pages.categorize_desc') }}
                    </p>
                </div>
            </div>
        </x-slot:title>

        <form id="categorize-form" wire:submit="save" class="space-y-6">
            @if ($isBulk)
                {{-- Bulk Preview --}}
                <div
                    class="bg-secondary-50 dark:bg-dark-700 rounded-xl p-4 border border-secondary-200 dark:border-dark-600">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">
                        {{ __('pages.preview_transactions_title', ['count' => count($transactionIds)]) }}
                    </h4>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach ($transactions->take(5) as $trans)
                            <div
                                class="flex items-start justify-between gap-4 py-2 border-b border-secondary-200 dark:border-dark-600 last:border-0">
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-dark-900 dark:text-dark-50 truncate">
                                        {{ $trans->description }}
                                    </div>
                                    <div class="text-xs text-dark-500 dark:text-dark-400">
                                        {{ $trans->transaction_date->format('d M Y') }} •
                                        {{ $trans->bankAccount->bank_name }}
                                    </div>
                                </div>
                                <div
                                    class="text-sm font-bold {{ $trans->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    Rp {{ number_format($trans->amount, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach

                        @if ($transactions->count() > 5)
                            <div class="text-center text-sm text-dark-500 dark:text-dark-400 py-2">
                                {{ __('pages.more_transactions', ['count' => $transactions->count() - 5]) }}
                            </div>
                        @endif
                    </div>
                </div>
            @else
                {{-- Single Transaction Preview --}}
                @if ($transaction)
                    <div
                        class="bg-{{ $transaction->transaction_type === 'credit' ? 'green' : 'red' }}-50 dark:bg-{{ $transaction->transaction_type === 'credit' ? 'green' : 'red' }}-900/20 rounded-xl p-4 border border-{{ $transaction->transaction_type === 'credit' ? 'green' : 'red' }}-200 dark:border-{{ $transaction->transaction_type === 'credit' ? 'green' : 'red' }}-800">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <x-icon
                                        name="arrow-{{ $transaction->transaction_type === 'credit' ? 'down' : 'up' }}"
                                        class="w-4 h-4 text-{{ $transaction->transaction_type === 'credit' ? 'green' : 'red' }}-600 dark:text-{{ $transaction->transaction_type === 'credit' ? 'green' : 'red' }}-400" />
                                    <span
                                        class="text-sm font-semibold text-{{ $transaction->transaction_type === 'credit' ? 'green' : 'red' }}-900 dark:text-{{ $transaction->transaction_type === 'credit' ? 'green' : 'red' }}-100">
                                        {{ $transaction->transaction_type === 'credit' ? __('pages.income_label') : __('pages.expense_label') }}
                                    </span>
                                </div>
                                <div class="text-base font-medium text-dark-900 dark:text-dark-50 mb-1">
                                    {{ $transaction->description }}
                                </div>
                                <div class="text-xs text-dark-600 dark:text-dark-400 space-y-0.5">
                                    <div>{{ $transaction->transaction_date->format('d M Y') }}</div>
                                    <div>{{ $transaction->bankAccount->bank_name }} -
                                        {{ $transaction->bankAccount->account_number }}</div>
                                    @if ($transaction->reference_number)
                                        <div class="font-mono">{{ __('pages.ref_prefix') }} {{ $transaction->reference_number }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <div
                                    class="text-2xl font-bold text-{{ $transaction->transaction_type === 'credit' ? 'green' : 'red' }}-600 dark:text-{{ $transaction->transaction_type === 'credit' ? 'green' : 'red' }}-400">
                                    Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </div>
                                @if ($transaction->category)
                                    <div class="mt-2">
                                        <x-badge text="{{ $transaction->category->label }}" color="purple"
                                            size="sm" />
                                    </div>
                                @else
                                    <div class="mt-2">
                                        <x-badge :text="__('pages.not_yet_categorized')" color="amber" size="sm" />
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Category Selection --}}
            <div>
                <x-select.styled wire:model.live="category_id" :options="$this->categoriesOptions" :placeholder="__('pages.select_category_placeholder')"
                    searchable>
                    <x-slot:label>
                        <div class="flex items-center gap-2">
                            <span>{{ $isBulk ? __('pages.category_for_all_label') : __('pages.category_for_one_label') }}</span>
                            <x-tooltip color="secondary"
                                :text="$isBulk ? __('pages.categorize_tooltip_bulk') : __('pages.categorize_tooltip_single')"
                                position="top" />
                        </div>
                    </x-slot:label>
                </x-select.styled>
            </div>

            {{-- Info Message --}}
            @if ($category_id)
                <div
                    class="bg-primary-50 dark:bg-primary-900/20 rounded-lg p-3 border border-primary-200 dark:border-primary-800">
                    <div class="flex items-start gap-2">
                        <x-icon name="information-circle"
                            class="w-5 h-5 text-primary-600 dark:text-primary-400 flex-shrink-0 mt-0.5" />
                        <div class="text-sm text-primary-700 dark:text-primary-300">
                            @if ($isBulk)
                                {!! __('pages.categorize_info_bulk', ['count' => '<strong>' . count($transactionIds) . '</strong>']) !!}
                            @else
                                {{ __('pages.categorize_info_single') }}
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="zinc"
                    class="w-full sm:w-auto order-2 sm:order-1">
                    {{ __('common.cancel') }}
                </x-button>

                <x-button type="submit" form="categorize-form" color="amber" icon="check" loading="save"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    {{ $isBulk ? __('pages.apply_to_all_btn') : __('pages.save_category_btn') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>

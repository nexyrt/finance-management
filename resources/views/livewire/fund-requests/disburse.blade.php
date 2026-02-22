<div>
    @if ($fundRequest)
        <x-modal title="{{ __('pages.disburse_fund_request') }}" wire="modal" size="3xl" center persistent>
            {{-- HEADER --}}
            <x-slot:title>
                <div class="flex items-center gap-4 my-3">
                    <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="banknotes" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.disburse_fund_request') }}</h3>
                        <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.disburse_fund_request_description') }}</p>
                    </div>
                </div>
            </x-slot:title>

            {{-- CONTENT --}}
            <form id="disburse-form" wire:submit="disburse" class="space-y-6">
                {{-- Request Summary --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.request_summary') }}</h4>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.request_number') }}</label>
                            <p class="text-sm font-mono font-semibold text-primary-600 dark:text-primary-400">{{ $fundRequest->request_number ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.requestor') }}</label>
                            <p class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ $fundRequest->user->name }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('common.total_amount') }}</label>
                            <p class="text-lg font-bold text-green-600 dark:text-green-400">
                                Rp {{ number_format($fundRequest->total_amount, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.fund_request_title') }}</label>
                            <p class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ translate_text($fundRequest->title) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Budget Items Preview --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.items_to_disburse') }}</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">
                            {{ __('pages.items_to_disburse_hint') }}
                        </p>
                    </div>

                    <div class="max-h-64 overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead class="sticky top-0 bg-white dark:bg-dark-800">
                                <tr class="border-b border-secondary-200 dark:border-dark-600">
                                    <th class="text-left pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold">{{ __('pages.item_description') }}</th>
                                    <th class="text-left pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold">{{ __('pages.category') }}</th>
                                    <th class="text-right pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold">{{ __('common.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($fundRequest->items as $item)
                                    <tr class="border-b border-secondary-100 dark:border-dark-700">
                                        <td class="py-2 px-2 text-dark-900 dark:text-dark-50">{{ $item->description }}</td>
                                        <td class="py-2 px-2 text-dark-700 dark:text-dark-300 text-xs">{{ $item->category->full_path }}</td>
                                        <td class="py-2 px-2 text-right font-semibold text-dark-900 dark:text-dark-50">
                                            Rp {{ number_format($item->amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="bg-secondary-50 dark:bg-dark-700 font-bold">
                                    <td colspan="2" class="py-3 px-2 text-right text-dark-900 dark:text-dark-50">{{ __('common.total') }}:</td>
                                    <td class="py-3 px-2 text-right text-lg text-green-600 dark:text-green-400">
                                        Rp {{ number_format($fundRequest->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Disbursement Details --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.disbursement_details') }}</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">
                            {{ __('pages.disbursement_details_description') }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        {{-- Bank Account --}}
                        <x-select.styled wire:model="bankAccountId"
                                         :label="__('pages.bank_account_label')"
                                         :options="$this->bankAccounts->map(fn($account) => [
                                             'label' => $account->account_name . ' (' . $account->bank_name . ')',
                                             'value' => $account->id
                                         ])->toArray()"
                                         :placeholder="__('pages.select_bank_account')"
                                         searchable />

                        {{-- Disbursement Date --}}
                        <x-date wire:model="disbursementDate"
                                label="{{ __('pages.disbursement_date_label') }}"
                                :max="today()" />

                        {{-- Reference/Notes --}}
                        <div class="lg:col-span-2">
                            <x-textarea wire:model="disbursementNotes"
                                        label="{{ __('pages.disbursement_notes_label') }}"
                                        placeholder="{{ __('pages.disbursement_notes_placeholder') }}"
                                        rows="2" />
                        </div>
                    </div>
                </div>

                {{-- Important Note --}}
                <div class="flex items-start gap-3 p-4 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800">
                    <x-icon name="exclamation-triangle" class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" />
                    <div class="text-sm text-yellow-800 dark:text-yellow-200">
                        <p class="font-semibold mb-1">{{ __('common.important') }}:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>{{ __('pages.disbursement_warning_transactions', ['count' => count($fundRequest->items)]) }}</li>
                            <li>{{ __('pages.disbursement_warning_categorization') }}</li>
                            <li>{{ __('pages.disbursement_warning_balance', ['amount' => 'Rp ' . number_format($fundRequest->total_amount, 0, ',', '.')]) }}</li>
                            <li>{{ __('pages.disbursement_warning_irreversible') }}</li>
                        </ul>
                    </div>
                </div>
            </form>

            {{-- FOOTER --}}
            <x-slot:footer>
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <x-button wire:click="$set('modal', false)"
                              color="zinc"
                              class="w-full sm:w-auto order-2 sm:order-1">
                        {{ __('common.cancel') }}
                    </x-button>

                    <x-button type="submit"
                              form="disburse-form"
                              color="green"
                              icon="check"
                              loading="disburse"
                              class="w-full sm:w-auto order-1 sm:order-2">
                        {{ __('pages.disburse_button') }}
                    </x-button>
                </div>
            </x-slot:footer>
        </x-modal>
    @endif
</div>

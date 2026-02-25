<div>
    <x-modal wire="modal" size="xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="currency-dollar" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.rcv_pay_title') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.rcv_pay_desc') }}</p>
                </div>
            </div>
        </x-slot:title>

        @if ($receivable)
            <form id="pay-receivable" wire:submit="save" class="space-y-6">
                {{-- Receivable Summary --}}
                <div class="bg-secondary-50 dark:bg-dark-700 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.rcv_remaining_principal') }}</div>
                            <div class="text-lg font-bold text-dark-900 dark:text-dark-50">
                                Rp {{ number_format($this->remainingPrincipal, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                {{ __('pages.rcv_paid_label') }}: Rp
                                {{ number_format($receivable->payments()->sum('principal_paid'), 0, ',', '.') }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.rcv_remaining_interest') }}</div>
                            <div class="text-lg font-bold text-dark-900 dark:text-dark-50">
                                Rp {{ number_format($this->remainingInterest, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                {{ __('pages.rcv_paid_label') }}: Rp
                                {{ number_format($receivable->payments()->sum('interest_paid'), 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Details --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.rcv_payment_section') }}</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.rcv_payment_section_desc') }}</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <x-date wire:model="payment_date" label="{{ __('pages.rcv_payment_date_label') }}" />

                        <x-select.native wire:model.live="payment_method" label="{{ __('pages.rcv_payment_method_label') }}"
                            :options="[
                                ['label' => __('pages.rcv_method_bank_transfer'), 'value' => 'bank_transfer'],
                                ['label' => __('pages.rcv_method_payroll'), 'value' => 'payroll_deduction'],
                                ['label' => __('pages.rcv_method_cash'), 'value' => 'cash'],
                            ]" />

                        {{-- Bank Account (only show for bank_transfer) --}}
                        @if ($payment_method === 'bank_transfer')
                            <div class="lg:col-span-2">
                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                                    <div class="flex items-start gap-3 mb-3">
                                        <x-icon name="information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" />
                                        <div>
                                            <div class="font-semibold text-blue-900 dark:text-blue-100 text-sm">{{ __('pages.rcv_bank_receive_title') }}</div>
                                            <div class="text-xs text-blue-700 dark:text-blue-300">{{ __('pages.rcv_bank_receive_desc') }}</div>
                                        </div>
                                    </div>
                                    <x-select.styled wire:model="bank_account_id" :options="$this->bankAccounts"
                                        label="{{ __('pages.rcv_bank_account_label') }}"
                                        placeholder="{{ __('pages.rcv_bank_account_placeholder') }}" searchable />
                                </div>
                            </div>
                        @endif

                        <x-currency-input wire:model="principal_paid"
                            label="{{ __('pages.rcv_principal_paid_label') }}"
                            hint="{{ __('pages.rcv_principal_paid_hint') }}" />

                        <x-currency-input wire:model="interest_paid"
                            label="{{ __('pages.rcv_interest_paid_label') }}"
                            hint="{{ __('pages.rcv_interest_paid_hint') }}" />

                        <x-input wire:model="reference_number"
                            label="{{ __('pages.rcv_reference_label') }}"
                            placeholder="{{ __('pages.rcv_reference_placeholder') }}" />

                        <x-textarea wire:model="notes"
                            label="{{ __('pages.rcv_notes_label') }}"
                            placeholder="{{ __('pages.rcv_notes_placeholder') }}" rows="3" />
                    </div>
                </div>
            </form>
        @endif

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="zinc"
                    class="w-full sm:w-auto order-2 sm:order-1">
                    {{ __('common.cancel') }}
                </x-button>
                <x-button type="submit" form="pay-receivable" color="green" icon="check" loading="save"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    {{ __('pages.rcv_btn_pay') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>

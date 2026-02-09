<div>
    <x-modal wire="showModal" :title="__('pages.record_payment_title')" size="2xl" center>
        @if ($invoice)
            {{-- Invoice Info Header --}}
            <div
                class="bg-gradient-to-r from-blue-50 to-primary-50 dark:from-blue-900/20 dark:to-primary-900/20 -m-4 mb-6 p-4 border-b border-blue-200 dark:border-blue-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-xl flex items-center justify-center">
                            <x-icon name="currency-dollar" class="w-5 h-5 text-blue-600" />
                        </div>
                        <div>
                            <h3 class="font-bold text-secondary-900 dark:text-dark-50">{{ $invoice->invoice_number }}
                            </h3>
                            <p class="text-sm text-secondary-600 dark:text-dark-400">{{ $invoice->client->name }}</p>
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-sm text-secondary-600 dark:text-dark-400">{{ __('pages.remaining_balance') }}</p>
                        <p class="text-xl font-bold text-secondary-900 dark:text-dark-50">
                            Rp {{ number_format($invoice->amount_remaining, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Payment Form --}}
            <div class="space-y-6">
                {{-- Amount & Date --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-wireui-currency wire:model.live="amount" :label="__('pages.payment_amount_label')" placeholder="0"
                            prefix="Rp" />
                        @if ($invoice->amount_remaining > 0)
                            <p class="text-xs text-secondary-500 dark:text-dark-400 mt-1">
                                {{ __('pages.max_amount', ['amount' => number_format($invoice->amount_remaining, 0, ',', '.')]) }}
                            </p>
                        @endif
                    </div>

                    <x-input wire:model="payment_date" :label="__('pages.payment_date_label')" type="date" icon="calendar" />
                </div>

                {{-- Payment Method & Bank Account --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-select.styled wire:model.live="payment_method" :label="__('pages.payment_method_label')" :options="[
                        ['label' => '💳 ' . __('pages.bank_transfer'), 'value' => 'bank_transfer'],
                        ['label' => '💵 ' . __('pages.cash'), 'value' => 'cash'],
                    ]" />

                    <x-select.styled wire:model="bank_account_id" :label="__('pages.bank_account_label')" :options="$this->bankAccounts"
                        :placeholder="__('pages.select_account')" searchable />
                </div>

                {{-- Reference Number --}}
                <div>
                    <x-input wire:model="reference_number" :label="__('pages.reference_number_label')"
                        :placeholder="__('pages.reference_placeholder')" icon="hashtag"
                        :hint="__('pages.reference_hint')" />
                </div>

                {{-- File Upload --}}
                <x-upload wire:model="attachment" :label="__('pages.payment_proof_label')"
                    :tip="__('pages.payment_proof_tip')" accept="image/*,.pdf" delete />

                {{-- Payment Summary --}}
                @if ($amount)
                    @php
                        $amountInteger = (int) $amount;
                        $remainingAfter = $invoice->amount_remaining - $amountInteger;
                    @endphp

                    <div
                        class="bg-secondary-50 dark:bg-dark-800 rounded-xl p-4 border border-secondary-200 dark:border-dark-700">
                        <h4 class="font-medium text-secondary-900 dark:text-dark-50 mb-3 flex items-center gap-2">
                            <x-icon name="calculator" class="w-4 h-4" />
                            {{ __('pages.payment_summary') }}
                        </h4>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-secondary-600 dark:text-dark-400">{{ __('pages.invoice_total') }}</span>
                                <span class="font-medium">Rp
                                    {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-secondary-600 dark:text-dark-400">{{ __('pages.already_paid') }}</span>
                                <span class="font-medium">Rp
                                    {{ number_format($invoice->amount_paid, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-secondary-600 dark:text-dark-400">{{ __('pages.this_payment') }}</span>
                                <span class="font-medium text-blue-600">Rp
                                    {{ number_format($amountInteger, 0, ',', '.') }}</span>
                            </div>
                            <hr class="border-secondary-300 dark:border-dark-600">
                            <div class="flex justify-between font-bold">
                                <span class="text-secondary-900 dark:text-dark-50">{{ __('pages.remaining_after_payment') }}</span>
                                <span class="{{ $remainingAfter <= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Rp {{ number_format(max(0, $remainingAfter), 0, ',', '.') }}
                                </span>
                            </div>
                            @if ($remainingAfter <= 0)
                                <div class="text-xs text-green-600 dark:text-green-400 italic text-center mt-2">
                                    {{ __('pages.invoice_will_be_paid') }}
                                </div>
                            @endif
                            @if ($attachment)
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-secondary-600 dark:text-dark-400">{{ __('pages.proof_attached') }}</span>
                                    <div class="flex items-center gap-2">
                                        <x-icon name="check-circle"
                                            class="w-4 h-4 text-green-600 dark:text-green-400" />
                                        <span class="text-sm font-medium text-green-600 dark:text-green-400">
                                            {{ __('pages.file_ready_upload') }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Footer Actions --}}
        <x-slot:footer>
            <div class="flex items-center justify-between w-full">
                <div></div>

                {{-- Main Actions --}}
                <div class="flex items-center gap-3">
                    <x-button wire:click="$set('showModal', false)" color="zinc">
                        {{ __('common.cancel') }}
                    </x-button>
                    <x-button wire:click="save" color="primary" icon="check" loading="save">
                        {{ __('common.save') }}
                    </x-button>
                </div>
            </div>
        </x-slot:footer>
    </x-modal>
</div>

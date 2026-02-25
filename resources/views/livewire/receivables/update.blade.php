<div>
    <x-modal wire="modal" size="xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="pencil" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.rcv_edit_title') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.rcv_edit_desc') }}</p>
                </div>
            </div>
        </x-slot:title>

        <form id="receivable-update" wire:submit="save" class="space-y-6">
            {{-- Section: Jenis Piutang --}}
            <div class="space-y-4">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.rcv_section_type') }}</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.rcv_section_type_desc') }}</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <x-select.native wire:model.live="type" label="{{ __('pages.rcv_type_label') }}" :options="[
                        ['label' => __('pages.rcv_type_employee'), 'value' => 'employee_loan'],
                        ['label' => __('pages.rcv_type_company'), 'value' => 'company_loan'],
                    ]" />

                    @if ($type === 'employee_loan')
                        <x-select.styled wire:model="debtor_id" :options="$this->employees"
                            label="{{ __('pages.rcv_employee_label') }}"
                            placeholder="{{ __('pages.rcv_employee_placeholder') }}" searchable />
                    @else
                        <x-select.styled wire:model="debtor_id" :options="$this->companies"
                            label="{{ __('pages.rcv_company_label') }}"
                            placeholder="{{ __('pages.rcv_company_placeholder') }}" searchable />
                    @endif
                </div>
            </div>

            {{-- Section: Detail Pinjaman --}}
            <div class="space-y-4">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.rcv_section_detail') }}</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.rcv_section_detail_desc') }}</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <x-currency-input wire:model="principal_amount" placeholder="0">
                        <x-slot:label>
                            <div class="flex items-center gap-2">
                                <span>{{ __('pages.rcv_principal_label') }}</span>
                                @if ($type === 'employee_loan')
                                    <x-tooltip color="secondary" text="{{ __('pages.rcv_principal_max_hint') }}" position="top" />
                                @endif
                            </div>
                        </x-slot:label>
                    </x-currency-input>

                    <x-select.native wire:model.live="interest_type" label="{{ __('pages.rcv_interest_type_label') }}" :options="[
                        ['label' => __('pages.rcv_interest_percentage_option'), 'value' => 'percentage'],
                        ['label' => __('pages.rcv_interest_fixed_option'), 'value' => 'fixed'],
                    ]" />

                    @if ($interest_type === 'fixed')
                        <x-currency-input wire:model="interest_amount"
                            label="{{ __('pages.rcv_interest_amount_label') }}"
                            placeholder="0" hint="{{ __('pages.rcv_interest_amount_hint') }}" />
                    @else
                        <x-input wire:model="interest_rate" type="number" step="0.01"
                            label="{{ __('pages.rcv_interest_rate_label') }}"
                            placeholder="0" suffix="%" hint="{{ __('pages.rcv_interest_rate_hint') }}" />
                    @endif

                    <x-input wire:model="installment_months" type="number"
                        label="{{ __('pages.rcv_tenor_label') }}" placeholder="12" />

                    <x-date wire:model="loan_date" label="{{ __('pages.rcv_loan_date_label') }}" />
                </div>
            </div>

            {{-- Section: Informasi Tambahan --}}
            <div class="space-y-4">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.rcv_section_info') }}</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.rcv_section_info_desc') }}</p>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <x-input wire:model="purpose"
                        label="{{ __('pages.rcv_purpose_label') }}"
                        placeholder="{{ __('pages.rcv_purpose_placeholder') }}" />

                    <x-input wire:model="disbursement_account"
                        label="{{ __('pages.rcv_disbursement_account_label') }}"
                        placeholder="{{ __('pages.rcv_disbursement_account_placeholder') }}"
                        hint="{{ __('pages.rcv_disbursement_account_hint') }}" />

                    <x-textarea wire:model="notes"
                        label="{{ __('pages.rcv_notes_label') }}"
                        placeholder="{{ __('pages.rcv_notes_placeholder') }}" rows="3" />

                    @if ($currentAttachment)
                        <div class="flex items-center gap-2 p-3 bg-secondary-50 dark:bg-dark-700 rounded-lg">
                            <x-icon name="paper-clip" class="w-5 h-5 text-dark-500 dark:text-dark-400" />
                            <span class="text-sm flex-1 text-dark-900 dark:text-dark-50">{{ __('pages.rcv_contract_existing') }}</span>
                            <x-button.circle icon="x-mark" color="red" size="sm" wire:click="removeAttachment"
                                title="{{ __('common.delete') }}" />
                        </div>
                    @endif

                    <x-upload wire:model="contract_attachment"
                        label="{{ __('pages.rcv_contract_label') }}"
                        hint="{{ $currentAttachment ? __('pages.rcv_contract_replace_hint') : __('pages.rcv_contract_new_hint') }}"
                        accept="application/pdf,image/jpeg,image/jpg,image/png" />
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="zinc"
                    class="w-full sm:w-auto order-2 sm:order-1">
                    {{ __('common.cancel') }}
                </x-button>
                <x-button type="submit" form="receivable-update" color="blue" icon="check" loading="save"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    {{ __('pages.rcv_btn_update') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>

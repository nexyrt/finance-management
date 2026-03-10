<div>
    <x-button wire:click="$toggle('modal')" color="green" icon="plus" class="w-full sm:w-auto">
        {{ __('pages.rcv_create_title') }}
    </x-button>

    <x-modal wire="modal" size="7xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-2">
                <div class="h-10 w-10 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="currency-dollar" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <h3 class="text-lg font-bold text-dark-900 dark:text-dark-50">{{ __('pages.rcv_create_title') }}</h3>
                    <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.rcv_create_desc') }}</p>
                </div>
            </div>
        </x-slot:title>

        <form id="receivable-create" wire:submit="save">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-0">

                {{-- LEFT: Main Form (2/3) --}}
                <div class="lg:col-span-2 space-y-0 divide-y divide-secondary-100 dark:divide-dark-600/50">

                    {{-- Step 1: Jenis Piutang --}}
                    <div class="p-6">
                        <div class="flex items-start gap-4 mb-5">
                            <div class="shrink-0 w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center">
                                <span class="text-xs font-bold text-white">1</span>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.rcv_section_type') }}</h4>
                                <p class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ __('pages.rcv_section_type_desc') }}</p>
                            </div>
                        </div>

                        {{-- Visual Type Toggle Cards --}}
                        <div class="grid grid-cols-2 gap-3 mb-5">
                            <button type="button" wire:click="$set('type', 'employee_loan')"
                                class="relative flex flex-col items-start gap-2 p-4 rounded-xl border-2 transition-all text-left
                                    {{ $type === 'employee_loan'
                                        ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/10'
                                        : 'border-secondary-200 dark:border-dark-600 hover:border-secondary-300 dark:hover:border-dark-500 bg-white dark:bg-dark-800' }}">
                                @if ($type === 'employee_loan')
                                    <div class="absolute top-3 right-3 w-4 h-4 rounded-full bg-emerald-500 flex items-center justify-center">
                                        <x-icon name="check" class="w-2.5 h-2.5 text-white" />
                                    </div>
                                @endif
                                <div class="w-9 h-9 rounded-lg {{ $type === 'employee_loan' ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-secondary-100 dark:bg-dark-700' }} flex items-center justify-center">
                                    <x-icon name="user" class="w-5 h-5 {{ $type === 'employee_loan' ? 'text-emerald-600 dark:text-emerald-400' : 'text-dark-500 dark:text-dark-400' }}" />
                                </div>
                                <div>
                                    <div class="text-sm font-semibold {{ $type === 'employee_loan' ? 'text-emerald-700 dark:text-emerald-300' : 'text-dark-900 dark:text-dark-50' }}">
                                        {{ __('pages.rcv_type_employee') }}
                                    </div>
                                    <div class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">Max Rp 10.000.000</div>
                                </div>
                            </button>

                            <button type="button" wire:click="$set('type', 'company_loan')"
                                class="relative flex flex-col items-start gap-2 p-4 rounded-xl border-2 transition-all text-left
                                    {{ $type === 'company_loan'
                                        ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/10'
                                        : 'border-secondary-200 dark:border-dark-600 hover:border-secondary-300 dark:hover:border-dark-500 bg-white dark:bg-dark-800' }}">
                                @if ($type === 'company_loan')
                                    <div class="absolute top-3 right-3 w-4 h-4 rounded-full bg-emerald-500 flex items-center justify-center">
                                        <x-icon name="check" class="w-2.5 h-2.5 text-white" />
                                    </div>
                                @endif
                                <div class="w-9 h-9 rounded-lg {{ $type === 'company_loan' ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-secondary-100 dark:bg-dark-700' }} flex items-center justify-center">
                                    <x-icon name="building-office" class="w-5 h-5 {{ $type === 'company_loan' ? 'text-emerald-600 dark:text-emerald-400' : 'text-dark-500 dark:text-dark-400' }}" />
                                </div>
                                <div>
                                    <div class="text-sm font-semibold {{ $type === 'company_loan' ? 'text-emerald-700 dark:text-emerald-300' : 'text-dark-900 dark:text-dark-50' }}">
                                        {{ __('pages.rcv_type_company') }}
                                    </div>
                                    <div class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ __('pages.rcv_contract_hint_required') }}</div>
                                </div>
                            </button>
                        </div>

                        {{-- Debtor Selector --}}
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

                    {{-- Step 2: Detail Pinjaman --}}
                    <div class="p-6">
                        <div class="flex items-start gap-4 mb-5">
                            <div class="shrink-0 w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center">
                                <span class="text-xs font-bold text-white">2</span>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.rcv_section_detail') }}</h4>
                                <p class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ __('pages.rcv_section_detail_desc') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-currency-input wire:model.live="principal_amount" placeholder="0">
                                <x-slot:label>
                                    <div class="flex items-center gap-2">
                                        <span>{{ __('pages.rcv_principal_label') }}</span>
                                        @if ($type === 'employee_loan')
                                            <x-tooltip color="secondary" text="{{ __('pages.rcv_principal_max_hint') }}" position="top" />
                                        @endif
                                    </div>
                                </x-slot:label>
                            </x-currency-input>

                            <x-date wire:model.live="loan_date" label="{{ __('pages.rcv_loan_date_label') }}" />

                            <x-select.native wire:model.live="interest_type" label="{{ __('pages.rcv_interest_type_label') }}" :options="[
                                ['label' => __('pages.rcv_interest_percentage_option'), 'value' => 'percentage'],
                                ['label' => __('pages.rcv_interest_fixed_option'), 'value' => 'fixed'],
                            ]" />

                            @if ($interest_type === 'fixed')
                                <x-currency-input wire:model.live="interest_amount"
                                    label="{{ __('pages.rcv_interest_amount_label') }}"
                                    placeholder="0"
                                    hint="{{ __('pages.rcv_interest_amount_hint') }}" />
                            @else
                                <x-input wire:model.live="interest_rate" type="number" step="0.01"
                                    label="{{ __('pages.rcv_interest_rate_label') }}"
                                    placeholder="0" suffix="%"
                                    hint="{{ __('pages.rcv_interest_rate_hint') }}" />
                            @endif

                            <div class="sm:col-span-2">
                                <x-input wire:model.live="installment_months" type="number"
                                    label="{{ __('pages.rcv_tenor_label') }}" placeholder="1" suffix="{{ __('pages.rcv_months_unit') }}" />
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: Informasi Tambahan --}}
                    <div class="p-6">
                        <div class="flex items-start gap-4 mb-5">
                            <div class="shrink-0 w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center">
                                <span class="text-xs font-bold text-white">3</span>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.rcv_section_info') }}</h4>
                                <p class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ __('pages.rcv_section_info_desc') }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <x-input wire:model="purpose"
                                    label="{{ __('pages.rcv_purpose_label') }}"
                                    placeholder="{{ __('pages.rcv_purpose_placeholder') }}" />

                                <x-input wire:model="disbursement_account"
                                    label="{{ __('pages.rcv_disbursement_account_label') }}"
                                    placeholder="{{ __('pages.rcv_disbursement_account_placeholder') }}"
                                    hint="{{ __('pages.rcv_disbursement_account_hint') }}" />
                            </div>

                            <x-textarea wire:model="notes"
                                label="{{ __('pages.rcv_notes_label') }}"
                                placeholder="{{ __('pages.rcv_notes_placeholder') }}" rows="2" />

                            <x-upload wire:model="contract_attachment"
                                label="{{ __('pages.rcv_contract_label') }}"
                                hint="{{ $type === 'company_loan' ? __('pages.rcv_contract_hint_required') : __('pages.rcv_contract_hint_optional') }}"
                                accept="application/pdf,image/jpeg,image/jpg,image/png" />
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Live Summary Panel (1/3) --}}
                <div class="lg:border-l border-secondary-100 dark:border-dark-600/50 bg-secondary-50/50 dark:bg-dark-700/30">
                    <div class="p-6 sticky top-0">
                        <p class="text-xs font-semibold text-dark-500 dark:text-dark-400 uppercase tracking-wider mb-4">
                            {{ __('pages.rcv_summary_title') }}
                        </p>

                        {{-- Loan Amount --}}
                        <div class="mb-6">
                            @php
                                $principalVal = (int) preg_replace('/[^0-9]/', '', $principal_amount ?? 0);
                                $interestVal  = 0;
                                if ($interest_type === 'fixed') {
                                    $interestVal = (int) preg_replace('/[^0-9]/', '', $interest_amount ?? 0);
                                } else {
                                    $interestVal = round($principalVal * (float)($interest_rate ?? 0) / 100);
                                }
                                $totalVal   = $principalVal + $interestVal;
                                $monthsVal  = max(1, (int)($installment_months ?? 1));
                                $cicilan    = $monthsVal > 0 ? round($totalVal / $monthsVal) : 0;
                                $dueDate    = $loan_date ? \Carbon\Carbon::parse($loan_date)->addMonths($monthsVal) : null;
                            @endphp

                            <div class="text-3xl font-bold text-dark-900 dark:text-dark-50 tabular-nums">
                                Rp {{ number_format($principalVal, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-dark-500 dark:text-dark-400 mt-1">{{ __('pages.rcv_principal_label') }}</div>
                        </div>

                        {{-- Breakdown rows --}}
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.rcv_interest_label') }}</span>
                                <span class="text-sm font-semibold text-dark-900 dark:text-dark-50 tabular-nums">
                                    Rp {{ number_format($interestVal, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.rcv_total_label') }}</span>
                                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">
                                    Rp {{ number_format($totalVal, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="border-t border-secondary-200 dark:border-dark-600 pt-3 flex items-center justify-between">
                                <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.rcv_monthly_installment') }}</span>
                                <span class="text-base font-bold text-dark-900 dark:text-dark-50 tabular-nums">
                                    Rp {{ number_format($cicilan, 0, ',', '.') }}/{{ __('pages.rcv_month_abbr') }}
                                </span>
                            </div>
                        </div>

                        {{-- Timeline --}}
                        <div class="space-y-2.5">
                            <p class="text-xs font-semibold text-dark-500 dark:text-dark-400 uppercase tracking-wider">{{ __('pages.rcv_timeline_title') }}</p>

                            <div class="flex items-start gap-3">
                                <div class="shrink-0 mt-0.5">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                </div>
                                <div>
                                    <div class="text-xs font-medium text-dark-700 dark:text-dark-200">{{ __('pages.rcv_start_date') }}</div>
                                    <div class="text-xs text-dark-500 dark:text-dark-400">
                                        {{ $loan_date ? \Carbon\Carbon::parse($loan_date)->translatedFormat('d M Y') : '—' }}
                                    </div>
                                </div>
                            </div>

                            <div class="ml-1 w-px h-4 bg-secondary-200 dark:bg-dark-600 mx-0.5"></div>

                            <div class="flex items-start gap-3">
                                <div class="shrink-0 mt-0.5">
                                    <div class="w-2 h-2 rounded-full {{ $dueDate && $dueDate->isPast() ? 'bg-red-500' : 'bg-secondary-300 dark:bg-dark-500' }}"></div>
                                </div>
                                <div>
                                    <div class="text-xs font-medium text-dark-700 dark:text-dark-200">{{ __('pages.rcv_due_date') }}</div>
                                    <div class="text-xs text-dark-500 dark:text-dark-400">
                                        {{ $dueDate ? $dueDate->translatedFormat('d M Y') : '—' }}
                                    </div>
                                </div>
                            </div>

                            <div class="ml-1 w-px h-4 bg-secondary-200 dark:bg-dark-600 mx-0.5"></div>

                            <div class="flex items-start gap-3">
                                <div class="shrink-0 mt-0.5">
                                    <div class="w-2 h-2 rounded-full bg-secondary-300 dark:bg-dark-500"></div>
                                </div>
                                <div>
                                    <div class="text-xs font-medium text-dark-700 dark:text-dark-200">{{ __('pages.rcv_tenor_label') }}</div>
                                    <div class="text-xs text-dark-500 dark:text-dark-400">{{ $monthsVal }} {{ __('pages.rcv_months_unit') }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Type badge --}}
                        <div class="mt-6 pt-5 border-t border-secondary-200 dark:border-dark-600">
                            <div class="flex items-center gap-2">
                                <x-icon name="{{ $type === 'employee_loan' ? 'user' : 'building-office' }}"
                                    class="w-4 h-4 text-dark-500 dark:text-dark-400" />
                                <span class="text-xs text-dark-600 dark:text-dark-400">
                                    {{ $type === 'employee_loan' ? __('pages.rcv_type_employee') : __('pages.rcv_type_company') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="zinc"
                    class="w-full sm:w-auto order-2 sm:order-1">
                    {{ __('common.cancel') }}
                </x-button>
                <x-button type="submit" form="receivable-create" color="green" icon="check" loading="save"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    {{ __('pages.rcv_btn_create') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>

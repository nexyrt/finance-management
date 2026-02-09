<div class="space-y-6">
    {{-- Responsive Filters untuk Payments --}}
    <div class="space-y-4">
        {{-- Filter Section --}}
        <div class="flex flex-col gap-4">
            {{-- Main Filters Grid - Responsive --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
                {{-- Payment Method --}}
                <div class="sm:col-span-1">
                    <x-select.styled wire:model.live="paymentMethodFilter" :label="__('pages.payment_method_filter')" :options="[
                        ['label' => __('pages.bank_transfer'), 'value' => 'bank_transfer'],
                        ['label' => __('pages.cash'), 'value' => 'cash'],
                    ]"
                        :placeholder="__('pages.all_methods')" />
                </div>

                {{-- Bank Account --}}
                <div class="sm:col-span-1 lg:col-span-1">
                    <x-select.styled wire:model.live="bankAccountFilter" :label="__('pages.bank_account_filter')" :disabled="$constrainedBankAccountId !== null"
                        :options="$this->bankAccounts
                            ->map(
                                fn($account) => [
                                    'label' => $account->bank_name . ' - ' . $account->account_name,
                                    'value' => $account->id,
                                ],
                            )
                            ->toArray()" :placeholder="__('pages.all_accounts')" searchable />
                </div>

                {{-- Invoice Status --}}
                <div class="sm:col-span-1 lg:col-span-1">
                    <x-select.styled wire:model.live="invoiceStatusFilter" :label="__('pages.invoice_status_filter')" :options="[
                        ['label' => __('pages.status_paid'), 'value' => 'paid'],
                        ['label' => __('pages.status_partially_paid'), 'value' => 'partially_paid'],
                        ['label' => __('pages.status_sent'), 'value' => 'sent'],
                        ['label' => __('pages.status_overdue'), 'value' => 'overdue'],
                    ]"
                        :placeholder="__('pages.all_statuses')" />
                </div>

                {{-- Month Picker --}}
                <div class="sm:col-span-1 lg:col-span-1">
                    <x-date month-year-only wire:model.live="selectedMonth" :label="__('pages.month_picker')"
                        :placeholder="__('pages.select_month')" />
                </div>

                {{-- Date Range --}}
                <div class="sm:col-span-2 lg:col-span-1">
                    <x-date wire:model.live="dateRange" :label="__('pages.date_range')" range :placeholder="__('pages.select_range')" />
                </div>
            </div>

            {{-- Search & Actions Row --}}
            <div class="flex flex-col sm:flex-row gap-3">
                {{-- Search Bar --}}
                <div class="flex-1">
                    <x-input wire:model.live.debounce.300ms="search" :label="__('pages.search_payment')"
                        :placeholder="__('pages.search_placeholder_payments')" icon="magnifying-glass" />
                </div>

                {{-- Export Actions --}}
                <div class="flex items-end gap-2">
                    <x-button wire:click="exportExcel" size="sm" color="green" icon="document-text" outline>
                        <span class="hidden sm:inline">Excel</span>
                        <span class="sm:hidden">XLS</span>
                    </x-button>
                    <x-button wire:click="exportPdf" size="sm" color="red" icon="document" outline>
                        PDF
                    </x-button>
                </div>
            </div>

            {{-- Filter Status Indicator --}}
            @php
                $activeFilters = collect([
                    $paymentMethodFilter,
                    $bankAccountFilter && !$constrainedBankAccountId,
                    $invoiceStatusFilter,
                    $selectedMonth,
                    !empty($dateRange),
                    $search,
                ])
                    ->filter()
                    ->count();
            @endphp

            @if ($activeFilters > 0)
                <div class="flex items-center justify-between">
                    <x-badge :text="__('pages.filters_active', ['count' => $activeFilters])" color="primary" size="sm" />

                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('pages.showing_payments', ['count' => $this->payments->count(), 'total' => $this->payments->total()]) }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Table --}}
    <x-table :$headers :$sort :rows="$this->payments" selectable wire:model="selected" paginate loading>

        {{-- Payment Date Column --}}
        @interact('column_payment_date', $row)
            <div>
                <div class="text-sm font-medium text-dark-900 dark:text-dark-50">
                    {{ \Carbon\Carbon::parse($row->payment_date)->format('d M Y') }}
                </div>
                <div class="text-xs text-dark-500 dark:text-dark-400">
                    {{ \Carbon\Carbon::parse($row->payment_date)->diffForHumans() }}
                </div>
            </div>
        @endinteract

        {{-- Invoice Number Column --}}
        @interact('column_invoice_number', $row)
            <div class="cursor-pointer" wire:click="viewInvoice({{ $row->invoice_id }})"
                loading="viewInvoice({{ $row->invoice_id }})">
                <div class="font-mono font-bold text-primary-600 dark:text-primary-400 hover:text-primary-700">
                    {{ $row->invoice_number }}
                </div>
                <div class="text-xs text-dark-500 dark:text-dark-400">
                    Status:
                    @php
                        $statusConfig = [
                            'paid' => ['text' => __('pages.status_paid'), 'color' => 'green'],
                            'partially_paid' => ['text' => __('pages.status_partially_paid'), 'color' => 'yellow'],
                            'sent' => ['text' => __('pages.status_sent'), 'color' => 'blue'],
                            'overdue' => ['text' => __('pages.status_overdue'), 'color' => 'red'],
                        ];
                        $config = $statusConfig[$row->invoice_status] ?? [
                            'text' => ucfirst($row->invoice_status),
                            'color' => 'gray',
                        ];
                    @endphp
                    <span class="text-{{ $config['color'] }}-600 dark:text-{{ $config['color'] }}-400 font-medium">
                        {{ $config['text'] }}
                    </span>
                </div>
            </div>
        @endinteract

        {{-- Client Column --}}
        @interact('column_client_name', $row)
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 {{ $row->client_type === 'individual'
                        ? 'bg-gradient-to-br from-primary-400 to-primary-600'
                        : 'bg-gradient-to-br from-purple-400 to-purple-600' }} 
                rounded-lg flex items-center justify-center">
                    <x-icon name="{{ $row->client_type === 'individual' ? 'user' : 'building-office' }}"
                        class="w-4 h-4 text-white" />
                </div>
                <div>
                    <p class="font-medium text-dark-900 dark:text-dark-50">{{ $row->client_name }}</p>
                    <div class="text-xs text-dark-500 dark:text-dark-400 capitalize">
                        {{ $row->client_type === 'individual' ? __('common.individual') : __('common.company') }}
                    </div>
                </div>
            </div>
        @endinteract

        {{-- Amount Column --}}
        @interact('column_amount', $row)
            <div class="text-right">
                <div class="font-bold text-lg text-green-600 dark:text-green-400">
                    Rp {{ number_format($row->amount, 0, ',', '.') }}
                </div>
                @if ($row->reference_number)
                    <div class="text-xs text-dark-500 dark:text-dark-400 font-mono">
                        Ref: {{ $row->reference_number }}
                    </div>
                @endif
            </div>
        @endinteract

        {{-- Payment Method Column --}}
        @interact('column_payment_method', $row)
            <x-badge :text="$row->payment_method === 'bank_transfer' ? __('pages.transfer_badge') : __('pages.cash_badge')" :color="$row->payment_method === 'bank_transfer' ? 'blue' : 'green'" :icon="$row->payment_method === 'bank_transfer' ? 'credit-card' : 'banknotes'" />
        @endinteract

        {{-- Bank Account Column --}}
        @interact('column_bank_account', $row)
            <div>
                <div class="font-medium text-dark-900 dark:text-dark-50">{{ $row->bank_name }}</div>
                <div class="text-xs text-dark-500 dark:text-dark-400">{{ $row->account_name }}</div>
            </div>
        @endinteract

        {{-- Actions Column --}}
        @interact('column_actions', $row)
            <div class="flex items-center gap-1">
                <x-button.circle icon="eye" color="blue" size="sm"
                    wire:click="viewInvoice({{ $row->invoice_id }})" loading="viewInvoice({{ $row->invoice_id }})"
                    :title="__('pages.view_invoice_tooltip')" />

                <x-button.circle icon="pencil" color="green" size="sm" wire:click="editPayment({{ $row->id }})"
                    loading="editPayment({{ $row->id }})" :title="__('pages.edit_payment_tooltip')" />

                <x-button.circle icon="trash" color="red" size="sm"
                    wire:click="deletePayment({{ $row->id }})" loading="deletePayment({{ $row->id }})"
                    :title="__('pages.delete_payment_tooltip')" />
            </div>
        @endinteract

    </x-table>

    {{-- Di bagian bawah blade file --}}
    <livewire:invoices.show @invoice-updated="$refresh" />
    <livewire:payments.edit @payment-updated="$refresh" />
</div>

<div class="space-y-6">
    {{-- Filter Section --}}
    <div class="space-y-4">
        <div class="flex flex-col gap-4">
            {{-- Main Filters Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                {{-- Payment Method --}}
                <x-select.styled wire:model.live="paymentMethodFilter" :label="__('pages.payment_method_filter')" :options="[
                    ['label' => __('pages.bank_transfer'), 'value' => 'bank_transfer'],
                    ['label' => __('pages.cash'), 'value' => 'cash'],
                ]" :placeholder="__('pages.all_methods')" />

                {{-- Invoice Status --}}
                <x-select.styled wire:model.live="invoiceStatusFilter" :label="__('pages.invoice_status_filter')" :options="[
                    ['label' => __('pages.status_paid'), 'value' => 'paid'],
                    ['label' => __('pages.status_partially_paid'), 'value' => 'partially_paid'],
                    ['label' => __('pages.status_sent'), 'value' => 'sent'],
                    ['label' => __('pages.status_overdue'), 'value' => 'overdue'],
                ]" :placeholder="__('pages.all_statuses')" />

                {{-- Month Picker --}}
                <x-date month-year-only wire:model.live="selectedMonth"
                    :label="__('pages.month_picker')"
                    :placeholder="__('pages.select_month')" />
            </div>

            {{-- Search + Filter Status Row --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">
                    {{-- Search --}}
                    <div class="w-full sm:w-64">
                        <x-input wire:model.live.debounce.300ms="search"
                            :placeholder="__('pages.search_placeholder_payments')"
                            icon="magnifying-glass" class="h-8" />
                    </div>

                    {{-- Active Filters + Result Count --}}
                    @php
                        $activeFilters = collect([
                            $paymentMethodFilter,
                            $invoiceStatusFilter,
                            $selectedMonth,
                            $search,
                        ])->filter()->count();
                    @endphp

                    <div class="flex items-center gap-3">
                        @if ($activeFilters > 0)
                            <x-badge :text="__('pages.filters_active_count', ['count' => $activeFilters])" color="primary" size="sm" />
                        @endif
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="hidden sm:inline">{{ __('pages.showing_from') }}</span>{{ $this->payments->count() }}<span class="hidden sm:inline">{{ __('pages.showing_of') }}{{ $this->payments->total() }}</span> {{ __('pages.payments_unit') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <x-table :$headers :$sort :rows="$this->payments" selectable wire:model="selected" paginate loading>

        {{-- Payment Date --}}
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

        {{-- Invoice Number --}}
        @interact('column_invoice_number', $row)
            <div>
                <a href="{{ route('invoices.index') }}" wire:navigate
                    class="font-mono font-bold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                    {{ $row->invoice_number }}
                </a>
                <div class="text-xs text-dark-500 dark:text-dark-400">
                    {{ __('pages.status_prefix') }}
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

        {{-- Client --}}
        @interact('column_client_name', $row)
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 {{ $row->client_type === 'individual'
                    ? 'bg-gradient-to-br from-primary-400 to-primary-600'
                    : 'bg-gradient-to-br from-purple-400 to-purple-600' }} rounded-lg flex items-center justify-center flex-shrink-0">
                    <x-icon name="{{ $row->client_type === 'individual' ? 'user' : 'building-office' }}"
                        class="w-4 h-4 text-white" />
                </div>
                <div class="min-w-0">
                    <p class="font-medium text-dark-900 dark:text-dark-50 truncate">{{ $row->client_name }}</p>
                    <div class="text-xs text-dark-500 dark:text-dark-400 capitalize">
                        {{ $row->client_type === 'individual' ? __('common.individual') : __('common.company') }}
                    </div>
                </div>
            </div>
        @endinteract

        {{-- Amount --}}
        @interact('column_amount', $row)
            <div class="text-right">
                <div class="font-bold text-green-600 dark:text-green-400">
                    Rp {{ number_format($row->amount, 0, ',', '.') }}
                </div>
                @if ($row->reference_number)
                    <div class="text-xs text-dark-500 dark:text-dark-400 font-mono">
                        {{ __('pages.ref_prefix') }} {{ $row->reference_number }}
                    </div>
                @endif
            </div>
        @endinteract

        {{-- Payment Method --}}
        @interact('column_payment_method', $row)
            <x-badge :text="$row->payment_method === 'bank_transfer' ? __('pages.transfer_badge') : __('pages.cash_badge')"
                :color="$row->payment_method === 'bank_transfer' ? 'blue' : 'green'"
                :icon="$row->payment_method === 'bank_transfer' ? 'credit-card' : 'banknotes'" />
        @endinteract

        {{-- Actions --}}
        @interact('column_actions', $row)
            <div class="flex items-center gap-1">
                <x-button.circle wire:click="deletePayment({{ $row->id }})"
                    loading="deletePayment({{ $row->id }})" color="red" icon="trash" size="sm" />
            </div>
        @endinteract
    </x-table>

    {{-- Bulk Actions Bar --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-lg border border-secondary-200 dark:border-dark-600 px-4 sm:px-6 py-4 sm:min-w-96">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-dark-900 dark:text-dark-50"
                            x-text="`${show.length} {{ __('pages.pmt_bulk_selected') }}`"></div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.bulk_action_hint') }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 justify-end">
                    <x-button wire:click="confirmBulkDelete" size="sm" color="red" icon="trash" class="whitespace-nowrap">
                        {{ __('pages.bulk_delete_btn') }}
                    </x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="gray" icon="x-mark" class="whitespace-nowrap">
                        {{ __('pages.bulk_cancel_btn') }}
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</div>

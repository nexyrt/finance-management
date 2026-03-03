<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.transfers_and_adjustments') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                {{ __('pages.transfers_description') }}
            </p>
        </div>
        <button
            wire:click="$toggle('guideModal')"
            class="h-9 px-4 flex items-center gap-2 rounded-xl border border-zinc-200 dark:border-dark-600 bg-white dark:bg-dark-800 text-dark-500 dark:text-dark-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-300 dark:hover:border-indigo-700 text-sm font-medium transition-all"
        >
            <x-icon name="information-circle" class="w-4 h-4" />
            {{ __('pages.cf_transfer_guide_btn') }}
        </button>
    </div>

    {{-- Section Switcher --}}
    <div class="flex items-center gap-1 p-1 bg-secondary-100 dark:bg-dark-700 rounded-xl w-fit">
        <button wire:click="switchSection('transfers')"
            class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                {{ $section === 'transfers'
                    ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm'
                    : 'text-dark-500 dark:text-dark-400 hover:text-dark-700 dark:hover:text-dark-200' }}">
            <div class="flex items-center gap-2">
                <x-icon name="arrow-path" class="w-4 h-4" />
                {{ __('pages.transfers') }}
            </div>
        </button>
        <button wire:click="switchSection('adjustments')"
            class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                {{ $section === 'adjustments'
                    ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm'
                    : 'text-dark-500 dark:text-dark-400 hover:text-dark-700 dark:hover:text-dark-200' }}">
            <div class="flex items-center gap-2">
                <x-icon name="adjustments-horizontal" class="w-4 h-4" />
                {{ __('pages.adjustments') }}
            </div>
        </button>
    </div>

    {{-- ============================================ --}}
    {{-- TRANSFER SECTION --}}
    {{-- ============================================ --}}
    @if ($section === 'transfers')
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            <x-card class="hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <x-icon name="arrow-path" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.total_transfers') }}</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            Rp {{ number_format($this->totalTransfers, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </x-card>

            <x-card class="hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-orange-50 dark:bg-orange-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <x-icon name="banknotes" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                    </div>
                    <div>
                        <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.total_admin_fees') }}</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                            Rp {{ number_format($this->totalAdminFees, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </x-card>

            <x-card class="hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <x-icon name="calendar" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div>
                        <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.period') }}</p>
                        <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                            @if (!empty($dateRange) && count($dateRange) >= 2)
                                {{ \Carbon\Carbon::parse($dateRange[0])->format('d M') }} -
                                {{ \Carbon\Carbon::parse($dateRange[1])->format('d M Y') }}
                            @else
                                {{ __('pages.all_time') }}
                            @endif
                        </p>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Filter Section --}}
        <div class="space-y-4">
            <div class="flex flex-col gap-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <x-date wire:model.live="dateRange" label="{{ __('pages.period') }}" range placeholder="{{ __('pages.select_date_range') }}" />
                    <x-select.styled wire:model.live="bankAccountFilters" :request="route('api.bank-accounts')"
                        label="{{ __('common.bank_accounts') }}" placeholder="{{ __('pages.all_banks') }}" multiple searchable />
                    <x-input wire:model.live.debounce.300ms="search" label="{{ __('common.search') }}" placeholder="{{ __('pages.search_data') }}"
                        icon="magnifying-glass" />
                </div>

                @php
                    $activeFilters = collect([
                        !empty($dateRange) && count($dateRange) >= 1,
                        !empty($bankAccountFilters),
                        $search,
                    ])->filter()->count();
                @endphp

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        @if ($activeFilters > 0)
                            <x-badge text="{{ $activeFilters }} {{ __('pages.filter_active') }}" color="primary" size="sm" />
                        @endif
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="hidden sm:inline">{{ __('pages.showing') }} </span>{{ $this->rows->count() }}
                            <span class="hidden sm:inline">{{ __('pages.of') }} {{ $this->rows->total() }}</span> {{ __('pages.results') }}
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-button wire:click="export" color="green" icon="arrow-down-tray" size="sm" loading="export">
                            {{ __('common.export') }}
                        </x-button>
                        <x-button wire:click="$dispatch('open-transfer-modal')" color="blue" icon="plus" size="sm">
                            {{ __('pages.new_transfer') }}
                        </x-button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transfer Table --}}
        <x-table :$headers :$sort :rows="$this->rows" selectable wire:model="selected" paginate filter>

            @interact('column_transaction_date', $row)
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-secondary-100 to-secondary-200 dark:from-dark-700 dark:to-dark-600 rounded-lg flex items-center justify-center">
                        <x-icon name="calendar" class="w-5 h-5 text-secondary-600 dark:text-secondary-400" />
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                            {{ $row->transaction_date->format('d M Y') }}
                        </div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">
                            {{ $row->transaction_date->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @endinteract

            @interact('column_from_account', $row)
                @if ($row->from_account)
                    <div class="flex items-center gap-2">
                        <div class="h-8 w-8 bg-gradient-to-br from-red-100 to-red-200 dark:from-red-900/30 dark:to-red-800/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <x-icon name="arrow-up" class="w-4 h-4 text-red-600 dark:text-red-400" />
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ $row->from_account->bank_name }}</div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">{{ $row->from_account->account_number }}</div>
                        </div>
                    </div>
                @endif
            @endinteract

            @interact('column_to_account', $row)
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 rounded-lg flex items-center justify-center flex-shrink-0">
                        <x-icon name="arrow-down" class="w-4 h-4 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ $row->bankAccount->bank_name }}</div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">{{ $row->bankAccount->account_number }}</div>
                    </div>
                </div>
            @endinteract

            @interact('column_description', $row)
                <div class="max-w-xs">
                    <div class="text-sm font-medium text-dark-900 dark:text-dark-50 line-clamp-2 mb-1">
                        {{ $row->description }}
                    </div>
                    @if ($row->reference_number)
                        <div class="flex items-center gap-1.5">
                            <x-icon name="document-duplicate" class="w-3 h-3 text-dark-400" />
                            <span class="text-xs text-dark-500 dark:text-dark-400 font-mono">{{ $row->reference_number }}</span>
                        </div>
                    @endif
                </div>
            @endinteract

            @interact('column_amount', $row)
                <div class="text-right">
                    <div class="text-lg font-bold text-blue-600 dark:text-blue-400">
                        Rp {{ number_format($row->amount, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.net_transfer') }}</div>
                </div>
            @endinteract

            @interact('column_total_debit', $row)
                <div class="text-right">
                    <div class="text-lg font-bold text-red-600 dark:text-red-400">
                        Rp {{ number_format($row->total_debit, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-orange-600 dark:text-orange-400">
                        + Rp {{ number_format($row->total_debit - $row->amount, 0, ',', '.') }} {{ __('pages.admin_fee_label') }}
                    </div>
                </div>
            @endinteract

            @interact('column_action', $row)
                <div class="flex items-center justify-center gap-1">
                    @if ($row->attachment_path)
                        <x-button.circle icon="paper-clip" color="primary" size="sm"
                            wire:click="$dispatch('view-attachment', {sourceType: 'transaction', id: {{ $row->id }}})"
                            title="{{ __('pages.view_attachment') }}" />
                    @endif
                    <x-button.circle icon="trash" color="red" size="sm"
                        wire:click="$dispatch('delete-transaction', {transactionId: {{ $row->id }}})"
                        title="{{ __('common.delete') }}" />
                </div>
            @endinteract
        </x-table>

        {{-- Transfer Bulk Actions --}}
        <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
            class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
            <div class="bg-white dark:bg-dark-800 rounded-xl shadow-lg border border-secondary-200 dark:border-dark-600 px-4 sm:px-6 py-4 sm:min-w-96">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                            <x-icon name="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <div class="font-semibold text-dark-900 dark:text-dark-50" x-text="`${show.length} {{ __('pages.transfers_selected') }}`"></div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.select_action_for_selected') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 justify-end">
                        <x-button wire:click="exportSelected" size="sm" color="green" icon="arrow-down-tray"
                            loading="exportSelected" class="whitespace-nowrap">{{ __('common.export') }}</x-button>
                        <x-button wire:click="bulkDelete" size="sm" color="red" icon="trash"
                            loading="executeBulkDelete" class="whitespace-nowrap">{{ __('common.delete') }}</x-button>
                        <x-button wire:click="$set('selected', [])" size="sm" color="secondary" icon="x-mark"
                            class="whitespace-nowrap">{{ __('common.cancel') }}</x-button>
                    </div>
                </div>
            </div>
        </div>

    {{-- ============================================ --}}
    {{-- ADJUSTMENTS SECTION --}}
    {{-- ============================================ --}}
    @else
        {{-- Adjustment Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            <x-card class="hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <x-icon name="minus-circle" class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>
                    <div>
                        <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.total_debit') }}</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                            Rp {{ number_format($this->adjStats['total_debits'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </x-card>

            <x-card class="hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <x-icon name="plus-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.total_credit') }}</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            Rp {{ number_format($this->adjStats['total_credits'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </x-card>

            <x-card class="hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-amber-50 dark:bg-amber-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <x-icon name="adjustments-horizontal" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.net_adjustment') }}</p>
                        <p class="text-2xl font-bold {{ $this->adjStats['net_adjustment'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            Rp {{ number_format($this->adjStats['net_adjustment'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Adjustment Filters --}}
        <div class="space-y-4">
            <div class="flex flex-col gap-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <x-date wire:model.live="adjDateRange" label="{{ __('pages.period') }}" range placeholder="{{ __('pages.select_date_range') }}" />
                    <x-select.styled wire:model.live="adjCategoryFilters"
                        :request="['url' => route('api.transaction-categories'), 'method' => 'get', 'params' => ['type' => 'adjustment']]"
                        label="{{ __('common.category') }}" placeholder="{{ __('pages.all_categories') }}" multiple searchable />
                    <x-select.styled wire:model.live="adjBankAccountFilters" :request="route('api.bank-accounts')"
                        label="{{ __('common.bank_accounts') }}" placeholder="{{ __('pages.all_banks') }}" multiple searchable />
                    <x-input wire:model.live.debounce.300ms="adjSearch" label="{{ __('common.search') }}" placeholder="{{ __('pages.search_data') }}"
                        icon="magnifying-glass" />
                </div>

                @php
                    $adjActiveFilters = collect([
                        !empty($adjDateRange) && count($adjDateRange) >= 1,
                        !empty($adjCategoryFilters),
                        !empty($adjBankAccountFilters),
                        $adjSearch,
                    ])->filter()->count();
                @endphp

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        @if ($adjActiveFilters > 0)
                            <x-badge text="{{ $adjActiveFilters }} {{ __('pages.filter_active') }}" color="primary" size="sm" />
                        @endif
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="hidden sm:inline">{{ __('pages.showing') }} </span>{{ $this->adjRows->count() }}
                            <span class="hidden sm:inline">{{ __('pages.of') }} {{ $this->adjRows->total() }}</span> {{ __('pages.results') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Adjustment Table --}}
        <x-table :headers="$adjHeaders" :sort="$adjSort" :rows="$this->adjRows" selectable wire:model="adjSelected" paginate>

            @interact('column_transaction_date', $row)
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-secondary-100 to-secondary-200 dark:from-dark-700 dark:to-dark-600 rounded-lg flex items-center justify-center">
                        <x-icon name="calendar" class="w-5 h-5 text-secondary-600 dark:text-secondary-400" />
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                            {{ $row->transaction_date->format('d M Y') }}
                        </div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">
                            {{ $row->transaction_date->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @endinteract

            @interact('column_transaction_type', $row)
                <x-badge :text="$row->transaction_type === 'debit' ? __('pages.debit_negative') : __('pages.credit_positive')" size="sm"
                    :color="$row->transaction_type === 'debit' ? 'red' : 'green'" />
            @endinteract

            @interact('column_description', $row)
                <div class="max-w-xs">
                    <div class="text-sm font-medium text-dark-900 dark:text-dark-50 line-clamp-2">
                        {{ $row->description ?? '-' }}
                    </div>
                    @if ($row->reference_number)
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <x-icon name="document-duplicate" class="w-3 h-3 text-dark-400" />
                            <span class="text-xs text-dark-500 dark:text-dark-400 font-mono">{{ $row->reference_number }}</span>
                        </div>
                    @endif
                </div>
            @endinteract

            @interact('column_category', $row)
                @if ($row->category)
                    <x-badge :text="translate_category($row->category->label)" size="sm" color="amber" />
                @else
                    <x-badge text="{{ __('pages.uncategorized') }}" size="sm" color="gray" />
                @endif
            @endinteract

            @interact('column_bank_account', $row)
                @if ($row->bankAccount)
                    <div class="flex items-center gap-2">
                        <div class="h-8 w-8 bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900/30 dark:to-primary-800/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <x-icon name="building-library" class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ $row->bankAccount->bank_name }}</div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">{{ $row->bankAccount->account_name }}</div>
                        </div>
                    </div>
                @endif
            @endinteract

            @interact('column_amount', $row)
                <div class="text-right">
                    <span class="text-lg font-bold {{ $row->transaction_type === 'debit' ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                        {{ $row->transaction_type === 'debit' ? '-' : '+' }}
                        Rp {{ number_format($row->amount, 0, ',', '.') }}
                    </span>
                </div>
            @endinteract

            @interact('column_action', $row)
                <div class="flex items-center justify-center gap-1">
                    @if ($row->attachment_path)
                        <x-button.circle icon="paper-clip" color="primary" size="sm"
                            wire:click="$dispatch('view-attachment', {sourceType: 'transaction', id: {{ $row->id }}})"
                            title="{{ __('pages.view_attachment') }}" />
                    @endif
                    <x-button.circle icon="trash" color="red" size="sm"
                        wire:click="$dispatch('delete-transaction', {transactionId: {{ $row->id }}})"
                        title="{{ __('common.delete') }}" />
                </div>
            @endinteract
        </x-table>

        {{-- Adjustment Bulk Actions --}}
        <div x-data="{ show: @entangle('adjSelected').live }" x-show="show.length > 0" x-transition
            class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
            <div class="bg-white dark:bg-dark-800 rounded-xl shadow-lg border border-secondary-200 dark:border-dark-600 px-4 sm:px-6 py-4 sm:min-w-96">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-amber-50 dark:bg-amber-900/20 rounded-xl flex items-center justify-center">
                            <x-icon name="check-circle" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                        </div>
                        <div>
                            <div class="font-semibold text-dark-900 dark:text-dark-50" x-text="`${show.length} {{ __('pages.adjustments_selected') }}`"></div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.select_action_for_selected') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 justify-end">
                        <x-button wire:click="adjBulkDelete" size="sm" color="red" icon="trash"
                            loading="executeAdjBulkDelete" class="whitespace-nowrap">{{ __('common.delete') }}</x-button>
                        <x-button wire:click="$set('adjSelected', [])" size="sm" color="secondary" icon="x-mark"
                            class="whitespace-nowrap">{{ __('common.cancel') }}</x-button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Child Components (shared) --}}
    <livewire:cash-flow.attachment-viewer />
    <livewire:transactions.transfer @transfer-completed="$refresh" />
    <livewire:transactions.delete @transaction-deleted="$refresh" />

    {{-- Workflow Guide Modal --}}
    <x-modal wire="guideModal" size="3xl" center>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="arrows-right-left" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.cf_transfer_guide_title') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.cf_transfer_guide_desc') }}</p>
                </div>
            </div>
        </x-slot:title>

        <div x-data="{ tab: 'transfer' }" class="space-y-5">
            {{-- Tab Navigation --}}
            <div class="flex flex-wrap gap-1 p-1 bg-zinc-100 dark:bg-dark-700 rounded-xl border border-zinc-200 dark:border-dark-600">
                <button
                    @click="tab = 'transfer'"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center"
                    :class="tab === 'transfer'
                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'"
                >
                    <x-icon name="arrows-right-left" class="w-3.5 h-3.5 flex-shrink-0" />
                    <span>{{ __('pages.cf_transfer_guide_tab_transfer') }}</span>
                </button>
                <button
                    @click="tab = 'adjustment'"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center"
                    :class="tab === 'adjustment'
                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'"
                >
                    <x-icon name="adjustments-horizontal" class="w-3.5 h-3.5 flex-shrink-0" />
                    <span>{{ __('pages.cf_transfer_guide_tab_adjustment') }}</span>
                </button>
            </div>

            {{-- TAB 1: TRANSFER --}}
            <div x-show="tab === 'transfer'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="relative">
                    <div class="absolute left-6 top-10 bottom-10 w-0.5 bg-gradient-to-b from-blue-300 via-purple-300 to-emerald-300 dark:from-blue-700 dark:via-purple-700 dark:to-emerald-700 hidden sm:block"></div>
                    <div class="space-y-4">
                        {{-- Step 1 --}}
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center shadow-lg shadow-blue-200 dark:shadow-blue-900/40 z-10">
                                <span class="text-white font-bold text-sm">1</span>
                            </div>
                            <div class="flex-1 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <x-icon name="plus-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-blue-900 dark:text-blue-200 mb-1">{{ __('pages.cf_transfer_guide_step1_title') }}</h4>
                                        <p class="text-sm text-blue-700 dark:text-blue-300 mb-2">{{ __('pages.cf_transfer_guide_step1_desc') }}</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                <span>{{ __('pages.cf_transfer_guide_step1_tip1') }}</span>
                                            </div>
                                            <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                <span>{{ __('pages.cf_transfer_guide_step1_tip2') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center shadow-lg shadow-purple-200 dark:shadow-purple-900/40 z-10">
                                <span class="text-white font-bold text-sm">2</span>
                            </div>
                            <div class="flex-1 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <x-icon name="bolt" class="w-5 h-5 text-purple-600 dark:text-purple-400 flex-shrink-0 mt-0.5" />
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-purple-900 dark:text-purple-200 mb-1">{{ __('pages.cf_transfer_guide_step2_title') }}</h4>
                                        <p class="text-sm text-purple-700 dark:text-purple-300 mb-2">{{ __('pages.cf_transfer_guide_step2_desc') }}</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                <span>{{ __('pages.cf_transfer_guide_step2_tip1') }}</span>
                                            </div>
                                            <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                <span>{{ __('pages.cf_transfer_guide_step2_tip2') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center shadow-lg shadow-emerald-200 dark:shadow-emerald-900/40 z-10">
                                <span class="text-white font-bold text-sm">3</span>
                            </div>
                            <div class="flex-1 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <x-icon name="eye" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" />
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-emerald-900 dark:text-emerald-200 mb-1">{{ __('pages.cf_transfer_guide_step3_title') }}</h4>
                                        <p class="text-sm text-emerald-700 dark:text-emerald-300">{{ __('pages.cf_transfer_guide_step3_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 2: PENYESUAIAN --}}
            <div x-show="tab === 'adjustment'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="space-y-4">
                    {{-- What is Adjustment --}}
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                <x-icon name="adjustments-horizontal" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-amber-900 dark:text-amber-200 mb-1">{{ __('pages.cf_transfer_guide_adj_what_title') }}</h4>
                                <p class="text-xs text-amber-700 dark:text-amber-300">{{ __('pages.cf_transfer_guide_adj_what_desc') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- When to use --}}
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                <x-icon name="light-bulb" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-2">{{ __('pages.cf_transfer_guide_adj_when_title') }}</h4>
                                <div class="space-y-1.5">
                                    <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                        <span>{{ __('pages.cf_transfer_guide_adj_when1') }}</span>
                                    </div>
                                    <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                        <span>{{ __('pages.cf_transfer_guide_adj_when2') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Debit/Credit signs --}}
                    <div class="p-4 bg-gray-50 dark:bg-dark-700 rounded-xl border border-gray-200 dark:border-dark-600">
                        <div class="flex items-start gap-3">
                            <x-icon name="information-circle" class="w-5 h-5 text-gray-500 dark:text-gray-400 flex-shrink-0 mt-0.5" />
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-2">{{ __('pages.cf_transfer_guide_adj_sign_title') }}</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="p-2.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-900/40 rounded-lg">
                                        <p class="text-xs font-semibold text-red-800 dark:text-red-200">{{ __('pages.cf_transfer_guide_adj_debit') }}</p>
                                        <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">{{ __('pages.cf_transfer_guide_adj_debit_desc') }}</p>
                                    </div>
                                    <div class="p-2.5 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900/40 rounded-lg">
                                        <p class="text-xs font-semibold text-green-800 dark:text-green-200">{{ __('pages.cf_transfer_guide_adj_credit') }}</p>
                                        <p class="text-xs text-green-600 dark:text-green-400 mt-0.5">{{ __('pages.cf_transfer_guide_adj_credit_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-slot:footer>
            <div class="flex justify-end">
                <x-button wire:click="$toggle('guideModal')" color="primary" icon="check">
                    {{ __('pages.client_guide_got_it') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>

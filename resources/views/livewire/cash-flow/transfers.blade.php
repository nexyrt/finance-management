<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                Transfer & Penyesuaian
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                Kelola transfer antar rekening dan penyesuaian saldo
            </p>
        </div>
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
                Transfer
            </div>
        </button>
        <button wire:click="switchSection('adjustments')"
            class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                {{ $section === 'adjustments'
                    ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm'
                    : 'text-dark-500 dark:text-dark-400 hover:text-dark-700 dark:hover:text-dark-200' }}">
            <div class="flex items-center gap-2">
                <x-icon name="adjustments-horizontal" class="w-4 h-4" />
                Penyesuaian
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
                        <p class="text-sm text-dark-600 dark:text-dark-400">Total Transfer</p>
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
                        <p class="text-sm text-dark-600 dark:text-dark-400">Total Biaya Admin</p>
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
                        <p class="text-sm text-dark-600 dark:text-dark-400">Periode</p>
                        <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                            @if (!empty($dateRange) && count($dateRange) >= 2)
                                {{ \Carbon\Carbon::parse($dateRange[0])->format('d M') }} -
                                {{ \Carbon\Carbon::parse($dateRange[1])->format('d M Y') }}
                            @else
                                Semua Waktu
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
                    <x-date wire:model.live="dateRange" label="Periode" range placeholder="Pilih range tanggal..." />
                    <x-select.styled wire:model.live="bankAccountFilters" label="Bank Account" :options="$this->bankAccounts"
                        placeholder="Semua bank..." multiple searchable />
                    <x-input wire:model.live.debounce.300ms="search" label="Cari" placeholder="Cari data..."
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
                            <x-badge text="{{ $activeFilters }} filter aktif" color="primary" size="sm" />
                        @endif
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="hidden sm:inline">Menampilkan </span>{{ $this->rows->count() }}
                            <span class="hidden sm:inline">dari {{ $this->rows->total() }}</span> hasil
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-button wire:click="export" color="green" icon="arrow-down-tray" size="sm" loading="export">
                            Export
                        </x-button>
                        <x-button wire:click="$dispatch('open-transfer-modal')" color="blue" icon="plus" size="sm">
                            Transfer Baru
                        </x-button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transfer Table --}}
        <x-table :$headers :$sort :rows="$this->rows" selectable wire:model="selected" paginate filter loading>

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
                    <div class="text-xs text-dark-500 dark:text-dark-400">Transfer bersih</div>
                </div>
            @endinteract

            @interact('column_total_debit', $row)
                <div class="text-right">
                    <div class="text-lg font-bold text-red-600 dark:text-red-400">
                        Rp {{ number_format($row->total_debit, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-orange-600 dark:text-orange-400">
                        + Rp {{ number_format($row->total_debit - $row->amount, 0, ',', '.') }} admin
                    </div>
                </div>
            @endinteract

            @interact('column_action', $row)
                <div class="flex items-center justify-center gap-1">
                    @if ($row->attachment_path)
                        <x-button.circle icon="paper-clip" color="primary" size="sm"
                            wire:click="$dispatch('view-attachment', {sourceType: 'transaction', id: {{ $row->id }}})"
                            title="Lihat Lampiran" />
                    @endif
                    <x-button.circle icon="trash" color="red" size="sm"
                        wire:click="$dispatch('delete-transaction', {transactionId: {{ $row->id }}})"
                        title="Hapus" />
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
                            <div class="font-semibold text-dark-900 dark:text-dark-50" x-text="`${show.length} transfer dipilih`"></div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">Pilih aksi untuk item yang dipilih</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 justify-end">
                        <x-button wire:click="exportSelected" size="sm" color="green" icon="arrow-down-tray"
                            loading="exportSelected" class="whitespace-nowrap">Export</x-button>
                        <x-button wire:click="bulkDelete" size="sm" color="red" icon="trash"
                            loading="executeBulkDelete" class="whitespace-nowrap">Hapus</x-button>
                        <x-button wire:click="$set('selected', [])" size="sm" color="secondary" icon="x-mark"
                            class="whitespace-nowrap">Batal</x-button>
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
                        <p class="text-sm text-dark-600 dark:text-dark-400">Total Debit</p>
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
                        <p class="text-sm text-dark-600 dark:text-dark-400">Total Credit</p>
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
                        <p class="text-sm text-dark-600 dark:text-dark-400">Net Adjustment</p>
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
                    <x-date wire:model.live="adjDateRange" label="Periode" range placeholder="Pilih range tanggal..." />
                    <x-select.styled wire:model.live="adjCategoryFilters" label="Kategori" :options="$this->adjustmentCategories"
                        placeholder="Semua kategori..." multiple searchable />
                    <x-select.styled wire:model.live="adjBankAccountFilters" label="Bank Account" :options="$this->bankAccounts"
                        placeholder="Semua bank..." multiple searchable />
                    <x-input wire:model.live.debounce.300ms="adjSearch" label="Cari" placeholder="Cari data..."
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
                            <x-badge text="{{ $adjActiveFilters }} filter aktif" color="primary" size="sm" />
                        @endif
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="hidden sm:inline">Menampilkan </span>{{ $this->adjRows->count() }}
                            <span class="hidden sm:inline">dari {{ $this->adjRows->total() }}</span> hasil
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Adjustment Table --}}
        <x-table :headers="$adjHeaders" :sort="$adjSort" :rows="$this->adjRows" selectable wire:model="adjSelected" paginate loading>

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
                <x-badge :text="$row->transaction_type === 'debit' ? 'Debit (-)' : 'Credit (+)'" size="sm"
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
                    <x-badge :text="$row->category->label" size="sm" color="amber" />
                @else
                    <x-badge text="Uncategorized" size="sm" color="gray" />
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
                            title="Lihat Lampiran" />
                    @endif
                    <x-button.circle icon="trash" color="red" size="sm"
                        wire:click="$dispatch('delete-transaction', {transactionId: {{ $row->id }}})"
                        title="Hapus" />
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
                            <div class="font-semibold text-dark-900 dark:text-dark-50" x-text="`${show.length} penyesuaian dipilih`"></div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">Pilih aksi untuk item yang dipilih</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 justify-end">
                        <x-button wire:click="adjBulkDelete" size="sm" color="red" icon="trash"
                            loading="executeAdjBulkDelete" class="whitespace-nowrap">Hapus</x-button>
                        <x-button wire:click="$set('adjSelected', [])" size="sm" color="secondary" icon="x-mark"
                            class="whitespace-nowrap">Batal</x-button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Child Components (shared) --}}
    <livewire:cash-flow.attachment-viewer />
    <livewire:transactions.transfer @transfer-completed="$refresh" />
    <livewire:transactions.delete @transaction-deleted="$refresh" />
</div>

<div class="space-y-6">
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-dark-600 dark:text-dark-400 mb-1">Total Pengeluaran</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                        Rp {{ number_format($this->totalExpense, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-dark-500 dark:text-dark-400 mt-2">
                        {{ $this->rows->total() }} transaksi
                    </p>
                </div>
                <div
                    class="h-12 w-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="arrow-trending-down" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-dark-600 dark:text-dark-400 mb-1">Rata-rata per Transaksi</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        Rp
                        {{ number_format($this->rows->total() > 0 ? $this->totalExpense / $this->rows->total() : 0, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-dark-500 dark:text-dark-400 mt-2">Berdasarkan filter aktif</p>
                </div>
                <div
                    class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="calculator" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-dark-600 dark:text-dark-400 mb-1">Periode</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        @if (!empty($dateRange) && count($dateRange) >= 2)
                            {{ \Carbon\Carbon::parse($dateRange[0])->format('d M') }} -
                            {{ \Carbon\Carbon::parse($dateRange[1])->format('d M Y') }}
                        @else
                            Semua Waktu
                        @endif
                    </p>
                    <p class="text-xs text-dark-500 dark:text-dark-400 mt-2">
                        {{ !empty($dateRange) ? 'Custom range' : 'Tidak ada filter' }}
                    </p>
                </div>
                <div
                    class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="calendar" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-xl p-4 lg:p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-date wire:model.live="dateRange" label="Periode" range placeholder="Pilih range tanggal..." />
            <x-select.styled wire:model.live="bankAccountFilters" label="Bank Account" :options="$this->bankAccounts"
                placeholder="Semua bank..." multiple searchable />
            <x-select.styled wire:model.live="categoryFilters" label="Kategori" :options="$this->expenseCategories"
                placeholder="Semua kategori..." multiple searchable />
            <x-input wire:model.live.debounce.300ms="search" label="Cari" placeholder="Cari data..."
                icon="magnifying-glass" />
        </div>

        @php
            $activeFilters = collect([
                !empty($dateRange) && count($dateRange) >= 1,
                !empty($bankAccountFilters),
                !empty($categoryFilters),
                $search,
            ])
                ->filter()
                ->count();
        @endphp

        @if ($activeFilters > 0)
            <div class="mt-4 pt-4 border-t border-secondary-200 dark:border-dark-600">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <x-badge text="{{ $activeFilters }} filter aktif" color="primary" size="sm" />
                        <button
                            wire:click="$set('dateRange', []); $set('bankAccountFilters', []); $set('categoryFilters', []); $set('search', null);"
                            class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
                            Reset semua filter
                        </button>
                    </div>
                    <div class="text-sm text-dark-500 dark:text-dark-400">
                        Menampilkan {{ $this->rows->count() }} dari {{ $this->rows->total() }} pengeluaran
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-xl overflow-hidden">
        <div class="px-4 lg:px-6 py-4 border-b border-secondary-200 dark:border-dark-600">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50">Daftar Pengeluaran</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Transaksi pengeluaran operasional</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <x-button wire:click="exportPdf" color="red" icon="document-text" size="sm" loading="exportPdf">
                        Export PDF
                    </x-button>
                    <x-button wire:click="export" color="green" icon="arrow-down-tray" size="sm"
                        loading="export">Export Excel</x-button>
                    <x-button wire:click="$dispatch('create-transaction', {allowedTypes: ['debit']})" color="primary"
                        icon="plus" size="sm">Tambah</x-button>
                </div>
            </div>
        </div>

        <div class="px-4 lg:px-6 py-4">
            <x-table :$headers :$sort :rows="$this->rows" selectable wire:model="selected" paginate filter loading>

                @interact('column_transaction_date', $row)
                    <div class="flex items-center gap-3">
                        <div
                            class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-secondary-100 to-secondary-200 dark:from-dark-700 dark:to-dark-600 rounded-lg flex items-center justify-center">
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

                @interact('column_category', $row)
                    @if ($row->category)
                        <div
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg">
                            <x-icon name="tag" class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" />
                            <span
                                class="text-xs font-medium text-purple-700 dark:text-purple-300">{{ $row->category->label }}</span>
                        </div>
                    @else
                        <x-badge text="Belum dikategorikan" color="amber" icon="exclamation-triangle" size="sm" />
                    @endif
                @endinteract

                @interact('column_description', $row)
                    <div class="max-w-xs">
                        <div class="text-sm font-medium text-dark-900 dark:text-dark-50 line-clamp-2 mb-1">
                            {{ $row->description }}
                        </div>
                        @if ($row->reference_number)
                            <div class="flex items-center gap-1.5">
                                <x-icon name="document-duplicate" class="w-3 h-3 text-dark-400" />
                                <span
                                    class="text-xs text-dark-500 dark:text-dark-400 font-mono">{{ $row->reference_number }}</span>
                            </div>
                        @endif
                        @if ($row->attachment_path)
                            <div class="flex items-center gap-1 text-xs text-primary-600 dark:text-primary-400 mt-1">
                                <x-icon name="paper-clip" class="w-3 h-3" />
                                <span class="font-medium">Ada lampiran</span>
                            </div>
                        @endif
                    </div>
                @endinteract

                @interact('column_bank_account', $row)
                    @if ($row->bankAccount)
                        <div class="flex items-center gap-2">
                            <div
                                class="h-8 w-8 bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900/30 dark:to-primary-800/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <x-icon name="building-library" class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                                    {{ $row->bankAccount->bank_name }}</div>
                                <div class="text-xs text-dark-500 dark:text-dark-400">
                                    {{ $row->bankAccount->account_number }}</div>
                            </div>
                        </div>
                    @endif
                @endinteract

                @interact('column_amount', $row)
                    <div class="text-right">
                        <div class="text-xl font-bold text-red-600 dark:text-red-400">
                            Rp {{ number_format($row->amount, 0, ',', '.') }}
                        </div>
                    </div>
                @endinteract

                @interact('column_action', $row)
                    <div class="flex items-center justify-center gap-1">
                        @if (!$row->category_id)
                            <x-button.circle icon="tag" color="amber" size="sm"
                                wire:click="$dispatch('categorize-transaction', {id: {{ $row->id }}})"
                                title="Kategorikan" />
                        @endif
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
        </div>
    </div>

    {{-- Bulk Actions --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
        <div
            class="bg-white dark:bg-dark-800 rounded-xl shadow-lg border border-secondary-200 dark:border-dark-600 px-4 sm:px-6 py-4 sm:min-w-96">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-red-600 dark:text-red-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-dark-900 dark:text-dark-50"
                            x-text="`${show.length} pengeluaran dipilih`"></div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">Pilih aksi untuk item yang dipilih</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 justify-end">
                    <x-button wire:click="exportSelected" size="sm" color="green" icon="arrow-down-tray"
                        loading="exportSelected" class="whitespace-nowrap">Export</x-button>
                    <x-button wire:click="openBulkCategorize" size="sm" color="amber" icon="tag"
                        loading="openBulkCategorize" class="whitespace-nowrap">Kategorikan</x-button>
                    <x-button wire:click="bulkDelete" size="sm" color="red" icon="trash"
                        loading="executeBulkDelete" class="whitespace-nowrap">Hapus</x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="secondary" icon="x-mark"
                        class="whitespace-nowrap">Batal</x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Child Components --}}
    <livewire:transactions.create @transaction-created="$refresh" />
    <livewire:transactions.categorize @transaction-categorized="$refresh" />
</div>

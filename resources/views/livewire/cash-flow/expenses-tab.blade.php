<div class="space-y-6">
    {{-- Filters Section --}}
    <div class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-xl p-4 lg:p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <x-date wire:model.live="dateRange" label="Periode" range placeholder="Pilih range tanggal..." />
            </div>

            <div>
                <x-select.styled wire:model.live="bankAccountFilters" label="Bank Account" :options="$this->bankAccounts"
                    placeholder="Semua bank..." multiple searchable />
            </div>

            <div>
                <x-select.styled wire:model.live="categoryFilters" label="Kategori" :options="$this->expenseCategories"
                    placeholder="Semua kategori..." multiple searchable />
            </div>

            <div>
                <x-input wire:model.live.debounce.300ms="search" label="Cari" placeholder="Cari data..."
                    icon="magnifying-glass" />
            </div>
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
                    <x-badge text="{{ $activeFilters }} filter aktif" color="primary" size="sm" />
                    <div class="text-sm text-dark-500 dark:text-dark-400">
                        Menampilkan {{ $this->rows->count() }} dari {{ $this->rows->total() }} pengeluaran
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Expense Table --}}
    <x-card class="">
        <x-slot:header>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50">Daftar Pengeluaran</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Transaksi pengeluaran operasional</p>
                </div>
                <div class="flex items-center gap-2">
                    <x-button wire:click="export" color="green" icon="arrow-down-tray" size="sm" loading="export">
                        Export
                    </x-button>
                    {{-- Tombol yang dispatch event --}}
                    <x-button wire:click="$dispatch('create-transaction', {allowedTypes: ['debit']})" color="red"
                        icon="plus" size="sm">
                        Tambah Pengeluaran
                    </x-button>
                </div>
            </div>
        </x-slot:header>

        <x-table :$headers :$sort :rows="$this->rows" selectable wire:model="selected" paginate filter loading>

            {{-- Date Column --}}
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

            {{-- Category Column --}}
            @interact('column_category', $row)
                @if ($row->category)
                    <div
                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg">
                        <x-icon name="tag" class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" />
                        <span class="text-xs font-medium text-purple-700 dark:text-purple-300">
                            {{ $row->category->label }}
                        </span>
                    </div>
                @else
                    <span class="text-xs text-dark-400 dark:text-dark-500 italic">Tidak ada</span>
                @endif
            @endinteract

            {{-- Description Column --}}
            @interact('column_description', $row)
                <div class="max-w-xs">
                    <div class="text-sm font-medium text-dark-900 dark:text-dark-50 line-clamp-2 mb-1">
                        {{ $row->description }}
                    </div>
                    @if ($row->reference_number)
                        <div class="flex items-center gap-1.5">
                            <x-icon name="document-duplicate" class="w-3 h-3 text-dark-400" />
                            <span class="text-xs text-dark-500 dark:text-dark-400 font-mono">
                                {{ $row->reference_number }}
                            </span>
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

            {{-- Bank Account Column --}}
            @interact('column_bank_account', $row)
                @if ($row->bankAccount)
                    <div class="flex items-center gap-2">
                        <div
                            class="h-8 w-8 bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900/30 dark:to-primary-800/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <x-icon name="building-library" class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                                {{ $row->bankAccount->bank_name }}
                            </div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">
                                {{ $row->bankAccount->account_number }}
                            </div>
                        </div>
                    </div>
                @endif
            @endinteract

            {{-- Amount Column --}}
            @interact('column_amount', $row)
                <div class="text-right">
                    <div class="text-xl font-bold text-red-600 dark:text-red-400">
                        Rp {{ number_format($row->amount, 0, ',', '.') }}
                    </div>
                </div>
            @endinteract

            {{-- Action Column --}}
            @interact('column_action', $row)
                <div class="flex items-center justify-center gap-1">
                    @if ($row->attachment_path)
                        <x-button.circle icon="paper-clip" color="primary" size="sm"
                            wire:click="$dispatch('view-attachment', {transactionId: {{ $row->id }}})"
                            title="Lihat Lampiran" />
                    @endif

                    <x-button.circle icon="pencil" color="green" size="sm"
                        wire:click="$dispatch('edit::expense', {id: {{ $row->id }}})" title="Edit" />

                    <livewire:cash-flow.delete-expense :expense="$row" :key="'delete-' . $row->id" @deleted="$refresh" />
                </div>
            @endinteract
        </x-table>
    </x-card>

    {{-- Bulk Actions Bar --}}
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
                        loading="exportSelected" class="whitespace-nowrap">
                        Export
                    </x-button>
                    <x-button wire:click="bulkDelete" size="sm" color="red" icon="trash"
                        loading="executeBulkDelete" class="whitespace-nowrap">
                        Hapus
                    </x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="secondary" icon="x-mark"
                        class="whitespace-nowrap">
                        Batal
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Child Components --}}
    {{-- <livewire:cash-flow.update-expense @updated="$refresh" /> --}}
    <livewire:transactions.create :allowedTypes="['debit']" @transaction-created="$refresh" />
    <livewire:cash-flow.attachment-viewer />
</div>

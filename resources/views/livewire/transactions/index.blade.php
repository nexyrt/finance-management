<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div>
            <h1
                class="text-2xl sm:text-3xl lg:text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-600 to-blue-700 dark:from-white dark:via-blue-300 dark:to-blue-200 bg-clip-text text-transparent">
                Semua Transaksi
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-base sm:text-lg">
                Kelola seluruh transaksi dari semua rekening
            </p>
        </div>
        <div class="flex gap-3">
            <livewire:transactions.create @transaction-created="$refresh" />
            <x-button wire:click="openTransfer" loading="openTransfer" color="blue" icon="arrow-path">
                Transfer Dana
            </x-button>
        </div>
    </div>

    {{-- this->stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="arrow-down" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Total Pemasukan</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                        Rp {{ number_format($this->stats['total_income'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="arrow-up" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Total Pengeluaran</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                        Rp {{ number_format($this->stats['total_expense'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="clipboard-document-list" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Total Transaksi</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ number_format($this->stats['total_transactions'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col lg:flex-row gap-4">
        <div class="flex flex-col sm:flex-row gap-4 lg:flex-1">
            <div class="w-full sm:w-48">
                <x-select.styled wire:model.live="account_id" :options="$this->accounts
                    ->map(
                        fn($account) => [
                            'label' => $account->account_name,
                            'value' => $account->id,
                        ],
                    )
                    ->prepend(['label' => 'Semua Rekening', 'value' => ''])
                    ->toArray()" placeholder="Filter rekening..." />
            </div>

            <div class="w-full sm:w-48">
                <x-select.styled wire:model.live="transaction_type" :options="[
                    ['label' => 'Semua Jenis', 'value' => ''],
                    ['label' => 'Pemasukan', 'value' => 'credit'],
                    ['label' => 'Pengeluaran', 'value' => 'debit'],
                ]" placeholder="Filter jenis..." />
            </div>

            @if ($account_id || $transaction_type || $search)
                <x-button wire:click="clearFilters" icon="x-mark" color="gray" size="sm"
                    class="h-[36px] whitespace-nowrap">
                    Clear
                </x-button>
            @endif
        </div>

        <div class="w-full lg:w-80">
            <x-input wire:model.live.debounce.300ms="search" placeholder="Cari transaksi..." icon="magnifying-glass" />
        </div>
    </div>

    {{-- Table --}}
    <x-table :$headers :$sort :rows="$this->transactions" selectable wire:model="selected" paginate loading>

        {{-- Transaction Description with Icon --}}
        @interact('column_description', $row)
            <div class="flex items-center gap-3">
                <div
                    class="h-10 w-10 {{ $row->transaction_type === 'credit' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }} rounded-lg flex items-center justify-center">
                    <x-icon name="{{ $row->transaction_type === 'credit' ? 'arrow-down' : 'arrow-up' }}"
                        class="w-5 h-5 {{ $row->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" />
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-50">{{ $row->description ?: 'No description' }}
                    </p>
                    @if ($row->reference_number)
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $row->reference_number }}</p>
                    @endif
                </div>
            </div>
        @endinteract

        {{-- Bank Account --}}
        @interact('column_bank_account_id', $row)
            <div>
                <p class="font-medium text-gray-900 dark:text-gray-50">{{ $row->bankAccount->account_name }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $row->bankAccount->bank_name }}</p>
            </div>
        @endinteract

        {{-- Transaction Date --}}
        @interact('column_transaction_date', $row)
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-50">
                    {{ $row->transaction_date->format('d M Y') }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $row->created_at->format('H:i') }}
                </p>
            </div>
        @endinteract

        {{-- Amount --}}
        @interact('column_amount', $row)
            <div class="text-right">
                <p
                    class="font-bold {{ $row->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ $row->transaction_type === 'credit' ? '+' : '-' }}Rp
                    {{ number_format($row->amount, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $row->transaction_type === 'credit' ? 'Pemasukan' : 'Pengeluaran' }}
                </p>
            </div>
        @endinteract

        {{-- Actions --}}
        @interact('column_action', $row)
            <div class="flex justify-center gap-1">
                <x-button.circle wire:click="viewAttachment({{ $row->id }})"
                    loading="viewAttachment({{ $row->id }})" color="blue" icon="paper-clip" size="sm" />
                <x-button.circle wire:click="deleteTransaction({{ $row->id }})"
                    loading="deleteTransaction({{ $row->id }})" color="red" icon="trash" size="sm" />
            </div>
        @endinteract
    </x-table>

    {{-- Bulk Actions Bar --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 px-4 sm:px-6 py-4 sm:min-w-96">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                {{-- Selection Info --}}
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-gray-50"
                            x-text="`${show.length} transaksi dipilih`"></div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Pilih aksi untuk transaksi yang dipilih
                        </div>
                    </div>
                </div>
                {{-- Actions --}}
                <div class="flex items-center gap-2 justify-end">
                    <x-button wire:click="confirmBulkDelete" size="sm" color="red" icon="trash"
                        class="whitespace-nowrap">
                        Hapus
                    </x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="gray" icon="x-mark"
                        class="whitespace-nowrap">
                        Batal
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Components --}}
    <livewire:transactions.delete @transaction-deleted="$refresh" />
    <livewire:transactions.transfer @transfer-completed="$refresh" />

    {{-- JavaScript for attachment viewing --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-attachment', (event) => {
                window.open(event.url, '_blank');
            });
        });
    </script>
</div>

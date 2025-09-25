<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Testing Transactions Read</h1>
        <p class="text-gray-600 dark:text-gray-400">Simple table testing with TallStackUI</p>
    </div>

    <livewire:transactions.create @transaction-created="$refresh" />

{{-- Table --}}
    <x-table :$headers :$sort :rows="$this->transactions" selectable wire:model="selected" paginate filter loading>

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
            <div class="flex justify-center">
                <x-button.circle wire:click="$dispatch('delete-transaction', { transactionId: {{ $row->id }} })"
                    color="red" icon="trash" size="sm" />
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

    {{-- Delete Component --}}
    <livewire:transactions.delete @transaction-deleted="$refresh" />
</div>

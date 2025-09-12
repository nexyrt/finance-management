<div>
    {{-- Transactions Table --}}
    <x-table :$headers :$sort :rows="$this->rows" selectable wire:model="selected" paginate filter loading>

        @interact('column_description', $row)
            <div class="flex items-center gap-3">
                <div
                    class="h-10 w-10 {{ $row->transaction_type === 'credit' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }} rounded-lg flex items-center justify-center">
                    <x-icon name="{{ $row->transaction_type === 'credit' ? 'arrow-down' : 'arrow-up' }}"
                        class="w-5 h-5 {{ $row->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" />
                </div>
                <div>
                    <p class="font-medium text-dark-900 dark:text-dark-50">{{ $row->description }}</p>
                    <p class="text-sm text-dark-600 dark:text-dark-400">
                        {{ $row->transaction_type === 'credit' ? 'Income' : 'Expense' }}
                    </p>
                </div>
            </div>
        @endinteract

        @interact('column_reference_number', $row)
            <span class="font-mono text-sm text-dark-600 dark:text-dark-400">
                {{ $row->reference_number ?: 'TXN' . str_pad($row->id, 6, '0', STR_PAD_LEFT) }}
            </span>
        @endinteract

        @interact('column_transaction_date', $row)
            <div>
                <p class="text-sm font-medium text-dark-900 dark:text-dark-50">
                    {{ $row->transaction_date->format('d M Y') }}
                </p>
                <p class="text-xs text-dark-600 dark:text-dark-400">
                    {{ $row->created_at->format('H:i') }}
                </p>
            </div>
        @endinteract

        @interact('column_amount', $row)
            <div class="text-right">
                <p
                    class="font-bold {{ $row->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ $row->transaction_type === 'credit' ? '+' : '-' }}Rp
                    {{ number_format($row->amount, 0, ',', '.') }}
                </p>
            </div>
        @endinteract

        @interact('column_action', $row)
            <div class="flex justify-center">
                <x-button.circle wire:click="deleteTransaction({{ $row->id }})"
                    loading="deleteTransaction({{ $row->id }})" color="red" icon="trash" size="sm" />
            </div>
        @endinteract
    </x-table>

    {{-- Empty State --}}
    @if ($this->rows->count() === 0)
        <div class="text-center py-12">
            <div
                class="h-16 w-16 bg-zinc-100 dark:bg-zinc-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <x-icon name="arrows-right-left" class="w-8 h-8 text-zinc-400" />
            </div>
            <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-2">
                No transactions found
            </h3>
            <p class="text-dark-600 dark:text-dark-400 mb-6">
                Start by adding your first transaction to track account activity.
            </p>
            <x-button wire:click="$dispatch('add-transaction')" color="primary" icon="plus">
                Add Transaction
            </x-button>
        </div>
    @endif

    {{-- Bulk Actions Bar --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
        <div
            class="bg-white dark:bg-dark-800 rounded-xl shadow-lg border border-zinc-200 dark:border-dark-600 px-4 sm:px-6 py-4 sm:min-w-80">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                {{-- Selection Info --}}
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-zinc-50 dark:bg-zinc-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-zinc-600 dark:text-zinc-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-dark-900 dark:text-dark-50"
                            x-text="`${show.length} transaction${show.length !== 1 ? 's' : ''} selected`"></div>
                        <div class="text-xs text-dark-600 dark:text-dark-400">Choose action for selected items</div>
                    </div>
                </div>
                {{-- Actions --}}
                <div class="flex items-center gap-2 justify-end">
                    <x-button wire:click="exportSelected" loading="exportSelected" size="sm" color="green"
                        icon="document-arrow-down" class="whitespace-nowrap">
                        Export
                    </x-button>
                    <x-button wire:click="confirmBulkDelete" loading="confirmBulkDelete" size="sm" color="red"
                        icon="trash" class="whitespace-nowrap">
                        Delete
                    </x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="zinc" icon="x-mark"
                        class="whitespace-nowrap">
                        Cancel
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</div>

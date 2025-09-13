{{-- resources/views/livewire/accounts/tables/transactions-table.blade.php --}}

<div class="space-y-4">
    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex gap-3">
            <div wire:ignore>
                <x-select.styled wire:model.live="transactionType" :options="$this->transactionTypeOptions" placeholder="Filter by type..."
                    class="w-48" />
            </div>

            <div wire:ignore>
                <x-date wire:model.live="dateRange" range placeholder="Select date range..." class="w-64" />
            </div>

            @if ($transactionType || !empty($dateRange) || $search)
                <x-button wire:click="clearFilters" color="secondary" outline icon="x-mark">
                    Clear
                </x-button>
            @endif
        </div>

        <x-input wire:model.live.debounce.300ms="search" placeholder="Search transactions..." icon="magnifying-glass"
            class="flex-1" />
    </div>

    {{-- Table with Bulk Actions --}}
    <x-table :$headers :$sort :rows="$this->rows" selectable wire:model="selected" paginate loading>

        {{-- Description --}}
        @interact('column_description', $row)
            <div class="max-w-xs">
                <div class="font-medium text-dark-900 dark:text-white truncate">
                    {{ $row->description }}
                </div>
                @if ($row->reference_number && str_starts_with($row->reference_number, 'TRF'))
                    <x-badge color="blue" text="Transfer" size="sm" class="mt-1" />
                @endif
            </div>
        @endinteract

        {{-- Reference --}}
        @interact('column_reference_number', $row)
            @if ($row->reference_number)
                <span class="font-mono text-sm text-dark-600 dark:text-dark-400">
                    {{ $row->reference_number }}
                </span>
            @else
                <span class="text-dark-400 italic">-</span>
            @endif
        @endinteract

        {{-- Date --}}
        @interact('column_transaction_date', $row)
            <div class="text-sm">
                <div class="text-dark-900 dark:text-white">{{ $row->transaction_date->format('d M Y') }}</div>
                <div class="text-xs text-dark-500 dark:text-dark-400">{{ $row->transaction_date->diffForHumans() }}</div>
            </div>
        @endinteract

        {{-- Amount --}}
        @interact('column_amount', $row)
            <div class="text-right">
                <div
                    class="font-semibold {{ $row->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ $row->transaction_type === 'credit' ? '+' : '-' }}Rp {{ number_format($row->amount, 0, ',', '.') }}
                </div>
                <div class="text-xs text-dark-500 dark:text-dark-400">
                    {{ $row->transaction_type === 'credit' ? 'Income' : 'Expense' }}
                </div>
            </div>
        @endinteract

        {{-- Actions --}}
        @interact('column_action', $row)
            <div class="flex items-center gap-1">
                <x-button.circle icon="trash" color="red" size="sm"
                    wire:click="deleteTransaction({{ $row->id }})" title="Delete" />
            </div>
        @endinteract
    </x-table>

    {{-- Bulk Actions Bar --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50">
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 px-6 py-4 min-w-96">
            <div class="flex items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <x-icon name="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-gray-50" x-text="`${show.length} selected`">
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Choose action</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-button wire:click="exportSelected" size="sm" color="green"
                        icon="document-arrow-down">Export</x-button>
                    <x-button wire:click="confirmBulkDelete" size="sm" color="red"
                        icon="trash">Delete</x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="gray"
                        icon="x-mark">Clear</x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Empty State --}}
    @if ($this->rows->isEmpty() && $selectedAccountId)
        <div class="text-center py-12">
            <x-icon name="banknotes" class="w-12 h-12 text-dark-400 mx-auto mb-3" />
            @if ($search || $transactionType || !empty($dateRange))
                <h3 class="text-lg font-semibold text-dark-900 dark:text-white mb-2">No transactions found</h3>
                <p class="text-dark-600 dark:text-dark-400 mb-4">Try adjusting your filters</p>
                <x-button wire:click="clearFilters" color="primary" outline>Clear Filters</x-button>
            @else
                <h3 class="text-lg font-semibold text-dark-900 dark:text-white mb-2">No transactions yet</h3>
                <p class="text-dark-600 dark:text-dark-400 mb-4">Add your first transaction</p>
                <x-button wire:click="addTransaction" color="primary" icon="plus">Add Transaction</x-button>
            @endif
        </div>
    @endif

    @if (!$selectedAccountId)
        <div class="text-center py-12">
            <x-icon name="building-library" class="w-12 h-12 text-dark-400 mx-auto mb-3" />
            <h3 class="text-lg font-semibold text-dark-900 dark:text-white mb-2">Select an Account</h3>
            <p class="text-dark-600 dark:text-dark-400">Choose an account to view transactions</p>
        </div>
    @endif
</div>

{{-- resources/views/livewire/accounts/tables/transactions-table.blade.php --}}

<div x-data="{}" @reinit-alpine.window="$nextTick(() => Alpine.initTree($el))" class="space-y-4">
    {{-- Internal Filters --}}
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex gap-3">
            <x-select.styled wire:model.live="transactionType" 
                           :options="$this->transactionTypeOptions"
                           placeholder="Filter by type..." 
                           class="w-48" />
            
            <x-date wire:model.live="dateRange" 
                   range 
                   placeholder="Select date range..."
                   class="w-64" />
            
            @if ($transactionType || !empty($dateRange) || $search)
                <x-button wire:click="clearFilters" 
                         loading="clearFilters" 
                         icon="x-mark"
                         color="secondary" 
                         outline>
                    Clear
                </x-button>
            @endif
        </div>
        
        <x-input wire:model.live.debounce.300ms="search"
                placeholder="Search transactions..."
                icon="magnifying-glass" 
                class="flex-1" />
    </div>

    {{-- Action Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <x-button wire:click="addTransaction" 
                     loading="addTransaction" 
                     color="primary" 
                     icon="plus"
                     size="sm">
                Add Transaction
            </x-button>
            
            @if (!empty($selected))
                <x-badge color="blue" :text="count($selected) . ' selected'" />
            @endif
        </div>

        @if (!empty($selected))
            <div class="flex items-center gap-2">
                <x-button wire:click="exportSelected" 
                         loading="exportSelected" 
                         color="green" 
                         icon="document-arrow-down"
                         size="sm">
                    Export
                </x-button>
                
                <x-button wire:click="confirmBulkDelete" 
                         loading="confirmBulkDelete" 
                         color="red" 
                         icon="trash"
                         size="sm">
                    Delete Selected
                </x-button>
            </div>
        @endif
    </div>

    {{-- Table --}}
    <div wire:loading.class="opacity-50" wire:target="search,transactionType,dateRange">
        <x-table :$headers 
                 :$sort 
                 :rows="$this->rows" 
                 selectable 
                 wire:model="selected" 
                 paginate 
                 loading>
            
            {{-- Description Column --}}
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

            {{-- Reference Column --}}
            @interact('column_reference_number', $row)
                @if ($row->reference_number)
                    <span class="font-mono text-sm text-dark-600 dark:text-dark-400">
                        {{ $row->reference_number }}
                    </span>
                @else
                    <span class="text-dark-400 italic">-</span>
                @endif
            @endinteract

            {{-- Date Column --}}
            @interact('column_transaction_date', $row)
                <div class="text-sm">
                    <div class="text-dark-900 dark:text-white">
                        {{ $row->transaction_date->format('d M Y') }}
                    </div>
                    <div class="text-xs text-dark-500 dark:text-dark-400">
                        {{ $row->transaction_date->diffForHumans() }}
                    </div>
                </div>
            @endinteract

            {{-- Amount Column --}}
            @interact('column_amount', $row)
                <div class="text-right">
                    <div class="font-semibold {{ $row->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $row->transaction_type === 'credit' ? '+' : '-' }}Rp {{ number_format($row->amount, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-dark-500 dark:text-dark-400">
                        {{ $row->transaction_type === 'credit' ? 'Income' : 'Expense' }}
                    </div>
                </div>
            @endinteract

            {{-- Actions Column --}}
            @interact('column_action', $row)
                <div class="flex items-center gap-1">
                    <x-button.circle icon="pencil" 
                                   color="blue" 
                                   size="sm"
                                   wire:click="editTransaction({{ $row->id }})"
                                   title="Edit" />
                    
                    <x-button.circle icon="trash" 
                                   color="red" 
                                   size="sm"
                                   wire:click="deleteTransaction({{ $row->id }})"
                                   title="Delete" />
                </div>
            @endinteract
        </x-table>
    </div>

    {{-- Empty State --}}
    @if ($this->rows->isEmpty() && $selectedAccountId)
        <div class="text-center py-12">
            <x-icon name="banknotes" class="w-12 h-12 text-dark-400 mx-auto mb-3" />
            
            @if ($search || $transactionType || !empty($dateRange))
                <h3 class="text-lg font-semibold text-dark-900 dark:text-white mb-2">No transactions found</h3>
                <p class="text-dark-600 dark:text-dark-400 mb-4">Try adjusting your search criteria</p>
                <x-button wire:click="clearFilters" color="primary" outline>Clear Filters</x-button>
            @else
                <h3 class="text-lg font-semibold text-dark-900 dark:text-white mb-2">No transactions yet</h3>
                <p class="text-dark-600 dark:text-dark-400 mb-4">Start by adding your first transaction</p>
                <x-button wire:click="addTransaction" color="primary" icon="plus">Add Transaction</x-button>
            @endif
        </div>
    @endif

    @if (!$selectedAccountId)
        <div class="text-center py-12">
            <x-icon name="building-library" class="w-12 h-12 text-dark-400 mx-auto mb-3" />
            <h3 class="text-lg font-semibold text-dark-900 dark:text-white mb-2">Select an Account</h3>
            <p class="text-dark-600 dark:text-dark-400">Choose an account to view its transactions</p>
        </div>
    @endif
</div>
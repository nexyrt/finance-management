<div class="space-y-6">
    {{-- Filters --}}
    <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <x-input type="date" label="Start Date" wire:model="startDate" />
            </div>
            <div>
                <x-input type="date" label="End Date" wire:model="endDate" />
            </div>
            <div>
                <x-select.styled label="From Account" wire:model="fromAccountId" :options="$this->bankAccounts"
                    placeholder="All Accounts" />
            </div>
            <div>
                <x-select.styled label="To Account" wire:model="toAccountId" :options="$this->bankAccounts"
                    placeholder="All Accounts" />
            </div>
        </div>
        <div class="grid grid-cols-1 mt-4">
            <div>
                <x-input label="Search" wire:model.live.debounce.500ms="search" placeholder="Search..."
                    icon="magnifying-glass" />
            </div>
        </div>
        <div class="flex gap-2 mt-4">
            <x-button wire:click="applyFilters" color="blue" icon="funnel" size="sm">Apply Filters</x-button>
            <x-button wire:click="resetFilters" color="gray" outline icon="x-mark" size="sm">Reset</x-button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Total Transfers --}}
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div
                        class="h-12 w-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                        <x-icon name="arrow-path" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div>
                        <p class="text-sm text-dark-600 dark:text-dark-400">Total Transfers (Filtered)</p>
                        <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                            Rp {{ number_format($this->totalTransfers, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-purple-500 dark:text-purple-400 mt-1">
                            {{ $this->transferTransactions->count() }} transactions
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <x-button size="sm" color="purple" icon="plus">New Transfer</x-button>
                    <x-button size="sm" color="gray" outline icon="document-arrow-down">Export</x-button>
                </div>
            </div>
        </div>

        {{-- Account Summary --}}
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <h4 class="text-sm font-semibold text-dark-600 dark:text-dark-400 mb-4">Transfers by Account</h4>
            @if ($this->transfersByAccount->isEmpty())
                <p class="text-zinc-500 dark:text-dark-400 text-sm text-center py-4">No data</p>
            @else
                <div class="space-y-3 max-h-48 overflow-y-auto">
                    @foreach ($this->transfersByAccount as $item)
                        <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-dark-700 rounded-lg">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-dark-900 dark:text-white">{{ $item['account'] }}</p>
                                <div class="flex items-center gap-4 mt-1">
                                    <span class="text-xs text-red-600 dark:text-red-400">
                                        Out: Rp {{ number_format($item['debits'], 0, ',', '.') }}
                                    </span>
                                    <span class="text-xs text-green-600 dark:text-green-400">
                                        In: Rp {{ number_format($item['credits'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            <x-badge :text="$item['count'] . ' txn'" size="sm" color="purple" />
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Transfers Listing --}}
    <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Transfer Transactions</h3>

        @if ($this->transferTransactions->isEmpty())
            <div class="text-center py-12">
                <x-icon name="arrow-path" class="w-16 h-16 text-zinc-300 dark:text-dark-700 mx-auto mb-4" />
                <p class="text-zinc-500 dark:text-dark-400">No transfer transactions found</p>
                <p class="text-zinc-400 dark:text-dark-600 text-sm mt-1">Try adjusting your filters</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-zinc-200 dark:border-dark-600">
                        <tr>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-dark-600 dark:text-dark-400">Date
                            </th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-dark-600 dark:text-dark-400">Type
                            </th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-dark-600 dark:text-dark-400">
                                Description</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-dark-600 dark:text-dark-400">
                                Category</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-dark-600 dark:text-dark-400">Bank
                                Account</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-dark-600 dark:text-dark-400">
                                Reference</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-dark-600 dark:text-dark-400">
                                Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-dark-600">
                        @foreach ($this->transferTransactions as $transaction)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-dark-700 transition-colors">
                                <td class="py-3 px-4 text-sm text-dark-900 dark:text-white whitespace-nowrap">
                                    {{ $transaction->transaction_date->format('d M Y') }}
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    <x-badge :text="$transaction->transaction_type === 'debit' ? 'Transfer Out' : 'Transfer In'" size="sm" :color="$transaction->transaction_type === 'debit' ? 'red' : 'green'" />
                                </td>
                                <td class="py-3 px-4 text-sm text-dark-900 dark:text-white">
                                    {{ $transaction->description ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    @if ($transaction->category)
                                        <x-badge :text="$transaction->category->label" size="sm" color="purple" />
                                    @else
                                        <x-badge text="Uncategorized" size="sm" color="gray" />
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-sm text-dark-900 dark:text-white">
                                    {{ $transaction->bankAccount->account_name }}
                                </td>
                                <td class="py-3 px-4 text-sm text-dark-600 dark:text-dark-400">
                                    {{ $transaction->reference_number ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-sm text-right font-semibold">
                                    <span
                                        class="{{ $transaction->transaction_type === 'debit' ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                        {{ $transaction->transaction_type === 'debit' ? '-' : '+' }}
                                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-zinc-300 dark:border-dark-500">
                        <tr>
                            <td colspan="6"
                                class="py-3 px-4 text-sm font-semibold text-right text-dark-900 dark:text-white">
                                Total:
                            </td>
                            <td class="py-3 px-4 text-sm text-right font-bold text-purple-600 dark:text-purple-400">
                                Rp {{ number_format($this->totalTransfers, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>

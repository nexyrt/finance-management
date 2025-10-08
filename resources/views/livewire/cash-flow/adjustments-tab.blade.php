<div class="space-y-6">
    {{-- Filters --}}
    <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            <div>
                <x-input type="date" label="Start Date" wire:model="startDate" />
            </div>
            <div>
                <x-input type="date" label="End Date" wire:model="endDate" />
            </div>
            <div>
                <x-select.styled label="Category" wire:model="categoryId" :options="$this->categories"
                    placeholder="All Categories" />
            </div>
            <div>
                <x-select.styled label="Bank Account" wire:model="bankAccountId" :options="$this->bankAccounts"
                    placeholder="All Accounts" />
            </div>
            <div>
                <x-select.styled label="Type" wire:model="transactionType" :options="$this->transactionTypes" />
            </div>
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Total Debits --}}
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="minus-circle" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Total Debits</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                        Rp {{ number_format($this->stats['total_debits'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Total Credits --}}
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="plus-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Total Credits</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                        Rp {{ number_format($this->stats['total_credits'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Net Adjustment --}}
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="adjustments-horizontal" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Net Adjustment</p>
                    <p
                        class="text-2xl font-bold {{ $this->stats['net_adjustment'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        Rp {{ number_format($this->stats['net_adjustment'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-amber-500 dark:text-amber-400 mt-1">
                        {{ $this->stats['total_transactions'] }} transactions
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons & Category Breakdown --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Action Card --}}
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <h4 class="text-sm font-semibold text-dark-600 dark:text-dark-400 mb-4">Quick Actions</h4>
            <div class="flex gap-2">
                <x-button color="amber" icon="plus" class="flex-1">Add Adjustment</x-button>
                <x-button color="gray" outline icon="document-arrow-down" class="flex-1">Export</x-button>
            </div>
            <div
                class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/10 rounded-lg border border-amber-200 dark:border-amber-800">
                <div class="flex gap-2">
                    <x-icon name="information-circle"
                        class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0" />
                    <div>
                        <p class="text-xs font-medium text-amber-800 dark:text-amber-300">About Adjustments</p>
                        <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                            Adjustments are used for corrections, exchange rate differences, and reconciliation entries.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Category Breakdown --}}
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <h4 class="text-sm font-semibold text-dark-600 dark:text-dark-400 mb-4">Adjustments by Category</h4>
            @if ($this->adjustmentsByCategory->isEmpty())
                <p class="text-zinc-500 dark:text-dark-400 text-sm text-center py-4">No data</p>
            @else
                <div class="space-y-3 max-h-48 overflow-y-auto">
                    @foreach ($this->adjustmentsByCategory as $item)
                        <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-dark-700 rounded-lg">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-dark-900 dark:text-white">{{ $item['category'] }}</p>
                                <div class="flex items-center gap-4 mt-1">
                                    <span class="text-xs text-red-600 dark:text-red-400">
                                        Debits: Rp {{ number_format($item['debits'], 0, ',', '.') }}
                                    </span>
                                    <span class="text-xs text-green-600 dark:text-green-400">
                                        Credits: Rp {{ number_format($item['credits'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            <x-badge :text="$item['count'] . ' txn'" size="sm" color="amber" />
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Adjustments Listing --}}
    <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Adjustment Transactions</h3>

        @if ($this->adjustmentTransactions->isEmpty())
            <div class="text-center py-12">
                <x-icon name="adjustments-horizontal" class="w-16 h-16 text-zinc-300 dark:text-dark-700 mx-auto mb-4" />
                <p class="text-zinc-500 dark:text-dark-400">No adjustment transactions found</p>
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
                        @foreach ($this->adjustmentTransactions as $transaction)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-dark-700 transition-colors">
                                <td class="py-3 px-4 text-sm text-dark-900 dark:text-white whitespace-nowrap">
                                    {{ $transaction->transaction_date->format('d M Y') }}
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    <x-badge :text="$transaction->transaction_type === 'debit' ? 'Debit (-)' : 'Credit (+)'" size="sm" :color="$transaction->transaction_type === 'debit' ? 'red' : 'green'" />
                                </td>
                                <td class="py-3 px-4 text-sm text-dark-900 dark:text-white">
                                    {{ $transaction->description ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    @if ($transaction->category)
                                        <x-badge :text="$transaction->category->label" size="sm" color="amber" />
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
                                Net Adjustment:
                            </td>
                            <td
                                class="py-3 px-4 text-sm text-right font-bold {{ $this->stats['net_adjustment'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $this->stats['net_adjustment'] >= 0 ? '+' : '' }}
                                Rp {{ number_format($this->stats['net_adjustment'], 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>

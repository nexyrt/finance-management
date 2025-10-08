<div class="space-y-6">
    {{-- Filters --}}
    <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
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
                <x-input label="Search" wire:model.live.debounce.500ms="search" placeholder="Search..."
                    icon="magnifying-glass" />
            </div>
        </div>
        <div class="flex gap-2 mt-4">
            <x-button wire:click="applyFilters" color="blue" icon="funnel" size="sm">Apply Filters</x-button>
            <x-button wire:click="resetFilters" color="gray" outline icon="x-mark" size="sm">Reset</x-button>
        </div>
    </div>

    {{-- Summary Card --}}
    <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="arrow-trending-up" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Total Income (Filtered)</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                        Rp {{ number_format($this->totalIncome, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-green-500 dark:text-green-400 mt-1">
                        {{ $this->incomeTransactions->count() }} transactions
                    </p>
                </div>
            </div>
            <div class="flex gap-2">
                <x-button size="sm" color="green" icon="plus">Add Income</x-button>
                <x-button size="sm" color="gray" outline icon="document-arrow-down">Export</x-button>
            </div>
        </div>
    </div>

    {{-- Income Listing --}}
    <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Income Transactions</h3>

        @if ($this->incomeTransactions->isEmpty())
            <div class="text-center py-12">
                <x-icon name="arrow-trending-up" class="w-16 h-16 text-zinc-300 dark:text-dark-700 mx-auto mb-4" />
                <p class="text-zinc-500 dark:text-dark-400">No income transactions found</p>
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
                        @foreach ($this->incomeTransactions as $transaction)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-dark-700 transition-colors">
                                <td class="py-3 px-4 text-sm text-dark-900 dark:text-white whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($transaction['date'])->format('d M Y') }}
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    <x-badge :text="$transaction['type'] === 'payment' ? 'Payment' : 'Transaction'" size="sm" :color="$transaction['type'] === 'payment' ? 'blue' : 'green'" />
                                </td>
                                <td class="py-3 px-4 text-sm text-dark-900 dark:text-white">
                                    {{ $transaction['description'] }}
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    <x-badge :text="$transaction['category']" size="sm" color="green" />
                                </td>
                                <td class="py-3 px-4 text-sm text-dark-900 dark:text-white">
                                    {{ $transaction['bank_account'] }}
                                </td>
                                <td class="py-3 px-4 text-sm text-dark-600 dark:text-dark-400">
                                    {{ $transaction['reference'] ?? '-' }}
                                </td>
                                <td
                                    class="py-3 px-4 text-sm text-right font-semibold text-green-600 dark:text-green-400">
                                    + Rp {{ number_format($transaction['amount'], 0, ',', '.') }}
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
                            <td class="py-3 px-4 text-sm text-right font-bold text-green-600 dark:text-green-400">
                                Rp {{ number_format($this->totalIncome, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>

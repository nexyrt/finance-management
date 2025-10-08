<div class="space-y-6">
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
        {{-- Total Income Card --}}
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="arrow-trending-up" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Total Income</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                        Rp {{ number_format($this->stats['total_income'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-green-500 dark:text-green-400">
                        {{ $period === 'this_month' ? 'This month' : ($period === 'last_3_months' ? 'Last 3 months' : 'Last year') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Total Expenses Card --}}
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="arrow-trending-down" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Total Expenses</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                        Rp {{ number_format($this->stats['total_expenses'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-red-500 dark:text-red-400">
                        {{ $period === 'this_month' ? 'This month' : ($period === 'last_3_months' ? 'Last 3 months' : 'Last year') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Net Cash Flow Card --}}
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="chart-bar" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Net Cash Flow</p>
                    <p class="text-2xl font-bold {{ $this->stats['net_cash_flow'] >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' }}">
                        Rp {{ number_format($this->stats['net_cash_flow'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs {{ $this->stats['net_cash_flow'] >= 0 ? 'text-blue-500 dark:text-blue-400' : 'text-red-500 dark:text-red-400' }}">
                        {{ $period === 'this_month' ? 'This month' : ($period === 'last_3_months' ? 'Last 3 months' : 'Last year') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Total Transfers Card --}}
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="arrow-path" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Total Transfers</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                        Rp {{ number_format($this->stats['total_transfers'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-purple-500 dark:text-purple-400">
                        {{ $period === 'this_month' ? 'This month' : ($period === 'last_3_months' ? 'Last 3 months' : 'Last year') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Placeholder --}}
    <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cash Flow Trend</h3>
            <div class="flex gap-2">
                <x-button wire:click="$set('period', 'this_month')" size="sm" :color="$period === 'this_month' ? 'blue' : 'gray'" :outline="$period !== 'this_month'">
                    This Month
                </x-button>
                <x-button wire:click="$set('period', 'last_3_months')" size="sm" :color="$period === 'last_3_months' ? 'blue' : 'gray'" :outline="$period !== 'last_3_months'">
                    Last 3 Months
                </x-button>
                <x-button wire:click="$set('period', 'last_year')" size="sm" :color="$period === 'last_year' ? 'blue' : 'gray'" :outline="$period !== 'last_year'">
                    Last Year
                </x-button>
            </div>
        </div>
        <div class="h-64 flex items-center justify-center bg-zinc-50 dark:bg-dark-900 rounded-lg border-2 border-dashed border-zinc-300 dark:border-dark-700">
            <div class="text-center">
                <x-icon name="chart-bar" class="w-16 h-16 text-zinc-300 dark:text-dark-700 mx-auto mb-2" />
                <p class="text-zinc-400 dark:text-dark-600 text-sm">Chart will be implemented in Task #5</p>
            </div>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Recent Transactions</h3>
        
        @if($this->recentTransactions->isEmpty())
            <div class="text-center py-12">
                <x-icon name="document-text" class="w-16 h-16 text-zinc-300 dark:text-dark-700 mx-auto mb-4" />
                <p class="text-zinc-500 dark:text-dark-400">No recent transactions</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-zinc-200 dark:border-dark-600">
                        <tr>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-dark-600 dark:text-dark-400">Date</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-dark-600 dark:text-dark-400">Description</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-dark-600 dark:text-dark-400">Category</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-dark-600 dark:text-dark-400">Bank Account</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-dark-600 dark:text-dark-400">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-dark-600">
                        @foreach($this->recentTransactions as $transaction)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-dark-700 transition-colors">
                                <td class="py-3 px-4 text-sm text-dark-900 dark:text-white">
                                    {{ $transaction->transaction_date->format('d M Y') }}
                                </td>
                                <td class="py-3 px-4 text-sm text-dark-900 dark:text-white">
                                    {{ $transaction->description ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    @if($transaction->category)
                                        <x-badge :text="$transaction->category->label" size="sm" 
                                            :color="match($transaction->category->type) {
                                                'income' => 'green',
                                                'expense' => 'red',
                                                'transfer' => 'purple',
                                                'adjustment' => 'amber',
                                                default => 'gray'
                                            }" />
                                    @else
                                        <span class="text-zinc-400 dark:text-dark-600">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-sm text-dark-900 dark:text-white">
                                    {{ $transaction->bankAccount->account_name }}
                                </td>
                                <td class="py-3 px-4 text-sm text-right font-semibold">
                                    <span class="{{ $transaction->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $transaction->transaction_type === 'credit' ? '+' : '-' }} 
                                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
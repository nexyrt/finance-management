{{-- resources/views/livewire/bank-accounts/index.blade.php --}}

<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-secondary-900 dark:text-dark-100">Bank Account Management</h1>
            <p class="text-secondary-600 dark:text-dark-400 mt-2">Manage your bank accounts, transactions, and monitor
                cash flow</p>
        </div>
        <div class="flex space-x-3 mt-4 sm:mt-0">
            <x-button color="secondary dark:dark" icon="document-arrow-down" outline>
                Import Statement
            </x-button>
            <x-button color="primary dark:primary" icon="plus" wire:click="createBankAccount">
                Add Bank Account
            </x-button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 rounded-xl">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        class="w-12 h-12 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg flex items-center justify-center">
                        <x-icon name="banknotes" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-secondary-600 dark:text-dark-400">Total Balance</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">Rp
                        {{ number_format($totalBalance ?? 125750000, 0, ',', '.') }}</p>
                    <p class="text-xs text-green-500 dark:text-green-400 mt-1">+12.5% from last month</p>
                </div>
            </div>
        </x-card>

        <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        class="w-12 h-12 bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-800 rounded-lg flex items-center justify-center">
                        <x-icon name="building-library" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-secondary-600 dark:text-dark-400">Active Accounts</p>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $activeAccountsCount ?? 4 }}
                    </p>
                    <p class="text-xs text-secondary-500 dark:text-dark-500 mt-1">Across 3 banks</p>
                </div>
            </div>
        </x-card>

        <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-lg flex items-center justify-center">
                        <x-icon name="arrow-trending-up" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-secondary-600 dark:text-dark-400">This Month Income</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">Rp
                        {{ number_format($monthlyIncome ?? 45200000, 0, ',', '.') }}</p>
                    <p class="text-xs text-emerald-500 dark:text-emerald-400 mt-1">+8.3% from last month</p>
                </div>
            </div>
        </x-card>

        <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        class="w-12 h-12 bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 rounded-lg flex items-center justify-center">
                        <x-icon name="arrow-trending-down" class="w-6 h-6 text-rose-600 dark:text-rose-400" />
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-secondary-600 dark:text-dark-400">This Month Expense</p>
                    <p class="text-2xl font-bold text-rose-600 dark:text-rose-400">Rp
                        {{ number_format($monthlyExpense ?? 18750000, 0, ',', '.') }}</p>
                    <p class="text-xs text-rose-500 dark:text-rose-400 mt-1">+2.1% from last month</p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Tabs Section --}}
    <x-tab selected="Bank Accounts">
        {{-- Tab 1: Bank Accounts --}}
        <x-tab.items tab="Bank Accounts">
            <div class="space-y-6">
                {{-- Will be replaced by BankAccounts\Listing component --}}
                @livewire('bank-accounts.listing')
            </div>
        </x-tab.items>

        {{-- Tab 2: Recent Transactions --}}
        <x-tab.items tab="Recent Transactions">
            <div class="space-y-6">
                {{-- Will be replaced by BankTransactions\Listing component --}}
                {{-- @livewire('bank-transactions.listing', ['context' => 'recent']) --}}
            </div>
        </x-tab.items>
    </x-tab>

    @livewire('bank-transactions.types.manual-transaction')
</div>

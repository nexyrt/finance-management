{{-- resources/views/livewire/bank-accounts/index.blade.php --}}

<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-secondary-900 dark:text-dark-100">Bank Account Management</h1>
            <p class="text-secondary-600 dark:text-dark-400 mt-2">Manage your bank accounts, transactions, and monitor cash flow</p>
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
                    <div class="w-12 h-12 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg flex items-center justify-center">
                        <x-icon name="banknotes" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-secondary-600 dark:text-dark-400">Total Balance</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($totalBalance ?? 125750000, 0, ',', '.') }}</p>
                    <p class="text-xs text-green-500 dark:text-green-400 mt-1">+12.5% from last month</p>
                </div>
            </div>
        </x-card>

        <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-800 rounded-lg flex items-center justify-center">
                        <x-icon name="building-library" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-secondary-600 dark:text-dark-400">Active Accounts</p>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $activeAccountsCount ?? 4 }}</p>
                    <p class="text-xs text-secondary-500 dark:text-dark-500 mt-1">Across 3 banks</p>
                </div>
            </div>
        </x-card>

        <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-lg flex items-center justify-center">
                        <x-icon name="arrow-trending-up" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-secondary-600 dark:text-dark-400">This Month Income</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($monthlyIncome ?? 45200000, 0, ',', '.') }}</p>
                    <p class="text-xs text-emerald-500 dark:text-emerald-400 mt-1">+8.3% from last month</p>
                </div>
            </div>
        </x-card>

        <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 rounded-lg flex items-center justify-center">
                        <x-icon name="arrow-trending-down" class="w-6 h-6 text-rose-600 dark:text-rose-400" />
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-secondary-600 dark:text-dark-400">This Month Expense</p>
                    <p class="text-2xl font-bold text-rose-600 dark:text-rose-400">Rp {{ number_format($monthlyExpense ?? 18750000, 0, ',', '.') }}</p>
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

        {{-- Tab 3: Cash Flow Summary --}}
        <x-tab.items tab="Cash Flow Summary">
            <div class="space-y-6">
                {{-- Cash Flow Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 rounded-lg">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <x-icon name="arrow-trending-up" class="w-8 h-8 text-green-600 dark:text-green-400" />
                            </div>
                            <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-100">Total Inflow</h3>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">Rp {{ number_format($totalInflow ?? 65450000, 0, ',', '.') }}</p>
                            <p class="text-sm text-secondary-500 dark:text-dark-400 mt-1">This month</p>
                        </div>
                    </x-card>

                    <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 rounded-lg">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <x-icon name="arrow-trending-down" class="w-8 h-8 text-rose-600 dark:text-rose-400" />
                            </div>
                            <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-100">Total Outflow</h3>
                            <p class="text-3xl font-bold text-rose-600 dark:text-rose-400 mt-2">Rp {{ number_format($totalOutflow ?? 23175000, 0, ',', '.') }}</p>
                            <p class="text-sm text-secondary-500 dark:text-dark-400 mt-1">This month</p>
                        </div>
                    </x-card>

                    <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 rounded-lg">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <x-icon name="banknotes" class="w-8 h-8 text-primary-600 dark:text-primary-400" />
                            </div>
                            <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-100">Net Cash Flow</h3>
                            <p class="text-3xl font-bold text-primary-600 dark:text-primary-400 mt-2">Rp {{ number_format($netCashFlow ?? 42275000, 0, ',', '.') }}</p>
                            <p class="text-sm text-secondary-500 dark:text-dark-400 mt-1">This month</p>
                        </div>
                    </x-card>
                </div>

                {{-- Cash Flow Chart Placeholder --}}
                <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 rounded-lg">
                    <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-100 mb-6">Monthly Cash Flow Trend</h3>
                    <div class="h-64 bg-secondary-100 dark:bg-dark-700 border border-secondary-200 dark:border-dark-600 rounded-lg flex items-center justify-center">
                        <div class="text-center">
                            <x-icon name="chart-bar" class="w-16 h-16 text-secondary-400 dark:text-dark-400 mx-auto mb-4" />
                            <p class="text-secondary-600 dark:text-dark-300">Cash Flow Chart</p>
                            <p class="text-sm text-secondary-500 dark:text-dark-400">Will be implemented with Chart.js</p>
                        </div>
                    </div>
                </x-card>
            </div>
        </x-tab.items>

        {{-- Tab 4: Quick Actions --}}
        <x-tab.items tab="Quick Actions">
            <div class="space-y-6">
                {{-- Transaction Actions --}}
                <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 rounded-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-800 rounded-lg flex items-center justify-center mr-3">
                            <x-icon name="plus" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-100">Transaction Management</h3>
                            <p class="text-sm text-secondary-600 dark:text-dark-400">Add, transfer, and manage your transactions</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <x-button color="green dark:green" icon="plus-circle" class="w-full justify-start" wire:click="addManualTransaction">
                            <span class="ml-2">Manual Transaction</span>
                        </x-button>
                        <x-button color="blue dark:blue" icon="arrow-right-circle" class="w-full justify-start" wire:click="addInterBankTransfer">
                            <span class="ml-2">Inter-Bank Transfer</span>
                        </x-button>
                        <x-button color="purple dark:purple" icon="arrows-right-left" class="w-full justify-start" wire:click="addInternalTransfer">
                            <span class="ml-2">Internal Transfer</span>
                        </x-button>
                        <x-button color="orange dark:orange" icon="arrow-path" class="w-full justify-start" wire:click="addRecurringTransaction">
                            <span class="ml-2">Recurring Transaction</span>
                        </x-button>
                        <x-button color="amber dark:amber" icon="calculator" class="w-full justify-start" wire:click="addSplitTransaction">
                            <span class="ml-2">Split Transaction</span>
                        </x-button>
                        <x-button color="indigo dark:indigo" icon="document-duplicate" class="w-full justify-start" wire:click="addBulkEntry">
                            <span class="ml-2">Bulk Entry</span>
                        </x-button>
                    </div>
                </x-card>

                {{-- Import & Data Actions --}}
                <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 rounded-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg flex items-center justify-center mr-3">
                            <x-icon name="document-arrow-down" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-100">Data Import & Integration</h3>
                            <p class="text-sm text-secondary-600 dark:text-dark-400">Import statements and connect external sources</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <x-button color="green dark:green" icon="document-text" class="w-full justify-start" outline wire:click="importStatement">
                            <span class="ml-2">Bank Statement (CSV/Excel)</span>
                        </x-button>
                        <x-button color="blue dark:blue" icon="camera" class="w-full justify-start" outline wire:click="scanReceipt">
                            <span class="ml-2">Receipt/Invoice Scanner</span>
                        </x-button>
                        <x-button color="purple dark:purple" icon="link" class="w-full justify-start" outline wire:click="connectBankApi">
                            <span class="ml-2">Bank API Integration</span>
                        </x-button>
                    </div>
                </x-card>

                {{-- Reports & Analysis --}}
                <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 rounded-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-lg flex items-center justify-center mr-3">
                            <x-icon name="document-text" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-100">Reports & Analysis</h3>
                            <p class="text-sm text-secondary-600 dark:text-dark-400">Generate insights and financial reports</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-button color="green dark:green" icon="chart-bar" class="w-full justify-start" outline wire:click="generateCashFlowReport">
                            <span class="ml-2">Cash Flow Report</span>
                        </x-button>
                        <x-button color="blue dark:blue" icon="scale" class="w-full justify-start" outline wire:click="generateReconciliationReport">
                            <span class="ml-2">Bank Reconciliation</span>
                        </x-button>
                        <x-button color="purple dark:purple" icon="document-check" class="w-full justify-start" outline wire:click="generateTaxSummary">
                            <span class="ml-2">Tax Summary</span>
                        </x-button>
                        <x-button color="amber dark:amber" icon="calendar" class="w-full justify-start" outline wire:click="scheduleReports">
                            <span class="ml-2">Scheduled Reports</span>
                        </x-button>
                    </div>
                </x-card>

                {{-- Account Management --}}
                <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 rounded-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 rounded-lg flex items-center justify-center mr-3">
                            <x-icon name="cog-6-tooth" class="w-5 h-5 text-rose-600 dark:text-rose-400" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-100">Account Management</h3>
                            <p class="text-sm text-secondary-600 dark:text-dark-400">Manage accounts, reconciliation, and settings</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <x-button color="primary dark:primary" icon="plus" class="w-full justify-start" wire:click="createBankAccount">
                            <span class="ml-2">Add Bank Account</span>
                        </x-button>
                        <x-button color="secondary dark:dark" icon="arrows-right-left" class="w-full justify-start" outline wire:click="addInternalTransfer">
                            <span class="ml-2">Account Transfer</span>
                        </x-button>
                        <x-button color="secondary dark:dark" icon="check-circle" class="w-full justify-start" outline wire:click="generateReconciliationReport">
                            <span class="ml-2">Reconcile Accounts</span>
                        </x-button>
                        <x-button color="secondary dark:dark" icon="bell" class="w-full justify-start" outline wire:click="setAlerts">
                            <span class="ml-2">Set Alerts</span>
                        </x-button>
                        <x-button color="secondary dark:dark" icon="archive-box" class="w-full justify-start" outline wire:click="archiveTransactions">
                            <span class="ml-2">Archive Transactions</span>
                        </x-button>
                        <x-button color="secondary dark:dark" icon="document-duplicate" class="w-full justify-start" outline wire:click="exportData">
                            <span class="ml-2">Export Data</span>
                        </x-button>
                    </div>
                </x-card>
            </div>
        </x-tab.items>
    </x-tab>
</div>
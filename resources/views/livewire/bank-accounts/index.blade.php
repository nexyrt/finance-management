{{-- resources/views/livewire/bank-account/index.blade.php --}}

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
            <x-button color="primary dark:primary" icon="plus">
                Add Bank Account
            </x-button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg flex items-center justify-center">
                        <x-icon name="banknotes" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-secondary-600 dark:text-dark-400">Total Balance</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">Rp 125,750,000</p>
                    <p class="text-xs text-green-500 dark:text-green-400 mt-1">+12.5% from last month</p>
                </div>
            </div>
        </x-card>

        <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-800 rounded-lg flex items-center justify-center">
                        <x-icon name="building-library" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-secondary-600 dark:text-dark-400">Active Accounts</p>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">4</p>
                    <p class="text-xs text-secondary-500 dark:text-dark-500 mt-1">Across 3 banks</p>
                </div>
            </div>
        </x-card>

        <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-lg flex items-center justify-center">
                        <x-icon name="arrow-trending-up" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-secondary-600 dark:text-dark-400">This Month Income</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">Rp 45,200,000</p>
                    <p class="text-xs text-emerald-500 dark:text-emerald-400 mt-1">+8.3% from last month</p>
                </div>
            </div>
        </x-card>

        <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 rounded-lg flex items-center justify-center">
                        <x-icon name="arrow-trending-down" class="w-6 h-6 text-rose-600 dark:text-rose-400" />
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-secondary-600 dark:text-dark-400">This Month Expense</p>
                    <p class="text-2xl font-bold text-rose-600 dark:text-rose-400">Rp 18,750,000</p>
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
                {{-- Filters --}}
                <x-card class="p-4 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <x-input placeholder="Search accounts..." icon="magnifying-glass" />
                        <x-select.styled 
                            placeholder="All Banks"
                            :options="[
                                ['label' => 'All Banks', 'value' => ''],
                                ['label' => 'Bank BCA', 'value' => 'bca'],
                                ['label' => 'Bank Mandiri', 'value' => 'mandiri'],
                                ['label' => 'Bank BNI', 'value' => 'bni']
                            ]"
                        />
                        <x-select.styled 
                            placeholder="Account Status"
                            :options="[
                                ['label' => 'All Status', 'value' => ''],
                                ['label' => 'Active', 'value' => 'active'],
                                ['label' => 'Inactive', 'value' => 'inactive']
                            ]"
                        />
                        <x-button color="primary dark:primary" outline icon="funnel">
                            Filter
                        </x-button>
                    </div>
                </x-card>

                {{-- Bank Accounts Grid --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Account 1 --}}
                    <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 hover:shadow-lg hover:border-primary-300 dark:hover:border-primary-600 transition-all duration-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-primary-600 dark:bg-primary-700 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">BCA</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-100">PT Maju Bersama - Operational</h3>
                                    <p class="text-sm text-secondary-500 dark:text-dark-400">Bank BCA • ****7890</p>
                                </div>
                            </div>
                            <x-badge text="Active" color="green dark:green" />
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-secondary-600 dark:text-dark-400">Current Balance</span>
                                <span class="text-xl font-bold text-green-600 dark:text-green-400">Rp 75,500,000</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-secondary-600 dark:text-dark-400">Last Transaction</span>
                                <span class="text-sm text-secondary-900 dark:text-dark-200">2 hours ago</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-secondary-600 dark:text-dark-400">This Month</span>
                                <span class="text-sm text-green-600 dark:text-green-400">+Rp 12,300,000</span>
                            </div>
                        </div>

                        <div class="flex space-x-2 mt-4 pt-4 border-t border-secondary-200 dark:border-dark-700">
                            <x-button size="sm" color="primary dark:primary" outline icon="eye">
                                View Details
                            </x-button>
                            <x-button size="sm" color="secondary dark:dark" outline icon="plus">
                                Add Transaction
                            </x-button>
                            <x-button.circle size="sm" color="secondary dark:dark" outline icon="pencil" />
                        </div>
                    </x-card>

                    {{-- Account 2 --}}
                    <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 hover:shadow-lg hover:border-primary-300 dark:hover:border-primary-600 transition-all duration-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-amber-500 dark:bg-amber-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">MDR</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-100">PT Maju Bersama - Savings</h3>
                                    <p class="text-sm text-secondary-500 dark:text-dark-400">Bank Mandiri • ****3456</p>
                                </div>
                            </div>
                            <x-badge text="Active" color="green dark:green" />
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-secondary-600 dark:text-dark-400">Current Balance</span>
                                <span class="text-xl font-bold text-green-600 dark:text-green-400">Rp 32,750,000</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-secondary-600 dark:text-dark-400">Last Transaction</span>
                                <span class="text-sm text-secondary-900 dark:text-dark-200">1 day ago</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-secondary-600 dark:text-dark-400">This Month</span>
                                <span class="text-sm text-green-600 dark:text-green-400">+Rp 5,200,000</span>
                            </div>
                        </div>

                        <div class="flex space-x-2 mt-4 pt-4 border-t border-secondary-200 dark:border-dark-700">
                            <x-button size="sm" color="primary dark:primary" outline icon="eye">
                                View Details
                            </x-button>
                            <x-button size="sm" color="secondary dark:dark" outline icon="plus">
                                Add Transaction
                            </x-button>
                            <x-button.circle size="sm" color="secondary dark:dark" outline icon="pencil" />
                        </div>
                    </x-card>

                    {{-- Account 3 --}}
                    <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 hover:shadow-lg hover:border-primary-300 dark:hover:border-primary-600 transition-all duration-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-orange-500 dark:bg-orange-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">BNI</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-100">PT Maju Bersama - Investment</h3>
                                    <p class="text-sm text-secondary-500 dark:text-dark-400">Bank BNI • ****9012</p>
                                </div>
                            </div>
                            <x-badge text="Active" color="green dark:green" />
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-secondary-600 dark:text-dark-400">Current Balance</span>
                                <span class="text-xl font-bold text-green-600 dark:text-green-400">Rp 15,500,000</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-secondary-600 dark:text-dark-400">Last Transaction</span>
                                <span class="text-sm text-secondary-900 dark:text-dark-200">3 days ago</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-secondary-600 dark:text-dark-400">This Month</span>
                                <span class="text-sm text-rose-600 dark:text-rose-400">-Rp 2,100,000</span>
                            </div>
                        </div>

                        <div class="flex space-x-2 mt-4 pt-4 border-t border-secondary-200 dark:border-dark-700">
                            <x-button size="sm" color="primary dark:primary" outline icon="eye">
                                View Details
                            </x-button>
                            <x-button size="sm" color="secondary dark:dark" outline icon="plus">
                                Add Transaction
                            </x-button>
                            <x-button.circle size="sm" color="secondary dark:dark" outline icon="pencil" />
                        </div>
                    </x-card>

                    {{-- Add New Account Card --}}
                    <x-card class="p-6 border-2 border-dashed border-secondary-300 dark:border-dark-600 dark:bg-dark-800 hover:border-primary-400 dark:hover:border-primary-600 hover:bg-primary-50/30 dark:hover:bg-primary-900/20 transition-all duration-200">
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-secondary-100 dark:bg-dark-700 border border-secondary-200 dark:border-dark-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <x-icon name="plus" class="w-8 h-8 text-secondary-500 dark:text-dark-400" />
                            </div>
                            <h3 class="text-lg font-medium text-secondary-900 dark:text-dark-100 mb-2">Add New Bank Account</h3>
                            <p class="text-secondary-500 dark:text-dark-400 text-sm mb-6">Connect another bank account to track your finances</p>
                            <x-button color="primary dark:primary" icon="plus">
                                Add Bank Account
                            </x-button>
                        </div>
                    </x-card>
                </div>
            </div>
        </x-tab.items>

        {{-- Tab 2: Recent Transactions --}}
        <x-tab.items tab="Recent Transactions">
            <div class="space-y-6">
                {{-- Transaction Filters --}}
                <x-card class="p-4 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <x-input placeholder="Search transactions..." icon="magnifying-glass" />
                        <x-select.styled 
                            placeholder="All Accounts"
                            :options="[
                                ['label' => 'All Accounts', 'value' => ''],
                                ['label' => 'BCA - Operational', 'value' => '1'],
                                ['label' => 'Mandiri - Savings', 'value' => '2'],
                                ['label' => 'BNI - Investment', 'value' => '3']
                            ]"
                        />
                        <x-select.styled 
                            placeholder="Transaction Type"
                            :options="[
                                ['label' => 'All Types', 'value' => ''],
                                ['label' => 'Credit (Income)', 'value' => 'credit'],
                                ['label' => 'Debit (Expense)', 'value' => 'debit']
                            ]"
                        />
                        <x-date placeholder="Date Range" range />
                        <x-button color="primary dark:primary" icon="magnifying-glass">
                            Search
                        </x-button>
                    </div>
                </x-card>

                {{-- Transactions Table --}}
                <x-card class="border border-secondary-200 dark:border-dark-700 dark:bg-dark-800">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-secondary-50 dark:bg-dark-700 border-b border-secondary-200 dark:border-dark-600">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-secondary-600 dark:text-dark-300 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-secondary-600 dark:text-dark-300 uppercase tracking-wider">Account</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-secondary-600 dark:text-dark-300 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-secondary-600 dark:text-dark-300 uppercase tracking-wider">Reference</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-secondary-600 dark:text-dark-300 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-secondary-600 dark:text-dark-300 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-secondary-600 dark:text-dark-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-dark-800 divide-y divide-secondary-200 dark:divide-dark-700">
                                <tr class="hover:bg-secondary-50 dark:hover:bg-dark-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-900 dark:text-dark-200">
                                        2024-01-15<br>
                                        <span class="text-xs text-secondary-500 dark:text-dark-400">14:30</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-primary-600 dark:bg-primary-700 rounded flex items-center justify-center mr-3">
                                                <span class="text-white text-xs font-bold">BCA</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-secondary-900 dark:text-dark-200">BCA Operational</div>
                                                <div class="text-xs text-secondary-500 dark:text-dark-400">****7890</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-secondary-900 dark:text-dark-200">
                                        Transfer from PT Client ABC<br>
                                        <span class="text-xs text-secondary-500 dark:text-dark-400">Payment for Invoice INV-2024-0145</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-500 dark:text-dark-400">TRF240115001</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-badge text="Credit" color="green dark:green" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-green-600 dark:text-green-400">
                                        +Rp 15,000,000
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <x-button.circle size="sm" color="secondary dark:dark" outline icon="pencil" />
                                    </td>
                                </tr>

                                <tr class="hover:bg-secondary-50 dark:hover:bg-dark-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-900 dark:text-dark-200">
                                        2024-01-14<br>
                                        <span class="text-xs text-secondary-500 dark:text-dark-400">09:15</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-primary-600 dark:bg-primary-700 rounded flex items-center justify-center mr-3">
                                                <span class="text-white text-xs font-bold">BCA</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-secondary-900 dark:text-dark-200">BCA Operational</div>
                                                <div class="text-xs text-secondary-500 dark:text-dark-400">****7890</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-secondary-900 dark:text-dark-200">
                                        Monthly Bank Administration Fee<br>
                                        <span class="text-xs text-secondary-500 dark:text-dark-400">January 2024</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-500 dark:text-dark-400">ADM240114</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-badge text="Debit" color="red dark:red" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-rose-600 dark:text-rose-400">
                                        -Rp 25,000
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <x-button.circle size="sm" color="secondary dark:dark" outline icon="pencil" />
                                    </td>
                                </tr>

                                <tr class="hover:bg-secondary-50 dark:hover:bg-dark-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-900 dark:text-dark-200">
                                        2024-01-13<br>
                                        <span class="text-xs text-secondary-500 dark:text-dark-400">16:45</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-amber-500 dark:bg-amber-600 rounded flex items-center justify-center mr-3">
                                                <span class="text-white text-xs font-bold">MDR</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-secondary-900 dark:text-dark-200">Mandiri Savings</div>
                                                <div class="text-xs text-secondary-500 dark:text-dark-400">****3456</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-secondary-900 dark:text-dark-200">
                                        Interest Income<br>
                                        <span class="text-xs text-secondary-500 dark:text-dark-400">December 2023 Interest</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-500 dark:text-dark-400">INT240113</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-badge text="Credit" color="green dark:green" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-green-600 dark:text-green-400">
                                        +Rp 125,000
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <x-button.circle size="sm" color="secondary dark:dark" outline icon="pencil" />
                                    </td>
                                </tr>

                                <tr class="hover:bg-secondary-50 dark:hover:bg-dark-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-900 dark:text-dark-200">
                                        2024-01-12<br>
                                        <span class="text-xs text-secondary-500 dark:text-dark-400">11:20</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-orange-500 dark:bg-orange-600 rounded flex items-center justify-center mr-3">
                                                <span class="text-white text-xs font-bold">BNI</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-secondary-900 dark:text-dark-200">BNI Investment</div>
                                                <div class="text-xs text-secondary-500 dark:text-dark-400">****9012</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-secondary-900 dark:text-dark-200">
                                        Transfer to BCA Operational<br>
                                        <span class="text-xs text-secondary-500 dark:text-dark-400">Internal fund transfer</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-500 dark:text-dark-400">TRF240112002</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-badge text="Debit" color="red dark:red" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-rose-600 dark:text-rose-400">
                                        -Rp 5,000,000
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <x-button.circle size="sm" color="secondary dark:dark" outline icon="pencil" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination --}}
                    <div class="px-6 py-4 border-t border-secondary-200 dark:border-dark-700 bg-secondary-50 dark:bg-dark-700">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-secondary-700 dark:text-dark-300">
                                Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">247</span> transactions
                            </div>
                            <div class="flex space-x-2">
                                <x-button size="sm" color="secondary dark:dark" outline>Previous</x-button>
                                <x-button size="sm" color="primary dark:primary">1</x-button>
                                <x-button size="sm" color="secondary dark:dark" outline>2</x-button>
                                <x-button size="sm" color="secondary dark:dark" outline>3</x-button>
                                <x-button size="sm" color="secondary dark:dark" outline>Next</x-button>
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>
        </x-tab.items>

        {{-- Tab 3: Cash Flow Summary --}}
        <x-tab.items tab="Cash Flow Summary">
            <div class="space-y-6">
                {{-- Cash Flow Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <x-icon name="arrow-trending-up" class="w-8 h-8 text-green-600 dark:text-green-400" />
                            </div>
                            <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-100">Total Inflow</h3>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">Rp 65,450,000</p>
                            <p class="text-sm text-secondary-500 dark:text-dark-400 mt-1">This month</p>
                        </div>
                    </x-card>

                    <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <x-icon name="arrow-trending-down" class="w-8 h-8 text-rose-600 dark:text-rose-400" />
                            </div>
                            <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-100">Total Outflow</h3>
                            <p class="text-3xl font-bold text-rose-600 dark:text-rose-400 mt-2">Rp 23,175,000</p>
                            <p class="text-sm text-secondary-500 dark:text-dark-400 mt-1">This month</p>
                        </div>
                    </x-card>

                    <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <x-icon name="banknotes" class="w-8 h-8 text-primary-600 dark:text-primary-400" />
                            </div>
                            <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-100">Net Cash Flow</h3>
                            <p class="text-3xl font-bold text-primary-600 dark:text-primary-400 mt-2">Rp 42,275,000</p>
                            <p class="text-sm text-secondary-500 dark:text-dark-400 mt-1">This month</p>
                        </div>
                    </x-card>
                </div>

                {{-- Cash Flow Chart Placeholder --}}
                <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800">
                    <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-100 mb-6">Monthly Cash Flow Trend</h3>
                    <div class="h-64 bg-secondary-100 dark:bg-dark-700 border border-secondary-200 dark:border-dark-600 rounded-lg flex items-center justify-center">
                        <div class="text-center">
                            <x-icon name="chart-bar" class="w-16 h-16 text-secondary-400 dark:text-dark-400 mx-auto mb-4" />
                            <p class="text-secondary-600 dark:text-dark-300">Cash Flow Chart</p>
                            <p class="text-sm text-secondary-500 dark:text-dark-400">Will be implemented with Chart.js</p>
                        </div>
                    </div>
                </x-card>

                {{-- Quick Actions --}}
                <x-card class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800">
                    <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-100 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-button color="primary dark:primary" icon="plus" class="w-full">
                            Add Transaction
                        </x-button>
                        <x-button color="secondary dark:dark" outline icon="document-arrow-down" class="w-full">
                            Import Statement
                        </x-button>
                        <x-button color="secondary dark:dark" outline icon="document-text" class="w-full">
                            Generate Report
                        </x-button>
                    </div>
                </x-card>
            </div>
        </x-tab.items>
    </x-tab>
</div>
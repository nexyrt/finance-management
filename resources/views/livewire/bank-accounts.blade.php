<!-- Part 1: Header, Stats, Search and Main Table Header -->
<section class="w-full">
    <div class="p-6 space-y-6">
        <!-- Page Title and Subtitle -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white">Bank Account Management</h1>
            <p class="text-zinc-400 mt-1">Manage your bank accounts and track transactions</p>
        </div>

        <!-- Statistics Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div
                class="bg-zinc-800/50 backdrop-blur-sm border border-zinc-700/50 rounded-xl p-4 flex flex-col hover:border-blue-500/50 hover:bg-zinc-800/80 transition-all duration-300 shadow-md">
                <span class="text-zinc-400 text-sm font-medium">Total Accounts</span>
                <span class="text-3xl font-bold text-white mt-1">{{ $this->accountsStats['totalAccounts'] }}</span>
            </div>

            <div
                class="bg-zinc-800/50 backdrop-blur-sm border border-zinc-700/50 rounded-xl p-4 flex flex-col hover:border-green-500/50 hover:bg-zinc-800/80 transition-all duration-300 shadow-md">
                <span class="text-zinc-400 text-sm font-medium">Total Balance</span>
                <div class="flex items-end gap-2">
                    <span
                        class="text-3xl font-bold text-white mt-1">{{ number_format($this->accountsStats['totalBalance'], 2) }}</span>
                    <span class="text-zinc-400 text-sm mb-1">mixed currencies</span>
                </div>
            </div>

            <div
                class="bg-zinc-800/50 backdrop-blur-sm border border-zinc-700/50 rounded-xl p-4 flex flex-col hover:border-purple-500/50 hover:bg-zinc-800/80 transition-all duration-300 shadow-md">
                <span class="text-zinc-400 text-sm font-medium">By Currency</span>
                <div class="mt-2 flex gap-3">
                    @foreach ($this->accountsStats['currencyGroups'] as $group)
                        <flux:badge class="bg-zinc-700/70 text-zinc-200 border border-zinc-600/50">
                            {{ $group->currency }}: {{ number_format($group->total, 2) }}
                        </flux:badge>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Search & Controls -->
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="w-full md:w-1/2">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                    placeholder="Search accounts..." class="rounded-lg" />
            </div>

            <div class="flex gap-4 w-full md:w-auto">
                <flux:button wire:click="createAccount" icon="plus" variant="primary"
                    class="px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                    Add Account
                </flux:button>
            </div>
        </div>

        <!-- Bank Accounts Table -->
        <div class="bg-zinc-800/70 rounded-xl shadow-lg backdrop-blur-sm border border-zinc-700/50 overflow-hidden">
            <div class="min-w-full divide-y divide-zinc-700/70">
                <table class="min-w-full divide-y divide-zinc-700/70">
                    <thead class="bg-zinc-900/70 backdrop-blur-sm">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                <div class="flex cursor-pointer items-center group" wire:click="sortBy('account_name')">
                                    <span class="group-hover:text-white transition-colors duration-200">Account
                                        Name</span>
                                    @if ($sortField === 'account_name')
                                        <span class="ml-1 text-blue-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                <div class="flex cursor-pointer items-center group"
                                    wire:click="sortBy('account_number')">
                                    <span class="group-hover:text-white transition-colors duration-200">Account
                                        Number</span>
                                    @if ($sortField === 'account_number')
                                        <span class="ml-1 text-blue-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                            </th>
                            <!-- Part 2: Main Table Body, Pagination and Bank Account Form Modal -->
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                <div class="flex cursor-pointer items-center group" wire:click="sortBy('bank_name')">
                                    <span class="group-hover:text-white transition-colors duration-200">Bank</span>
                                    @if ($sortField === 'bank_name')
                                        <span class="ml-1 text-blue-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                <div class="flex cursor-pointer items-center group"
                                    wire:click="sortBy('current_balance')">
                                    <span class="group-hover:text-white transition-colors duration-200">Balance</span>
                                    @if ($sortField === 'current_balance')
                                        <span class="ml-1 text-blue-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-right text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-zinc-800/30 backdrop-blur-sm divide-y divide-zinc-700/50">
                        @forelse($this->bankAccounts as $account)
                            <tr class="hover:bg-zinc-700/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-200">
                                    {{ $account->account_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-200">
                                    <flux:badge variant="outline" class="font-mono">
                                        {{ $account->account_number }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-200">
                                    {{ $account->bank_name }}
                                    @if ($account->branch)
                                        <span class="text-zinc-400 text-xs">
                                            ({{ $account->branch }})
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center">
                                        <span class="font-mono text-zinc-200 mr-2">{{ $account->currency }}</span>
                                        <span class="font-mono font-medium text-zinc-100">
                                            {{ number_format($account->current_balance, 2) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <flux:button wire:click="viewAccountDetails({{ $account->id }})"
                                            size="sm" variant="ghost" class="text-zinc-400 hover:text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </flux:button>
                                        <flux:button wire:click="openTransactionForm({{ $account->id }})"
                                            size="sm" variant="ghost" class="text-zinc-400 hover:text-blue-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </flux:button>
                                        <flux:button wire:click="editAccount({{ $account->id }})" size="sm"
                                            variant="ghost" class="text-zinc-400 hover:text-yellow-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </flux:button>
                                        <flux:button wire:click="confirmDelete({{ $account->id }})" size="sm"
                                            variant="ghost" class="text-zinc-400 hover:text-red-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="px-6 py-12 whitespace-nowrap text-sm text-zinc-400 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-zinc-600 mb-4"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        <p class="text-zinc-500 mb-1">No bank accounts found</p>
                                        <p class="text-zinc-600 text-xs">Click "Add Account" to create one</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-4 py-3 bg-zinc-900/50 backdrop-blur-sm border-t border-zinc-700/50">
                {{ $this->bankAccounts->links() }}
            </div>
        </div>
    </div>

    <!-- Part 3: Account Form Modal and Delete Confirmation Modal -->

    <!-- Bank Account Form Modal -->
    <flux:modal wire:model.self="showAccountFormModal">
        <div class="space-y-6 p-2">
            <div>
                <flux:heading size="lg" class="text-white">
                    {{ $editMode ? 'Edit Bank Account' : 'Add New Bank Account' }}
                </flux:heading>
                <flux:text class="mt-2 text-zinc-400">
                    {{ $editMode ? 'Update bank account details.' : 'Fill in the bank account details.' }}
                </flux:text>
            </div>

            <form wire:submit="saveAccount" class="space-y-5">
                <flux:input wire:model="account_name" label="Account Name" placeholder="Enter account name"
                    error="{{ $errors->first('account_name') }}" class="rounded-lg" />

                <flux:input wire:model="account_number" label="Account Number" placeholder="Enter account number"
                    error="{{ $errors->first('account_number') }}" class="rounded-lg" />

                <flux:input wire:model="bank_name" label="Bank Name" placeholder="Enter bank name"
                    error="{{ $errors->first('bank_name') }}" class="rounded-lg" />

                <flux:input wire:model="branch" label="Branch (Optional)" placeholder="Enter branch name"
                    error="{{ $errors->first('branch') }}" class="rounded-lg" />

                <flux:select wire:model.live="currency" label="Currency" placeholder="Select currency"
                    error="{{ $errors->first('currency') }}" class="rounded-lg">
                    @foreach ($currencyOptions as $option)
                        <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}
                        </flux:select.option>
                    @endforeach
                </flux:select>

                @if (!$editMode)
                    <flux:input wire:model="initial_balance" type="number" step="0.01" label="Initial Balance"
                        placeholder="0.00" error="{{ $errors->first('initial_balance') }}" class="rounded-lg" />
                @endif

                <div class="flex justify-end space-x-3 pt-2">
                    <flux:button wire:click="cancelAccountForm" variant="subtle" class="rounded-lg">
                        Cancel
                    </flux:button>
                    <flux:button type="submit" variant="primary" class="rounded-lg shadow-md">
                        {{ $editMode ? 'Update Account' : 'Create Account' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal wire:model.self="showDeleteConfirmModal">
        <div class="space-y-6 p-2">
            <div>
                <flux:heading size="lg" class="text-white">Confirm Deletion</flux:heading>
                <flux:text class="mt-2 text-zinc-400">
                    Are you sure you want to delete this bank account?
                    This action cannot be undone.
                </flux:text>
            </div>

            @if ($accountToDelete)
                <div class="bg-zinc-900/70 backdrop-blur-sm rounded-lg p-4 space-y-2 border border-zinc-800">
                    <div class="flex justify-between">
                        <p class="font-medium text-zinc-200">{{ $accountToDelete->account_name }}</p>
                        <p class="text-zinc-400 font-mono">{{ $accountToDelete->account_number }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-zinc-400">{{ $accountToDelete->bank_name }}</p>
                        <p class="font-medium text-zinc-200">
                            <span class="text-zinc-400 text-sm">Current Balance:</span>
                            {{ $accountToDelete->currency }} {{ number_format($accountToDelete->current_balance, 2) }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="flex justify-end space-x-3 pt-2">
                <flux:button wire:click="$set('showDeleteConfirmModal', false)" variant="subtle" class="rounded-lg">
                    Cancel
                </flux:button>
                <flux:button wire:click="deleteAccount" variant="danger" class="rounded-lg shadow-md">
                    Delete Account
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Account Details Modal (Part 1) -->
    <flux:modal wire:model.self="showDetailsModal" class="md:w-5xl">
        @if ($selectedAccount)
            <div class="space-y-6 p-2">
                <div>
                    <flux:heading size="lg" class="text-white">Account Details</flux:heading>
                    <flux:text class="mt-2 text-zinc-400">
                        Viewing account details and transaction history
                    </flux:text>
                </div>

                <!-- Account Info -->
                <div class="bg-zinc-900/50 backdrop-blur-sm rounded-lg p-5 space-y-4 border border-zinc-800">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-white">{{ $selectedAccount->account_name }}</h3>
                        <flux:badge class="bg-zinc-700/70 text-zinc-100 border border-zinc-600/50">
                            {{ $selectedAccount->bank_name }}
                        </flux:badge>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-zinc-400 text-sm">Account Number</p>
                            <p class="text-zinc-200 font-mono">{{ $selectedAccount->account_number }}</p>
                        </div>
                        <div>
                            <p class="text-zinc-400 text-sm">Branch</p>
                            <p class="text-zinc-200">{{ $selectedAccount->branch ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-zinc-400 text-sm">Currency</p>
                            <p class="text-zinc-200">{{ $selectedAccount->currency }}</p>
                        </div>
                        <div>
                            <p class="text-zinc-400 text-sm">Current Balance</p>
                            <p class="text-zinc-100 font-semibold font-mono">
                                {{ number_format($selectedAccount->current_balance, 2) }}</p>
                        </div>
                    </div>

                    <div class="flex justify-between pt-2">
                        <flux:button wire:click="editAccount({{ $selectedAccount->id }})" variant="subtle"
                            size="sm" class="rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit Account
                        </flux:button>

                        <flux:button wire:click="openTransactionForm({{ $selectedAccount->id }})" variant="primary"
                            size="sm" class="rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            New Transaction
                        </flux:button>
                    </div>
                </div>
                <!-- Part 4: Transaction Stats, Filters, List and Transaction Form Modal -->

                <!-- Transaction Stats -->
                @if ($this->transactionStats)
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-zinc-900/50 backdrop-blur-sm rounded-lg p-3 border border-zinc-800">
                            <p class="text-zinc-400 text-xs">Incoming</p>
                            <p class="text-green-400 font-semibold font-mono text-lg">
                                {{ number_format($this->transactionStats['incoming'], 2) }}
                            </p>
                        </div>
                        <div class="bg-zinc-900/50 backdrop-blur-sm rounded-lg p-3 border border-zinc-800">
                            <p class="text-zinc-400 text-xs">Outgoing</p>
                            <p class="text-red-400 font-semibold font-mono text-lg">
                                {{ number_format($this->transactionStats['outgoing'], 2) }}
                            </p>
                        </div>
                        <div class="bg-zinc-900/50 backdrop-blur-sm rounded-lg p-3 border border-zinc-800">
                            <p class="text-zinc-400 text-xs">Net</p>
                            <p class="text-blue-400 font-semibold font-mono text-lg">
                                {{ number_format($this->transactionStats['net'], 2) }}
                            </p>
                        </div>
                    </div>
                @endif


                <!-- Transaction Filters -->
                <div class="flex flex-col md:flex-row gap-4 pt-2">
                    <div class="w-full md:w-1/2">
                        <x-inputs.daterangepicker wire:model.live="dateFilter" startName="dateRangeStart"
                            endName="dateRangeEnd" label="Date Range" placeholder="Select date range" />
                    </div>

                    <div class="w-full md:w-1/2">
                        <flux:select wire:model.live="transactionTypeFilter" label="Transaction Type"
                            placeholder="All Types" class="w-full">
                            <flux:select.option value="">All Types</flux:select.option>
                            @foreach ($transactionTypeOptions as $option)
                                <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>

                <div class="flex justify-end">
                    <flux:button wire:click="resetFilters" variant="subtle" size="sm" class="rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset Filters
                    </flux:button>
                </div>

                <!-- Transactions Table -->
                <div class="bg-zinc-900/30 backdrop-blur-sm rounded-lg border border-zinc-800 overflow-hidden">
                    <table class="min-w-full divide-y divide-zinc-800">
                        <thead class="bg-zinc-900/70">
                            <tr>
                                <th
                                    class="px-4 py-3.5 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                    Date</th>
                                <th
                                    class="px-4 py-3.5 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                    Type</th>
                                <th
                                    class="px-4 py-3.5 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                    Reference</th>
                                <th
                                    class="px-4 py-3.5 text-right text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                    Amount</th>
                            </tr>
                         </thead>
                        <tbody class="bg-zinc-900/20 divide-y divide-zinc-800">
                            @forelse($this->accountTransactions as $transaction)
                                <tr class="hover:bg-zinc-800/30 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-200">
                                        {{ $transaction->transaction_date->format('d M, Y') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <flux:badge
                                            class="
                                            @if ($transaction->transaction_type === 'deposit') bg-green-900/40 text-green-100 border border-green-700/50
                                            @elseif($transaction->transaction_type === 'withdrawal') bg-red-900/40 text-red-100 border border-red-700/50
                                            @elseif($transaction->transaction_type === 'transfer') bg-blue-900/40 text-blue-100 border border-blue-700/50
                                            @elseif($transaction->transaction_type === 'fee') bg-orange-900/40 text-orange-100 border border-orange-700/50
                                            @else bg-purple-900/40 text-purple-100 border border-purple-700/50 @endif">
                                            {{ ucfirst($transaction->transaction_type) }}
                                        </flux:badge>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-300 font-mono">
                                        {{ $transaction->reference_number ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-mono">
                                        <span
                                            class="@if ($transaction->amount > 0) text-green-400 @else text-red-400 @endif">
                                            {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        class="px-6 py-8 whitespace-nowrap text-sm text-zinc-400 text-center">
                                        <p>No transactions found for the selected period</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </flux:modal>

    <!-- Transaction Form Modal -->
    <flux:modal wire:model.self="showTransactionFormModal">
        @if ($selectedAccount)
            <div class="space-y-6 p-2">
                <div>
                    <flux:heading size="lg" class="text-white">New Transaction</flux:heading>
                    <flux:text class="mt-2 text-zinc-400">
                        Create a new transaction for {{ $selectedAccount->account_name }}
                    </flux:text>
                </div>

                <div class="bg-zinc-900/50 backdrop-blur-sm rounded-lg p-3 border border-zinc-800">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-zinc-400 text-xs">Current Balance</span>
                            <span class="block text-zinc-200 font-semibold font-mono">
                                {{ $selectedAccount->currency }}
                                {{ number_format($selectedAccount->current_balance, 2) }}
                            </span>
                        </div>
                        <flux:badge class="bg-zinc-700/70 text-zinc-100 border border-zinc-600/50">
                            {{ $selectedAccount->bank_name }}
                        </flux:badge>
                    </div>
                </div>

                <form wire:submit="saveTransaction" class="space-y-5">
                    <flux:select wire:model.live="transaction_type" label="Transaction Type"
                        placeholder="Select transaction type" error="{{ $errors->first('transaction_type') }}"
                        class="rounded-lg">
                        @foreach ($transactionTypeOptions as $option)
                            <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input wire:model="transaction_amount" type="number" step="0.01" label="Amount"
                        placeholder="0.00" error="{{ $errors->first('transaction_amount') }}" class="rounded-lg" />

                    <x-inputs.datepicker wire:model.live="transaction_date" label="Transaction Date"
                        error="{{ $errors->first('transaction_date') }}" class="rounded-lg" modalMode="true" />

                    <flux:input wire:model="reference_number" label="Reference Number (Optional)"
                        placeholder="Enter reference number" error="{{ $errors->first('reference_number') }}"
                        class="rounded-lg" />

                    <flux:input wire:model="description" label="Description (Optional)"
                        placeholder="Enter description" error="{{ $errors->first('description') }}"
                        class="rounded-lg" />

                    <div class="flex justify-end space-x-3 pt-2">
                        <flux:button wire:click="$set('showTransactionFormModal', false)" variant="subtle"
                            class="rounded-lg">
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary" class="rounded-lg shadow-md">
                            Save Transaction
                        </flux:button>
                    </div>
                </form>
            </div>
        @endif
    </flux:modal>

    <!-- Include flash message component -->
    @include('components.shared.flash-message')
</section>

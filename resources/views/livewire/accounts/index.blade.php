{{-- resources/views/livewire/accounts/index.blade.php --}}

<div>
    {{-- Header Section --}}
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-1">
                <h1
                    class="text-2xl sm:text-3xl lg:text-4xl font-bold bg-gradient-to-r from-dark-900 via-primary-600 to-primary-700 dark:from-white dark:via-primary-300 dark:to-primary-200 bg-clip-text text-transparent">
                    Bank Account Management
                </h1>
                <p class="text-dark-600 dark:text-dark-400 text-base sm:text-lg">
                    Manage bank accounts, transactions, and monitor cashflow
                </p>
            </div>
            @if ($selectedAccountId)
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:pr-5">
                    <x-dropdown icon="cog-6-tooth" position="bottom-end">
                        <x-slot:trigger>
                            <x-button color="secondary" outline icon="cog-6-tooth" class="w-full sm:w-auto"
                                loading="editAccount({{ $selectedAccountId }})">
                                Account Settings
                            </x-button>
                        </x-slot:trigger>
                        <x-dropdown.items text="Edit Account" icon="pencil"
                            wire:click="editAccount({{ $selectedAccountId }})"
                            loading="editAccount({{ $selectedAccountId }})" />
                        <x-dropdown.items text="Delete Account" icon="trash"
                            wire:click="deleteAccount({{ $selectedAccountId }})"
                            loading="deleteAccount({{ $selectedAccountId }})" class="text-red-600 dark:text-red-400" />
                    </x-dropdown>
                </div>
            @endif
        </div>
    </div>

    <div class="flex flex-col xl:flex-row gap-6">
        {{-- Left Sidebar - Account Cards --}}
        <div class="w-full xl:w-80 2xl:w-96 xl:flex-shrink-0 space-y-4">
            {{-- Header --}}
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-dark-900 dark:text-dark-50">My Cards</h2>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Select account to manage</p>
                </div>
                <x-button wire:click="openCreateAccount" color="primary" icon="plus" size="sm"
                    loading="openCreateAccount">
                    Add
                </x-button>
            </div>

            {{-- Account Cards --}}
            @foreach ($this->accountsData as $account)
                <div wire:click="selectAccount({{ $account['id'] }})"
                    class="p-4 bg-white dark:bg-dark-800 border-2 border-zinc-200 dark:border-dark-600 rounded-xl cursor-pointer transition-all hover:shadow-md {{ $selectedAccountId == $account['id'] ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : '' }}">

                    {{-- Loading overlay for selection --}}
                    <div wire:loading wire:target="selectAccount({{ $account['id'] }})"
                        class="absolute inset-0 bg-white/50 dark:bg-dark-800/50 rounded-xl flex items-center justify-center">
                        <div class="flex items-center gap-2">
                            <x-icon name="arrow-path" class="w-4 h-4 text-primary-600 animate-spin" />
                            <span class="text-sm text-primary-600">Loading...</span>
                        </div>
                    </div>

                    {{-- Card Header --}}
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-12 w-12 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center">
                                <x-icon name="building-library" class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-dark-900 dark:text-dark-50">{{ $account['name'] }}</h3>
                                <p class="text-sm text-dark-600 dark:text-dark-400">{{ $account['bank'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            @if ($account['trend'] === 'up')
                                <x-icon name="arrow-trending-up" class="w-4 h-4 text-green-600 dark:text-green-400" />
                            @else
                                <x-icon name="arrow-trending-down" class="w-4 h-4 text-red-600 dark:text-red-400" />
                            @endif
                        </div>
                    </div>

                    {{-- Balance --}}
                    <div class="mb-3">
                        <p
                            class="text-2xl font-bold {{ $account['balance'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            Rp {{ number_format($account['balance'], 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-dark-600 dark:text-dark-400">•••• •••• ••••
                            {{ substr($account['account_number'], -4) }}</p>
                    </div>

                    {{-- Recent Transactions Preview --}}
                    @if ($account['recent_transactions']->count() > 0)
                        <div class="space-y-2">
                            @foreach ($account['recent_transactions']->take(2) as $transaction)
                                <div class="flex items-center justify-between text-xs">
                                    <span
                                        class="text-dark-600 dark:text-dark-400 truncate flex-1">{{ Str::limit($transaction->description, 20) }}</span>
                                    <span
                                        class="{{ $transaction->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-medium">
                                        {{ $transaction->transaction_type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount / 1000, 0) }}k
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            @if ($this->accountsData->count() === 0)
                <div class="text-center py-8">
                    <x-icon name="building-library" class="w-12 h-12 text-zinc-400 mx-auto mb-3" />
                    <p class="text-dark-600 dark:text-dark-400 mb-4">No accounts yet</p>
                    <x-button wire:click="openCreateAccount" color="primary" icon="plus" size="sm"
                        loading="openCreateAccount">
                        Add First Account
                    </x-button>
                </div>
            @endif
        </div>

        {{-- Main Content --}}
        <div class="flex-1 space-y-6">
            @if ($selectedAccountId)
                {{-- Quick Actions & Chart Section --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Quick Actions --}}
                    <div
                        class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6 h-full">
                        <div class="flex flex-col h-full justify-between space-y-4">
                            <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-4">Quick Actions</h3>

                            {{-- Add Transaction --}}
                            <button wire:click="openTransaction"
                                class="w-full flex items-center gap-4 p-4 bg-primary-50 dark:bg-primary-900/20 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-xl transition-all group cursor-pointer relative">
                                <div wire:loading wire:target="openTransaction"
                                    class="absolute inset-0 bg-primary-50/80 dark:bg-primary-900/40 rounded-xl flex items-center justify-center">
                                    <x-icon name="arrow-path" class="w-5 h-5 text-primary-600 animate-spin" />
                                </div>
                                <div class="h-12 w-12 bg-primary-500 rounded-xl flex items-center justify-center">
                                    <x-icon name="plus" class="w-6 h-6 text-white" />
                                </div>
                                <div class="text-left">
                                    <h4 class="font-semibold text-dark-900 dark:text-dark-50">Add Transaction</h4>
                                    <p class="text-sm text-dark-600 dark:text-dark-400">Record income or expense</p>
                                </div>
                            </button>

                            {{-- Transfer --}}
                            <button wire:click="openTransfer"
                                class="w-full flex items-center gap-4 p-4 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-xl transition-all group cursor-pointer relative">
                                <div wire:loading wire:target="openTransfer"
                                    class="absolute inset-0 bg-blue-50/80 dark:bg-blue-900/40 rounded-xl flex items-center justify-center">
                                    <x-icon name="arrow-path" class="w-5 h-5 text-blue-600 animate-spin" />
                                </div>
                                <div class="h-12 w-12 bg-blue-500 rounded-xl flex items-center justify-center">
                                    <x-icon name="arrow-path" class="w-6 h-6 text-white" />
                                </div>
                                <div class="text-left">
                                    <h4 class="font-semibold text-dark-900 dark:text-dark-50">Transfer</h4>
                                    <p class="text-sm text-dark-600 dark:text-dark-400">Move funds between accounts</p>
                                </div>
                            </button>

                            {{-- Export Report --}}
                            <button wire:click="exportReport"
                                class="w-full flex items-center gap-4 p-4 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-xl transition-all group cursor-pointer relative">
                                <div wire:loading wire:target="exportReport"
                                    class="absolute inset-0 bg-green-50/80 dark:bg-green-900/40 rounded-xl flex items-center justify-center">
                                    <x-icon name="arrow-path" class="w-5 h-5 text-green-600 animate-spin" />
                                </div>
                                <div class="h-12 w-12 bg-green-500 rounded-xl flex items-center justify-center">
                                    <x-icon name="document-arrow-down" class="w-6 h-6 text-white" />
                                </div>
                                <div class="text-left">
                                    <h4 class="font-semibold text-dark-900 dark:text-dark-50">Export Report</h4>
                                    <p class="text-sm text-dark-600 dark:text-dark-400">Download transaction history
                                    </p>
                                </div>
                            </button>
                        </div>
                    </div>

                    {{-- Chart Placeholder --}}
                    <div class="lg:col-span-2">
                        <div
                            class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6 h-[400px]">
                            <h2 class="text-xl font-bold text-dark-900 dark:text-dark-50 mb-4">Financial Overview</h2>

                            {{-- Chart Coming Soon --}}
                            <div
                                class="flex items-center justify-center h-80 bg-zinc-50 dark:bg-dark-700 rounded-lg border-2 border-dashed border-zinc-300 dark:border-dark-600">
                                <div class="text-center">
                                    <div
                                        class="h-16 w-16 bg-primary-100 dark:bg-primary-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <x-icon name="chart-bar"
                                            class="w-8 h-8 text-primary-600 dark:text-primary-400" />
                                    </div>
                                    <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-2">Chart Coming
                                        Soon</h3>
                                    <p class="text-dark-600 dark:text-dark-400 text-sm max-w-xs">
                                        Cashflow visualization and financial analysis are under development
                                    </p>
                                    <div class="mt-4">
                                        <x-badge text="Coming Soon" color="primary" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab Navigation --}}
                <div
                    class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl overflow-hidden">
                    <div class="p-6 border-b border-zinc-200 dark:border-dark-600">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50">Account Activity</h3>

                            {{-- Tab Buttons --}}
                            <div class="flex items-center gap-2 bg-zinc-100 dark:bg-dark-700 p-1 rounded-lg">
                                <button wire:click="switchTab('transactions')"
                                    class="px-4 py-2 cursor-pointer text-sm font-medium rounded-md transition-all {{ $activeTab === 'transactions' ? 'bg-white dark:bg-dark-600 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-dark-600 dark:text-dark-400 hover:text-dark-900 dark:hover:text-dark-200' }}">
                                    <div class="flex items-center gap-2">
                                        <x-icon name="arrows-right-left" class="w-4 h-4" />
                                        Transactions
                                    </div>
                                </button>
                                <button wire:click="switchTab('payments')"
                                    class="px-4 py-2 cursor-pointer text-sm font-medium rounded-md transition-all {{ $activeTab === 'payments' ? 'bg-white dark:bg-dark-600 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-dark-600 dark:text-dark-400 hover:text-dark-900 dark:hover:text-dark-200' }}">
                                    <div class="flex items-center gap-2">
                                        <x-icon name="banknotes" class="w-4 h-4" />
                                        Payments
                                    </div>
                                </button>
                            </div>
                        </div>

                        {{-- Filters and Search --}}
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex gap-3">
                                @if ($activeTab === 'transactions')
                                    <div class="w-48">
                                        <x-select.styled wire:model.live="transactionType" :options="[
                                            ['label' => 'All Types', 'value' => ''],
                                            ['label' => 'Income', 'value' => 'credit'],
                                            ['label' => 'Expense', 'value' => 'debit'],
                                        ]"
                                            placeholder="Filter by type..." />
                                    </div>
                                @endif

                                <div class="w-64">
                                    <x-date wire:model.live="dateRange" range placeholder="Select date range..." />
                                </div>

                                @if ($transactionType || !empty($dateRange) || $search)
                                    <div class="transition-all duration-300 ease-in-out">
                                        <x-button wire:click="clearFilters" icon="x-mark" class="h-[36px]"
                                            color="secondary" loading="clearFilters">
                                            Clear
                                        </x-button>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1">
                                <x-input wire:model.live.debounce.300ms="search"
                                    placeholder="{{ $activeTab === 'transactions' ? 'Search transactions...' : 'Search payments...' }}"
                                    icon="magnifying-glass" />
                            </div>
                        </div>
                    </div>

                    {{-- Tab Content --}}
                    <div class="p-6">
                        @if ($activeTab === 'transactions')
                            {{-- Transactions Table --}}
                            <x-table :headers="$this->transactionHeaders" :rows="$this->transactions" :sort="$sort" selectable
                                wire:model="selected">
                                @interact('column_description', $row)
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 {{ $row->transaction_type === 'credit' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }} rounded-lg flex items-center justify-center">
                                            <x-icon
                                                name="{{ $row->transaction_type === 'credit' ? 'arrow-down' : 'arrow-up' }}"
                                                class="w-5 h-5 {{ $row->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" />
                                        </div>
                                        <div>
                                            <p class="font-medium text-dark-900 dark:text-dark-50">{{ $row->description }}
                                            </p>
                                            <p class="text-sm text-dark-600 dark:text-dark-400">
                                                {{ $row->transaction_type === 'credit' ? 'Income' : 'Expense' }}
                                            </p>
                                        </div>
                                    </div>
                                @endinteract

                                @interact('column_reference_number', $row)
                                    <span class="font-mono text-sm text-dark-600 dark:text-dark-400">
                                        {{ $row->reference_number ?: 'TXN' . str_pad($row->id, 6, '0', STR_PAD_LEFT) }}
                                    </span>
                                @endinteract

                                @interact('column_transaction_date', $row)
                                    <div>
                                        <p class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                            {{ $row->transaction_date->format('d M Y') }}
                                        </p>
                                        <p class="text-xs text-dark-600 dark:text-dark-400">
                                            {{ $row->created_at->format('H:i') }}
                                        </p>
                                    </div>
                                @endinteract

                                @interact('column_amount', $row)
                                    <div class="text-right">
                                        <p
                                            class="font-bold {{ $row->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $row->transaction_type === 'credit' ? '+' : '-' }}Rp
                                            {{ number_format($row->amount, 0, ',', '.') }}
                                        </p>
                                    </div>
                                @endinteract

                                @interact('column_action', $row)
                                    <div class="flex justify-center">
                                        <x-button.circle wire:click="deleteTransaction({{ $row->id }})"
                                            color="red" icon="trash" size="sm"
                                            loading="deleteTransaction({{ $row->id }})" />
                                    </div>
                                @endinteract
                            </x-table>
                        @else
                            {{-- Payments Table --}}
                            <x-table :headers="$this->paymentHeaders" :rows="$this->payments" :sort="$sort" selectable
                                wire:model="selected">
                                @interact('column_invoice', $row)
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                            <x-icon name="document-text"
                                                class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                        </div>
                                        <div>
                                            <p class="font-medium text-dark-900 dark:text-dark-50">
                                                {{ $row->invoice->invoice_number }}</p>
                                            <p class="text-sm text-dark-600 dark:text-dark-400">
                                                Due: {{ $row->invoice->due_date->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                @endinteract

                                @interact('column_client', $row)
                                    <div>
                                        <p class="font-medium text-dark-900 dark:text-dark-50">
                                            {{ $row->invoice->client->name }}</p>
                                        <p class="text-sm text-dark-600 dark:text-dark-400">
                                            {{ ucfirst($row->invoice->client->type) }}
                                        </p>
                                    </div>
                                @endinteract

                                @interact('column_payment_date', $row)
                                    <div>
                                        <p class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                            {{ $row->payment_date->format('d M Y') }}
                                        </p>
                                        <p class="text-xs text-dark-600 dark:text-dark-400">
                                            {{ $row->created_at->format('H:i') }}
                                        </p>
                                    </div>
                                @endinteract

                                @interact('column_amount', $row)
                                    <div class="text-right">
                                        <p class="font-bold text-green-600 dark:text-green-400">
                                            +Rp {{ number_format($row->amount, 0, ',', '.') }}
                                        </p>
                                        @if ($row->reference_number)
                                            <p class="text-xs text-dark-600 dark:text-dark-400 font-mono">
                                                {{ $row->reference_number }}
                                            </p>
                                        @endif
                                    </div>
                                @endinteract

                                @interact('column_payment_method', $row)
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="h-6 w-6 {{ $row->payment_method === 'bank_transfer' ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-green-100 dark:bg-green-900/30' }} rounded-md flex items-center justify-center">
                                            <x-icon
                                                name="{{ $row->payment_method === 'bank_transfer' ? 'building-library' : 'banknotes' }}"
                                                class="w-3 h-3 {{ $row->payment_method === 'bank_transfer' ? 'text-blue-600 dark:text-blue-400' : 'text-green-600 dark:text-green-400' }}" />
                                        </div>
                                        <span class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                            {{ ucfirst(str_replace('_', ' ', $row->payment_method)) }}
                                        </span>
                                    </div>
                                @endinteract

                                @interact('column_action', $row)
                                    <div class="flex justify-center">
                                        <x-button.circle wire:click="deletePayment({{ $row->id }})" color="red"
                                            icon="trash" size="sm"
                                            loading="deletePayment({{ $row->id }})" />
                                    </div>
                                @endinteract
                            </x-table>
                        @endif

                        {{-- Empty State --}}
                        @if (
                            ($activeTab === 'transactions' && $this->transactions->count() === 0) ||
                                ($activeTab === 'payments' && $this->payments->count() === 0))
                            <div class="text-center py-12">
                                <div
                                    class="h-16 w-16 bg-zinc-100 dark:bg-zinc-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <x-icon
                                        name="{{ $activeTab === 'transactions' ? 'arrows-right-left' : 'banknotes' }}"
                                        class="w-8 h-8 text-zinc-400" />
                                </div>
                                <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-2">
                                    No {{ $activeTab }} found
                                </h3>
                                <p class="text-dark-600 dark:text-dark-400 mb-6">
                                    @if ($activeTab === 'transactions')
                                        Start by adding your first transaction to track account activity.
                                    @else
                                        Invoice payments received will appear here automatically.
                                    @endif
                                </p>
                                @if ($activeTab === 'transactions')
                                    <x-button wire:click="openTransaction" color="primary" icon="plus">
                                        Add Transaction
                                    </x-button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Enhanced Bulk Actions Bar --}}
                <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
                    class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
                    <div
                        class="bg-white dark:bg-dark-800 rounded-xl shadow-lg border border-zinc-200 dark:border-dark-600 px-4 sm:px-6 py-4 sm:min-w-80">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                            {{-- Selection Info --}}
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 bg-zinc-50 dark:bg-zinc-900/20 rounded-xl flex items-center justify-center">
                                    <x-icon name="check-circle" class="w-5 h-5 text-zinc-600 dark:text-zinc-400" />
                                </div>
                                <div>
                                    <div class="font-semibold text-dark-900 dark:text-dark-50"
                                        x-text="`${show.length} ${show.length === 1 ? '{{ rtrim($activeTab, 's') }}' : '{{ $activeTab }}'} selected`">
                                    </div>
                                    <div class="text-xs text-dark-600 dark:text-dark-400">
                                        Choose action for selected items
                                    </div>
                                </div>
                            </div>
                            {{-- Actions --}}
                            <div class="flex items-center gap-2 justify-end">
                                {{-- Delete Selected --}}
                                <x-button wire:click="bulkDelete" size="sm" color="red" icon="trash"
                                    class="whitespace-nowrap" loading="bulkDelete">
                                    Delete
                                </x-button>
                                {{-- Cancel Selection --}}
                                <x-button wire:click="$set('selected', [])" size="sm" color="zinc"
                                    icon="x-mark" class="whitespace-nowrap">
                                    Cancel
                                </x-button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- No Account Selected State --}}
                <div
                    class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-12 text-center">
                    <x-icon name="building-library" class="w-16 h-16 text-zinc-400 mx-auto mb-4" />
                    <h3 class="text-xl font-semibold text-dark-900 dark:text-dark-50 mb-2">Select an Account</h3>
                    <p class="text-dark-600 dark:text-dark-400 mb-6">Choose an account from the sidebar to view
                        transactions and manage settings</p>
                    <x-button wire:click="openCreateAccount" color="primary" icon="plus"
                        loading="openCreateAccount">
                        Create New Account
                    </x-button>
                </div>
            @endif
        </div>
    </div>

    {{-- Modals --}}
    <livewire:accounts.create @account-created="refreshData" />
    <livewire:accounts.delete @account-deleted="refreshData" />
    <livewire:accounts.edit @account-updated="refreshData" />
    <livewire:transactions.create @transaction-created="refreshData" />
    <livewire:transactions.delete @transaction-deleted="refreshData" />
    <livewire:transactions.transfer @transfer-completed="refreshData" />
    <livewire:payments.delete @payment-deleted="refreshData" />
</div>

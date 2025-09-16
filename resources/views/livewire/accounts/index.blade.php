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
                <x-dropdown icon="cog-6-tooth" position="bottom-end">
                    <x-slot:trigger>
                        <x-button color="secondary" outline icon="cog-6-tooth" class="w-full sm:w-auto">
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
            @endif
        </div>
    </div>

    <div class="flex flex-col xl:flex-row gap-6">
        {{-- Left Sidebar - Account Cards --}}
        <div class="w-full xl:w-80 2xl:w-96 xl:flex-shrink-0 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-dark-900 dark:text-dark-50">My Cards</h2>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Select account to manage</p>
                </div>
                <x-button wire:click="createAccount" loading="createAccount" color="primary" icon="plus"
                    size="sm">
                    Add
                </x-button>
            </div>

            {{-- Account Cards --}}
            @foreach ($this->accountsData as $account)
                <div wire:click="selectAccount({{ $account['id'] }})"
                    class="p-4 bg-white dark:bg-dark-800 border-2 border-zinc-200 dark:border-dark-600 rounded-xl cursor-pointer transition-all hover:shadow-md {{ $selectedAccountId == $account['id'] ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : '' }}">

                    <div wire:loading wire:target="selectAccount({{ $account['id'] }})"
                        class="absolute inset-0 bg-white/50 dark:bg-dark-800/50 rounded-xl flex items-center justify-center">
                        <div class="flex items-center gap-2">
                            <x-icon name="arrow-path" class="w-4 h-4 text-primary-600 animate-spin" />
                            <span class="text-sm text-primary-600">Loading...</span>
                        </div>
                    </div>

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
                        <x-icon name="{{ $account['trend'] === 'up' ? 'arrow-trending-up' : 'arrow-trending-down' }}"
                            class="w-4 h-4 {{ $account['trend'] === 'up' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" />
                    </div>

                    <div class="mb-3">
                        <p
                            class="text-2xl font-bold {{ $account['balance'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            Rp {{ number_format($account['balance'], 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-dark-600 dark:text-dark-400">•••• •••• ••••
                            {{ substr($account['account_number'], -4) }}</p>
                    </div>

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
                    <x-button wire:click="createAccount" loading="createAccount" color="primary" icon="plus"
                        size="sm">
                        Add First Account
                    </x-button>
                </div>
            @endif
        </div>

        {{-- Main Content --}}
        <div class="flex-1 space-y-6">
            @if ($selectedAccountId)
                {{-- Quick Actions & Chart Component --}}
                <livewire:accounts.quick-actions-overview :selectedAccountId="$selectedAccountId" />

                {{-- Tab Navigation & Tables --}}
                <x-tab wire:model.live="activeTab">
                    <x-tab.items tab="transactions">
                        <x-slot:left>
                            <x-icon name="arrows-right-left" class="w-4 h-4" />
                        </x-slot:left>

                        {{-- Transactions Table Content --}}
                        <div class="mt-3">
                            <livewire:accounts.tables.transactions-table :selectedAccountId="$selectedAccountId" :key="'transactions-' . $selectedAccountId" />
                        </div>
                    </x-tab.items>

                    <x-tab.items tab="payments">
                        <x-slot:left>
                            <x-icon name="banknotes" class="w-4 h-4" />
                        </x-slot:left>

                        {{-- Payments Table Content --}}
                        <div class="mt-3">
                            <livewire:accounts.tables.payments-table :selectedAccountId="$selectedAccountId" :key="'payments-' . $selectedAccountId" />
                        </div>
                    </x-tab.items>
                </x-tab>
            @else
                {{-- No Account Selected --}}
                <div
                    class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-12 text-center">
                    <x-icon name="building-library" class="w-16 h-16 text-zinc-400 mx-auto mb-4" />
                    <h3 class="text-xl font-semibold text-dark-900 dark:text-dark-50 mb-2">Select an Account</h3>
                    <p class="text-dark-600 dark:text-dark-400 mb-6">Choose an account from the sidebar to view
                        transactions and manage settings</p>
                    <x-button wire:click="createAccount" loading="createAccount" color="primary" icon="plus">
                        Create New Account
                    </x-button>
                </div>
            @endif
        </div>
    </div>

    {{-- Child Components --}}
    <livewire:accounts.create @account-created="refreshData" />
    <livewire:accounts.delete @account-deleted="refreshData" />
    <livewire:accounts.edit @account-updated="refreshData" />
    <livewire:transactions.create @transaction-created="refreshData" />
    <livewire:transactions.delete @transaction-deleted="refreshData" />
    <livewire:transactions.transfer @transfer-completed="refreshData" />
    <livewire:payments.delete @payment-deleted="refreshData" />
</div>

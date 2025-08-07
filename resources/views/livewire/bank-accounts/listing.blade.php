{{-- resources/views/livewire/bank-accounts/listing.blade.php --}}
<div class="space-y-6">
    {{-- Page Title --}}
    {{-- Filters --}}
    <x-card class="p-4 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-input placeholder="Search accounts..." icon="magnifying-glass" wire:model.live.debounce.500ms="search" />

            <x-select.styled placeholder="All Banks" :options="$bankOptions" wire:model.live="selectedBank" />

            <x-select.styled placeholder="Account Status" :options="$statusOptions" wire:model.live="selectedStatus" />

            <x-button color="primary dark:primary" outline icon="funnel">
                Filter
            </x-button>
        </div>
    </x-card>

    {{-- Bank Accounts Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse($bankAccounts as $account)
            <x-card
                class="p-6 border border-secondary-200 dark:border-dark-700 dark:bg-dark-800 hover:shadow-lg hover:border-primary-300 dark:hover:border-primary-600 transition-all duration-200 rounded-lg">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-12 h-12 bg-primary-600 dark:bg-primary-700 rounded-lg flex items-center justify-center">
                            <span
                                class="text-white font-bold text-lg">{{ strtoupper(substr($account->bank_name, 0, 3)) }}</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-100">
                                {{ $account->account_name }}</h3>
                            <p class="text-sm text-secondary-500 dark:text-dark-400">{{ $account->bank_name }} •
                                ****{{ substr($account->account_number, -4) }}</p>
                        </div>
                    </div>
                    <x-badge text="Active" color="green dark:green" />
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-secondary-600 dark:text-dark-400">Current Balance</span>
                        <span class="text-xl font-bold text-green-600 dark:text-green-400">Rp
                            {{ number_format($account->current_balance, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-secondary-600 dark:text-dark-400">Last Transaction</span>
                        <span
                            class="text-sm text-secondary-900 dark:text-dark-200">{{ $account->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-secondary-600 dark:text-dark-400">This Month</span>
                        <span class="text-sm text-green-600 dark:text-green-400">+Rp
                            {{ number_format(rand(1000000, 10000000), 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex space-x-2 mt-4 pt-4 border-t border-secondary-200 dark:border-dark-700">
                    <x-button size="sm" color="primary dark:primary" outline icon="eye"
                        wire:click="viewAccount({{ $account->id }})">
                        View Details
                    </x-button>
                    <x-button size="sm" color="secondary dark:dark" outline icon="plus"
                        wire:click="addTransaction({{ $account->id }})">
                        Add Transaction
                    </x-button>
                    <x-button.circle size="sm" color="secondary dark:dark" outline icon="pencil"
                        wire:click="editAccount({{ $account->id }})" />
                </div>
            </x-card>
        @empty
            {{-- Empty State --}}
            <div class="lg:col-span-2">
                <x-card
                    class="p-12 border-2 border-dashed border-secondary-300 dark:border-dark-600 dark:bg-dark-800 rounded-lg">
                    <div class="text-center">
                        <div
                            class="w-16 h-16 bg-secondary-100 dark:bg-dark-700 border border-secondary-200 dark:border-dark-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <x-icon name="building-library" class="w-8 h-8 text-secondary-500 dark:text-dark-400" />
                        </div>
                        <h3 class="text-lg font-medium text-secondary-900 dark:text-dark-100 mb-2">No Bank Accounts
                            Found</h3>
                        @if ($search || $selectedBank || $selectedStatus)
                            <p class="text-secondary-500 dark:text-dark-400 text-sm mb-6">Try adjusting your search
                                filters or create a new bank account.</p>
                            <div class="flex justify-center space-x-4">
                                <x-button color="secondary dark:dark" outline wire:click="$set('search', '')"
                                    wire:click="$set('selectedBank', '')" wire:click="$set('selectedStatus', 'active')">
                                    Clear Filters
                                </x-button>
                                <x-button color="primary dark:primary" icon="plus" wire:click="createAccount">
                                    Add Bank Account
                                </x-button>
                            </div>
                        @else
                            <p class="text-secondary-500 dark:text-dark-400 text-sm mb-6">Get started by adding your
                                first bank account to track your finances.</p>
                            <x-button color="primary dark:primary" icon="plus" wire:click="createAccount">
                                Add Bank Account
                            </x-button>
                        @endif
                    </div>
                </x-card>
            </div>
        @endforelse

        {{-- Add New Account Card --}}
        @if ($bankAccounts->isNotEmpty())
            <x-card
                class="px-6 border-2 border-dashed border-secondary-300 dark:border-dark-600 dark:bg-dark-800 hover:border-primary-400 dark:hover:border-primary-600 hover:bg-primary-50/30 dark:hover:bg-primary-900/20 transition-all duration-200 rounded-lg">
                <div class="text-center py-2">
                    <div
                        class="w-16 h-16 bg-secondary-100 dark:bg-dark-700 border border-secondary-200 dark:border-dark-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <x-icon name="plus" class="w-8 h-8 text-secondary-500 dark:text-dark-400" />
                    </div>
                    <h3 class="text-lg font-medium text-secondary-900 dark:text-dark-100 mb-2">Add New Bank Account</h3>
                    <p class="text-secondary-500 dark:text-dark-400 text-sm mb-6">Connect another bank account to track
                        your finances</p>
                    <x-button color="primary dark:primary" icon="plus" wire:click="createAccount">
                        Add Bank Account
                    </x-button>
                </div>
            </x-card>
        @endif
    </div>
</div>

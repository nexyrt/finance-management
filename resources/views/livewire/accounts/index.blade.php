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
                {{-- Quick Actions & Chart Section --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Quick Actions --}}
                    <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <x-button wire:click="addTransaction" loading="addTransaction" color="primary"
                                icon="plus" class="w-full justify-start">
                                <div class="text-left">
                                    <div class="font-semibold">Add Transaction</div>
                                    <div class="text-xs opacity-70">Record income or expense</div>
                                </div>
                            </x-button>
                            <x-button wire:click="transferFunds" loading="transferFunds" color="blue" outline
                                icon="arrow-path" class="w-full justify-start">
                                <div class="text-left">
                                    <div class="font-semibold">Transfer</div>
                                    <div class="text-xs opacity-70">Move between accounts</div>
                                </div>
                            </x-button>
                            <x-button wire:click="exportReport" loading="exportReport" color="green" outline
                                icon="document-arrow-down" class="w-full justify-start">
                                <div class="text-left">
                                    <div class="font-semibold">Export Report</div>
                                    <div class="text-xs opacity-70">Download history</div>
                                </div>
                            </x-button>
                        </div>
                    </div>

                    {{-- Financial Overview Chart --}}
                    <div class="lg:col-span-2">
                        <div
                            class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6 h-[400px]">
                            <h2 class="text-xl font-bold text-dark-900 dark:text-dark-50 mb-4">Financial Overview</h2>
                            <div class="h-80">
                                <canvas id="cashflowChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab Navigation & Tables --}}
                <div
                    class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl overflow-hidden">
                    <div class="p-6 border-b border-zinc-200 dark:border-dark-600">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50">Account Activity</h3>

                            {{-- Tab Buttons --}}
                            <div class="flex items-center gap-2 bg-zinc-100 dark:bg-dark-700 p-1 rounded-lg">
                                <button wire:click="switchTab('transactions')" wire:loading.attr="disabled"
                                    wire:target="switchTab"
                                    class="px-4 py-2 text-sm font-medium rounded-md transition-all {{ $activeTab === 'transactions' ? 'bg-white dark:bg-dark-600 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-dark-600 dark:text-dark-400 hover:text-dark-900 dark:hover:text-dark-200' }}">
                                    <div class="flex items-center gap-2">
                                        <x-icon name="arrows-right-left" class="w-4 h-4" />
                                        Transactions
                                    </div>
                                </button>
                                <button wire:click="switchTab('payments')" wire:loading.attr="disabled"
                                    wire:target="switchTab"
                                    class="px-4 py-2 text-sm font-medium rounded-md transition-all {{ $activeTab === 'payments' ? 'bg-white dark:bg-dark-600 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-dark-600 dark:text-dark-400 hover:text-dark-900 dark:hover:text-dark-200' }}">
                                    <div class="flex items-center gap-2">
                                        <x-icon name="banknotes" class="w-4 h-4" />
                                        Payments
                                    </div>
                                </button>
                            </div>
                        </div>

                        {{-- Filters --}}
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex gap-3">
                                @if ($activeTab === 'transactions')
                                    <x-select.styled wire:model.live="transactionType" :options="[
                                        ['label' => 'All Types', 'value' => ''],
                                        ['label' => 'Income', 'value' => 'credit'],
                                        ['label' => 'Expense', 'value' => 'debit'],
                                    ]"
                                        placeholder="Filter by type..." class="w-48" />
                                @endif
                                <x-date wire:model.live="dateRange" range placeholder="Select date range..."
                                    class="w-64" />
                                @if ($transactionType || !empty($dateRange) || $search)
                                    <x-button wire:click="clearFilters" loading="clearFilters" icon="x-mark"
                                        color="secondary" outline>
                                        Clear
                                    </x-button>
                                @endif
                            </div>
                            <x-input wire:model.live.debounce.300ms="search"
                                placeholder="{{ $activeTab === 'transactions' ? 'Search transactions...' : 'Search payments...' }}"
                                icon="magnifying-glass" class="flex-1" />
                        </div>
                    </div>

                    {{-- Table Components --}}
                    <div class="p-6">
                        @if ($activeTab === 'transactions')
                            <livewire:accounts.tables.transactions-table :selectedAccountId="$selectedAccountId" :search="$search"
                                :transactionType="$transactionType" :dateRange="$dateRange" :key="'transactions-' . $selectedAccountId" />
                        @else
                            <livewire:accounts.tables.payments-table :selectedAccountId="$selectedAccountId" :search="$search"
                                :dateRange="$dateRange" :key="'payments-' . $selectedAccountId" />
                        @endif
                    </div>
                </div>
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

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let chart;

                function isDarkMode() {
                    return document.documentElement.classList.contains('dark');
                }

                function createChart(chartData) {
                    const ctx = document.getElementById('cashflowChart');
                    if (!ctx || !chartData || chartData.length === 0) return;

                    if (chart) {
                        chart.destroy();
                    }

                    const isDark = isDarkMode();

                    chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: chartData.map(item => item.month),
                            datasets: [{
                                label: 'Income',
                                data: chartData.map(item => item.income),
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                borderWidth: 3,
                                fill: false,
                                tension: 0.4,
                            }, {
                                label: 'Expense',
                                data: chartData.map(item => item.expense),
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                borderWidth: 3,
                                fill: false,
                                tension: 0.4,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                tooltip: {
                                    backgroundColor: isDark ? '#374151' : '#ffffff',
                                    titleColor: isDark ? '#f3f4f6' : '#111827',
                                    bodyColor: isDark ? '#d1d5db' : '#374151',
                                    borderColor: isDark ? '#6b7280' : '#e5e7eb',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': Rp ' +
                                                new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                        }
                                    }
                                },
                                legend: {
                                    labels: {
                                        color: isDark ? '#9ca3af' : '#6b7280'
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    ticks: {
                                        color: isDark ? '#9ca3af' : '#6b7280',
                                        callback: function(value) {
                                            return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                        }
                                    },
                                    grid: {
                                        color: isDark ? '#374151' : '#f3f4f6'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: isDark ? '#9ca3af' : '#6b7280'
                                    },
                                    grid: {
                                        color: isDark ? '#374151' : '#f3f4f6'
                                    }
                                }
                            }
                        }
                    });
                }

                // Initial render
                const initialData = @json($this->chartData);
                createChart(initialData);

                // Listen for Livewire updates
                document.addEventListener('chartDataUpdated', event => {
                    const chartData = event.detail[0].chartData;
                    createChart(chartData);
                });

                // Handle theme changes
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.attributeName === 'class' && chart) {
                            const currentData = chart.data.datasets[0].data;
                            const currentLabels = chart.data.labels;

                            setTimeout(() => {
                                createChart(currentLabels.map((label, index) => ({
                                    month: label,
                                    income: chart.data.datasets[0].data[index],
                                    expense: chart.data.datasets[1].data[index]
                                })));
                            }, 100);
                        }
                    });
                });

                observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class']
                });

                // Cleanup
                window.addEventListener('beforeunload', () => {
                    if (chart) chart.destroy();
                });
            });
        </script>
    @endpush
</div>

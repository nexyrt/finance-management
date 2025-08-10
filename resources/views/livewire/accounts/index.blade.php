{{-- resources/views/livewire/accounts/index.blade.php --}}

<div>
    {{-- Header Section --}}
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-1">
                <h1
                    class="text-2xl sm:text-3xl lg:text-4xl font-bold bg-gradient-to-r from-dark-900 via-primary-600 to-primary-700 dark:from-white dark:via-primary-300 dark:to-primary-200 bg-clip-text text-transparent">
                    Manajemen Rekening Bank
                </h1>
                <p class="text-dark-600 dark:text-dark-400 text-base sm:text-lg">
                    Kelola rekening bank, transaksi, dan monitor cashflow
                </p>
            </div>
            @if ($selectedAccountId)
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:pr-5">
                    <x-dropdown icon="cog-6-tooth" position="bottom-end">
                        <x-slot:trigger>
                            <x-button color="secondary" outline icon="cog-6-tooth" class="w-full sm:w-auto">
                                Account Settings
                            </x-button>
                        </x-slot:trigger>
                        <x-dropdown.items text="Edit Account" icon="pencil"
                            wire:click="$dispatch('edit-account', { accountId: {{ $selectedAccountId }} })" />
                        <x-dropdown.items text="Delete Account" icon="trash"
                            wire:click="$dispatch('delete-account', { accountId: {{ $selectedAccountId }} })"
                            class="text-red-600 dark:text-red-400" />
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
                <x-button wire:click="$dispatch('open-create-account-modal')" color="primary" icon="plus"
                    size="sm">
                    Add
                </x-button>
            </div>

            {{-- Account Cards --}}
            @foreach ($accountsData as $account)
                <div wire:click="selectAccount({{ $account['id'] }})"
                    class="p-4 bg-white dark:bg-dark-800 border-2 border-zinc-200 dark:border-dark-600 rounded-xl cursor-pointer transition-all hover:shadow-md {{ $selectedAccountId == $account['id'] ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : '' }}">

                    {{-- Card Header --}}
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-12 w-12 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center">
                                <x-icon name="building-library" class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-dark-900 dark:text-dark-50">{{ $account['name'] }}</h3>
                                <p class="text-sm text-dark-500 dark:text-dark-400">{{ $account['bank'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            @if ($account['trend'] === 'up')
                                <x-icon name="arrow-trending-up" class="w-4 h-4 text-income" />
                            @else
                                <x-icon name="arrow-trending-down" class="w-4 h-4 text-expense" />
                            @endif
                        </div>
                    </div>

                    {{-- Balance --}}
                    <div class="mb-3">
                        <p class="text-2xl font-bold {{ $account['balance'] >= 0 ? 'text-income' : 'text-expense' }}">
                            Rp {{ number_format($account['balance'], 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-dark-500 dark:text-dark-400">•••• •••• ••••
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
                                        class="{{ $transaction->transaction_type === 'credit' ? 'text-income' : 'text-expense' }} font-medium">
                                        {{ $transaction->transaction_type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount / 1000, 0) }}k
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            @if ($accountsData->count() === 0)
                <div class="text-center py-8">
                    <x-icon name="building-library" class="w-12 h-12 text-zinc-400 mx-auto mb-3" />
                    <p class="text-dark-500 dark:text-dark-400 mb-4">No accounts yet</p>
                    <x-button wire:click="$dispatch('open-create-account-modal')" color="primary" icon="plus"
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
                    <div
                        class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6 h-full">
                        <div class="flex flex-col h-full justify-between space-y-4">
                            <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-4">Quick Actions</h3>
                            {{-- Add Transaction --}}
                            <button
                                wire:click="$dispatch('open-transaction-modal', { accountId: {{ $selectedAccountId }} })"
                                class="w-full flex items-center gap-4 p-4 bg-primary-50 dark:bg-primary-900/20 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-xl transition-all group cursor-pointer">
                                <div class="h-12 w-12 bg-primary-500 rounded-xl flex items-center justify-center">
                                    <x-icon name="plus" class="w-6 h-6 text-white" />
                                </div>
                                <div class="text-left">
                                    <h4 class="font-semibold text-dark-900 dark:text-dark-50">Add Transaction</h4>
                                    <p class="text-sm text-dark-600 dark:text-dark-400">Record income or expense</p>
                                </div>
                            </button>

                            {{-- Transfer --}}
                            <button
                                class="w-full flex items-center gap-4 p-4 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-xl transition-all group cursor-pointer">
                                <div class="h-12 w-12 bg-blue-500 rounded-xl flex items-center justify-center">
                                    <x-icon name="arrow-path" class="w-6 h-6 text-white" />
                                </div>
                                <div class="text-left">
                                    <h4 class="font-semibold text-dark-900 dark:text-dark-50">Transfer</h4>
                                    <p class="text-sm text-dark-600 dark:text-dark-400">Move funds between accounts</p>
                                </div>
                            </button>

                            {{-- Export Report --}}
                            <button
                                class="w-full flex items-center gap-4 p-4 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-xl transition-all group cursor-pointer">
                                <div class="h-12 w-12 bg-green-500 rounded-xl flex items-center justify-center">
                                    <x-icon name="document-arrow-down" class="w-6 h-6 text-white" />
                                </div>
                                <div class="text-left">
                                    <h4 class="font-semibold text-dark-900 dark:text-dark-50">Export Report</h4>
                                    <p class="text-sm text-dark-600 dark:text-dark-400">Download transaction history</p>
                                </div>
                            </button>
                        </div>
                    </div>

                    {{-- Chart Section --}}
                    <div class="lg:col-span-2">

                        {{-- Chart Container --}}
                        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6 h-[400px]"
                            wire:ignore x-data="{
                                chartInstance: null,
                                currentChartType: 'cashflow',
                                cashflowData: @js($cashflowData),
                                isChartReady: false,
                                canvasId: 'financialChart_' + Math.random().toString(36).substr(2, 9),
                            
                                init() {
                                    // Wait for Chart.js to be available
                                    this.waitForChart().then(() => {
                                        this.isChartReady = true;
                                        this.$nextTick(() => {
                                            setTimeout(() => {
                                                this.initCashflowChart();
                                            }, 200);
                                        });
                                    });
                            
                                    // Listen for chart type changes
                                    this.$el.addEventListener('switch-chart', (e) => {
                                        if (!this.isChartReady) return;
                                        this.currentChartType = e.detail.type;
                                        this.switchChart(e.detail.type);
                                    });
                            
                                    // Listen for account selection changes
                                    window.addEventListener('account-selected', (e) => {
                                        if (!this.isChartReady) return;
                                        console.log('Account selected, refreshing chart data...');
                                        this.$wire.call('getCashflowData').then(data => {
                                            this.cashflowData = data;
                                            this.updateChart();
                                        });
                                    });
                            
                                    // Listen for chart data updates
                                    window.addEventListener('chart-data-updated', () => {
                                        if (!this.isChartReady) return;
                                        console.log('Chart data updated, refreshing...');
                                        this.$wire.call('getCashflowData').then(data => {
                                            this.cashflowData = data;
                                            this.updateChart();
                                        });
                                    });
                            
                                    // Listen for chart refresh requests
                                    window.addEventListener('chart-refresh-requested', () => {
                                        if (!this.isChartReady) return;
                                        this.updateChart();
                                    });
                            
                                    // Cleanup on component destroy
                                    this.$el.addEventListener('destroy', () => {
                                        this.destroy();
                                    });
                                },
                            
                                async waitForChart() {
                                    let attempts = 0;
                                    const maxAttempts = 50;
                            
                                    while (typeof Chart === 'undefined' && attempts < maxAttempts) {
                                        await new Promise(resolve => setTimeout(resolve, 100));
                                        attempts++;
                                    }
                            
                                    if (typeof Chart === 'undefined') {
                                        throw new Error('Chart.js failed to load after 5 seconds');
                                    }
                            
                                    console.log('Chart.js is ready');
                                    return true;
                                },
                            
                                destroy() {
                                    if (this.chartInstance) {
                                        try {
                                            this.chartInstance.destroy();
                                            console.log('Chart destroyed successfully');
                                        } catch (error) {
                                            console.warn('Error destroying chart:', error);
                                        }
                                        this.chartInstance = null;
                                    }
                                },
                            
                                switchChart(type) {
                                    if (!this.isChartReady) return;
                            
                                    // Always destroy first
                                    this.destroy();
                            
                                    // Wait a bit before creating new chart
                                    setTimeout(() => {
                                        switch (type) {
                                            case 'cashflow':
                                                this.initCashflowChart();
                                                break;
                                            case 'balance':
                                                this.initBalanceChart();
                                                break;
                                            case 'distribution':
                                                this.initDistributionChart();
                                                break;
                                        }
                                    }, 100);
                                },
                            
                                getCanvas() {
                                    const canvas = document.getElementById(this.canvasId);
                                    if (!canvas) {
                                        console.error('Canvas element not found:', this.canvasId);
                                        return null;
                                    }
                                    return canvas;
                                },
                            
                                initCashflowChart() {
                                    if (typeof Chart === 'undefined') {
                                        console.error('Chart.js not available');
                                        return;
                                    }
                            
                                    const canvas = this.getCanvas();
                                    if (!canvas) return;
                            
                                    const ctx = canvas.getContext('2d');
                                    if (!ctx) {
                                        console.error('Canvas context not available');
                                        return;
                                    }
                            
                                    const isDark = document.documentElement.classList.contains('dark');
                                    const textColor = isDark ? '#a1a1aa' : '#6b7280';
                                    const gridColor = isDark ? '#374151' : '#e5e7eb';
                            
                                    try {
                                        this.chartInstance = new Chart(ctx, {
                                            type: 'bar',
                                            data: {
                                                labels: this.cashflowData.map(item => item.month),
                                                datasets: [{
                                                    label: 'Income',
                                                    data: this.cashflowData.map(item => item.income),
                                                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                                                    borderColor: 'rgba(16, 185, 129, 1)',
                                                    borderWidth: 2,
                                                    borderRadius: 6,
                                                    borderSkipped: false,
                                                }, {
                                                    label: 'Expense',
                                                    data: this.cashflowData.map(item => item.expense),
                                                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                                                    borderColor: 'rgba(239, 68, 68, 1)',
                                                    borderWidth: 2,
                                                    borderRadius: 6,
                                                    borderSkipped: false,
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false,
                                                plugins: {
                                                    legend: { display: false },
                                                    tooltip: {
                                                        backgroundColor: isDark ? '#1f2937' : '#ffffff',
                                                        titleColor: isDark ? '#f9fafb' : '#111827',
                                                        bodyColor: isDark ? '#d1d5db' : '#374151',
                                                        borderColor: isDark ? '#374151' : '#e5e7eb',
                                                        borderWidth: 1,
                                                        cornerRadius: 8,
                                                        callbacks: {
                                                            label: function(context) {
                                                                const value = context.parsed.y;
                                                                return context.dataset.label + ': Rp ' + value.toLocaleString('id-ID');
                                                            }
                                                        }
                                                    }
                                                },
                                                scales: {
                                                    x: {
                                                        grid: { color: gridColor },
                                                        ticks: { color: textColor }
                                                    },
                                                    y: {
                                                        grid: { color: gridColor },
                                                        ticks: {
                                                            color: textColor,
                                                            callback: function(value) {
                                                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                                                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                                                return 'Rp ' + value.toLocaleString('id-ID');
                                                            }
                                                        }
                                                    }
                                                },
                                                animation: { duration: 800, easing: 'easeOutQuart' }
                                            }
                                        });
                            
                                        console.log('Cashflow chart initialized successfully');
                                    } catch (error) {
                                        console.error('Error initializing cashflow chart:', error);
                                    }
                                },
                            
                                initBalanceChart() {
                                    if (typeof Chart === 'undefined') return;
                            
                                    const canvas = this.getCanvas();
                                    if (!canvas) return;
                            
                                    const ctx = canvas.getContext('2d');
                                    const isDark = document.documentElement.classList.contains('dark');
                                    const textColor = isDark ? '#a1a1aa' : '#6b7280';
                                    const gridColor = isDark ? '#374151' : '#e5e7eb';
                            
                                    // Calculate cumulative balance
                                    let cumulativeBalance = 0;
                                    const balanceData = this.cashflowData.map(item => {
                                        cumulativeBalance += (item.income - item.expense);
                                        return cumulativeBalance;
                                    });
                            
                                    try {
                                        this.chartInstance = new Chart(ctx, {
                                            type: 'line',
                                            data: {
                                                labels: this.cashflowData.map(item => item.month),
                                                datasets: [{
                                                    label: 'Account Balance',
                                                    data: balanceData,
                                                    borderColor: 'rgba(59, 130, 246, 1)',
                                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                                    borderWidth: 3,
                                                    fill: true,
                                                    tension: 0.4,
                                                    pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                                                    pointBorderColor: '#ffffff',
                                                    pointBorderWidth: 2,
                                                    pointRadius: 6,
                                                    pointHoverRadius: 8,
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false,
                                                plugins: {
                                                    legend: { display: false },
                                                    tooltip: {
                                                        backgroundColor: isDark ? '#1f2937' : '#ffffff',
                                                        titleColor: isDark ? '#f9fafb' : '#111827',
                                                        bodyColor: isDark ? '#d1d5db' : '#374151',
                                                        borderColor: isDark ? '#374151' : '#e5e7eb',
                                                        borderWidth: 1,
                                                        cornerRadius: 8,
                                                        callbacks: {
                                                            label: function(context) {
                                                                return 'Balance: Rp ' + context.parsed.y.toLocaleString('id-ID');
                                                            }
                                                        }
                                                    }
                                                },
                                                scales: {
                                                    x: {
                                                        grid: { color: gridColor },
                                                        ticks: { color: textColor }
                                                    },
                                                    y: {
                                                        grid: { color: gridColor },
                                                        ticks: {
                                                            color: textColor,
                                                            callback: function(value) {
                                                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                                                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                                                return 'Rp ' + value.toLocaleString('id-ID');
                                                            }
                                                        }
                                                    }
                                                },
                                                animation: { duration: 800, easing: 'easeOutQuart' }
                                            }
                                        });
                            
                                        console.log('Balance chart initialized successfully');
                                    } catch (error) {
                                        console.error('Error initializing balance chart:', error);
                                    }
                                },
                            
                                initDistributionChart() {
                                    if (typeof Chart === 'undefined') return;
                            
                                    const canvas = this.getCanvas();
                                    if (!canvas) return;
                            
                                    const ctx = canvas.getContext('2d');
                                    const isDark = document.documentElement.classList.contains('dark');
                            
                                    const totalIncome = this.cashflowData.reduce((sum, item) => sum + item.income, 0);
                                    const totalExpense = this.cashflowData.reduce((sum, item) => sum + item.expense, 0);
                                    const netProfit = totalIncome - totalExpense;
                            
                                    try {
                                        this.chartInstance = new Chart(ctx, {
                                            type: 'doughnut',
                                            data: {
                                                labels: ['Total Income', 'Total Expense', 'Net Profit'],
                                                datasets: [{
                                                    data: [totalIncome, totalExpense, Math.max(0, netProfit)],
                                                    backgroundColor: [
                                                        'rgba(16, 185, 129, 0.8)',
                                                        'rgba(239, 68, 68, 0.8)',
                                                        'rgba(59, 130, 246, 0.8)'
                                                    ],
                                                    borderColor: [
                                                        'rgba(16, 185, 129, 1)',
                                                        'rgba(239, 68, 68, 1)',
                                                        'rgba(59, 130, 246, 1)'
                                                    ],
                                                    borderWidth: 2,
                                                    hoverOffset: 4
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false,
                                                plugins: {
                                                    legend: {
                                                        position: 'bottom',
                                                        labels: {
                                                            padding: 20,
                                                            color: isDark ? '#a1a1aa' : '#6b7280',
                                                            font: { size: 12 },
                                                            usePointStyle: true,
                                                            pointStyle: 'circle'
                                                        }
                                                    },
                                                    tooltip: {
                                                        backgroundColor: isDark ? '#1f2937' : '#ffffff',
                                                        titleColor: isDark ? '#f9fafb' : '#111827',
                                                        bodyColor: isDark ? '#d1d5db' : '#374151',
                                                        borderColor: isDark ? '#374151' : '#e5e7eb',
                                                        borderWidth: 1,
                                                        cornerRadius: 8,
                                                        callbacks: {
                                                            label: function(context) {
                                                                const value = context.parsed;
                                                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                                const percentage = ((value / total) * 100).toFixed(1);
                                                                return context.label + ': Rp ' + value.toLocaleString('id-ID') + ' (' + percentage + '%)';
                                                            }
                                                        }
                                                    }
                                                },
                                                animation: { duration: 800, easing: 'easeOutQuart' }
                                            }
                                        });
                            
                                        console.log('Distribution chart initialized successfully');
                                    } catch (error) {
                                        console.error('Error initializing distribution chart:', error);
                                    }
                                },
                            
                                updateChart() {
                                    if (!this.isChartReady) return;
                                    this.switchChart(this.currentChartType);
                                }
                            }" x-init="init()"
                            x-on:livewire:navigating.window="destroy()">
                            {{-- Chart Header --}}
                            <h2 class="text-xl font-bold text-dark-900 dark:text-dark-50 mb-4">Financial Overview</h2>

                            {{-- Loading State --}}
                            <div x-show="!isChartReady" class="flex items-center justify-center h-80">
                                <div class="flex items-center gap-3 text-gray-500">
                                    <svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span class="text-sm">Loading chart...</span>
                                </div>
                            </div>

                            {{-- Chart Canvas --}}
                            <div x-show="isChartReady" class="relative h-80">
                                <canvas :id="canvasId" class="w-full h-full"></canvas>
                            </div>

                            {{-- Chart Legend (for cashflow) --}}
                            {{-- <div x-show="isChartReady && currentChartType === 'cashflow'"
                                class="flex items-center justify-center gap-6 mt-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    <span class="text-sm text-dark-600 dark:text-dark-400">Income</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                    <span class="text-sm text-dark-600 dark:text-dark-400">Expense</span>
                                </div>
                            </div> --}}

                            {{-- Chart Legend (for balance) --}}
                            <div x-show="isChartReady && currentChartType === 'balance'"
                                class="flex items-center justify-center gap-6 mt-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                    <span class="text-sm text-dark-600 dark:text-dark-400">Account Balance Trend</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filters and Search --}}
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex gap-3">
                        <div class="w-48">
                            <x-select.styled wire:model.live="transactionType" :options="[
                                ['label' => 'All Types', 'value' => ''],
                                ['label' => 'Income', 'value' => 'credit'],
                                ['label' => 'Expense', 'value' => 'debit'],
                            ]"
                                placeholder="Filter by type..." />
                        </div>

                        <div class="w-64">
                            <x-date wire:model.live="dateRange" range placeholder="Select date range..." />
                        </div>

                        @if ($transactionType || !empty($dateRange) || $search)
                            <div class="transition-all duration-300 ease-in-out">
                                <x-button wire:click="clearFilters" icon="x-mark" class="h-[36px]"
                                    color="secondary">
                                    Clear
                                </x-button>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1">
                        <x-input wire:model.live.debounce.300ms="search" placeholder="Search transactions..."
                            icon="magnifying-glass" />
                    </div>
                </div>

                {{-- Transactions Table --}}
                <div
                    class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl overflow-hidden">
                    <div class="p-4 border-b border-zinc-200 dark:border-dark-600">
                        <h3 class="font-semibold text-dark-900 dark:text-dark-50">Transactions</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-zinc-50 dark:bg-dark-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-dark-500 dark:text-dark-400 uppercase">
                                        Transaction Name</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-dark-500 dark:text-dark-400 uppercase">
                                        Transaction ID</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-dark-500 dark:text-dark-400 uppercase">
                                        Date & Time</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-dark-500 dark:text-dark-400 uppercase">
                                        Amount</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-dark-500 dark:text-dark-400 uppercase">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-dark-700">
                                @forelse($transactions as $transaction)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-dark-700">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="h-10 w-10 {{ $transaction->transaction_type === 'credit' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }} rounded-lg flex items-center justify-center">
                                                    <x-icon
                                                        name="{{ $transaction->transaction_type === 'credit' ? 'arrow-down' : 'arrow-up' }}"
                                                        class="w-5 h-5 {{ $transaction->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" />
                                                </div>
                                                <div>
                                                    <p class="font-medium text-dark-900 dark:text-dark-50">
                                                        {{ $transaction->description }}</p>
                                                    <p class="text-sm text-dark-500 dark:text-dark-400">
                                                        {{ $transaction->transaction_type === 'credit' ? 'Income' : 'Expense' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-mono text-sm text-dark-600 dark:text-dark-400">
                                                {{ $transaction->reference_number ?: 'TXN' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                                    {{ $transaction->transaction_date->format('Y-m-d') }}</p>
                                                <p class="text-xs text-dark-500 dark:text-dark-400">
                                                    {{ $transaction->created_at->format('H:i A') }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <p
                                                class="font-bold {{ $transaction->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $transaction->transaction_type === 'credit' ? '+' : '-' }}Rp
                                                {{ number_format($transaction->amount, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <x-badge text="Completed" color="green" />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <x-icon name="clipboard-document-list"
                                                class="w-12 h-12 text-zinc-400 mx-auto mb-4" />
                                            <h3 class="text-lg font-medium text-dark-900 dark:text-dark-50 mb-2">No
                                                transactions found</h3>
                                            <p class="text-dark-500 dark:text-dark-400 mb-4">
                                                {{ $selectedAccountId ? 'This account has no transactions yet' : 'Select an account to view transactions' }}
                                            </p>
                                            @if ($selectedAccountId)
                                                <x-button
                                                    wire:click="$dispatch('open-transaction-modal', { accountId: {{ $selectedAccountId }} })"
                                                    color="primary" icon="plus">
                                                    Add Transaction
                                                </x-button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                @if ($transactions->hasPages())
                    <div class="flex justify-center">
                        {{ $transactions->links() }}
                    </div>
                @endif
            @else
                {{-- No Account Selected State --}}
                <div
                    class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-12 text-center">
                    <x-icon name="building-library" class="w-16 h-16 text-zinc-400 mx-auto mb-4" />
                    <h3 class="text-xl font-semibold text-dark-900 dark:text-dark-50 mb-2">Select an Account</h3>
                    <p class="text-dark-600 dark:text-dark-400 mb-6">Choose an account from the sidebar to view
                        transactions and manage settings</p>
                    <x-button wire:click="$dispatch('open-create-account-modal')" color="primary" icon="plus">
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
</div>

{{-- Chart.js Scripts - Load before Alpine.js --}}
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Verify Chart.js is loaded
            if (typeof Chart === 'undefined') {
                console.error('Chart.js failed to load');
                return;
            }

            console.log('Chart.js loaded successfully:', Chart.version);

            // Make Chart available globally
            window.Chart = Chart;

            // Global event dispatchers for better communication
            window.dispatchAccountSelected = function(accountId) {
                window.dispatchEvent(new CustomEvent('account-selected', {
                    detail: {
                        accountId: accountId
                    }
                }));
            };

            window.dispatchChartDataUpdated = function() {
                window.dispatchEvent(new CustomEvent('chart-data-updated'));
            };

            window.dispatchChartRefreshRequested = function() {
                window.dispatchEvent(new CustomEvent('chart-refresh-requested'));
            };

            // Listen for Livewire events and convert to window events
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('account-selected', (event) => {
                    window.dispatchAccountSelected(event.accountId);
                });

                Livewire.on('chart-data-updated', () => {
                    window.dispatchChartDataUpdated();
                });

                Livewire.on('chart-refresh-requested', () => {
                    window.dispatchChartRefreshRequested();
                });
            });
        });
    </script>
@endpush

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
                    <p
                        class="text-2xl font-bold {{ $this->stats['net_cash_flow'] >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' }}">
                        Rp {{ number_format($this->stats['net_cash_flow'], 0, ',', '.') }}
                    </p>
                    <p
                        class="text-xs {{ $this->stats['net_cash_flow'] >= 0 ? 'text-blue-500 dark:text-blue-400' : 'text-red-500 dark:text-red-400' }}">
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

    {{-- Period Filter --}}
    <div class="flex justify-center">
        <div class="inline-flex gap-2 p-1 bg-zinc-100 dark:bg-dark-700 rounded-lg">
            <button wire:click="$set('period', 'this_month')"
                class="px-4 py-2 text-sm font-medium rounded-md transition-colors
                {{ $period === 'this_month'
                    ? 'bg-blue-600 text-white shadow-sm'
                    : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-dark-600' }}">
                This Month
            </button>
            <button wire:click="$set('period', 'last_3_months')"
                class="px-4 py-2 text-sm font-medium rounded-md transition-colors
                {{ $period === 'last_3_months'
                    ? 'bg-blue-600 text-white shadow-sm'
                    : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-dark-600' }}">
                Last 3 Months
            </button>
            <button wire:click="$set('period', 'last_year')"
                class="px-4 py-2 text-sm font-medium rounded-md transition-colors
                {{ $period === 'last_year'
                    ? 'bg-blue-600 text-white shadow-sm'
                    : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-dark-600' }}">
                Last Year
            </button>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Income vs Expense Trend Chart (2/3 width) --}}
        <div class="lg:col-span-2 bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Income vs Expense Trend</h3>
            <div class="h-80">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- Category Breakdown Pie Chart (1/3 width) --}}
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Expenses by Category</h3>
            <div class="h-80 flex items-center justify-center">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Transactions</h3>
                <p class="text-sm text-dark-600 dark:text-dark-400 mt-1">Latest 10 transactions across all accounts</p>
            </div>
            @if (!$this->recentTransactions->isEmpty())
                <a href="{{ route('transactions.index') }}" wire:navigate
                    class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 bg-transparent border border-blue-600 dark:border-blue-400 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                    View All
                </a>
            @endif
        </div>

        @if ($this->recentTransactions->isEmpty())
            <div
                class="text-center py-16 bg-zinc-50 dark:bg-zinc-900/50 rounded-lg border-2 border-dashed border-zinc-200 dark:border-zinc-700">
                <div
                    class="w-20 h-20 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="document-text" class="w-10 h-10 text-zinc-400 dark:text-zinc-500" />
                </div>
                <p class="text-lg font-medium text-zinc-900 dark:text-white mb-1">No transactions yet</p>
                <p class="text-sm text-zinc-500 dark:text-zinc-300 mb-4">Start by creating your first transaction</p>
                <a href="{{ route('transactions.index') }}" wire:navigate
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Create Transaction</span>
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($this->recentTransactions as $transaction)
                    @php
                        $isIncome = $transaction->transaction_type === 'credit';
                        $bgClass = $isIncome ? 'bg-green-50 dark:bg-green-950/50' : 'bg-red-50 dark:bg-red-950/50';
                        $iconColorClass = $isIncome
                            ? 'text-green-600 dark:text-green-500'
                            : 'text-red-600 dark:text-red-500';
                        $amountColorClass = $isIncome
                            ? 'text-green-700 dark:text-green-400'
                            : 'text-red-700 dark:text-red-400';
                    @endphp

                    <div
                        class="flex items-center gap-4 p-4 rounded-lg border border-zinc-200 dark:border-dark-600 hover:bg-zinc-50 dark:hover:bg-dark-700 transition-all group">
                        {{-- Icon Based on Type --}}
                        <div
                            class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center {{ $bgClass }}">
                            <x-icon :name="$isIncome ? 'arrow-down-circle' : 'arrow-up-circle'" class="w-6 h-6 {{ $iconColorClass }}" />
                        </div>

                        {{-- Transaction Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">
                                    {{ $transaction->description ?: 'No description' }}
                                </p>
                                @if ($transaction->category)
                                    <x-badge :text="$transaction->category->label" size="sm" :color="match ($transaction->category->type) {
                                        'income' => 'green',
                                        'expense' => 'red',
                                        'transfer' => 'purple',
                                        'adjustment' => 'amber',
                                        default => 'gray',
                                    }" />
                                @endif
                            </div>
                            <div class="flex items-center gap-3 text-xs text-zinc-600 dark:text-zinc-300">
                                <span class="flex items-center gap-1">
                                    <x-icon name="calendar" class="w-3.5 h-3.5" />
                                    {{ $transaction->transaction_date->format('d M Y') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <x-icon name="building-library" class="w-3.5 h-3.5" />
                                    {{ $transaction->bankAccount->account_name }}
                                </span>
                                @if ($transaction->reference_number)
                                    <span class="flex items-center gap-1">
                                        <x-icon name="hashtag" class="w-3.5 h-3.5" />
                                        {{ $transaction->reference_number }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Amount --}}
                        <div class="flex-shrink-0 text-right">
                            <p class="text-base font-bold {{ $amountColorClass }}">
                                {{ $isIncome ? '+' : '-' }}
                                Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-zinc-600 dark:text-zinc-300 mt-1">
                                {{ $transaction->transaction_date->diffForHumans() }}
                            </p>
                        </div>

                        {{-- Hover Action --}}
                        <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button
                                class="w-8 h-8 flex items-center justify-center rounded-full text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-dark-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Show More Link --}}
            <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-dark-600">
                <a href="{{ route('transactions.index') }}" wire:navigate
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-transparent border border-zinc-300 dark:border-dark-600 rounded-lg hover:bg-zinc-50 dark:hover:bg-dark-700 transition-colors">
                    <span>View All Transactions</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let trendChart;
            let categoryChart;

            function isDarkMode() {
                return document.documentElement.classList.contains('dark');
            }

            function getChartColors() {
                const isDark = isDarkMode();
                return {
                    gridColor: isDark ? '#374151' : '#e5e7eb',
                    textColor: isDark ? '#d1d5db' : '#6b7280',
                    tooltipBg: isDark ? '#1f2937' : '#ffffff',
                    tooltipTitle: isDark ? '#ffffff' : '#111827',
                    tooltipBody: isDark ? '#e5e7eb' : '#374151',
                    tooltipBorder: isDark ? '#4b5563' : '#d1d5db',
                };
            }

            function createTrendChart(trendData) {
                const ctx = document.getElementById('trendChart');
                if (!ctx) return;

                if (trendChart) {
                    trendChart.destroy();
                }

                const colors = getChartColors();

                trendChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: trendData.map(item => item.month),
                        datasets: [{
                                label: 'Income',
                                data: trendData.map(item => item.income),
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                            },
                            {
                                label: 'Expenses',
                                data: trendData.map(item => item.expenses),
                                borderColor: 'rgb(239, 68, 68)',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    color: colors.textColor,
                                    usePointStyle: true,
                                    padding: 15,
                                }
                            },
                            tooltip: {
                                backgroundColor: colors.tooltipBg,
                                titleColor: colors.tooltipTitle,
                                bodyColor: colors.tooltipBody,
                                borderColor: colors.tooltipBorder,
                                borderWidth: 1,
                                padding: 12,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: colors.textColor,
                                    callback: function(value) {
                                        if (value >= 1000000000) {
                                            return 'Rp ' + (value / 1000000000).toFixed(1) + ' Miliar';
                                        } else if (value >= 1000000) {
                                            return 'Rp ' + (value / 1000000).toFixed(0) + ' Juta';
                                        } else if (value >= 1000) {
                                            return 'Rp ' + (value / 1000).toFixed(0) + ' Ribu';
                                        }
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                },
                                grid: {
                                    color: colors.gridColor,
                                    lineWidth: 1
                                }
                            },
                            x: {
                                ticks: {
                                    color: colors.textColor
                                },
                                grid: {
                                    color: colors.gridColor,
                                    lineWidth: 1
                                }
                            }
                        }
                    }
                });
            }

            function createCategoryChart(categoryData) {
                const ctx = document.getElementById('categoryChart');
                if (!ctx) return;

                if (categoryChart) {
                    categoryChart.destroy();
                }

                const colors = getChartColors();

                // Generate vibrant colors for categories
                const backgroundColors = [
                    'rgba(239, 68, 68, 0.8)', // red
                    'rgba(249, 115, 22, 0.8)', // orange
                    'rgba(234, 179, 8, 0.8)', // yellow
                    'rgba(34, 197, 94, 0.8)', // green
                    'rgba(59, 130, 246, 0.8)', // blue
                    'rgba(168, 85, 247, 0.8)', // purple
                    'rgba(236, 72, 153, 0.8)', // pink
                    'rgba(100, 116, 139, 0.8)', // slate
                ];

                categoryChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: categoryData.map(item => item.category),
                        datasets: [{
                            data: categoryData.map(item => item.total),
                            backgroundColor: backgroundColors,
                            borderWidth: 2,
                            borderColor: isDarkMode() ? '#1f2937' : '#ffffff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    color: colors.textColor,
                                    padding: 15,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: colors.tooltipBg,
                                titleColor: colors.tooltipTitle,
                                bodyColor: colors.tooltipBody,
                                borderColor: colors.tooltipBorder,
                                borderWidth: 1,
                                padding: 12,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return label + ': Rp ' + value.toLocaleString('id-ID') + ' (' +
                                            percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Initial render
            const initialTrendData = @json($this->trendChartData);
            const initialCategoryData = @json($this->categoryChartData);

            createTrendChart(initialTrendData);
            createCategoryChart(initialCategoryData);

            // Listen for Livewire updates
            document.addEventListener('chartDataUpdated', event => {
                const trendData = event.detail[0].trendData;
                const categoryData = event.detail[0].categoryData;

                createTrendChart(trendData);
                createCategoryChart(categoryData);
            });

            // Handle theme changes
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        setTimeout(() => {
                            if (trendChart) {
                                createTrendChart(trendChart.data.labels.map((label,
                                    index) => ({
                                    month: label,
                                    income: trendChart.data.datasets[0]
                                        .data[index],
                                    expenses: trendChart.data.datasets[1]
                                        .data[index]
                                })));
                            }
                            if (categoryChart) {
                                createCategoryChart(categoryChart.data.labels.map((label,
                                    index) => ({
                                    category: label,
                                    total: categoryChart.data.datasets[0]
                                        .data[index]
                                })));
                            }
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
                if (trendChart) trendChart.destroy();
                if (categoryChart) categoryChart.destroy();
            });
        });
    </script>
@endpush

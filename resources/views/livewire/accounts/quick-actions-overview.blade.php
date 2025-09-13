{{-- resources/views/livewire/accounts/quick-actions-overview.blade.php --}}

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Quick Actions --}}
    <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-4">Quick Actions</h3>
        <div class="space-y-3">
            <x-button wire:click="addTransaction" loading="addTransaction" color="primary" icon="plus"
                class="w-full justify-start">
                <div class="text-left">
                    <div class="font-semibold">Add Transaction</div>
                    <div class="text-xs opacity-70">Record income or expense</div>
                </div>
            </x-button>

            <x-button wire:click="transferFunds" loading="transferFunds" color="blue" outline icon="arrow-path"
                class="w-full justify-start">
                <div class="text-left">
                    <div class="font-semibold">Transfer</div>
                    <div class="text-xs opacity-70">Move between accounts</div>
                </div>
            </x-button>

            <x-button wire:click="exportReport" loading="exportReport" color="green" outline icon="document-arrow-down"
                class="w-full justify-start">
                <div class="text-left">
                    <div class="font-semibold">Export Report</div>
                    <div class="text-xs opacity-70">Download history</div>
                </div>
            </x-button>
        </div>

        {{-- Month Stats --}}
        @if ($selectedAccountId)
            <div class="mt-5 pt-4 border-t border-zinc-200 dark:border-dark-600">
                <h4 class="text-sm font-medium text-dark-700 dark:text-dark-300 mb-3">This Month</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <div class="text-lg font-bold text-green-600 dark:text-green-400">
                            +{{ number_format($this->accountStats['total_income'] / 1000, 0) }}K
                        </div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">Income</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-red-600 dark:text-red-400">
                            -{{ number_format($this->accountStats['total_expense'] / 1000, 0) }}K
                        </div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">Expense</div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Financial Overview Chart --}}
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6 h-[400px]">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-dark-900 dark:text-dark-50">Financial Overview</h2>
                @if ($selectedAccountId)
                    <div class="flex items-center gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-dark-600 dark:text-dark-400">Income</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-dark-600 dark:text-dark-400">Expense</span>
                        </div>
                    </div>
                @endif
            </div>

            @if ($selectedAccountId)
                <div class="h-80">
                    <canvas id="cashflowChart"></canvas>
                </div>
            @else
                <div class="h-80 flex items-center justify-center">
                    <div class="text-center">
                        <x-icon name="chart-bar" class="w-12 h-12 text-dark-400 mx-auto mb-3" />
                        <p class="text-dark-600 dark:text-dark-400">Select an account to view chart</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

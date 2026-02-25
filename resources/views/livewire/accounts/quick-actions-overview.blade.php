{{-- Complete Responsive QuickActions Overview --}}
<div class="grid grid-cols-1 lg:grid-cols-7 gap-4 lg:gap-6"
     x-data="cashflowChart(@js($this->chartData))"
     x-init="initChart()"
>
    {{-- Quick Actions Card - 2 cols out of 7 --}}
    <div class="lg:col-span-2">
        <div
            class="bg-white dark:bg-dark-700 border border-zinc-200 dark:border-dark-600 rounded-xl p-4 lg:p-6 h-full flex flex-col">
            <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-4">{{ __('pages.quick_actions') }}</h3>

            {{-- Actions Grid - Responsive --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 gap-3 flex-1">
                <livewire:transactions.create-expense @transaction-created="$refresh" />

                <livewire:transactions.create-income @transaction-created="$refresh" />

                <x-button wire:click="exportReport" loading="exportReport" color="green" outline
                    icon="document-arrow-down" class="w-full justify-start h-auto">
                    <div class="text-left py-1">
                        <div class="font-semibold text-sm">{{ __('pages.export_report') }}</div>
                        <div class="text-xs opacity-70">{{ __('pages.download_history') }}</div>
                    </div>
                </x-button>
            </div>

            {{-- Month Stats - Vertical Stack --}}
            @if ($selectedAccountId)
                <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-dark-600">
                    <h4 class="text-sm font-medium text-dark-700 dark:text-dark-300 mb-3">{{ __('pages.this_month') }}</h4>
                    <div class="space-y-3">
                        {{-- Income Card --}}
                        <div
                            class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-xs text-green-600 dark:text-green-400 font-medium mb-1">{{ __('pages.income') }}
                                    </div>
                                    <div class="text-lg font-bold text-green-700 dark:text-green-300">
                                        Rp {{ number_format($this->accountStats['total_income'], 0, ',', '.') }}
                                    </div>
                                </div>
                                <div
                                    class="w-8 h-8 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center">
                                    <x-icon name="arrow-trending-up"
                                        class="w-4 h-4 text-green-600 dark:text-green-400" />
                                </div>
                            </div>
                        </div>

                        {{-- Expense Card --}}
                        <div
                            class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-xs text-red-600 dark:text-red-400 font-medium mb-1">{{ __('pages.expense') }}</div>
                                    <div class="text-lg font-bold text-red-700 dark:text-red-300">
                                        Rp {{ number_format($this->accountStats['total_expense'], 0, ',', '.') }}
                                    </div>
                                </div>
                                <div
                                    class="w-8 h-8 bg-red-100 dark:bg-red-800 rounded-full flex items-center justify-center">
                                    <x-icon name="arrow-trending-down" class="w-4 h-4 text-red-600 dark:text-red-400" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Financial Overview Chart - 5 cols out of 7 --}}
    <div class="lg:col-span-5">
        <div
            class="bg-white dark:bg-dark-700 border border-zinc-200 dark:border-dark-600 rounded-xl p-4 lg:p-6 h-full min-h-[400px] flex flex-col">
            {{-- Chart Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <h2 class="text-lg lg:text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.financial_overview') }}</h2>
                @if ($selectedAccountId)
                    <div class="flex items-center gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-dark-600 dark:text-dark-400">{{ __('pages.income') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-dark-600 dark:text-dark-400">{{ __('pages.expense') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span class="text-dark-600 dark:text-dark-400">{{ __('pages.net_flow') }}</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Chart Content --}}
            <div class="flex-1 min-h-0">
                @if ($selectedAccountId)
                    <div class="h-full min-h-[300px]">
                        <canvas id="cashflowChart"></canvas>
                    </div>
                @else
                    <div class="h-full flex items-center justify-center min-h-[300px]">
                        <div class="text-center">
                            <div
                                class="w-16 h-16 bg-dark-100 dark:bg-dark-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <x-icon name="chart-bar" class="w-8 h-8 text-dark-400" />
                            </div>
                            <h3 class="font-medium text-dark-900 dark:text-dark-50 mb-2">{{ __('pages.no_account_selected') }}</h3>
                            <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.choose_account_to_view_overview') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@script
<script>
    Alpine.data('cashflowChart', (initialData) => ({
        chartInstance: null,

        initChart() {
            // Load Chart.js if not already loaded
            if (typeof Chart === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                script.onload = () => this.createChart(initialData);
                document.head.appendChild(script);
            } else {
                this.$nextTick(() => this.createChart(initialData));
            }

            // Listen for chart updates from Livewire
            Livewire.on('chartDataUpdated', (data) => {
                this.createChart(data[0].chartData);
            });

            Livewire.on('reinitialize-chart', (data) => {
                this.destroyChart();
                setTimeout(() => this.createChart(data[0].chartData), 100);
            });

            // Handle PDF download
            Livewire.on('download-pdf', (event) => {
                window.open(event.url, '_blank');
            });

            // Handle theme changes
            this._themeObserver = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'class' && this.chartInstance) {
                        const labels = this.chartInstance.data.labels;
                        const datasets = this.chartInstance.data.datasets;
                        setTimeout(() => {
                            this.createChart(labels.map((label, index) => ({
                                month: label,
                                income: datasets[0].data[index],
                                expense: datasets[1].data[index]
                            })));
                        }, 100);
                    }
                });
            });
            this._themeObserver.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });
        },

        destroyChart() {
            if (this.chartInstance) {
                this.chartInstance.destroy();
                this.chartInstance = null;
            }
        },

        createChart(chartData) {
            const ctx = this.$el.querySelector('#cashflowChart');
            if (!ctx || !chartData || chartData.length === 0) return;

            this.destroyChart();

            const isDark = document.documentElement.classList.contains('dark');
            const incomeData = chartData.map(item => item.income);
            const expenseData = chartData.map(item => item.expense);
            const netData = chartData.map(item => item.income - item.expense);

            const incomeLabel = @js(__('pages.income'));
            const expenseLabel = @js(__('pages.expense'));
            const netCashFlowLabel = @js(__('pages.net_cash_flow'));
            const netLabel = @js(__('pages.net'));
            const ratioLabel = @js(__('pages.ratio'));
            const ratioSuffix = @js(__('pages.income_expense_ratio'));

            this.chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.map(item => item.month),
                    datasets: [{
                            label: incomeLabel,
                            data: incomeData,
                            backgroundColor: 'rgba(34, 197, 94, 0.8)',
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 2,
                            borderRadius: 8,
                            yAxisID: 'y',
                        },
                        {
                            label: expenseLabel,
                            data: expenseData,
                            backgroundColor: 'rgba(239, 68, 68, 0.8)',
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 2,
                            borderRadius: 8,
                            yAxisID: 'y',
                        },
                        {
                            label: netCashFlowLabel,
                            data: netData,
                            type: 'line',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y1',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        tooltip: {
                            backgroundColor: isDark ? '#1f2937' : '#ffffff',
                            titleColor: isDark ? '#f3f4f6' : '#111827',
                            bodyColor: isDark ? '#d1d5db' : '#374151',
                            borderColor: isDark ? '#374151' : '#e5e7eb',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed.y;
                                    return context.dataset.label + ': Rp ' +
                                        new Intl.NumberFormat('id-ID').format(value);
                                },
                                afterBody: function(tooltipItems) {
                                    const dataIndex = tooltipItems[0].dataIndex;
                                    const income = incomeData[dataIndex];
                                    const expense = expenseData[dataIndex];
                                    const net = income - expense;
                                    const ratio = expense > 0 ? ((income / expense) * 100).toFixed(1) : '∞';

                                    return [
                                        '',
                                        `${netLabel}: Rp ${new Intl.NumberFormat('id-ID').format(net)}`,
                                        `${ratioLabel}: ${ratio}% ${ratioSuffix}`
                                    ];
                                }
                            }
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: isDark ? '#9ca3af' : '#6b7280',
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: isDark ? '#374151' : '#f3f4f6',
                                drawBorder: false,
                            },
                            ticks: {
                                color: isDark ? '#9ca3af' : '#6b7280',
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            grid: {
                                color: isDark ? '#374151' : '#f3f4f6',
                                drawBorder: false,
                            },
                            ticks: {
                                color: isDark ? '#9ca3af' : '#6b7280',
                                font: {
                                    size: 11
                                },
                                callback: function(value) {
                                    if (value >= 1000000000) {
                                        return 'Rp ' + (value / 1000000000).toFixed(1) + 'B';
                                    } else if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000).toFixed(0) + 'Jt';
                                    } else if (value >= 1000) {
                                        return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                    } else {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                            ticks: {
                                color: isDark ? '#60a5fa' : '#3b82f6',
                                font: {
                                    size: 11
                                },
                                callback: function(value) {
                                    const sign = value >= 0 ? '+' : '';
                                    if (Math.abs(value) >= 1000000) {
                                        return sign + (value / 1000000).toFixed(1) + 'Jt';
                                    } else if (Math.abs(value) >= 1000) {
                                        return sign + (value / 1000).toFixed(0) + 'K';
                                    } else {
                                        return sign + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        }
                    },
                    elements: {
                        bar: {
                            borderWidth: 2,
                        }
                    }
                }
            });
        },

        destroy() {
            this.destroyChart();
            if (this._themeObserver) {
                this._themeObserver.disconnect();
            }
        }
    }));
</script>
@endscript

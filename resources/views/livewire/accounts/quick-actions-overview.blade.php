{{-- Quick Actions Overview — Chart full-width (quick actions panel removed) --}}
<div x-data="cashflowChart(@js($this->chartData))"
     x-init="initChart()"
     class="bg-white dark:bg-dark-700 border border-zinc-200 dark:border-dark-600 rounded-xl p-4 lg:p-6"
>
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

    {{-- Month Stats (compact inline — 3 mini cards) --}}
    @if ($selectedAccountId)
        <div class="grid grid-cols-3 gap-3 mb-4">
            {{-- Income --}}
            <div class="flex items-center gap-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-900/30">
                <div class="h-8 w-8 bg-green-100 dark:bg-green-900/40 rounded-lg flex items-center justify-center flex-shrink-0">
                    <x-icon name="arrow-trending-up" class="w-4 h-4 text-green-600 dark:text-green-400" />
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-green-600 dark:text-green-400 font-medium truncate">{{ __('pages.income') }}</p>
                    <p class="text-sm font-bold text-green-700 dark:text-green-300 truncate">
                        Rp {{ number_format($this->accountStats['total_income'], 0, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- Expense --}}
            <div class="flex items-center gap-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-100 dark:border-red-900/30">
                <div class="h-8 w-8 bg-red-100 dark:bg-red-900/40 rounded-lg flex items-center justify-center flex-shrink-0">
                    <x-icon name="arrow-trending-down" class="w-4 h-4 text-red-600 dark:text-red-400" />
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-red-600 dark:text-red-400 font-medium truncate">{{ __('pages.expense') }}</p>
                    <p class="text-sm font-bold text-red-700 dark:text-red-300 truncate">
                        Rp {{ number_format($this->accountStats['total_expense'], 0, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- Net --}}
            @php $net = $this->accountStats['net_cashflow']; @endphp
            <div class="flex items-center gap-3 p-3 {{ $net >= 0 ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-100 dark:border-blue-900/30' : 'bg-orange-50 dark:bg-orange-900/20 border-orange-100 dark:border-orange-900/30' }} rounded-xl border">
                <div class="h-8 w-8 {{ $net >= 0 ? 'bg-blue-100 dark:bg-blue-900/40' : 'bg-orange-100 dark:bg-orange-900/40' }} rounded-lg flex items-center justify-center flex-shrink-0">
                    <x-icon name="{{ $net >= 0 ? 'plus-circle' : 'minus-circle' }}" class="w-4 h-4 {{ $net >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-orange-600 dark:text-orange-400' }}" />
                </div>
                <div class="min-w-0">
                    <p class="text-xs {{ $net >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-orange-600 dark:text-orange-400' }} font-medium truncate">{{ __('pages.net_flow') }}</p>
                    <p class="text-sm font-bold {{ $net >= 0 ? 'text-blue-700 dark:text-blue-300' : 'text-orange-700 dark:text-orange-300' }} truncate">
                        {{ $net >= 0 ? '+' : '' }}Rp {{ number_format($net, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Chart --}}
    <div class="min-h-[320px] lg:min-h-[380px]">
        @if ($selectedAccountId)
            <div class="h-[320px] lg:h-[380px]">
                <canvas id="cashflowChart"></canvas>
            </div>
        @else
            <div class="h-[320px] flex items-center justify-center">
                <div class="text-center">
                    <div class="w-16 h-16 bg-zinc-100 dark:bg-dark-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <x-icon name="chart-bar" class="w-8 h-8 text-zinc-400" />
                    </div>
                    <h3 class="font-medium text-dark-900 dark:text-dark-50 mb-2">{{ __('pages.no_account_selected') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.choose_account_to_view_overview') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>

@script
<script>
    Alpine.data('cashflowChart', (initialData) => ({
        chartInstance: null,

        initChart() {
            if (typeof Chart === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                script.onload = () => this.createChart(initialData);
                document.head.appendChild(script);
            } else {
                this.$nextTick(() => this.createChart(initialData));
            }

            Livewire.on('chartDataUpdated', (data) => {
                this.createChart(data[0].chartData);
            });

            Livewire.on('reinitialize-chart', (data) => {
                this.destroyChart();
                setTimeout(() => this.createChart(data[0].chartData), 100);
            });

            Livewire.on('download-pdf', (event) => {
                window.open(event.url, '_blank');
            });

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
                                font: { size: 12 }
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
                                font: { size: 11 }
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
                                font: { size: 11 },
                                callback: function(value) {
                                    if (value >= 1000000000) return 'Rp ' + (value / 1000000000).toFixed(1) + 'B';
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(0) + 'Jt';
                                    if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: {
                                color: isDark ? '#60a5fa' : '#3b82f6',
                                font: { size: 11 },
                                callback: function(value) {
                                    const sign = value >= 0 ? '+' : '';
                                    if (Math.abs(value) >= 1000000) return sign + (value / 1000000).toFixed(1) + 'Jt';
                                    if (Math.abs(value) >= 1000) return sign + (value / 1000).toFixed(0) + 'K';
                                    return sign + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    },
                    elements: {
                        bar: { borderWidth: 2 }
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

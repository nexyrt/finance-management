<div class="space-y-6">
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
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
                </div>
            </div>
        </div>

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
                </div>
            </div>
        </div>

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
                </div>
            </div>
        </div>

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
                </div>
            </div>
        </div>
    </div>

    {{-- Period Filter --}}
    <div class="flex justify-center">
        <div class="inline-flex gap-2 p-1 bg-zinc-100 dark:bg-dark-700 rounded-lg">
            <button wire:click="$set('period', 'this_month')"
                class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
                :class="$wire.period === 'this_month' ? 'bg-blue-600 text-white shadow-sm' :
                    'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-dark-600'">
                This Month
            </button>
            <button wire:click="$set('period', 'last_3_months')"
                class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
                :class="$wire.period === 'last_3_months' ? 'bg-blue-600 text-white shadow-sm' :
                    'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-dark-600'">
                Last 3 Months
            </button>
            <button wire:click="$set('period', 'last_year')"
                class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
                :class="$wire.period === 'last_year' ? 'bg-blue-600 text-white shadow-sm' :
                    'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-dark-600'">
                Last Year
            </button>
        </div>
    </div>

    {{-- Charts Container --}}
    <div x-data="cashFlowCharts()" x-init="initCharts(@js($this->monthlyTrendData), @js($this->expenseByCategoryData))" class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Monthly Trend --}}
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Income vs Expense Trend</h3>
            <div class="h-80">
                <canvas x-ref="trendChart"></canvas>
            </div>
        </div>

        {{-- Expense by Category --}}
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Expenses by Category</h3>
            <div class="h-80">
                <canvas x-ref="categoryChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Top 5 Expenses --}}
    <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Top 5 Expense Categories</h3>
        <div class="space-y-4">
            @forelse ($this->top5Expenses as $index => $expense)
                <div class="flex items-center gap-4">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br {{ match ($index) {
                            0 => 'from-red-500 to-red-600',
                            1 => 'from-orange-500 to-orange-600',
                            2 => 'from-yellow-500 to-yellow-600',
                            3 => 'from-green-500 to-green-600',
                            4 => 'from-blue-500 to-blue-600',
                            default => 'from-gray-500 to-gray-600',
                        } }} flex items-center justify-center text-white font-bold text-sm">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white">{{ $expense['category'] }}</span>
                            <span class="text-sm font-bold text-red-600 dark:text-red-400">
                                Rp {{ number_format($expense['total'], 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-dark-600 rounded-full h-2">
                            <div class="h-2 rounded-full {{ match ($index) {
                                0 => 'bg-gradient-to-r from-red-500 to-red-600',
                                1 => 'bg-gradient-to-r from-orange-500 to-orange-600',
                                2 => 'bg-gradient-to-r from-yellow-500 to-yellow-600',
                                3 => 'bg-gradient-to-r from-green-500 to-green-600',
                                4 => 'bg-gradient-to-r from-blue-500 to-blue-600',
                                default => 'bg-gradient-to-r from-gray-500 to-gray-600',
                            } }}"
                                style="width: {{ ($expense['total'] / $this->stats['total_expenses']) * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-xs text-gray-500 dark:text-gray-400">
                        {{ number_format(($expense['total'] / $this->stats['total_expenses']) * 100, 1) }}%
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 dark:text-gray-400 py-8">No expense data available</p>
            @endforelse
        </div>
    </div>
</div>

@assets
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
@endassets

@script
    <script>
        Alpine.data('cashFlowCharts', () => ({
            trendChart: null,
            categoryChart: null,
            themeObserver: null,

            initCharts(monthlyData, categoryData) {
                this.$nextTick(() => {
                    this.renderTrendChart(monthlyData);
                    this.renderCategoryChart(categoryData);
                    this.watchThemeChanges();
                    this.listenForUpdates();
                });
            },

            listenForUpdates() {
                Livewire.on('charts-updated', (data) => {
                    this.renderTrendChart(data[0].monthlyData);
                    this.renderCategoryChart(data[0].categoryData);
                });
            },

            watchThemeChanges() {
                this.themeObserver = new MutationObserver(() => {
                    if (this.trendChart && this.categoryChart) {
                        this.updateChartTheme(this.trendChart);
                        this.updateChartTheme(this.categoryChart);
                    }
                });

                this.themeObserver.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            },

            renderTrendChart(data) {
                if (this.trendChart) {
                    this.trendChart.destroy();
                }

                const colors = this.getThemeColors();

                this.trendChart = new Chart(this.$refs.trendChart, {
                    type: 'line',
                    data: {
                        labels: data.map(d => d.month),
                        datasets: [{
                            label: 'Income',
                            data: data.map(d => d.income),
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                        }, {
                            label: 'Expenses',
                            data: data.map(d => d.expenses),
                            borderColor: 'rgb(239, 68, 68)',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: colors.text
                                }
                            },
                            tooltip: {
                                backgroundColor: colors.tooltipBg,
                                titleColor: colors.tooltipTitle,
                                bodyColor: colors.tooltipBody,
                                borderColor: colors.border,
                                borderWidth: 1,
                                callbacks: {
                                    label: (ctx) =>
                                        `${ctx.dataset.label}: Rp ${ctx.parsed.y.toLocaleString('id-ID')}`
                                }
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                    color: colors.text
                                },
                                grid: {
                                    color: colors.grid
                                }
                            },
                            x: {
                                ticks: {
                                    color: colors.text
                                },
                                grid: {
                                    color: colors.grid
                                }
                            }
                        }
                    }
                });
            },

            renderCategoryChart(data) {
                if (this.categoryChart) {
                    this.categoryChart.destroy();
                }

                const colors = this.getThemeColors();

                this.categoryChart = new Chart(this.$refs.categoryChart, {
                    type: 'bar',
                    data: {
                        labels: data.map(d => d.category),
                        datasets: [{
                            label: 'Total Expense',
                            data: data.map(d => d.total),
                            backgroundColor: [
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(249, 115, 22, 0.8)',
                                'rgba(234, 179, 8, 0.8)',
                                'rgba(34, 197, 94, 0.8)',
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(168, 85, 247, 0.8)',
                                'rgba(236, 72, 153, 0.8)',
                            ],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: colors.tooltipBg,
                                titleColor: colors.tooltipTitle,
                                bodyColor: colors.tooltipBody,
                                borderColor: colors.border,
                                borderWidth: 1,
                                callbacks: {
                                    label: (ctx) => `Rp ${ctx.parsed.y.toLocaleString('id-ID')}`
                                }
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                    color: colors.text
                                },
                                grid: {
                                    color: colors.grid
                                }
                            },
                            x: {
                                ticks: {
                                    color: colors.text
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            },

            updateChartTheme(chart) {
                const colors = this.getThemeColors();

                chart.options.plugins.legend.labels.color = colors.text;
                chart.options.plugins.tooltip.backgroundColor = colors.tooltipBg;
                chart.options.plugins.tooltip.titleColor = colors.tooltipTitle;
                chart.options.plugins.tooltip.bodyColor = colors.tooltipBody;
                chart.options.plugins.tooltip.borderColor = colors.border;

                chart.options.scales.y.ticks.color = colors.text;
                chart.options.scales.y.grid.color = colors.grid;
                chart.options.scales.x.ticks.color = colors.text;
                chart.options.scales.x.grid.color = colors.grid;

                chart.update();
            },

            getThemeColors() {
                const isDark = document.documentElement.classList.contains('dark');
                return {
                    grid: isDark ? '#374151' : '#e5e7eb',
                    text: isDark ? '#d1d5db' : '#6b7280',
                    border: isDark ? '#4b5563' : '#d1d5db',
                    tooltipBg: isDark ? '#1f2937' : '#ffffff',
                    tooltipTitle: isDark ? '#ffffff' : '#111827',
                    tooltipBody: isDark ? '#e5e7eb' : '#374151',
                };
            },

            destroy() {
                if (this.trendChart) this.trendChart.destroy();
                if (this.categoryChart) this.categoryChart.destroy();
                if (this.themeObserver) this.themeObserver.disconnect();
            }
        }));
    </script>
@endscript

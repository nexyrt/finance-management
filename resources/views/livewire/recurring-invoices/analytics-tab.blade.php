<div class="space-y-6">
    <!-- Controls -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div class="flex gap-3">
            <x-select.styled wire:model.live="selectedYear" :options="$this->yearOptions" label="Year" />
            <x-select.styled wire:model.live="period" :options="[['label' => 'Monthly', 'value' => 'monthly'], ['label' => 'Quarterly', 'value' => 'quarterly']]" label="Period" />
        </div>

        <div class="text-sm text-dark-600 dark:text-dark-400">
            Last updated: {{ now()->format('d M Y, H:i') }}
        </div>
    </div>

    <!-- Revenue Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="chart-bar" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ $this->selectedYear }} Revenue</p>
                    <p class="text-xl font-bold text-blue-600 dark:text-blue-400">
                        Rp {{ number_format($this->revenueMetrics['current_year'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="arrow-trending-up" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">YoY Growth</p>
                    <p
                        class="text-xl font-bold {{ $this->revenueMetrics['growth_rate'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $this->revenueMetrics['growth_rate'] >= 0 ? '+' : '' }}{{ number_format($this->revenueMetrics['growth_rate'], 1) }}%
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="calendar" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">This Month</p>
                    <p class="text-xl font-bold text-purple-600 dark:text-purple-400">
                        Rp {{ number_format($this->revenueMetrics['current_month'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="calculator" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Avg Monthly</p>
                    <p class="text-xl font-bold text-amber-600 dark:text-amber-400">
                        Rp {{ number_format($this->revenueMetrics['average_monthly'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-gray-100 dark:bg-gray-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="banknotes" class="w-6 h-6 text-gray-600 dark:text-gray-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Previous Year</p>
                    <p class="text-xl font-bold text-gray-600 dark:text-gray-400">
                        Rp {{ number_format($this->revenueMetrics['previous_year'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50">
                {{ ucfirst($period) }} Revenue Trend
            </h3>
            <x-badge :text="$this->selectedYear" color="primary" />
        </div>

        <div class="h-80">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Template Performance -->
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-4">Top Templates by Revenue</h3>

            @if (count($this->templatePerformance) > 0)
                <div class="space-y-3">
                    @foreach ($this->templatePerformance as $index => $template)
                        <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-dark-700 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-bold text-primary-600 dark:text-primary-400">
                                        {{ $index + 1 }}
                                    </span>
                                </div>
                                <div>
                                    <div class="font-medium text-dark-900 dark:text-dark-100">{{ $template['name'] }}
                                    </div>
                                    <div class="text-xs text-dark-600 dark:text-dark-400">{{ $template['client'] }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-dark-900 dark:text-dark-100">
                                    Rp {{ number_format($template['revenue'], 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-dark-600 dark:text-dark-400">
                                    {{ $template['count'] }} invoices
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-dark-600 dark:text-dark-400">
                    <x-icon name="document-text" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                    <p>No template data available</p>
                </div>
            @endif
        </div>

        <!-- Invoice Status Breakdown -->
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-4">Invoice Status Breakdown</h3>

            <div class="space-y-4">
                <!-- Published -->
                <div
                    class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex items-center gap-2">
                            <x-icon name="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400" />
                            <span class="font-medium text-green-800 dark:text-green-200">Published</span>
                        </div>
                        <x-badge :text="number_format($this->statusBreakdown['published']['percentage'], 1) . '%'" color="green" />
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-green-600 dark:text-green-400">Count</div>
                            <div class="font-bold text-green-800 dark:text-green-200">
                                {{ number_format($this->statusBreakdown['published']['count']) }}
                            </div>
                        </div>
                        <div>
                            <div class="text-green-600 dark:text-green-400">Revenue</div>
                            <div class="font-bold text-green-800 dark:text-green-200">
                                Rp {{ number_format($this->statusBreakdown['published']['revenue'], 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Draft -->
                <div
                    class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex items-center gap-2">
                            <x-icon name="clock" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                            <span class="font-medium text-amber-800 dark:text-amber-200">Draft</span>
                        </div>
                        <x-badge :text="number_format($this->statusBreakdown['draft']['percentage'], 1) . '%'" color="amber" />
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-amber-600 dark:text-amber-400">Count</div>
                            <div class="font-bold text-amber-800 dark:text-amber-200">
                                {{ number_format($this->statusBreakdown['draft']['count']) }}
                            </div>
                        </div>
                        <div>
                            <div class="text-amber-600 dark:text-amber-400">Potential Revenue</div>
                            <div class="font-bold text-amber-800 dark:text-amber-200">
                                Rp {{ number_format($this->statusBreakdown['draft']['revenue'], 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Summary -->
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-blue-800 dark:text-blue-200">Total</span>
                        <div class="text-right">
                            <div class="font-bold text-blue-800 dark:text-blue-200">
                                {{ number_format($this->statusBreakdown['total']['count']) }} invoices
                            </div>
                            <div class="text-sm text-blue-600 dark:text-blue-400">
                                Rp {{ number_format($this->statusBreakdown['total']['revenue'], 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        function setupRevenueChart() {
            let revenueChart;

            function isDarkMode() {
                return document.documentElement.classList.contains('dark');
            }

            function createChart(chartData) {
                const ctx = document.getElementById('revenueChart');
                if (!ctx) return;

                // Destroy existing chart instance
                const existingChart = Chart.getChart(ctx);
                if (existingChart) {
                    existingChart.destroy();
                }

                const isDark = isDarkMode();

                revenueChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.map(item => item[Object.keys(item)[0]]),
                        datasets: [{
                            label: 'Revenue',
                            data: chartData.map(item => item.revenue),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: 'rgb(59, 130, 246)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: isDark ? '#374151' : '#ffffff',
                                titleColor: isDark ? '#f3f4f6' : '#111827',
                                bodyColor: isDark ? '#d1d5db' : '#374151',
                                borderColor: isDark ? '#6b7280' : '#e5e7eb',
                                borderWidth: 1,
                                callbacks: {
                                    label: function(context) {
                                        return 'Revenue: Rp ' + new Intl.NumberFormat('id-ID').format(
                                            context.parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: isDark ? '#9ca3af' : '#6b7280',
                                    callback: function(value) {
                                        return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                    }
                                },
                                grid: {
                                    color: isDark ? '#374151' : '#f3f4f6',
                                    borderColor: isDark ? '#6b7280' : '#d1d5db'
                                }
                            },
                            x: {
                                ticks: {
                                    color: isDark ? '#9ca3af' : '#6b7280'
                                },
                                grid: {
                                    color: isDark ? '#374151' : '#f3f4f6',
                                    borderColor: isDark ? '#6b7280' : '#d1d5db'
                                }
                            }
                        }
                    }
                });
            }

            const initialChartData = @json($this->revenueChart);
            createChart(initialChartData);

            // Listen for Livewire updates
            document.addEventListener('chartDataUpdated', event => {
                const chartData = event.detail[0].chartData;
                createChart(chartData);
            });

            // Dark mode observer
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class' && revenueChart) {
                        const currentData = revenueChart.data.datasets[0].data;
                        const currentLabels = revenueChart.data.labels;

                        setTimeout(() => {
                            createChart(currentLabels.map((label, index) => ({
                                [Object.keys(@json($this->revenueChart)[0])[
                                0]]: label,
                                revenue: currentData[index]
                            })));
                        }, 100);
                    }
                });
            });

            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });

            // Cleanup on page leave
            window.addEventListener('beforeunload', () => {
                if (revenueChart) {
                    revenueChart.destroy();
                }
            });
        }

        // Only use livewire:navigated
        document.addEventListener('livewire:navigated', () => {
            setupRevenueChart();
        });
    </script>
@endpush

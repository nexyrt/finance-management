{{-- Quick Actions Overview — Charts with wire:ignore for reliable rendering --}}
<div class="space-y-4">
    {{-- Month Stats (3 mini cards) --}}
    @if ($selectedAccountId)
        <div class="grid grid-cols-3 gap-3">
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

    {{-- Charts Grid: Income vs Expense (left) + Category Breakdown (right) --}}
    @if ($selectedAccountId)
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
            {{-- Income vs Expense Bar Chart --}}
            <div class="lg:col-span-3 bg-white dark:bg-dark-800 rounded-xl border border-secondary-200 dark:border-dark-600 p-4 lg:p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <x-icon name="chart-bar" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.income_vs_expense') }}</h3>
                            <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.last_12_months') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-xs">
                        <div class="flex items-center gap-1.5">
                            <div class="w-2.5 h-2.5 bg-green-500 rounded-full"></div>
                            <span class="text-dark-500 dark:text-dark-400">{{ __('pages.income') }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-2.5 h-2.5 bg-red-500 rounded-full"></div>
                            <span class="text-dark-500 dark:text-dark-400">{{ __('pages.expense') }}</span>
                        </div>
                    </div>
                </div>
                <div class="h-[260px]" wire:ignore
                     x-data="bankAccountCharts('incomeExpense', @js($this->chartData))"
                     >
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>

            {{-- Category Breakdown Donut --}}
            <div class="lg:col-span-2 bg-white dark:bg-dark-800 rounded-xl border border-secondary-200 dark:border-dark-600 p-4 lg:p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-9 w-9 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <x-icon name="chart-pie" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.category_breakdown') }}</h3>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.this_month_expenses') }}</p>
                    </div>
                </div>

                @if (count($this->categoryBreakdown) > 0)
                    <div class="h-[160px] mb-3" wire:ignore
                         x-data="bankAccountCharts('categoryBreakdown', @js($this->categoryBreakdown))"
                         >
                        <canvas x-ref="canvas"></canvas>
                    </div>

                    {{-- Category Legend --}}
                    <div class="space-y-1.5">
                        @php
                            $colors = ['#8b5cf6', '#06b6d4', '#f59e0b', '#ef4444', '#10b981', '#6366f1'];
                            $totalExpense = collect($this->categoryBreakdown)->sum('total');
                        @endphp
                        @foreach ($this->categoryBreakdown as $i => $cat)
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: {{ $colors[$i] ?? '#9ca3af' }}"></div>
                                    <span class="text-dark-600 dark:text-dark-400 truncate">{{ $cat['name'] }}</span>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="font-medium text-dark-900 dark:text-dark-50">
                                        Rp {{ number_format($cat['total'], 0, ',', '.') }}
                                    </span>
                                    <span class="text-dark-400 dark:text-dark-500 w-8 text-right">
                                        {{ $totalExpense > 0 ? round(($cat['total'] / $totalExpense) * 100) : 0 }}%
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-[200px] flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-gray-100 dark:bg-dark-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                <x-icon name="chart-pie" class="w-6 h-6 text-gray-400 dark:text-dark-500" />
                            </div>
                            <p class="text-sm text-dark-500 dark:text-dark-400">{{ __('pages.no_category_data') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        {{-- No Account Selected --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl border border-secondary-200 dark:border-dark-600 p-8">
            <div class="flex items-center justify-center min-h-[200px]">
                <div class="text-center">
                    <div class="w-14 h-14 bg-gray-100 dark:bg-dark-700 rounded-full flex items-center justify-center mx-auto mb-3">
                        <x-icon name="chart-bar" class="w-7 h-7 text-gray-400 dark:text-dark-500" />
                    </div>
                    <h3 class="font-medium text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.no_account_selected') }}</h3>
                    <p class="text-sm text-dark-500 dark:text-dark-400">{{ __('pages.choose_account_to_view_overview') }}</p>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
(function() {
    // Guard against duplicate registration
    if (window.__bankAccountChartsRegistered) return;
    window.__bankAccountChartsRegistered = true;

    function registerCharts() {
        if (typeof Alpine === 'undefined') return;

        Alpine.data('bankAccountCharts', (chartType, initialData) => ({
            chart: null,
            data: initialData,

            isDark() { return document.documentElement.classList.contains('dark'); },
            textColor() { return this.isDark() ? '#9ca3af' : '#6b7280'; },
            gridColor() { return this.isDark() ? '#374151' : '#f3f4f6'; },

            formatRp(value) {
                if (Math.abs(value) >= 1000000000) return 'Rp ' + (value / 1000000000).toFixed(1) + 'B';
                if (Math.abs(value) >= 1000000) return 'Rp ' + (value / 1000000).toFixed(0) + 'Jt';
                if (Math.abs(value) >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
            },

            render() {
                if (typeof Chart === 'undefined') return;
                if (chartType === 'incomeExpense') this.renderBarChart();
                if (chartType === 'categoryBreakdown') this.renderDonutChart();
            },

            init() {
                const self = this;

                // Load Chart.js if not present
                if (typeof Chart === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                    script.onload = () => self.render();
                    document.head.appendChild(script);
                } else {
                    this.$nextTick(() => self.render());
                }

                // Listen for data updates from Livewire
                Livewire.on('account-charts-updated', (payload) => {
                    const newData = payload[0];
                    if (chartType === 'incomeExpense' && newData.incomeExpense) {
                        self.data = newData.incomeExpense;
                        self.renderBarChart();
                    }
                    if (chartType === 'categoryBreakdown' && newData.categoryBreakdown) {
                        self.data = newData.categoryBreakdown;
                        self.renderDonutChart();
                    }
                });

                // Listen for PDF download
                Livewire.on('download-pdf', (event) => {
                    window.open(event.url, '_blank');
                });

                // Dark mode observer
                this._themeObserver = new MutationObserver(() => {
                    if (self.chart) {
                        setTimeout(() => self.render(), 50);
                    }
                });
                this._themeObserver.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            },

            destroyChart() {
                if (this.chart) {
                    this.chart.destroy();
                    this.chart = null;
                }
            },

            renderBarChart() {
                this.destroyChart();
                if (!this.data || this.data.length === 0 || !this.$refs.canvas) return;

                const isDark = this.isDark();

                this.chart = new Chart(this.$refs.canvas, {
                    type: 'bar',
                    data: {
                        labels: this.data.map(item => item.month),
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: this.data.map(item => item.income),
                                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                                borderColor: 'rgba(34, 197, 94, 1)',
                                borderWidth: 1,
                                borderRadius: 6,
                            },
                            {
                                label: 'Pengeluaran',
                                data: this.data.map(item => item.expense),
                                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                                borderColor: 'rgba(239, 68, 68, 1)',
                                borderWidth: 1,
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            tooltip: {
                                backgroundColor: isDark ? '#1f2937' : '#ffffff',
                                titleColor: isDark ? '#f3f4f6' : '#111827',
                                bodyColor: isDark ? '#d1d5db' : '#374151',
                                borderColor: isDark ? '#374151' : '#e5e7eb',
                                borderWidth: 1,
                                cornerRadius: 8,
                                callbacks: {
                                    label: (ctx) => {
                                        return ctx.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y);
                                    }
                                }
                            },
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                grid: { color: this.gridColor(), drawBorder: false },
                                ticks: { color: this.textColor(), font: { size: 10 } }
                            },
                            y: {
                                grid: { color: this.gridColor(), drawBorder: false },
                                ticks: {
                                    color: this.textColor(),
                                    font: { size: 10 },
                                    callback: (value) => this.formatRp(value)
                                }
                            }
                        }
                    }
                });
            },

            renderDonutChart() {
                this.destroyChart();
                if (!this.data || this.data.length === 0 || !this.$refs.canvas) return;

                const isDark = this.isDark();
                const colors = ['#8b5cf6', '#06b6d4', '#f59e0b', '#ef4444', '#10b981', '#6366f1'];

                this.chart = new Chart(this.$refs.canvas, {
                    type: 'doughnut',
                    data: {
                        labels: this.data.map(item => item.name),
                        datasets: [{
                            data: this.data.map(item => item.total),
                            backgroundColor: colors.slice(0, this.data.length),
                            borderColor: isDark ? '#27272a' : '#ffffff',
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: isDark ? '#1f2937' : '#ffffff',
                                titleColor: isDark ? '#f3f4f6' : '#111827',
                                bodyColor: isDark ? '#d1d5db' : '#374151',
                                borderColor: isDark ? '#374151' : '#e5e7eb',
                                borderWidth: 1,
                                cornerRadius: 8,
                                callbacks: {
                                    label: (ctx) => {
                                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                        const pct = total > 0 ? Math.round((ctx.parsed / total) * 100) : 0;
                                        return ctx.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed) + ' (' + pct + '%)';
                                    }
                                }
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
    }

    // Register immediately or on Alpine init
    if (window.Alpine) {
        registerCharts();
    } else {
        document.addEventListener('alpine:init', () => registerCharts());
    }

    // Re-register after Livewire SPA navigation
    document.addEventListener('livewire:navigated', () => {
        window.__bankAccountChartsRegistered = false;
        setTimeout(() => registerCharts(), 50);
    });
})();
</script>
@endscript

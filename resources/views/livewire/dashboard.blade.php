<div class="space-y-6">
    {{-- Header Section (WAJIB SAMA DI SEMUA PAGE) --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                Dashboard Keuangan
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                Ringkasan keuangan dan aktivitas bisnis
            </p>
        </div>

        {{-- Period Filter --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
            <x-select.native wire:model.live="period" class="w-full sm:w-48">
                <option value="this_month">Bulan Ini</option>
                <option value="last_month">Bulan Lalu</option>
                <option value="this_quarter">Kuartal Ini</option>
                <option value="last_quarter">Kuartal Lalu</option>
                <option value="this_year">Tahun Ini</option>
                <option value="last_year">Tahun Lalu</option>
                <option value="custom">Kustom</option>
            </x-select.native>

            @if ($period === 'custom')
                <div class="flex gap-2">
                    <x-input type="date" wire:model.live="startDate" placeholder="Dari" class="w-full sm:w-auto" />
                    <x-input type="date" wire:model.live="endDate" placeholder="Sampai" class="w-full sm:w-auto" />
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Stats - 4 Cards --}}
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
        {{-- Card 1: Total Saldo --}}
        <x-card class="hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400">Total Saldo</span>
                <div class="h-7 w-7 md:h-8 md:w-8 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 flex items-center justify-center">
                    <x-icon name="wallet" class="h-3.5 w-3.5 md:h-4 md:w-4 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
            <div class="text-lg md:text-2xl font-bold">{{ $this->formatCurrency($this->totalBankBalance) }}</div>
            <p class="text-[10px] md:text-xs flex items-center gap-1 mt-1">
                <x-icon name="arrow-trending-up" class="h-3 w-3 text-green-600 dark:text-green-400" />
                <span class="text-green-600 dark:text-green-400 font-medium">Saldo bank</span>
            </p>
        </x-card>

        {{-- Card 2: Pemasukan --}}
        <x-card class="hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400">Pemasukan</span>
                <div class="h-7 w-7 md:h-8 md:w-8 rounded-lg bg-green-500/10 dark:bg-green-500/20 flex items-center justify-center">
                    <x-icon name="arrow-down-right" class="h-3.5 w-3.5 md:h-4 md:w-4 text-green-600 dark:text-green-400" />
                </div>
            </div>
            <div class="text-lg md:text-2xl font-bold">{{ $this->formatCurrency($this->incomeThisMonth) }}</div>
            <p class="text-[10px] md:text-xs flex items-center gap-1 mt-1">
                <span class="text-gray-600 dark:text-gray-400">{{ $this->getPeriodLabel() }}</span>
            </p>
        </x-card>

        {{-- Card 3: Pengeluaran --}}
        <x-card class="hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400">Pengeluaran</span>
                <div class="h-7 w-7 md:h-8 md:w-8 rounded-lg bg-red-500/10 dark:bg-red-500/20 flex items-center justify-center">
                    <x-icon name="arrow-up-right" class="h-3.5 w-3.5 md:h-4 md:w-4 text-red-600 dark:text-red-400" />
                </div>
            </div>
            <div class="text-lg md:text-2xl font-bold">{{ $this->formatCurrency($this->expensesThisMonth) }}</div>
            <p class="text-[10px] md:text-xs flex items-center gap-1 mt-1">
                <span class="text-gray-600 dark:text-gray-400">{{ $this->getPeriodLabel() }}</span>
            </p>
        </x-card>

        {{-- Card 4: Invoice Tertunda --}}
        <x-card class="hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400">Invoice Tertunda</span>
                <div class="h-7 w-7 md:h-8 md:w-8 rounded-lg bg-amber-500/10 dark:bg-amber-500/20 flex items-center justify-center">
                    <x-icon name="document-text" class="h-3.5 w-3.5 md:h-4 md:w-4 text-amber-600 dark:text-amber-400" />
                </div>
            </div>
            <div class="text-lg md:text-2xl font-bold">{{ $this->formatCurrency($this->pendingInvoicesAmount) }}</div>
            <p class="text-[10px] md:text-xs flex items-center gap-1 mt-1">
                <x-icon name="clock" class="h-3 w-3 text-amber-600 dark:text-amber-400" />
                <span class="text-amber-600 dark:text-amber-400 font-medium">{{ $this->pendingInvoicesCount }} invoice</span>
                <span class="text-gray-600 dark:text-gray-400 hidden sm:inline">menunggu</span>
            </p>
        </x-card>
    </div>

    {{-- Main Charts Row --}}
    <div class="grid gap-4 lg:grid-cols-3 items-start">
        {{-- Cash Flow Chart - lg:col-span-2 --}}
        <div class="lg:col-span-2">
            <x-card class="hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="h-8 w-8 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 flex items-center justify-center">
                            <x-icon name="chart-bar" class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h3 class="text-sm md:text-base font-semibold">Arus Kas</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $this->getChartPeriodLabel() }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        {{-- Legend --}}
                        <div class="hidden sm:flex items-center gap-4 text-xs">
                            <div class="flex items-center gap-1.5">
                                <div class="w-2.5 h-2.5 rounded-full bg-green-600"></div>
                                <span class="text-gray-600 dark:text-gray-400">Pemasukan</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-2.5 h-2.5 rounded-full bg-red-600"></div>
                                <span class="text-gray-600 dark:text-gray-400">Pengeluaran</span>
                            </div>
                        </div>
                        {{-- Chart Period Filter --}}
                        <div class="flex items-center rounded-lg border border-gray-200 dark:border-dark-600 text-xs">
                            <button wire:click="$set('chartPeriod', 'this_month')"
                                class="px-2.5 py-1 rounded-l-lg transition-colors {{ $chartPeriod === 'this_month' ? 'bg-primary-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-700' }}">
                                Bulan Ini
                            </button>
                            <button wire:click="$set('chartPeriod', '6_months')"
                                class="px-2.5 py-1 border-x border-gray-200 dark:border-dark-600 transition-colors {{ $chartPeriod === '6_months' ? 'bg-primary-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-700' }}">
                                6 Bulan
                            </button>
                            <button wire:click="$set('chartPeriod', '12_months')"
                                class="px-2.5 py-1 rounded-r-lg transition-colors {{ $chartPeriod === '12_months' ? 'bg-primary-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-700' }}">
                                12 Bulan
                            </button>
                        </div>
                    </div>
                </div>
                <div class="h-[220px] md:h-[260px]"
                    x-data="dashboardChart('cashFlow', @js($this->cashFlowChart))"
                    x-init="renderCashFlow()"
                    wire:ignore>
                    <canvas x-ref="canvas"></canvas>
                </div>
            </x-card>
        </div>

        {{-- Expense by Category --}}
        <div>
            <x-card class="hover:shadow-md transition-shadow">
                <div class="flex items-center gap-2 mb-3">
                    <div class="h-8 w-8 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 flex items-center justify-center">
                        <x-icon name="chart-pie" class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h3 class="text-sm md:text-base font-semibold">Pengeluaran per Kategori</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ ucfirst($this->getPeriodLabel()) }}</p>
                    </div>
                </div>
                <div class="h-[160px]"
                    x-data="dashboardChart('expenseCategory', @js($this->expensesByCategoryChart))"
                    x-init="renderExpenseCategory()"
                    wire:ignore>
                    <canvas x-ref="canvas"></canvas>
                </div>
                <div class="space-y-1.5 mt-2">
                    @forelse ($this->expensesByCategoryChart as $category)
                        <div class="flex items-center justify-between text-xs py-1">
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $category['color'] }}"></div>
                                <span class="text-gray-600 dark:text-gray-400">{{ $category['name'] }}</span>
                            </div>
                            <span class="font-medium">{{ $this->formatCurrency($category['value']) }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center py-4">Belum ada data</p>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>

    {{-- Second Row --}}
    <div class="grid gap-4 lg:grid-cols-2 items-start">
        {{-- Revenue vs Expenses Chart --}}
        <x-card class="hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 flex items-center justify-center">
                        <x-icon name="chart-bar" class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h3 class="text-sm md:text-base font-semibold">Pendapatan vs Pengeluaran</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $this->getChartPeriodLabel() }}</p>
                    </div>
                </div>
            </div>
            <div class="h-[180px]"
                x-data="dashboardChart('revenueExpense', @js($this->revenueVsExpensesChart))"
                x-init="renderRevenueExpense()"
                wire:ignore>
                <canvas x-ref="canvas"></canvas>
            </div>
            <div class="flex items-center justify-center gap-4 mt-2 text-xs">
                <div class="flex items-center gap-1.5">
                    <div class="w-2.5 h-2.5 rounded bg-green-600"></div>
                    <span class="text-gray-600 dark:text-gray-400">Pendapatan</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-2.5 h-2.5 rounded bg-red-600"></div>
                    <span class="text-gray-600 dark:text-gray-400">Pengeluaran</span>
                </div>
            </div>
        </x-card>

        {{-- Bank Accounts --}}
        <x-card class="hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 flex items-center justify-center">
                        <x-icon name="building-library" class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h3 class="text-sm md:text-base font-semibold">Saldo Rekening</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Posisi per bank</p>
                    </div>
                </div>
                <a href="{{ route('bank-accounts.index') }}" wire:navigate class="text-xs text-blue-600 hover:underline flex items-center gap-1 dark:text-blue-400">
                    Kelola <x-icon name="arrow-up-right" class="h-3 w-3" />
                </a>
            </div>
            <div class="space-y-2">
                @forelse ($this->bankAccounts as $account)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-dark-700 hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 flex items-center justify-center">
                                <x-icon name="credit-card" class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div>
                                <p class="font-medium text-sm">{{ $account['name'] }}</p>
                                <p class="text-[10px] text-gray-600 dark:text-gray-400">{{ $account['account_number'] }} • {{ $account['bank'] }}</p>
                            </div>
                        </div>
                        <p class="font-semibold">{{ $this->formatCurrency($account['balance']) }}</p>
                    </div>
                @empty
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center py-4">Belum ada rekening</p>
                @endforelse
                @if ($this->bankAccounts->count() > 0)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-blue-500/5 border border-blue-500/20 dark:bg-blue-500/10 dark:border-blue-500/30">
                        <span class="font-medium text-sm">Total</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400">{{ $this->formatCurrency($this->totalBankBalance) }}</span>
                    </div>
                @endif
            </div>
        </x-card>
    </div>

    {{-- Third Row --}}
    <div class="grid gap-4 lg:grid-cols-2 items-start">
        {{-- Pending Invoices --}}
        <x-card class="hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-amber-500/10 dark:bg-amber-500/20 flex items-center justify-center">
                        <x-icon name="document-text" class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <h3 class="text-sm md:text-base font-semibold">Invoice Tertunda</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Perlu difollow-up</p>
                    </div>
                </div>
                <a href="{{ route('invoices.index') }}" wire:navigate class="text-xs text-blue-600 hover:underline flex items-center gap-1 dark:text-blue-400">
                    Lihat Semua <x-icon name="arrow-up-right" class="h-3 w-3" />
                </a>
            </div>
            <div class="space-y-2">
                @forelse ($this->pendingInvoicesList as $invoice)
                    @php
                        $isOverdue = $invoice['days_until_due'] < 0;
                        $isWarning = $invoice['days_until_due'] >= 0 && $invoice['days_until_due'] < 7;
                    @endphp
                    <div class="flex items-center justify-between p-3 rounded-lg border transition-colors {{ $isOverdue ? 'bg-red-500/5 border-red-500/20 dark:bg-red-500/10 dark:border-red-500/30' : ($isWarning ? 'bg-amber-500/5 border-amber-500/20 dark:bg-amber-500/10 dark:border-amber-500/30' : 'bg-gray-50 dark:bg-dark-700 border-transparent') }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ $isOverdue ? 'bg-red-500/10 dark:bg-red-500/20' : ($isWarning ? 'bg-amber-500/10 dark:bg-amber-500/20' : 'bg-gray-200 dark:bg-dark-600') }}">
                                @if ($isOverdue)
                                    <x-icon name="exclamation-triangle" class="h-4 w-4 text-red-600 dark:text-red-400" />
                                @elseif ($isWarning)
                                    <x-icon name="clock" class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                                @else
                                    <x-icon name="check-circle" class="h-4 w-4 text-gray-600 dark:text-gray-400" />
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-sm truncate">{{ $invoice['client'] }}</p>
                                <p class="text-[10px] text-gray-600 dark:text-gray-400">{{ $invoice['invoice_number'] }}</p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-2">
                            <p class="font-semibold">{{ $this->formatCurrency($invoice['amount']) }}</p>
                            <span class="text-[10px] font-medium {{ $isOverdue ? 'text-red-600 dark:text-red-400' : ($isWarning ? 'text-amber-600 dark:text-amber-400' : 'text-gray-600 dark:text-gray-400') }}">
                                {{ $isOverdue ? 'Lewat ' . abs($invoice['days_until_due']) . ' hari' : $invoice['days_until_due'] . ' hari lagi' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center py-4">Tidak ada invoice tertunda</p>
                @endforelse
            </div>
        </x-card>

        {{-- Recent Transactions --}}
        <x-card class="hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 flex items-center justify-center">
                        <x-icon name="arrow-trending-up" class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h3 class="text-sm md:text-base font-semibold">Transaksi Terbaru</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Aktivitas terakhir</p>
                    </div>
                </div>
                <a href="{{ route('cash-flow.index') }}" wire:navigate class="text-xs text-blue-600 hover:underline flex items-center gap-1 dark:text-blue-400">
                    Lihat Semua <x-icon name="arrow-up-right" class="h-3 w-3" />
                </a>
            </div>
            <div class="space-y-2">
                @forelse ($this->recentTransactions as $transaction)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-dark-700 hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ $transaction['type'] === 'income' ? 'bg-green-500/10 dark:bg-green-500/20' : 'bg-red-500/10 dark:bg-red-500/20' }}">
                                @if ($transaction['type'] === 'income')
                                    <x-icon name="arrow-down-right" class="h-4 w-4 text-green-600 dark:text-green-400" />
                                @else
                                    <x-icon name="arrow-up-right" class="h-4 w-4 text-red-600 dark:text-red-400" />
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-sm truncate">{{ $transaction['description'] }}</p>
                                <p class="text-[10px] text-gray-600 dark:text-gray-400">{{ $transaction['date']->diffForHumans() }}</p>
                            </div>
                        </div>
                        <p class="font-semibold flex-shrink-0 ml-2 {{ $transaction['type'] === 'income' ? 'text-green-600 dark:text-green-400' : '' }}">
                            {{ $transaction['type'] === 'income' ? '+' : '-' }}{{ $this->formatNumber($transaction['amount']) }}
                        </p>
                    </div>
                @empty
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center py-4">Belum ada transaksi</p>
                @endforelse
            </div>
        </x-card>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardChart', (chartType, initialData) => ({
            chart: null,
            data: initialData,
            chartType: chartType,

            isDark() { return document.documentElement.classList.contains('dark') },
            textColor() { return this.isDark() ? '#9ca3af' : '#6b7280' },
            gridColor() { return this.isDark() ? '#374151' : '#e5e7eb' },
            formatRp(value) {
                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(0) + 'jt';
                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                return 'Rp ' + value;
            },
            tooltipStyle() {
                return {
                    backgroundColor: this.isDark() ? '#1f2937' : '#ffffff',
                    titleColor: this.isDark() ? '#f3f4f6' : '#111827',
                    bodyColor: this.isDark() ? '#f3f4f6' : '#111827',
                    borderColor: this.isDark() ? '#374151' : '#e5e7eb',
                    borderWidth: 1,
                };
            },

            destroyChart() {
                if (this.chart) {
                    this.chart.destroy();
                    this.chart = null;
                }
            },

            init() {
                // Listen for Livewire charts-refresh event to fetch fresh data
                Livewire.on('charts-refresh', () => {
                    const chartNameMap = {
                        'cashFlow': 'cashFlowChart',
                        'expenseCategory': 'expensesByCategoryChart',
                        'revenueExpense': 'revenueVsExpensesChart',
                    };
                    const computedName = chartNameMap[this.chartType];
                    if (!computedName) return;

                    this.$wire.getChartData(computedName).then((freshData) => {
                        this.data = freshData;
                        const renderMap = {
                            'cashFlow': () => this.renderCashFlow(),
                            'expenseCategory': () => this.renderExpenseCategory(),
                            'revenueExpense': () => this.renderRevenueExpense(),
                        };
                        if (renderMap[this.chartType]) {
                            renderMap[this.chartType]();
                        }
                    });
                });
            },

            renderCashFlow() {
                this.destroyChart();
                if (!this.data || this.data.length === 0) return;
                const self = this;
                this.chart = new Chart(this.$refs.canvas, {
                    type: 'line',
                    data: {
                        labels: this.data.map(d => d.label),
                        datasets: [
                            {
                                label: 'Pemasukan', data: this.data.map(d => d.income),
                                borderColor: 'rgb(22, 163, 74)', backgroundColor: 'rgba(22, 163, 74, 0.1)',
                                fill: true, tension: 0.4, borderWidth: 2
                            },
                            {
                                label: 'Pengeluaran', data: this.data.map(d => d.expenses),
                                borderColor: 'rgb(220, 38, 38)', backgroundColor: 'rgba(220, 38, 38, 0.1)',
                                fill: true, tension: 0.4, borderWidth: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { ...this.tooltipStyle(), callbacks: { label: (ctx) => ctx.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y) } }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { color: self.textColor(), font: { size: 11 }, callback: (v) => self.formatRp(v) }, grid: { color: self.gridColor() } },
                            x: { ticks: { color: self.textColor(), font: { size: 11 } }, grid: { display: false } }
                        }
                    }
                });
            },

            renderExpenseCategory() {
                this.destroyChart();
                if (!this.data || this.data.length === 0) return;
                this.chart = new Chart(this.$refs.canvas, {
                    type: 'doughnut',
                    data: {
                        labels: this.data.map(d => d.name),
                        datasets: [{
                            data: this.data.map(d => d.value),
                            backgroundColor: this.data.map(d => d.color),
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { ...this.tooltipStyle(), callbacks: { label: (ctx) => ctx.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed) } }
                        },
                        cutout: '65%'
                    }
                });
            },

            renderRevenueExpense() {
                this.destroyChart();
                if (!this.data || this.data.length === 0) return;
                const self = this;
                this.chart = new Chart(this.$refs.canvas, {
                    type: 'bar',
                    data: {
                        labels: this.data.map(d => d.label),
                        datasets: [
                            { label: 'Pendapatan', data: this.data.map(d => d.revenue), backgroundColor: 'rgba(22, 163, 74, 0.8)', borderRadius: 4 },
                            { label: 'Pengeluaran', data: this.data.map(d => d.expenses), backgroundColor: 'rgba(220, 38, 38, 0.8)', borderRadius: 4 }
                        ]
                    },
                    options: {
                        indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { ...this.tooltipStyle(), callbacks: { label: (ctx) => ctx.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.x) } }
                        },
                        scales: {
                            x: { beginAtZero: true, ticks: { color: self.textColor(), font: { size: 10 }, callback: (v) => self.formatRp(v) }, grid: { color: self.gridColor() } },
                            y: { ticks: { color: self.textColor(), font: { size: 10 } }, grid: { display: false } }
                        }
                    }
                });
            },

            destroy() {
                this.destroyChart();
            }
        }));
    });
</script>
@endpush

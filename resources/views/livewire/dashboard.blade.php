<div class="min-h-screen bg-gray-50 dark:bg-dark-900 p-6">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-dark-900 dark:text-white">{{ __('pages.financial_dashboard') }}</h1>
        <p class="text-dark-600 dark:text-dark-400 mt-1">{{ __('pages.complete_financial_summary') }}</p>
    </div>

    {{-- Top Metrics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        {{-- Total Revenue --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="currency-dollar" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                @if ($this->revenueGrowth != 0)
                    <x-badge :text="($this->revenueGrowth >= 0 ? '+' : '') . $this->revenueGrowth . '%'" :color="$this->revenueGrowth >= 0 ? 'green' : 'red'" sm />
                @endif
            </div>
            <p class="text-sm text-dark-600 dark:text-dark-400 mb-1">{{ __('pages.total_revenue') }}</p>
            <p class="text-2xl font-bold text-dark-900 dark:text-white">{{ $this->formatCurrency($this->totalRevenue) }}</p>
        </x-card>

        {{-- Outstanding --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="clock" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
                @if ($this->overdueInvoices > 0)
                    <x-badge :text="$this->overdueInvoices . ' ' . __('pages.overdue')" color="red" sm />
                @endif
            </div>
            <p class="text-sm text-dark-600 dark:text-dark-400 mb-1">{{ __('pages.outstanding_bills') }}</p>
            <p class="text-2xl font-bold text-dark-900 dark:text-white">{{ $this->formatCurrency($this->outstandingAmount) }}</p>
        </x-card>

        {{-- Total Invoices --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-secondary-100 dark:bg-secondary-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="document-text" class="w-6 h-6 text-secondary-600 dark:text-secondary-400" />
                </div>
            </div>
            <p class="text-sm text-dark-600 dark:text-dark-400 mb-1">{{ __('pages.total_invoices') }}</p>
            <p class="text-2xl font-bold text-dark-900 dark:text-white">{{ number_format($this->totalInvoices) }}</p>
        </x-card>

        {{-- Collection Rate --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
            <p class="text-sm text-dark-600 dark:text-dark-400 mb-1">{{ __('pages.collection_rate') }}</p>
            <p class="text-2xl font-bold text-dark-900 dark:text-white">{{ $this->collectionRate }}%</p>
        </x-card>

        {{-- Gross Profit --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="arrow-trending-up" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <x-badge :text="$this->profitMargin . '% ' . __('pages.margin')" color="green" sm />
            </div>
            <p class="text-sm text-dark-600 dark:text-dark-400 mb-1">{{ __('pages.gross_profit') }}</p>
            <p class="text-2xl font-bold text-dark-900 dark:text-white">{{ $this->formatCurrency($this->grossProfit) }}</p>
        </x-card>

        {{-- Active Clients --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="user-group" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                @if ($this->newClientsThisMonth > 0)
                    <x-badge :text="'+' . $this->newClientsThisMonth . ' ' . __('pages.new')" color="indigo" sm />
                @endif
            </div>
            <p class="text-sm text-dark-600 dark:text-dark-400 mb-1">{{ __('pages.active_clients') }}</p>
            <p class="text-2xl font-bold text-dark-900 dark:text-white">{{ number_format($this->activeClients) }}</p>
        </x-card>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Revenue Trend --}}
        <x-card class="lg:col-span-2" header="{{ __('pages.revenue_trend_12_months') }}">
            <div class="h-80">
                <canvas id="revenueChart"></canvas>
            </div>
        </x-card>

        {{-- Invoice Status --}}
        <x-card header="{{ __('pages.invoice_status') }}">
            <div class="h-80 flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </x-card>
    </div>

    {{-- Profit vs Revenue Chart --}}
    <div class="mb-8">
        <x-card header="{{ __('pages.revenue_vs_profit') }}">
            <div class="h-80">
                <canvas id="profitChart"></canvas>
            </div>
        </x-card>
    </div>

    {{-- Insights Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Top Clients --}}
        <x-card>
            <x-slot:header class="flex items-center justify-between p-4 border-b border-gray-100 dark:border-dark-600">
                <span class="text-md font-medium text-secondary-700 dark:text-dark-300">{{ __('pages.top_clients') }}</span>
                <a href="{{ route('clients') }}" wire:navigate class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                    {{ __('pages.view_all') }} →
                </a>
            </x-slot:header>
            <div class="space-y-3">
                @forelse ($this->topClients as $client)
                    <div class="flex items-center justify-between p-3 bg-dark-50 dark:bg-dark-700 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white font-bold">
                                {{ $client['rank'] }}
                            </div>
                            <div>
                                <p class="font-medium text-dark-900 dark:text-white">{{ $client['name'] }}</p>
                                <p class="text-xs text-dark-500 dark:text-dark-400 capitalize">{{ __('pages.' . $client['type']) }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-dark-900 dark:text-white">{{ $this->formatCurrency($client['total_revenue']) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-dark-500 dark:text-dark-400 py-8">{{ __('pages.no_client_data_yet') }}</p>
                @endforelse
            </div>
        </x-card>

        {{-- Recurring Revenue --}}
        <x-card>
            <x-slot:header class="flex items-center justify-between p-4 border-b border-gray-100 dark:border-dark-600">
                <span class="text-md font-medium text-secondary-700 dark:text-dark-300">{{ __('pages.recurring_revenue') }}</span>
                <a href="{{ route('recurring-invoices.index') }}" wire:navigate class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                    {{ __('pages.manage') }} →
                </a>
            </x-slot:header>
            <div class="space-y-4">
                <div class="p-4 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg">
                    <p class="text-sm text-blue-700 dark:text-blue-300 mb-1">{{ __('pages.mrr_monthly_recurring_revenue') }}</p>
                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $this->formatCurrency($this->monthlyRecurringRevenue) }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-dark-50 dark:bg-dark-700 rounded-lg">
                        <p class="text-sm text-dark-600 dark:text-dark-400 mb-1">{{ __('pages.active_templates') }}</p>
                        <p class="text-xl font-bold text-dark-900 dark:text-white">{{ $this->activeTemplates }}</p>
                    </div>
                    <div class="p-4 bg-dark-50 dark:bg-dark-700 rounded-lg">
                        <p class="text-sm text-dark-600 dark:text-dark-400 mb-1">{{ __('pages.draft_invoices') }}</p>
                        <p class="text-xl font-bold text-dark-900 dark:text-white">{{ $this->draftRecurringInvoices }}</p>
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Recent Activity & Quick Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Invoices --}}
        <x-card class="lg:col-span-2">
            <x-slot:header class="flex items-center justify-between p-4 border-b border-gray-100 dark:border-dark-600">
                <span class="text-md font-medium text-secondary-700 dark:text-dark-300">{{ __('pages.recent_invoices') }}</span>
                <a href="{{ route('invoices.index') }}" wire:navigate class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                    {{ __('pages.view_all') }} →
                </a>
            </x-slot:header>
            <div class="space-y-2">
                @forelse ($this->recentInvoices as $invoice)
                    <div class="flex items-center justify-between p-3 hover:bg-dark-50 dark:hover:bg-dark-700 rounded-lg transition-colors">
                        <div class="flex-1">
                            <p class="font-mono font-bold text-sm text-dark-900 dark:text-white">{{ $invoice['invoice_number'] }}</p>
                            <p class="text-xs text-dark-600 dark:text-dark-400">{{ $invoice['client_name'] }} • {{ $invoice['issue_date'] }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="font-bold text-dark-900 dark:text-white">{{ $this->formatCurrency($invoice['total_amount']) }}</p>
                            <x-badge :text="match ($invoice['status']) {
                                'draft' => __('pages.draft'),
                                'sent' => __('pages.sent'),
                                'paid' => __('pages.paid'),
                                'partially_paid' => __('pages.installment'),
                                'overdue' => __('pages.late'),
                                default => $invoice['status']
                            }" :color="match ($invoice['status']) {
                                'draft' => 'gray',
                                'sent' => 'blue',
                                'paid' => 'green',
                                'partially_paid' => 'yellow',
                                'overdue' => 'red',
                                default => 'gray'
                            }" />
                        </div>
                    </div>
                @empty
                    <p class="text-center text-dark-500 dark:text-dark-400 py-8">{{ __('pages.no_invoices_yet') }}</p>
                @endforelse
            </div>
        </x-card>

        {{-- Quick Stats & Actions --}}
        <div class="space-y-6">
            {{-- Bank Balance - Keep custom styling for gradient background --}}
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl p-6 text-white shadow-md">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <x-icon name="building-library" class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-sm text-emerald-100">{{ __('pages.total_bank_balance') }}</p>
                        <p class="text-2xl font-bold">{{ $this->formatCurrency($this->totalBankBalance) }}</p>
                    </div>
                </div>
                <div class="pt-4 border-t border-emerald-400/30">
                    <p class="text-xs text-emerald-100 mb-1">{{ __('pages.cash_flow_this_month') }}</p>
                    <p class="text-lg font-semibold">{{ $this->formatCurrency($this->cashFlowThisMonth) }}</p>
                </div>
            </div>

            {{-- Reimbursements --}}
            @if ($this->pendingReimbursements > 0)
                <x-card>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-10 w-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                            <x-icon name="currency-dollar" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                        </div>
                        <div>
                            <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.reimbursement_pending') }}</p>
                            <p class="text-xl font-bold text-dark-900 dark:text-white">{{ $this->pendingReimbursements }} {{ __('pages.submissions') }}</p>
                        </div>
                    </div>
                    <x-slot:footer>
                        <div class="flex items-center justify-between w-full">
                            <div>
                                <p class="text-xs text-dark-600 dark:text-dark-400 mb-1">{{ __('pages.total_value') }}</p>
                                <p class="text-lg font-semibold text-dark-900 dark:text-white">{{ $this->formatCurrency($this->pendingReimbursementAmount) }}</p>
                            </div>
                            <a href="{{ route('reimbursements.index') }}" wire:navigate class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium">
                                {{ __('pages.review_now') }} →
                            </a>
                        </div>
                    </x-slot:footer>
                </x-card>
            @endif

            {{-- Quick Actions --}}
            <x-card header="{{ __('pages.quick_actions') }}">
                <div class="space-y-2">
                    <a href="{{ route('invoices.create') }}" wire:navigate class="flex items-center gap-3 p-3 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors group">
                        <div class="h-8 w-8 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center group-hover:bg-primary-200 dark:group-hover:bg-primary-900/50 transition-colors">
                            <x-icon name="plus" class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                        </div>
                        <span class="text-sm font-medium text-dark-900 dark:text-white">{{ __('pages.create_new_invoice') }}</span>
                    </a>
                    <a href="{{ route('cash-flow.index') }}" wire:navigate class="flex items-center gap-3 p-3 rounded-lg hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors group">
                        <div class="h-8 w-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center group-hover:bg-emerald-200 dark:group-hover:bg-emerald-900/50 transition-colors">
                            <x-icon name="chart-bar" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm font-medium text-dark-900 dark:text-white">{{ __('pages.view_cash_flow') }}</span>
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    function setupDashboardCharts() {
        let revenueChart, statusChart, profitChart;

        function isDarkMode() {
            return document.documentElement.classList.contains('dark');
        }

        // Revenue Chart
        function createRevenueChart() {
            const ctx = document.getElementById('revenueChart');
            if (!ctx) return;

            // Destroy existing chart instance
            const existingChart = Chart.getChart(ctx);
            if (existingChart) {
                existingChart.destroy();
            }

            const isDark = isDarkMode();
            const data = @json($this->monthlyRevenueChart);

            revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(item => item.month),
                    datasets: [{
                        label: 'Pendapatan',
                        data: data.map(item => item.revenue),
                        borderColor: 'rgb(37, 99, 235)', // Professional Blue #2563EB
                        backgroundColor: 'rgba(37, 99, 235, 0.15)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgb(37, 99, 235)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: isDark ? '#27272a' : '#ffffff',
                            titleColor: isDark ? '#fafafa' : '#18181b',
                            bodyColor: isDark ? '#fafafa' : '#18181b',
                            borderColor: isDark ? '#3f3f46' : '#e4e4e7',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    return '{{ __("pages.revenue") }}: Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: isDark ? '#a1a1aa' : '#71717a',
                                callback: function(value) {
                                    if (value >= 1000000000) return 'Rp ' + (value / 1000000000).toFixed(1) + 'M';
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(0) + 'Jt';
                                    return 'Rp ' + (value / 1000).toFixed(0) + 'Rb';
                                }
                            },
                            grid: { color: isDark ? '#27272a' : '#f4f4f5' }
                        },
                        x: {
                            ticks: { color: isDark ? '#a1a1aa' : '#71717a' },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Status Chart
        function createStatusChart() {
            const ctx = document.getElementById('statusChart');
            if (!ctx) return;

            // Destroy existing chart instance
            const existingChart = Chart.getChart(ctx);
            if (existingChart) {
                existingChart.destroy();
            }

            const isDark = isDarkMode();
            const data = @json($this->invoiceStatusChart);

            statusChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(data),
                    datasets: [{
                        data: Object.values(data),
                        backgroundColor: [
                            'rgba(161, 161, 170, 0.85)', // draft - zinc-400
                            'rgba(37, 99, 235, 0.85)',   // sent - Professional Blue
                            'rgba(5, 150, 105, 0.85)',   // paid - emerald-600
                            'rgba(217, 119, 6, 0.85)',   // partially_paid - amber-600
                            'rgba(220, 38, 38, 0.85)'    // overdue - red-600
                        ],
                        borderWidth: 3,
                        borderColor: isDark ? '#18181b' : '#ffffff',
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: isDark ? '#fafafa' : '#18181b',
                                padding: 15,
                                font: { size: 12 }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }

        // Profit Chart
        function createProfitChart() {
            const ctx = document.getElementById('profitChart');
            if (!ctx) return;

            // Destroy existing chart instance
            const existingChart = Chart.getChart(ctx);
            if (existingChart) {
                existingChart.destroy();
            }

            const isDark = isDarkMode();
            const data = @json($this->profitRevenueChart);

            profitChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.month),
                    datasets: [
                        {
                            label: '{{ __("pages.revenue") }}',
                            data: data.map(item => item.revenue),
                            backgroundColor: 'rgba(37, 99, 235, 0.8)', // Professional Blue
                            borderColor: 'rgb(37, 99, 235)',
                            borderWidth: 1,
                            borderRadius: 8,
                            hoverBackgroundColor: 'rgba(37, 99, 235, 0.95)'
                        },
                        {
                            label: '{{ __("pages.profit") }}',
                            data: data.map(item => item.profit),
                            backgroundColor: 'rgba(5, 150, 105, 0.8)', // Emerald Success
                            borderColor: 'rgb(5, 150, 105)',
                            borderWidth: 1,
                            borderRadius: 8,
                            hoverBackgroundColor: 'rgba(5, 150, 105, 0.95)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: isDark ? '#fafafa' : '#18181b',
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            backgroundColor: isDark ? '#27272a' : '#ffffff',
                            titleColor: isDark ? '#fafafa' : '#18181b',
                            bodyColor: isDark ? '#fafafa' : '#18181b',
                            borderColor: isDark ? '#3f3f46' : '#e4e4e7',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: isDark ? '#a1a1aa' : '#71717a',
                                callback: function(value) {
                                    if (value >= 1000000000) return 'Rp ' + (value / 1000000000).toFixed(1) + 'M';
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(0) + 'Jt';
                                    return 'Rp ' + (value / 1000).toFixed(0) + 'Rb';
                                }
                            },
                            grid: { color: isDark ? '#27272a' : '#f4f4f5' }
                        },
                        x: {
                            ticks: { color: isDark ? '#a1a1aa' : '#71717a' },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Initialize all charts
        createRevenueChart();
        createStatusChart();
        createProfitChart();

        // Dark mode observer
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    setTimeout(() => {
                        createRevenueChart();
                        createStatusChart();
                        createProfitChart();
                    }, 100);
                }
            });
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });
    }

    // Only use livewire:navigated (handles both first load and SPA navigation)
    document.addEventListener('livewire:navigated', () => {
        setupDashboardCharts();
    });
</script>
@endpush
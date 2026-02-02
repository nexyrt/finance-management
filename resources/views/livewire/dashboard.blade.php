<div class="min-h-screen bg-gray-50 dark:bg-dark-900 p-6">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-dark-900 dark:text-white">{{ __('pages.financial_dashboard') }}</h1>
        <p class="text-dark-600 dark:text-dark-400 mt-1">{{ __('pages.complete_financial_summary') }}</p>
    </div>

    {{-- Top Metrics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        {{-- Total Revenue --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl p-6 border border-dark-200 dark:border-dark-600 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                @if ($this->revenueGrowth != 0)
                    <span class="text-xs font-medium px-2 py-1 rounded-full {{ $this->revenueGrowth >= 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                        {{ $this->revenueGrowth >= 0 ? '+' : '' }}{{ $this->revenueGrowth }}%
                    </span>
                @endif
            </div>
            <p class="text-sm text-dark-600 dark:text-dark-400 mb-1">{{ __('pages.total_revenue') }}</p>
            <p class="text-2xl font-bold text-dark-900 dark:text-white">{{ $this->formatCurrency($this->totalRevenue) }}</p>
        </div>

        {{-- Outstanding --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl p-6 border border-dark-200 dark:border-dark-600 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                @if ($this->overdueInvoices > 0)
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                        {{ $this->overdueInvoices }} {{ __('pages.overdue') }}
                    </span>
                @endif
            </div>
            <p class="text-sm text-dark-600 dark:text-dark-400 mb-1">{{ __('pages.outstanding_bills') }}</p>
            <p class="text-2xl font-bold text-dark-900 dark:text-white">{{ $this->formatCurrency($this->outstandingAmount) }}</p>
        </div>

        {{-- Total Invoices --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl p-6 border border-dark-200 dark:border-dark-600 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-dark-600 dark:text-dark-400 mb-1">{{ __('pages.total_invoices') }}</p>
            <p class="text-2xl font-bold text-dark-900 dark:text-white">{{ number_format($this->totalInvoices) }}</p>
        </div>

        {{-- Collection Rate --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl p-6 border border-dark-200 dark:border-dark-600 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-dark-600 dark:text-dark-400 mb-1">{{ __('pages.collection_rate') }}</p>
            <p class="text-2xl font-bold text-dark-900 dark:text-white">{{ $this->collectionRate }}%</p>
        </div>

        {{-- Gross Profit --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl p-6 border border-dark-200 dark:border-dark-600 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                    {{ $this->profitMargin }}% {{ __('pages.margin') }}
                </span>
            </div>
            <p class="text-sm text-dark-600 dark:text-dark-400 mb-1">{{ __('pages.gross_profit') }}</p>
            <p class="text-2xl font-bold text-dark-900 dark:text-white">{{ $this->formatCurrency($this->grossProfit) }}</p>
        </div>

        {{-- Active Clients --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl p-6 border border-dark-200 dark:border-dark-600 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                @if ($this->newClientsThisMonth > 0)
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                        +{{ $this->newClientsThisMonth }} {{ __('pages.new') }}
                    </span>
                @endif
            </div>
            <p class="text-sm text-dark-600 dark:text-dark-400 mb-1">{{ __('pages.active_clients') }}</p>
            <p class="text-2xl font-bold text-dark-900 dark:text-white">{{ number_format($this->activeClients) }}</p>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Revenue Trend --}}
        <div class="lg:col-span-2 bg-white dark:bg-dark-800 rounded-xl p-6 border border-dark-200 dark:border-dark-600">
            <h3 class="text-lg font-semibold text-dark-900 dark:text-white mb-6">{{ __('pages.revenue_trend_12_months') }}</h3>
            <div class="h-80">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Invoice Status --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl p-6 border border-dark-200 dark:border-dark-600">
            <h3 class="text-lg font-semibold text-dark-900 dark:text-white mb-6">{{ __('pages.invoice_status') }}</h3>
            <div class="h-80 flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Profit vs Revenue Chart --}}
    <div class="bg-white dark:bg-dark-800 rounded-xl p-6 border border-dark-200 dark:border-dark-600 mb-8">
        <h3 class="text-lg font-semibold text-dark-900 dark:text-white mb-6">{{ __('pages.revenue_vs_profit') }}</h3>
        <div class="h-80">
            <canvas id="profitChart"></canvas>
        </div>
    </div>

    {{-- Insights Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Top Clients --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl p-6 border border-dark-200 dark:border-dark-600">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-dark-900 dark:text-white">{{ __('pages.top_clients') }}</h3>
                <a href="{{ route('clients') }}" wire:navigate class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                    {{ __('pages.view_all') }} →
                </a>
            </div>
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
        </div>

        {{-- Recurring Revenue --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl p-6 border border-dark-200 dark:border-dark-600">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-dark-900 dark:text-white">{{ __('pages.recurring_revenue') }}</h3>
                <a href="{{ route('recurring-invoices.index') }}" wire:navigate class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                    {{ __('pages.manage') }} →
                </a>
            </div>
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
        </div>
    </div>

    {{-- Recent Activity & Quick Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Invoices --}}
        <div class="lg:col-span-2 bg-white dark:bg-dark-800 rounded-xl p-6 border border-dark-200 dark:border-dark-600">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-dark-900 dark:text-white">{{ __('pages.recent_invoices') }}</h3>
                <a href="{{ route('invoices.index') }}" wire:navigate class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                    {{ __('pages.view_all') }} →
                </a>
            </div>
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
        </div>

        {{-- Quick Stats & Actions --}}
        <div class="space-y-6">
            {{-- Bank Balance --}}
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl p-6 text-white">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                        </svg>
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
                <div class="bg-white dark:bg-dark-800 rounded-xl p-6 border border-dark-200 dark:border-dark-600">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-10 w-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.reimbursement_pending') }}</p>
                            <p class="text-xl font-bold text-dark-900 dark:text-white">{{ $this->pendingReimbursements }} {{ __('pages.submissions') }}</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-dark-200 dark:border-dark-600">
                        <p class="text-xs text-dark-600 dark:text-dark-400 mb-1">{{ __('pages.total_value') }}</p>
                        <p class="text-lg font-semibold text-dark-900 dark:text-white">{{ $this->formatCurrency($this->pendingReimbursementAmount) }}</p>
                    </div>
                    <a href="{{ route('reimbursements.index') }}" wire:navigate class="block mt-4 text-center text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium">
                        {{ __('pages.review_now') }} →
                    </a>
                </div>
            @endif

            {{-- Quick Actions --}}
            <div class="bg-white dark:bg-dark-800 rounded-xl p-6 border border-dark-200 dark:border-dark-600">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-white mb-4">{{ __('pages.quick_actions') }}</h4>
                <div class="space-y-2">
                    <a href="{{ route('invoices.create') }}" wire:navigate class="flex items-center gap-3 p-3 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors group">
                        <div class="h-8 w-8 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center group-hover:bg-primary-200 dark:group-hover:bg-primary-900/50 transition-colors">
                            <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-dark-900 dark:text-white">{{ __('pages.create_new_invoice') }}</span>
                    </a>
                    <a href="{{ route('cash-flow.index') }}" wire:navigate class="flex items-center gap-3 p-3 rounded-lg hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors group">
                        <div class="h-8 w-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center group-hover:bg-emerald-200 dark:group-hover:bg-emerald-900/50 transition-colors">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-dark-900 dark:text-white">{{ __('pages.view_cash_flow') }}</span>
                    </a>
                </div>
            </div>
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
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
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
                            'rgba(156, 163, 175, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(251, 191, 36, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderWidth: 2,
                        borderColor: isDark ? '#18181b' : '#ffffff'
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
                            backgroundColor: 'rgba(59, 130, 246, 0.7)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1,
                            borderRadius: 6
                        },
                        {
                            label: '{{ __("pages.profit") }}',
                            data: data.map(item => item.profit),
                            backgroundColor: 'rgba(34, 197, 94, 0.7)',
                            borderColor: 'rgb(34, 197, 94)',
                            borderWidth: 1,
                            borderRadius: 6
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
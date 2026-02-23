<div class="space-y-6">

    {{-- ═══════════════════════════════════════════
        HEADER
    ═══════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.financial_dashboard') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                {{ __('pages.financial_dashboard_desc') }}
            </p>
        </div>

        {{-- Period Filter --}}
        <div class="flex flex-col items-end gap-1 w-full sm:w-auto">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                <x-select.native wire:model.live="period" class="w-full sm:w-48">
                    <option value="this_month">{{ __('pages.this_month') }}</option>
                    <option value="last_month">{{ __('pages.last_month') }}</option>
                    <option value="this_quarter">{{ __('pages.this_quarter') }}</option>
                    <option value="last_quarter">{{ __('pages.last_quarter') }}</option>
                    <option value="this_year">{{ __('pages.this_year') }}</option>
                    <option value="last_year">{{ __('pages.last_year') }}</option>
                    <option value="custom">{{ __('pages.custom') }}</option>
                </x-select.native>

                @if ($period === 'custom')
                    <div class="flex gap-2">
                        <x-input type="date" wire:model.live="startDate" placeholder="{{ __('pages.from') }}" class="w-full sm:w-auto" />
                        <x-input type="date" wire:model.live="endDate" placeholder="{{ __('pages.to') }}" class="w-full sm:w-auto" />
                    </div>
                @endif
            </div>
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $this->getPeriodDateRange() }}</span>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
        ROW 1 — 4 QUICK STATS (horizontal layout, design system standard)
    ═══════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        {{-- Total Saldo --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="wallet" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="min-w-0">
                    <p class="text-sm text-dark-600 dark:text-dark-400 truncate">{{ __('pages.total_balance') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50 truncate">{{ $this->formatCurrency($this->totalBankBalance) }}</p>
                    <p class="text-xs text-green-600 dark:text-green-400 flex items-center gap-1 mt-0.5">
                        <x-icon name="arrow-trending-up" class="h-3 w-3" />
                        {{ __('pages.bank_balance') }}
                    </p>
                </div>
            </div>
        </x-card>

        {{-- Pemasukan --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="arrow-down-left" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div class="min-w-0">
                    <p class="text-sm text-dark-600 dark:text-dark-400 truncate">{{ __('pages.income') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50 truncate">{{ $this->formatCurrency($this->incomeThisMonth) }}</p>
                    <p class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ $this->getPeriodLabel() }}</p>
                </div>
            </div>
        </x-card>

        {{-- Pengeluaran --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="arrow-up-right" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div class="min-w-0">
                    <p class="text-sm text-dark-600 dark:text-dark-400 truncate">{{ __('pages.expenses') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50 truncate">{{ $this->formatCurrency($this->expensesThisMonth) }}</p>
                    <p class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ $this->getPeriodLabel() }}</p>
                </div>
            </div>
        </x-card>

        {{-- Invoice Tertunda --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-amber-50 dark:bg-amber-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="document-text" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div class="min-w-0">
                    <p class="text-sm text-dark-600 dark:text-dark-400 truncate">{{ __('pages.pending_invoices') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50 truncate">{{ $this->formatCurrency($this->pendingInvoicesAmount) }}</p>
                    <p class="text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1 mt-0.5">
                        <x-icon name="clock" class="h-3 w-3" />
                        {{ __('pages.n_invoices', ['count' => $this->pendingInvoicesCount]) }} {{ __('pages.waiting') }}
                    </p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- ═══════════════════════════════════════════
        ROW 2 — CASH FLOW CHART (full width)
    ═══════════════════════════════════════════ --}}
    <x-card class="hover:shadow-lg transition-shadow">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="chart-bar" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h3 class="font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.cash_flow') }}</h3>
                    <p class="text-xs text-dark-500 dark:text-dark-400">{{ $this->getChartPeriodLabel() }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                {{-- Legend --}}
                <div class="hidden sm:flex items-center gap-4 text-xs">
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        <span class="text-dark-600 dark:text-dark-400">{{ __('pages.income') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <span class="text-dark-600 dark:text-dark-400">{{ __('pages.expenses') }}</span>
                    </div>
                </div>
                {{-- Period Toggle --}}
                <div class="flex items-center rounded-lg border border-gray-200 dark:border-dark-600 text-xs overflow-hidden">
                    <button wire:click="$set('chartPeriod', 'this_month')"
                        class="px-3 py-1.5 transition-colors {{ $chartPeriod === 'this_month' ? 'bg-primary-600 text-white' : 'text-dark-600 dark:text-dark-400 hover:bg-gray-100 dark:hover:bg-dark-700' }}">
                        {{ __('pages.this_month') }}
                    </button>
                    <button wire:click="$set('chartPeriod', '6_months')"
                        class="px-3 py-1.5 border-x border-gray-200 dark:border-dark-600 transition-colors {{ $chartPeriod === '6_months' ? 'bg-primary-600 text-white' : 'text-dark-600 dark:text-dark-400 hover:bg-gray-100 dark:hover:bg-dark-700' }}">
                        6 {{ __('pages.six_months') }}
                    </button>
                    <button wire:click="$set('chartPeriod', '12_months')"
                        class="px-3 py-1.5 transition-colors {{ $chartPeriod === '12_months' ? 'bg-primary-600 text-white' : 'text-dark-600 dark:text-dark-400 hover:bg-gray-100 dark:hover:bg-dark-700' }}">
                        12 {{ __('pages.twelve_months') }}
                    </button>
                </div>
            </div>
        </div>
        <div class="h-[280px]"
            x-data="dashboardChart('cashFlow', @js($this->cashFlowChart))"
            x-init="renderCashFlow()"
            wire:ignore>
            <canvas x-ref="canvas"></canvas>
        </div>
    </x-card>

    {{-- ═══════════════════════════════════════════
        ROW 3 — REVENUE vs EXPENSES (2/3) + EXPENSE BY CATEGORY (1/3)
        Lebih proporsional: chart bar besar di kiri, pie chart di kanan
    ═══════════════════════════════════════════ --}}
    <div class="grid gap-4 lg:grid-cols-5 items-start">

        {{-- Revenue vs Expenses — lebar 3/5 --}}
        <div class="lg:col-span-3">
            <x-card class="hover:shadow-lg transition-shadow h-full">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <x-icon name="presentation-chart-bar" class="h-5 w-5 text-green-600 dark:text-green-400" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.revenue_vs_expenses') }}</h3>
                            <p class="text-xs text-dark-500 dark:text-dark-400">{{ $this->getChartPeriodLabel() }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-xs">
                        <div class="flex items-center gap-1.5">
                            <div class="w-3 h-3 rounded bg-emerald-500"></div>
                            <span class="text-dark-600 dark:text-dark-400">{{ __('pages.revenue') }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-3 h-3 rounded bg-red-500"></div>
                            <span class="text-dark-600 dark:text-dark-400">{{ __('pages.expenses') }}</span>
                        </div>
                    </div>
                </div>
                <div class="h-[240px]"
                    x-data="dashboardChart('revenueExpense', @js($this->revenueVsExpensesChart))"
                    x-init="renderRevenueExpense()"
                    wire:ignore>
                    <canvas x-ref="canvas"></canvas>
                </div>
            </x-card>
        </div>

        {{-- Expense by Category — lebar 2/5 --}}
        <div class="lg:col-span-2">
            <x-card class="hover:shadow-lg transition-shadow h-full">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-10 w-10 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <x-icon name="chart-pie" class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.expense_by_category') }}</h3>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ ucfirst($this->getPeriodLabel()) }}</p>
                    </div>
                </div>
                <div class="h-[160px]"
                    x-data="dashboardChart('expenseCategory', @js($this->expensesByCategoryChart))"
                    x-init="renderExpenseCategory()"
                    wire:ignore>
                    <canvas x-ref="canvas"></canvas>
                </div>
                <div class="mt-3 space-y-2">
                    @forelse ($this->expensesByCategoryChart as $category)
                        <div class="flex items-center justify-between text-xs py-1">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $category['color'] }}"></div>
                                <span class="text-dark-600 dark:text-dark-400 truncate">{{ $category['name'] }}</span>
                            </div>
                            <span class="font-semibold text-dark-900 dark:text-dark-50 flex-shrink-0 ml-2">{{ $this->formatCurrency($category['value']) }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-dark-500 dark:text-dark-400 text-center py-4">{{ __('pages.no_data_yet') }}</p>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
        ROW 4 — BANK ACCOUNTS (1/3) + PENDING INVOICES (1/3) + RECENT TRANSACTIONS (1/3)
        3 kolom sejajar — proporsi seimbang
    ═══════════════════════════════════════════ --}}
    <div class="grid gap-4 lg:grid-cols-3 items-start">

        {{-- Bank Accounts --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <x-icon name="building-library" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.account_balances') }}</h3>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.position_per_bank') }}</p>
                    </div>
                </div>
                <a href="{{ route('bank-accounts.index') }}" wire:navigate
                   class="text-xs text-primary-600 dark:text-primary-400 hover:underline flex items-center gap-1 flex-shrink-0">
                    {{ __('pages.manage') }} <x-icon name="arrow-up-right" class="h-3 w-3" />
                </a>
            </div>
            <div class="space-y-2">
                @forelse ($this->bankAccounts as $account)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-dark-700 hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="h-8 w-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center flex-shrink-0">
                                <x-icon name="credit-card" class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-sm text-dark-900 dark:text-dark-50 truncate">{{ $account['name'] }}</p>
                                <p class="text-[10px] text-dark-500 dark:text-dark-400 truncate">{{ $account['bank'] }}</p>
                            </div>
                        </div>
                        <p class="font-semibold text-sm text-dark-900 dark:text-dark-50 flex-shrink-0 ml-2">{{ $this->formatCurrency($account['balance']) }}</p>
                    </div>
                @empty
                    <p class="text-xs text-dark-500 dark:text-dark-400 text-center py-6">{{ __('pages.no_accounts_yet') }}</p>
                @endforelse

                @if ($this->bankAccounts->count() > 0)
                    <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/30 mt-1">
                        <span class="text-sm font-medium text-dark-700 dark:text-dark-300">{{ __('common.total') }}</span>
                        <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $this->formatCurrency($this->totalBankBalance) }}</span>
                    </div>
                @endif
            </div>
        </x-card>

        {{-- Pending Invoices --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-amber-50 dark:bg-amber-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <x-icon name="document-text" class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.pending_invoices') }}</h3>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.needs_followup') }}</p>
                    </div>
                </div>
                <a href="{{ route('invoices.index') }}" wire:navigate
                   class="text-xs text-primary-600 dark:text-primary-400 hover:underline flex items-center gap-1 flex-shrink-0">
                    {{ __('pages.view_all') }} <x-icon name="arrow-up-right" class="h-3 w-3" />
                </a>
            </div>
            <div class="space-y-2">
                @forelse ($this->pendingInvoicesList as $invoice)
                    @php
                        $isOverdue = $invoice['days_until_due'] < 0;
                        $isWarning = $invoice['days_until_due'] >= 0 && $invoice['days_until_due'] < 7;
                    @endphp
                    <div class="flex items-center justify-between p-3 rounded-xl border transition-colors
                        {{ $isOverdue
                            ? 'bg-red-50 border-red-100 dark:bg-red-900/10 dark:border-red-900/30'
                            : ($isWarning
                                ? 'bg-amber-50 border-amber-100 dark:bg-amber-900/10 dark:border-amber-900/30'
                                : 'bg-gray-50 border-transparent dark:bg-dark-700') }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0
                                {{ $isOverdue ? 'bg-red-100 dark:bg-red-900/20' : ($isWarning ? 'bg-amber-100 dark:bg-amber-900/20' : 'bg-gray-200 dark:bg-dark-600') }}">
                                @if ($isOverdue)
                                    <x-icon name="exclamation-triangle" class="h-4 w-4 text-red-600 dark:text-red-400" />
                                @elseif ($isWarning)
                                    <x-icon name="clock" class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                                @else
                                    <x-icon name="document-check" class="h-4 w-4 text-dark-500 dark:text-dark-400" />
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-sm text-dark-900 dark:text-dark-50 truncate">{{ $invoice['client'] }}</p>
                                <p class="text-[10px] text-dark-500 dark:text-dark-400">{{ $invoice['invoice_number'] }}</p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-2">
                            <p class="font-semibold text-sm text-dark-900 dark:text-dark-50">{{ $this->formatCurrency($invoice['amount']) }}</p>
                            <span class="text-[10px] font-medium
                                {{ $isOverdue ? 'text-red-600 dark:text-red-400' : ($isWarning ? 'text-amber-600 dark:text-amber-400' : 'text-dark-500 dark:text-dark-400') }}">
                                {{ $isOverdue
                                    ? __('pages.overdue_days', ['count' => abs($invoice['days_until_due'])])
                                    : __('pages.days_remaining', ['count' => $invoice['days_until_due']]) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-dark-500 dark:text-dark-400 text-center py-6">{{ __('pages.no_pending_invoices') }}</p>
                @endforelse
            </div>
        </x-card>

        {{-- Recent Transactions --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <x-icon name="arrow-trending-up" class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.recent_transactions') }}</h3>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.recent_activity') }}</p>
                    </div>
                </div>
                <a href="{{ route('cash-flow.index') }}" wire:navigate
                   class="text-xs text-primary-600 dark:text-primary-400 hover:underline flex items-center gap-1 flex-shrink-0">
                    {{ __('pages.view_all') }} <x-icon name="arrow-up-right" class="h-3 w-3" />
                </a>
            </div>
            <div class="space-y-2">
                @forelse ($this->recentTransactions as $transaction)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-dark-700 hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0
                                {{ $transaction['type'] === 'income' ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
                                @if ($transaction['type'] === 'income')
                                    <x-icon name="arrow-down-left" class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                                @else
                                    <x-icon name="arrow-up-right" class="h-4 w-4 text-red-600 dark:text-red-400" />
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-sm text-dark-900 dark:text-dark-50 truncate">{{ $transaction['description'] }}</p>
                                <p class="text-[10px] text-dark-500 dark:text-dark-400">{{ $transaction['date']->diffForHumans() }}</p>
                            </div>
                        </div>
                        <p class="font-semibold text-sm flex-shrink-0 ml-2
                            {{ $transaction['type'] === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $transaction['type'] === 'income' ? '+' : '-' }}{{ $this->formatNumber($transaction['amount']) }}
                        </p>
                    </div>
                @empty
                    <p class="text-xs text-dark-500 dark:text-dark-400 text-center py-6">{{ __('pages.no_transactions_yet') }}</p>
                @endforelse
            </div>
        </x-card>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Translation labels for charts
    const chartLabels = {
        income: @js(__('pages.income')),
        expenses: @js(__('pages.expenses')),
        revenue: @js(__('pages.revenue')),
    };

    // Register immediately if Alpine is already loaded, otherwise wait for alpine:init
    function registerDashboardChart() {
        if (window.__dashboardChartRegistered) return;
        window.__dashboardChartRegistered = true;

        Alpine.data('dashboardChart', (chartType, initialData) => ({
            chart: null,
            data: initialData,
            chartType: chartType,

            isDark() { return document.documentElement.classList.contains('dark') },
            textColor() { return this.isDark() ? '#a1a1aa' : '#71717a' },
            gridColor() { return this.isDark() ? '#3f3f46' : '#f4f4f5' },
            formatRp(value) {
                if (value >= 1000000000) return 'Rp ' + (value / 1000000000).toFixed(1) + 'M';
                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(0) + 'jt';
                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                return 'Rp ' + value;
            },
            tooltipStyle() {
                return {
                    backgroundColor: this.isDark() ? '#27272a' : '#ffffff',
                    titleColor: this.isDark() ? '#fafafa' : '#09090b',
                    bodyColor: this.isDark() ? '#d4d4d8' : '#52525b',
                    borderColor: this.isDark() ? '#52525b' : '#e4e4e7',
                    borderWidth: 1,
                    padding: 10,
                    cornerRadius: 8,
                };
            },

            destroyChart() {
                if (this.chart) {
                    this.chart.destroy();
                    this.chart = null;
                }
            },

            init() {
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
                                label: chartLabels.income,
                                data: this.data.map(d => d.income),
                                borderColor: 'rgb(16, 185, 129)',
                                backgroundColor: 'rgba(16, 185, 129, 0.08)',
                                fill: true, tension: 0.4, borderWidth: 2,
                                pointRadius: 3, pointHoverRadius: 5,
                                pointBackgroundColor: 'rgb(16, 185, 129)',
                            },
                            {
                                label: chartLabels.expenses,
                                data: this.data.map(d => d.expenses),
                                borderColor: 'rgb(239, 68, 68)',
                                backgroundColor: 'rgba(239, 68, 68, 0.06)',
                                fill: true, tension: 0.4, borderWidth: 2,
                                pointRadius: 3, pointHoverRadius: 5,
                                pointBackgroundColor: 'rgb(239, 68, 68)',
                            }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: { ...this.tooltipStyle(), callbacks: { label: (ctx) => ' ' + ctx.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y) } }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { color: self.textColor(), font: { size: 11 }, callback: (v) => self.formatRp(v) },
                                grid: { color: self.gridColor(), drawBorder: false }
                            },
                            x: {
                                ticks: { color: self.textColor(), font: { size: 11 } },
                                grid: { display: false }
                            }
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
                            borderWidth: 0,
                            hoverOffset: 4,
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { ...this.tooltipStyle(), callbacks: { label: (ctx) => ' ' + ctx.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed) } }
                        },
                        cutout: '68%'
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
                            {
                                label: chartLabels.revenue,
                                data: this.data.map(d => d.revenue),
                                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                                borderRadius: { topLeft: 4, topRight: 4 },
                            },
                            {
                                label: chartLabels.expenses,
                                data: this.data.map(d => d.expenses),
                                backgroundColor: 'rgba(239, 68, 68, 0.75)',
                                borderRadius: { topLeft: 4, topRight: 4 },
                            }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: { ...this.tooltipStyle(), callbacks: { label: (ctx) => ' ' + ctx.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y) } }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { color: self.textColor(), font: { size: 11 }, callback: (v) => self.formatRp(v) },
                                grid: { color: self.gridColor(), drawBorder: false }
                            },
                            x: {
                                ticks: { color: self.textColor(), font: { size: 11 } },
                                grid: { display: false }
                            }
                        }
                    }
                });
            },

            destroy() {
                this.destroyChart();
            }
        }));
    }

    if (window.Alpine) {
        registerDashboardChart();
    }
    document.addEventListener('alpine:init', () => registerDashboardChart());
    document.addEventListener('livewire:navigated', () => {
        setTimeout(() => registerDashboardChart(), 50);
    });
</script>
@endpush

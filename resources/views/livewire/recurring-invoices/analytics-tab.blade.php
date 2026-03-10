<div class="space-y-5">

    {{-- Toolbar: Year + Period toggle + timestamp --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            {{-- Year --}}
            <div class="w-28">
                <x-select.styled wire:model.live="selectedYear"
                    :options="$this->yearOptions"
                    select="label:label|value:value" />
            </div>
            {{-- Period toggle --}}
            <div class="inline-flex items-center bg-dark-100 dark:bg-dark-700 rounded-lg p-0.5">
                <button wire:click="$set('period', 'monthly')"
                    class="px-3 py-1.5 text-xs font-medium rounded-md transition-all duration-150
                        {{ $period === 'monthly'
                            ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm'
                            : 'text-dark-500 dark:text-dark-400 hover:text-dark-700 dark:hover:text-dark-200' }}">
                    {{ __('pages.ri_period_monthly') }}
                </button>
                <button wire:click="$set('period', 'quarterly')"
                    class="px-3 py-1.5 text-xs font-medium rounded-md transition-all duration-150
                        {{ $period === 'quarterly'
                            ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm'
                            : 'text-dark-500 dark:text-dark-400 hover:text-dark-700 dark:hover:text-dark-200' }}">
                    {{ __('pages.ri_period_quarterly') }}
                </button>
            </div>
        </div>
        <span class="text-[11px] text-dark-400 dark:text-dark-500">
            {{ __('pages.ri_last_updated', ['datetime' => now()->format('d M Y, H:i')]) }}
        </span>
    </div>

    {{-- KPI Cards --}}
    @php
        $metrics    = $this->revenueMetrics;
        $growth     = $metrics['growth_rate'];
        $isPositive = $growth >= 0;
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

        {{-- Current Year Revenue --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl border border-dark-200 dark:border-dark-600 px-5 py-4">
            <p class="text-[11px] font-medium text-dark-400 dark:text-dark-500 uppercase tracking-wider mb-1.5">
                {{ __('pages.ri_year_revenue_label', ['year' => $selectedYear]) }}
            </p>
            <p class="text-xl font-bold text-dark-900 dark:text-dark-50 tabular-nums">
                Rp {{ number_format($metrics['current_year'] / 1000000, 1) }}M
            </p>
            <p class="text-[11px] text-dark-400 dark:text-dark-500 mt-1 tabular-nums">
                {{ number_format($metrics['current_year'], 0, ',', '.') }}
            </p>
        </div>

        {{-- YoY Growth --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl border border-dark-200 dark:border-dark-600 px-5 py-4">
            <p class="text-[11px] font-medium text-dark-400 dark:text-dark-500 uppercase tracking-wider mb-1.5">
                {{ __('pages.ri_yoy_growth_label') }}
            </p>
            <div class="flex items-center gap-1.5">
                <p class="text-xl font-bold tabular-nums {{ $isPositive ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                    {{ $isPositive ? '+' : '' }}{{ number_format($growth, 1) }}%
                </p>
                <span class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 {{ $isPositive ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
                    <x-icon name="{{ $isPositive ? 'arrow-trending-up' : 'arrow-trending-down' }}"
                        class="w-3 h-3 {{ $isPositive ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}" />
                </span>
            </div>
            <p class="text-[11px] text-dark-400 dark:text-dark-500 mt-1">
                vs {{ (int) $selectedYear - 1 }}: Rp {{ number_format($metrics['previous_year'] / 1000000, 1) }}M
            </p>
        </div>

        {{-- This Month --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl border border-dark-200 dark:border-dark-600 px-5 py-4">
            <p class="text-[11px] font-medium text-dark-400 dark:text-dark-500 uppercase tracking-wider mb-1.5">
                {{ __('pages.ri_this_month_label') }}
            </p>
            <p class="text-xl font-bold text-dark-900 dark:text-dark-50 tabular-nums">
                Rp {{ number_format($metrics['current_month'] / 1000000, 1) }}M
            </p>
            <p class="text-[11px] text-dark-400 dark:text-dark-500 mt-1">
                {{ now()->format('F Y') }}
            </p>
        </div>

        {{-- Monthly Average --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl border border-dark-200 dark:border-dark-600 px-5 py-4">
            <p class="text-[11px] font-medium text-dark-400 dark:text-dark-500 uppercase tracking-wider mb-1.5">
                {{ __('pages.ri_avg_monthly_label') }}
            </p>
            <p class="text-xl font-bold text-dark-900 dark:text-dark-50 tabular-nums">
                Rp {{ number_format($metrics['average_monthly'] / 1000000, 1) }}M
            </p>
            <p class="text-[11px] text-dark-400 dark:text-dark-500 mt-1">
                {{ __('pages.ri_per_month_label') }}
            </p>
        </div>

    </div>

    {{-- Chart + Status Breakdown side by side --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Revenue Chart (2/3) --}}
        <div class="lg:col-span-2 bg-white dark:bg-dark-800 rounded-xl border border-dark-200 dark:border-dark-600 p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                        {{ __('pages.ri_revenue_trend_title', ['period' => $period === 'monthly' ? __('pages.ri_period_monthly') : __('pages.ri_period_quarterly')]) }}
                    </h3>
                    <p class="text-xs text-dark-400 dark:text-dark-500 mt-0.5">{{ $selectedYear }}</p>
                </div>
            </div>
            <div class="h-52">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Status Breakdown (1/3) --}}
        @php $breakdown = $this->statusBreakdown; @endphp
        <div class="bg-white dark:bg-dark-800 rounded-xl border border-dark-200 dark:border-dark-600 p-5 flex flex-col">
            <h3 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-4">
                {{ __('pages.ri_status_breakdown_title') }}
            </h3>

            {{-- Total --}}
            <div class="mb-4 pb-4 border-b border-dark-100 dark:border-dark-700">
                <p class="text-[11px] font-medium text-dark-400 dark:text-dark-500 uppercase tracking-wider mb-1">
                    {{ __('pages.ri_total_label') }}
                </p>
                <p class="text-2xl font-bold text-dark-900 dark:text-dark-50 tabular-nums">
                    {{ number_format($breakdown['total']['count']) }}
                    <span class="text-sm font-normal text-dark-400 ml-0.5">{{ __('pages.ri_invoices_unit') }}</span>
                </p>
                <p class="text-[11px] text-dark-400 dark:text-dark-500 mt-0.5 tabular-nums">
                    Rp {{ number_format($breakdown['total']['revenue'], 0, ',', '.') }}
                </p>
            </div>

            {{-- Published --}}
            <div class="space-y-4 flex-1">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-green-500 shrink-0"></span>
                            <span class="text-xs font-medium text-dark-700 dark:text-dark-300">{{ __('pages.ri_published_label') }}</span>
                        </div>
                        <span class="text-xs font-semibold text-dark-900 dark:text-dark-50 tabular-nums">
                            {{ $breakdown['published']['count'] }}
                            <span class="text-dark-400 font-normal text-[11px]">({{ number_format($breakdown['published']['percentage'], 0) }}%)</span>
                        </span>
                    </div>
                    <div class="h-1.5 w-full bg-dark-100 dark:bg-dark-700 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500 rounded-full transition-all duration-700"
                            style="width: {{ number_format($breakdown['published']['percentage'], 1) }}%"></div>
                    </div>
                    <p class="text-[11px] text-dark-400 dark:text-dark-500 mt-1 tabular-nums">
                        Rp {{ number_format($breakdown['published']['revenue'], 0, ',', '.') }}
                    </p>
                </div>

                {{-- Draft --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
                            <span class="text-xs font-medium text-dark-700 dark:text-dark-300">{{ __('pages.ri_draft_label') }}</span>
                        </div>
                        <span class="text-xs font-semibold text-dark-900 dark:text-dark-50 tabular-nums">
                            {{ $breakdown['draft']['count'] }}
                            <span class="text-dark-400 font-normal text-[11px]">({{ number_format($breakdown['draft']['percentage'], 0) }}%)</span>
                        </span>
                    </div>
                    <div class="h-1.5 w-full bg-dark-100 dark:bg-dark-700 rounded-full overflow-hidden">
                        <div class="h-full bg-amber-400 rounded-full transition-all duration-700"
                            style="width: {{ number_format($breakdown['draft']['percentage'], 1) }}%"></div>
                    </div>
                    <p class="text-[11px] text-dark-400 dark:text-dark-500 mt-1 tabular-nums">
                        Rp {{ number_format($breakdown['draft']['revenue'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Template Performance Table --}}
    @php $templateStats = $this->templateStats; @endphp
    @if (count($templateStats) > 0)
        <div class="bg-white dark:bg-dark-800 rounded-xl border border-dark-200 dark:border-dark-600 overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-dark-100 dark:border-dark-700">
                <div>
                    <h3 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.ri_top_templates_title') }}</h3>
                    <p class="text-xs text-dark-400 dark:text-dark-500 mt-0.5">{{ $selectedYear }}</p>
                </div>
                <span class="text-xs text-dark-400">{{ count($templateStats) }} {{ __('pages.ri_template_unit') }}</span>
            </div>

            {{-- Column headers --}}
            <div class="hidden md:grid grid-cols-12 gap-3 px-5 py-2 bg-dark-50 dark:bg-dark-700/40 border-b border-dark-100 dark:border-dark-700">
                <div class="col-span-5 text-[10px] font-semibold text-dark-400 uppercase tracking-wider">{{ __('pages.ri_template_col') }}</div>
                <div class="col-span-3 text-[10px] font-semibold text-dark-400 uppercase tracking-wider text-right">{{ __('pages.ri_revenue_col') }}</div>
                <div class="col-span-2 text-[10px] font-semibold text-dark-400 uppercase tracking-wider text-center">{{ __('pages.ri_invoice_count_col') }}</div>
                <div class="col-span-1 text-[10px] font-semibold text-dark-400 uppercase tracking-wider text-center">{{ __('pages.ri_success_rate_col') }}</div>
                <div class="col-span-1 text-[10px] font-semibold text-dark-400 uppercase tracking-wider text-right">{{ __('pages.ri_margin_col') }}</div>
            </div>

            {{-- Rows --}}
            @php $maxRevenue = $templateStats[0]['revenue'] ?? 1; @endphp
            @foreach ($templateStats as $index => $tmpl)
                @php
                    $barWidth = $maxRevenue > 0 ? ($tmpl['revenue'] / $maxRevenue) * 100 : 0;
                    $rate     = $tmpl['success_rate'];
                @endphp
                <div class="grid grid-cols-12 gap-3 px-5 py-3 items-center border-b border-dark-50 dark:border-dark-700/40 last:border-0 hover:bg-dark-50/60 dark:hover:bg-dark-700/20 transition-colors duration-100">

                    {{-- Rank + Template name + bar --}}
                    <div class="col-span-12 md:col-span-5 flex items-center gap-3 min-w-0">
                        <span class="w-5 h-5 shrink-0 inline-flex items-center justify-center rounded text-[10px] font-bold
                            {{ $index === 0 ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' : 'bg-dark-100 dark:bg-dark-700 text-dark-500 dark:text-dark-400' }}">
                            {{ $index + 1 }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-dark-900 dark:text-dark-50 truncate leading-tight">{{ $tmpl['name'] }}</p>
                            <p class="text-[10px] text-dark-400 dark:text-dark-500 truncate mt-0.5">{{ $tmpl['client'] }}</p>
                            <div class="mt-1.5 h-0.5 w-full bg-dark-100 dark:bg-dark-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-linear-to-r from-primary-500 to-blue-400 transition-all duration-700"
                                    style="width: {{ number_format($barWidth, 1) }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Revenue --}}
                    <div class="col-span-4 md:col-span-3 text-right">
                        <p class="text-xs font-bold text-dark-900 dark:text-dark-50 tabular-nums">
                            Rp {{ number_format($tmpl['revenue'] / 1000000, 2) }}M
                        </p>
                        <p class="text-[10px] text-dark-400 tabular-nums mt-0.5 hidden md:block">
                            {{ number_format($tmpl['revenue'], 0, ',', '.') }}
                        </p>
                    </div>

                    {{-- Invoice count --}}
                    <div class="col-span-3 md:col-span-2 text-center">
                        <span class="text-xs font-medium text-dark-700 dark:text-dark-300 tabular-nums">
                            <span class="font-bold">{{ $tmpl['published'] }}</span>
                            <span class="text-dark-400">/{{ $tmpl['count'] }}</span>
                        </span>
                    </div>

                    {{-- Success rate --}}
                    <div class="col-span-3 md:col-span-1 text-center">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold tabular-nums
                            {{ $rate >= 80 ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : ($rate >= 50 ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400' : 'bg-dark-100 dark:bg-dark-700 text-dark-500') }}">
                            {{ $rate }}%
                        </span>
                    </div>

                    {{-- Margin --}}
                    <div class="col-span-2 md:col-span-1 text-right">
                        <span class="text-xs font-semibold tabular-nums
                            {{ $tmpl['profit_margin'] >= 30 ? 'text-green-600 dark:text-green-400' : 'text-dark-500 dark:text-dark-400' }}">
                            {{ $tmpl['profit_margin'] }}%
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-dark-800 rounded-xl border border-dark-200 dark:border-dark-600 p-10 text-center">
            <x-icon name="chart-bar" class="w-8 h-8 mx-auto mb-3 text-dark-300 dark:text-dark-600" />
            <p class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ __('pages.ri_no_template_data') }}</p>
            <p class="text-xs text-dark-400 mt-1">{{ $selectedYear }}</p>
        </div>
    @endif

</div>

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        function setupRevenueChart() {
            let revenueChart;

            function isDark() {
                return document.documentElement.classList.contains('dark');
            }

            function palette() {
                const dark = isDark();
                return {
                    bar:     'rgba(59, 130, 246, 0.12)',
                    barHov:  'rgba(59, 130, 246, 0.22)',
                    border:  'rgb(59, 130, 246)',
                    grid:    dark ? '#27272a' : '#f4f4f5',
                    tick:    dark ? '#71717a' : '#a1a1aa',
                    tip_bg:  dark ? '#3f3f46' : '#ffffff',
                    tip_txt: dark ? '#fafafa' : '#09090b',
                    tip_sub: dark ? '#a1a1aa' : '#71717a',
                    tip_brd: dark ? '#52525b' : '#e4e4e7',
                };
            }

            function buildChart(data) {
                const ctx = document.getElementById('revenueChart');
                if (!ctx) return;

                const existing = Chart.getChart(ctx);
                if (existing) existing.destroy();

                const p = palette();
                const labels = data.map(d => d.month ?? d.quarter);

                revenueChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            data: data.map(d => d.revenue),
                            backgroundColor: p.bar,
                            borderColor: p.border,
                            borderWidth: 1.5,
                            borderRadius: 4,
                            borderSkipped: false,
                            hoverBackgroundColor: p.barHov,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: p.tip_bg,
                                titleColor: p.tip_txt,
                                bodyColor: p.tip_sub,
                                borderColor: p.tip_brd,
                                borderWidth: 1,
                                padding: 10,
                                callbacks: {
                                    label: c => 'Rp ' + new Intl.NumberFormat('id-ID').format(c.parsed.y),
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                border: { display: false },
                                ticks: {
                                    color: p.tick,
                                    maxTicksLimit: 5,
                                    callback: v => 'Rp ' + (v / 1000000).toFixed(1) + 'M',
                                },
                                grid: { color: p.grid }
                            },
                            x: {
                                border: { display: false },
                                ticks: { color: p.tick },
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            buildChart(@json($this->revenueChart));

            document.addEventListener('chartDataUpdated', e => buildChart(e.detail[0].chartData));

            new MutationObserver(() => {
                if (!revenueChart) return;
                const currentData = revenueChart.data.labels.map((label, i) => ({
                    [revenueChart.data.labels.length > 4 ? 'month' : 'quarter']: label,
                    revenue: revenueChart.data.datasets[0].data[i],
                }));
                setTimeout(() => buildChart(currentData), 80);
            }).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

            window.addEventListener('beforeunload', () => revenueChart?.destroy());
        }

        document.addEventListener('livewire:navigated', setupRevenueChart);
    </script>
@endpush

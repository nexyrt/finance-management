<?php

namespace App\Livewire\RecurringInvoices;

use App\Models\RecurringInvoice;
use App\Models\RecurringTemplate;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class AnalyticsTab extends Component
{
    public string $selectedYear;
    public string $period = 'monthly';

    public function placeholder(): View
    {
        return view('livewire.placeholders.stats-skeleton');
    }

    public function mount(): void
    {
        $this->selectedYear = (string) now()->year;
    }

    public function updatedSelectedYear()
    {
        $this->dispatchChartUpdate();
    }

    public function updatedPeriod()
    {
        $this->dispatchChartUpdate();
    }

    private function dispatchChartUpdate(): void
    {
        $this->dispatch('chartDataUpdated', [
            'chartData' => $this->revenueChart,
        ]);
    }

    #[Computed]
    public function yearOptions(): array
    {
        $currentYear = now()->year;
        return collect(range($currentYear - 2, $currentYear + 1))->map(fn($year) => [
            'label' => (string) $year,
            'value' => (string) $year
        ])->toArray();
    }

    /**
     * Load all invoices for a year in a single query, keyed by month.
     * Used internally to avoid repeated DB queries across methods.
     */
    private function getYearInvoicesByMonth(int $year): \Illuminate\Support\Collection
    {
        return RecurringInvoice::whereYear('scheduled_date', $year)
            ->get(['id', 'scheduled_date', 'invoice_data', 'status'])
            ->groupBy(fn($inv) => (int) $inv->scheduled_date->format('n'));
    }

    #[Computed]
    public function revenueMetrics(): array
    {
        $currentYear = (int) $this->selectedYear;
        $previousYear = $currentYear - 1;
        $currentMonth = now()->month;

        // Two queries total (current year + previous year)
        $currentByMonth = $this->getYearInvoicesByMonth($currentYear);
        $previousByMonth = $this->getYearInvoicesByMonth($previousYear);

        $currentTotal = $currentByMonth->flatten()->sum(fn($inv) => $inv->invoice_data['total_amount'] ?? 0);
        $previousTotal = $previousByMonth->flatten()->sum(fn($inv) => $inv->invoice_data['total_amount'] ?? 0);

        $growthRate = $previousTotal > 0
            ? (($currentTotal - $previousTotal) / $previousTotal) * 100
            : 0;

        $currentMonthRevenue = ($currentByMonth[$currentMonth] ?? collect())
            ->sum(fn($inv) => $inv->invoice_data['total_amount'] ?? 0);

        return [
            'current_year' => $currentTotal,
            'previous_year' => $previousTotal,
            'growth_rate' => $growthRate,
            'current_month' => $currentMonthRevenue,
            'average_monthly' => $currentTotal > 0 ? $currentTotal / 12 : 0,
        ];
    }

    #[Computed]
    public function revenueChart(): array
    {
        if ($this->period === 'monthly') {
            return $this->getMonthlyRevenueChart();
        }

        return $this->getQuarterlyRevenueChart();
    }

    /**
     * Single query — merged from templateEfficiencyChart + templatePerformance.
     * Both previously ran identical DB queries separately.
     */
    #[Computed]
    public function templateStats(): array
    {
        $templates = RecurringTemplate::with([
            'recurringInvoices' => fn($q) => $q->whereYear('scheduled_date', (int) $this->selectedYear)
                ->select(['id', 'template_id', 'status', 'invoice_data']),
            'client:id,name',
        ])->get(['id', 'template_name', 'client_id']);

        return $templates->map(function ($template) {
            $invoices      = $template->recurringInvoices;
            $total         = $invoices->count();
            $published     = $invoices->where('status', 'published')->count();
            $revenue       = $invoices->sum(fn($inv) => $inv->invoice_data['total_amount'] ?? 0);
            $totalProfit   = $invoices->sum(function ($inv) {
                return collect($inv->invoice_data['items'] ?? [])
                    ->sum(fn($item) => ($item['amount'] ?? 0) - ($item['cogs_amount'] ?? 0));
            });
            $successRate   = $total > 0 ? ($published / $total) * 100 : 0;
            $profitMargin  = $revenue > 0 ? ($totalProfit / $revenue) * 100 : 0;

            return [
                'name'          => $template->template_name,
                'client'        => $template->client->name,
                'revenue'       => $revenue,
                'count'         => $total,
                'published'     => $published,
                'success_rate'  => round($successRate, 1),
                'profit_margin' => round($profitMargin, 1),
            ];
        })
        ->sortByDesc('revenue')
        ->values()
        ->toArray();
    }

    #[Computed]
    public function statusBreakdown(): array
    {
        // Single query — group in PHP, no loop queries
        $invoices = RecurringInvoice::whereYear('scheduled_date', (int) $this->selectedYear)
            ->get(['id', 'status', 'invoice_data']);

        $draft = $invoices->where('status', 'draft');
        $published = $invoices->where('status', 'published');
        $total = $invoices->count();

        $draftRevenue = $draft->sum(fn($inv) => $inv->invoice_data['total_amount'] ?? 0);
        $publishedRevenue = $published->sum(fn($inv) => $inv->invoice_data['total_amount'] ?? 0);

        return [
            'draft' => [
                'count' => $draft->count(),
                'revenue' => $draftRevenue,
                'percentage' => $total > 0 ? ($draft->count() / $total) * 100 : 0,
            ],
            'published' => [
                'count' => $published->count(),
                'revenue' => $publishedRevenue,
                'percentage' => $total > 0 ? ($published->count() / $total) * 100 : 0,
            ],
            'total' => [
                'count' => $total,
                'revenue' => $draftRevenue + $publishedRevenue,
            ],
        ];
    }

    private function getMonthlyRevenueChart(): array
    {
        $year = (int) $this->selectedYear;

        // Single query for the whole year, grouped by month in PHP
        $byMonth = $this->getYearInvoicesByMonth($year);

        return collect(range(1, 12))->map(function ($month) use ($year, $byMonth) {
            $monthRevenue = ($byMonth[$month] ?? collect())
                ->sum(fn($inv) => $inv->invoice_data['total_amount'] ?? 0);

            return [
                'month' => Carbon::create($year, $month)->format('M'),
                'revenue' => $monthRevenue,
                'formatted' => 'Rp ' . number_format($monthRevenue / 1000000, 1) . 'M',
            ];
        })->toArray();
    }

    private function getQuarterlyRevenueChart(): array
    {
        $year = (int) $this->selectedYear;

        // Single query for the whole year, grouped by quarter in PHP
        $byMonth = $this->getYearInvoicesByMonth($year);

        return collect(range(1, 4))->map(function ($quarter) use ($year, $byMonth) {
            $startMonth = ($quarter - 1) * 3 + 1;
            $endMonth = $quarter * 3;

            $quarterRevenue = collect(range($startMonth, $endMonth))
                ->sum(fn($month) => ($byMonth[$month] ?? collect())
                    ->sum(fn($inv) => $inv->invoice_data['total_amount'] ?? 0));

            return [
                'quarter' => "Q{$quarter}",
                'revenue' => $quarterRevenue,
                'formatted' => 'Rp ' . number_format($quarterRevenue / 1000000, 1) . 'M',
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.recurring-invoices.analytics-tab');
    }
}
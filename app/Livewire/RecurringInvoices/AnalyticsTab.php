<?php

namespace App\Livewire\RecurringInvoices;

use App\Models\RecurringInvoice;
use App\Models\RecurringTemplate;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Carbon\Carbon;

class AnalyticsTab extends Component
{
    public string $selectedYear;
    public string $period = 'monthly';

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

    #[Computed]
    public function revenueMetrics(): array
    {
        $currentYear = (int) $this->selectedYear;
        $previousYear = $currentYear - 1;

        $currentData = $this->getYearRevenue($currentYear);
        $previousData = $this->getYearRevenue($previousYear);

        $currentTotal = $currentData['total'];
        $previousTotal = $previousData['total'];

        $growthRate = $previousTotal > 0
            ? (($currentTotal - $previousTotal) / $previousTotal) * 100
            : 0;

        return [
            'current_year' => $currentTotal,
            'previous_year' => $previousTotal,
            'growth_rate' => $growthRate,
            'current_month' => $this->getCurrentMonthRevenue(),
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

    #[Computed]
    public function templateEfficiencyChart(): array
    {
        $templates = RecurringTemplate::with([
            'recurringInvoices' => function ($query) {
                $query->whereYear('scheduled_date', (int) $this->selectedYear);
            },
            'client'
        ])->get();

        return $templates->map(function ($template) {
            $invoices = $template->recurringInvoices;
            $totalGenerated = $invoices->count();
            $published = $invoices->where('status', 'published')->count();

            $revenue = $invoices->sum(function ($invoice) {
                return $invoice->invoice_data['total_amount'] ?? 0;
            });

            $totalProfit = $invoices->sum(function ($invoice) {
                $items = $invoice->invoice_data['items'] ?? [];
                return collect($items)->sum(function ($item) {
                    return ($item['amount'] ?? 0) - ($item['cogs_amount'] ?? 0);
                });
            });

            $successRate = $totalGenerated > 0 ? ($published / $totalGenerated) * 100 : 0;
            $profitMargin = $revenue > 0 ? ($totalProfit / $revenue) * 100 : 0;

            return [
                'name' => $template->template_name,
                'success_rate' => round($successRate, 1),
                'profit_margin' => round($profitMargin, 1),
                'revenue' => $revenue,
            ];
        })->toArray();
    }

    #[Computed]
    public function templatePerformance(): array
    {
        $templates = RecurringTemplate::with([
            'recurringInvoices' => function ($query) {
                $query->whereYear('scheduled_date', (int) $this->selectedYear);
            },
            'client'
        ])->get();

        return $templates->map(function ($template) {
            $invoices = $template->recurringInvoices;
            $totalGenerated = $invoices->count();

            $revenue = $invoices->sum(function ($invoice) {
                return $invoice->invoice_data['total_amount'] ?? 0;
            });

            return [
                'name' => $template->template_name,
                'client' => $template->client->name,
                'revenue' => $revenue,
                'count' => $totalGenerated,
            ];
        })
            ->sortByDesc('revenue')
            ->take(5)
            ->values()
            ->toArray();
    }

    #[Computed]
    public function statusBreakdown(): array
    {
        $invoices = RecurringInvoice::whereYear('scheduled_date', (int) $this->selectedYear)->get();

        $draft = $invoices->where('status', 'draft');
        $published = $invoices->where('status', 'published');

        $draftRevenue = $draft->sum(function ($invoice) {
            return $invoice->invoice_data['total_amount'] ?? 0;
        });

        $publishedRevenue = $published->sum(function ($invoice) {
            return $invoice->invoice_data['total_amount'] ?? 0;
        });

        $totalRevenue = $draftRevenue + $publishedRevenue;

        return [
            'draft' => [
                'count' => $draft->count(),
                'revenue' => $draftRevenue,
                'percentage' => $invoices->count() > 0 ? ($draft->count() / $invoices->count()) * 100 : 0,
            ],
            'published' => [
                'count' => $published->count(),
                'revenue' => $publishedRevenue,
                'percentage' => $invoices->count() > 0 ? ($published->count() / $invoices->count()) * 100 : 0,
            ],
            'total' => [
                'count' => $invoices->count(),
                'revenue' => $totalRevenue,
            ],
        ];
    }

    private function getYearRevenue(int $year): array
    {
        $invoices = RecurringInvoice::whereYear('scheduled_date', $year)->get();

        $totalRevenue = $invoices->sum(function ($invoice) {
            return $invoice->invoice_data['total_amount'] ?? 0;
        });

        return [
            'total' => $totalRevenue,
            'count' => $invoices->count(),
        ];
    }

    private function getCurrentMonthRevenue(): int
    {
        return RecurringInvoice::whereYear('scheduled_date', (int) $this->selectedYear)
            ->whereMonth('scheduled_date', now()->month)
            ->get()
            ->sum(function ($invoice) {
                return $invoice->invoice_data['total_amount'] ?? 0;
            });
    }

    private function getMonthlyRevenueChart(): array
    {
        $data = [];
        $year = (int) $this->selectedYear;

        for ($month = 1; $month <= 12; $month++) {
            $monthRevenue = RecurringInvoice::whereYear('scheduled_date', $year)
                ->whereMonth('scheduled_date', $month)
                ->get()
                ->sum(function ($invoice) {
                    return $invoice->invoice_data['total_amount'] ?? 0;
                });

            $data[] = [
                'month' => Carbon::create($year, $month)->format('M'),
                'revenue' => $monthRevenue,
                'formatted' => 'Rp ' . number_format($monthRevenue / 1000000, 1) . 'M',
            ];
        }

        return $data;
    }

    private function getQuarterlyRevenueChart(): array
    {
        $data = [];
        $year = (int) $this->selectedYear;

        for ($quarter = 1; $quarter <= 4; $quarter++) {
            $quarterRevenue = 0;
            $startMonth = ($quarter - 1) * 3 + 1;
            $endMonth = $quarter * 3;

            for ($month = $startMonth; $month <= $endMonth; $month++) {
                $monthRevenue = RecurringInvoice::whereYear('scheduled_date', $year)
                    ->whereMonth('scheduled_date', $month)
                    ->get()
                    ->sum(function ($invoice) {
                        return $invoice->invoice_data['total_amount'] ?? 0;
                    });
                $quarterRevenue += $monthRevenue;
            }

            $data[] = [
                'quarter' => "Q{$quarter}",
                'revenue' => $quarterRevenue,
                'formatted' => 'Rp ' . number_format($quarterRevenue / 1000000, 1) . 'M',
            ];
        }

        return $data;
    }

    public function render()
    {
        return view('livewire.recurring-invoices.analytics-tab');
    }
}
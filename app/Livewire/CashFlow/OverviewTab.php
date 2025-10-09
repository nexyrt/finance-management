<?php

namespace App\Livewire\CashFlow;

use App\Models\BankTransaction;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Carbon\Carbon;

class OverviewTab extends Component
{
    public ?string $period = 'last_year';

    #[Computed]
    public function stats(): array
    {
        $startDate = $this->getStartDate();
        $endDate = now();

        // 💰 INCOME CALCULATION (Fixed - Total Profit based)
        // 1. Bank Income: BankTransaction credit dengan category type='income'
        $bankIncome = BankTransaction::where('transaction_type', 'credit')
            ->whereHas('category', function ($query) {
                $query->where('type', 'income');
            })
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        // 2. Invoice Profit: Total Revenue - COGS - Tax Deposits
        $totalRevenue = Invoice::whereBetween('issue_date', [$startDate, $endDate])
            ->sum('total_amount');

        $totalCogs = InvoiceItem::whereHas('invoice', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('issue_date', [$startDate, $endDate]);
        })
            ->where('is_tax_deposit', false)
            ->sum('cogs_amount');

        $totalTaxDeposits = InvoiceItem::whereHas('invoice', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('issue_date', [$startDate, $endDate]);
        })
            ->where('is_tax_deposit', true)
            ->sum('amount');

        $invoiceProfit = $totalRevenue - $totalCogs - $totalTaxDeposits;
        $totalIncome = $bankIncome + $invoiceProfit;

        // 💸 EXPENSE CALCULATION
        $totalExpenses = BankTransaction::where('transaction_type', 'debit')
            ->whereHas('category', function ($query) {
                $query->where('type', 'expense');
            })
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        // 📊 NET CASH FLOW
        $netCashFlow = $totalIncome - $totalExpenses;

        // 🔄 TRANSFERS
        $totalTransfers = BankTransaction::where('transaction_type', 'debit')
            ->whereHas('category', function ($query) {
                $query->where('type', 'transfer');
            })
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        return [
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_cash_flow' => $netCashFlow,
            'total_transfers' => $totalTransfers,
        ];
    }

    #[Computed]
    public function trendChartData(): array
    {
        $startDate = $this->getStartDate();
        $endDate = now();
        $months = $this->generateMonthLabels($startDate, $endDate);

        $chartData = [];
        foreach ($months as $month) {
            $monthStart = Carbon::parse($month['start']);
            $monthEnd = Carbon::parse($month['end']);

            // 💰 INCOME for this month
            // 1. Bank Income
            $bankIncome = BankTransaction::where('transaction_type', 'credit')
                ->whereHas('category', function ($query) {
                    $query->where('type', 'income');
                })
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount');

            // 2. Invoice Profit untuk bulan ini
            $monthRevenue = Invoice::whereBetween('issue_date', [$monthStart, $monthEnd])
                ->sum('total_amount');

            $monthCogs = InvoiceItem::whereHas('invoice', function ($query) use ($monthStart, $monthEnd) {
                $query->whereBetween('issue_date', [$monthStart, $monthEnd]);
            })
                ->where('is_tax_deposit', false)
                ->sum('cogs_amount');

            $monthTaxDeposits = InvoiceItem::whereHas('invoice', function ($query) use ($monthStart, $monthEnd) {
                $query->whereBetween('issue_date', [$monthStart, $monthEnd]);
            })
                ->where('is_tax_deposit', true)
                ->sum('amount');

            $monthProfit = $monthRevenue - $monthCogs - $monthTaxDeposits;
            $income = $bankIncome + $monthProfit;

            // 💸 EXPENSES for this month
            $expenses = BankTransaction::where('transaction_type', 'debit')
                ->whereHas('category', function ($query) {
                    $query->where('type', 'expense');
                })
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount');

            $chartData[] = [
                'month' => $month['label'],
                'income' => $income,
                'expenses' => $expenses,
            ];
        }

        return $chartData;
    }

    #[Computed]
    public function categoryChartData(): array
    {
        $startDate = $this->getStartDate();
        $endDate = now();

        // 📊 Category breakdown hanya untuk EXPENSES
        $expenses = BankTransaction::with('category')
            ->where('transaction_type', 'debit')
            ->whereHas('category', function ($query) {
                $query->where('type', 'expense');
            })
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get()
            ->groupBy(fn($t) => $t->category?->label ?? 'Uncategorized')
            ->map(fn($transactions) => [
                'category' => $transactions->first()->category?->label ?? 'Uncategorized',
                'total' => $transactions->sum('amount'),
            ])
            ->sortByDesc('total')
            ->values()
            ->toArray();

        return $expenses;
    }

    #[Computed]
    public function recentTransactions()
    {
        // 📝 Recent transactions: exclude transfers & adjustments untuk clarity
        return BankTransaction::with(['bankAccount', 'category'])
            ->whereHas('category', function ($query) {
                $query->whereIn('type', ['income', 'expense']);
            })
            ->latest('transaction_date')
            ->take(10)
            ->get();
    }

    private function getStartDate(): Carbon
    {
        return match ($this->period) {
            'this_month' => now()->startOfMonth(),
            'last_3_months' => now()->subMonths(2)->startOfMonth(),
            'last_year' => now()->subMonths(11)->startOfMonth(),
            default => now()->startOfMonth(),
        };
    }

    private function generateMonthLabels(Carbon $startDate, Carbon $endDate): array
    {
        $months = [];
        $current = $startDate->copy()->startOfMonth();

        while ($current <= $endDate) {
            $months[] = [
                'label' => $current->format('M Y'),
                'start' => $current->copy()->startOfMonth()->format('Y-m-d'),
                'end' => $current->copy()->endOfMonth()->format('Y-m-d'),
            ];
            $current->addMonth();
        }

        return $months;
    }

    public function updatedPeriod(): void
    {
        $this->dispatch('chartDataUpdated', [
            'trendData' => $this->trendChartData,
            'categoryData' => $this->categoryChartData,
        ]);
    }

    public function render(): View
    {
        return view('livewire.cash-flow.overview-tab');
    }
}
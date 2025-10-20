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

    // Dispatch event when period changes
    public function updatedPeriod(): void
    {
        $this->dispatch('charts-updated', [
            'monthlyData' => $this->monthlyTrendData,
            'categoryData' => $this->expenseByCategoryData
        ]);
    }

    #[Computed]
    public function stats(): array
    {
        $startDate = $this->getStartDate();
        $endDate = now();

        // Income: Bank income + Invoice profit
        $bankIncome = BankTransaction::where('transaction_type', 'credit')
            ->whereHas('category', fn($q) => $q->where('type', 'income'))
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $totalRevenue = Invoice::whereBetween('issue_date', [$startDate, $endDate])->sum('total_amount');
        $totalCogs = InvoiceItem::whereHas('invoice', fn($q) => $q->whereBetween('issue_date', [$startDate, $endDate]))
            ->where('is_tax_deposit', false)
            ->sum('cogs_amount');
        $totalTaxDeposits = InvoiceItem::whereHas('invoice', fn($q) => $q->whereBetween('issue_date', [$startDate, $endDate]))
            ->where('is_tax_deposit', true)
            ->sum('amount');

        $invoiceProfit = $totalRevenue - $totalCogs - $totalTaxDeposits;
        $totalIncome = $bankIncome + $invoiceProfit;

        // Expenses
        $totalExpenses = BankTransaction::where('transaction_type', 'debit')
            ->whereHas('category', fn($q) => $q->where('type', 'expense'))
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        // Transfers
        $totalTransfers = BankTransaction::where('transaction_type', 'debit')
            ->whereHas('category', fn($q) => $q->where('type', 'transfer'))
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        return [
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_cash_flow' => $totalIncome - $totalExpenses,
            'total_transfers' => $totalTransfers,
        ];
    }

    #[Computed]
    public function monthlyTrendData(): array
    {
        $startDate = $this->getStartDate();
        $endDate = now();
        $months = $this->generateMonthLabels($startDate, $endDate);

        return array_map(function ($month) {
            $start = Carbon::parse($month['start']);
            $end = Carbon::parse($month['end']);

            // Bank Income
            $bankIncome = BankTransaction::where('transaction_type', 'credit')
                ->whereHas('category', fn($q) => $q->where('type', 'income'))
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');

            // Invoice Profit
            $revenue = Invoice::whereBetween('issue_date', [$start, $end])->sum('total_amount');
            $cogs = InvoiceItem::whereHas('invoice', fn($q) => $q->whereBetween('issue_date', [$start, $end]))
                ->where('is_tax_deposit', false)
                ->sum('cogs_amount');
            $taxDeposits = InvoiceItem::whereHas('invoice', fn($q) => $q->whereBetween('issue_date', [$start, $end]))
                ->where('is_tax_deposit', true)
                ->sum('amount');

            $income = $bankIncome + ($revenue - $cogs - $taxDeposits);

            // Expenses
            $expenses = BankTransaction::where('transaction_type', 'debit')
                ->whereHas('category', fn($q) => $q->where('type', 'expense'))
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');

            return [
                'month' => $month['label'],
                'income' => $income,
                'expenses' => $expenses,
            ];
        }, $months);
    }

    #[Computed]
    public function expenseByCategoryData(): array
    {
        $startDate = $this->getStartDate();
        $endDate = now();

        return BankTransaction::with('category.parent')
            ->where('transaction_type', 'debit')
            ->whereHas('category', fn($q) => $q->where('type', 'expense'))
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($t) {
                if (!$t->category)
                    return 'Uncategorized';
                return $t->category->parent ? $t->category->parent->label : $t->category->label;
            })
            ->map(fn($items) => [
                'category' => $items->first()->category?->parent?->label ?? $items->first()->category?->label ?? 'Uncategorized',
                'total' => $items->sum('amount'),
            ])
            ->sortByDesc('total')
            ->values()
            ->toArray();
    }

    #[Computed]
    public function top5Expenses(): array
    {
        return collect($this->expenseByCategoryData)->take(5)->toArray();
    }

    #[Computed]
    public function recentTransactions()
    {
        return BankTransaction::with(['bankAccount', 'category'])
            ->whereHas('category', fn($q) => $q->whereIn('type', ['income', 'expense']))
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

    public function render(): View
    {
        return view('livewire.cash-flow.overview-tab');
    }
}
<?php

namespace App\Livewire\CashFlow;

use App\Models\BankTransaction;
use App\Models\Payment;
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

        // βœ… INCOME CALCULATION (Fixed)
        // 1. Payments dari Invoice (revenue recognition)
        $paymentsIncome = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');

        // 2. Direct Income: BankTransaction credit dengan category type='income'
        //    (pendapatan lain-lain, bunga bank, refund, dll)
        $directIncome = BankTransaction::where('transaction_type', 'credit')
            ->whereHas('category', function ($query) {
                $query->where('type', 'income');
            })
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $totalIncome = $paymentsIncome + $directIncome;

        // βœ… EXPENSE CALCULATION (Sudah benar)
        $totalExpenses = BankTransaction::where('transaction_type', 'debit')
            ->whereHas('category', function ($query) {
                $query->where('type', 'expense');
            })
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        // βœ… NET CASH FLOW
        $netCashFlow = $totalIncome - $totalExpenses;

        // βœ… TRANSFERS (Fixed - hanya hitung outgoing/debit untuk avoid double)
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

            // βœ… INCOME for this month
            // 1. Payments
            $payments = Payment::whereBetween('payment_date', [$monthStart, $monthEnd])
                ->sum('amount');

            // 2. Direct Income (BankTransaction credit dengan category income)
            $directIncome = BankTransaction::where('transaction_type', 'credit')
                ->whereHas('category', function ($query) {
                    $query->where('type', 'income');
                })
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount');

            $income = $payments + $directIncome;

            // βœ… EXPENSES for this month
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

        // βœ… Category breakdown hanya untuk EXPENSES
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
        // βœ… Recent transactions: exclude transfers & adjustments untuk clarity
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
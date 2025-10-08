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

        // Total Income: Credits + Payments
        $creditTransactions = BankTransaction::where('transaction_type', 'credit')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $payments = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');

        $totalIncome = $creditTransactions + $payments;

        // Total Expenses: Debits
        $totalExpenses = BankTransaction::where('transaction_type', 'debit')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        // Net Cash Flow
        $netCashFlow = $totalIncome - $totalExpenses;

        // Total Transfers: Transactions with transfer category
        $totalTransfers = BankTransaction::whereHas('category', function ($query) {
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

        // Generate month labels based on period
        $months = $this->generateMonthLabels($startDate, $endDate);

        $chartData = [];
        foreach ($months as $month) {
            $monthStart = Carbon::parse($month['start']);
            $monthEnd = Carbon::parse($month['end']);

            // Income for this month
            $creditTransactions = BankTransaction::where('transaction_type', 'credit')
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount');

            $payments = Payment::whereBetween('payment_date', [$monthStart, $monthEnd])
                ->sum('amount');

            $income = $creditTransactions + $payments;

            // Expenses for this month
            $expenses = BankTransaction::where('transaction_type', 'debit')
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

        $expenses = BankTransaction::with('category')
            ->where('transaction_type', 'debit')
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
        return BankTransaction::with(['bankAccount', 'category'])
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
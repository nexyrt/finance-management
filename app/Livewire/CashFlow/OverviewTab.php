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
    public ?string $period = 'this_month';

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
            'last_3_months' => now()->subMonths(3)->startOfMonth(),
            'last_year' => now()->subYear()->startOfYear(),
            default => now()->startOfMonth(),
        };
    }

    public function render(): View
    {
        return view('livewire.cash-flow.overview-tab');
    }
}
<?php

namespace App\Livewire\Accounts;

use App\Models\BankTransaction;
use App\Models\Payment;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use TallStackUi\Traits\Interactions;

class QuickActionsOverview extends Component
{
    use Interactions;

    public $selectedAccountId;

    public function render()
    {
        return view('livewire.accounts.quick-actions-overview');
    }

    // Listen to account changes from parent
    #[On('account-selected')]
    public function handleAccountChange($accountId): void
    {
        $this->selectedAccountId = $accountId;

        // Update chart
        $this->dispatch('chartDataUpdated', [
            'chartData' => $this->chartData,
        ]);
    }

    // Quick Actions
    public function addTransaction(): void
    {
        if (!$this->selectedAccountId) {
            $this->toast()->warning('Warning', 'Please select an account first')->send();
            return;
        }
        $this->dispatch('open-transaction-modal', accountId: $this->selectedAccountId);
    }

    public function transferFunds(): void
    {
        if (!$this->selectedAccountId) {
            $this->toast()->warning('Warning', 'Please select an account first')->send();
            return;
        }
        $this->dispatch('open-transfer-modal', fromAccountId: $this->selectedAccountId);
    }

    public function exportReport(): void
    {
        if (!$this->selectedAccountId) {
            $this->toast()->warning('Warning', 'Please select an account first')->send();
            return;
        }
        $this->toast()->info('Export Started', 'Your report is being generated')->send();
    }

    // Chart data
    #[Computed]
    public function chartData(): array
    {
        if (!$this->selectedAccountId) {
            return [];
        }

        $months = collect();
        $currentDate = now()->startOfMonth()->subMonths(11); // Last 12 months

        for ($i = 0; $i < 12; $i++) {
            $monthStart = $currentDate->copy()->addMonths($i);
            $monthEnd = $monthStart->copy()->endOfMonth();

            // Income dari payments + credit transactions
            $paymentsIncome = Payment::where('bank_account_id', $this->selectedAccountId)
                ->whereBetween('payment_date', [$monthStart, $monthEnd])
                ->sum('amount');

            $transactionsIncome = BankTransaction::where('bank_account_id', $this->selectedAccountId)
                ->where('transaction_type', 'credit')
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount');

            // Expense dari debit transactions
            $expense = BankTransaction::where('bank_account_id', $this->selectedAccountId)
                ->where('transaction_type', 'debit')
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount');

            $months->push([
                'month' => $monthStart->format('M Y'),
                'income' => $paymentsIncome + $transactionsIncome,
                'expense' => $expense
            ]);
        }

        return $months->toArray();
    }

    // Account stats
    #[Computed]
    public function accountStats(): array
    {
        if (!$this->selectedAccountId) {
            return [
                'total_income' => 0,
                'total_expense' => 0,
                'net_cashflow' => 0,
                'transaction_count' => 0
            ];
        }

        // This month data
        $thisMonthStart = now()->startOfMonth();
        $thisMonthEnd = now()->endOfMonth();

        $paymentsIncome = Payment::where('bank_account_id', $this->selectedAccountId)
            ->whereBetween('payment_date', [$thisMonthStart, $thisMonthEnd])
            ->sum('amount');

        $transactionsIncome = BankTransaction::where('bank_account_id', $this->selectedAccountId)
            ->where('transaction_type', 'credit')
            ->whereBetween('transaction_date', [$thisMonthStart, $thisMonthEnd])
            ->sum('amount');

        $totalIncome = $paymentsIncome + $transactionsIncome;

        $totalExpense = BankTransaction::where('bank_account_id', $this->selectedAccountId)
            ->where('transaction_type', 'debit')
            ->whereBetween('transaction_date', [$thisMonthStart, $thisMonthEnd])
            ->sum('amount');

        $transactionCount = BankTransaction::where('bank_account_id', $this->selectedAccountId)
            ->whereBetween('transaction_date', [$thisMonthStart, $thisMonthEnd])
            ->count();

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_cashflow' => $totalIncome - $totalExpense,
            'transaction_count' => $transactionCount
        ];
    }
}
<?php

namespace App\Livewire\Accounts;

use App\Models\BankTransaction;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use TallStackUi\Traits\Interactions;

#[Lazy]
class QuickActionsOverview extends Component
{
    use Interactions;

    public $selectedAccountId;

    public function placeholder(): View
    {
        return view('livewire.placeholders.quick-actions-skeleton');
    }

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

    public function exportReport(): void
    {
        if (!$this->selectedAccountId) {
            $this->toast()->warning(__('common.warning'), __('pages.select_account_first'))->send();
            return;
        }

        $url = route('bank-account.export.pdf', [
            'bank_account_id' => $this->selectedAccountId,
        ]);

        $this->dispatch('download-pdf', url: $url);
        $this->toast()->info(__('pages.export_started'), __('pages.report_generating'))->send();

        // Re-initialize chart after export (same as account selection)
        $this->dispatch('chartDataUpdated', [
            'chartData' => $this->chartData,
        ]);
    }

    // Chart data -- 3 batch queries instead of 36
    #[Computed]
    public function chartData(): array
    {
        if (!$this->selectedAccountId) {
            return [];
        }

        $globalStart = now()->startOfMonth()->subMonths(11);
        $globalEnd = now()->endOfMonth();

        // Batch query 1: Payment income by month
        $paymentsByMonth = Payment::where('bank_account_id', $this->selectedAccountId)
            ->whereBetween('payment_date', [$globalStart, $globalEnd])
            ->selectRaw('YEAR(payment_date) as y, MONTH(payment_date) as m, SUM(amount) as total')
            ->groupByRaw('YEAR(payment_date), MONTH(payment_date)')
            ->get()
            ->keyBy(fn($row) => $row->y . '-' . $row->m);

        // Batch query 2: Transaction income/expense by month (CASE WHEN)
        $trxByMonth = BankTransaction::where('bank_account_id', $this->selectedAccountId)
            ->whereBetween('transaction_date', [$globalStart, $globalEnd])
            ->selectRaw("
                YEAR(transaction_date) as y,
                MONTH(transaction_date) as m,
                SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as credit_total,
                SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as debit_total
            ")
            ->groupByRaw('YEAR(transaction_date), MONTH(transaction_date)')
            ->get()
            ->keyBy(fn($row) => $row->y . '-' . $row->m);

        // Build months from cached data
        $months = [];
        $currentDate = $globalStart->copy();

        for ($i = 0; $i < 12; $i++) {
            $month = $currentDate->copy()->addMonths($i);
            $key = $month->year . '-' . $month->month;

            $paymentIncome = (int) ($paymentsByMonth[$key]->total ?? 0);
            $creditIncome = (int) ($trxByMonth[$key]->credit_total ?? 0);
            $expense = (int) ($trxByMonth[$key]->debit_total ?? 0);

            $months[] = [
                'month' => $month->format('M Y'),
                'income' => $paymentIncome + $creditIncome,
                'expense' => $expense,
            ];
        }

        return $months;
    }

    // Account stats -- 2 queries instead of 4
    #[Computed]
    public function accountStats(): array
    {
        if (!$this->selectedAccountId) {
            return [
                'total_income' => 0,
                'total_expense' => 0,
                'net_cashflow' => 0,
                'transaction_count' => 0,
            ];
        }

        $thisMonthStart = now()->startOfMonth();
        $thisMonthEnd = now()->endOfMonth();

        // Single query for all transaction stats using CASE WHEN
        $trxStats = BankTransaction::where('bank_account_id', $this->selectedAccountId)
            ->whereBetween('transaction_date', [$thisMonthStart, $thisMonthEnd])
            ->selectRaw("
                SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as credit_total,
                SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as debit_total,
                COUNT(*) as trx_count
            ")
            ->first();

        // Separate query for payments (different table)
        $paymentsIncome = (int) Payment::where('bank_account_id', $this->selectedAccountId)
            ->whereBetween('payment_date', [$thisMonthStart, $thisMonthEnd])
            ->sum('amount');

        $totalIncome = $paymentsIncome + (int) $trxStats->credit_total;
        $totalExpense = (int) $trxStats->debit_total;

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_cashflow' => $totalIncome - $totalExpense,
            'transaction_count' => (int) $trxStats->trx_count,
        ];
    }
}

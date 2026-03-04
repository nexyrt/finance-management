<?php

namespace App\Livewire\Accounts;

use App\Models\BankTransaction;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy]
class QuickActionsOverview extends Component
{
    public $selectedAccountId;

    public function placeholder(): View
    {
        return view('livewire.placeholders.quick-actions-skeleton');
    }

    public function render()
    {
        return view('livewire.accounts.quick-actions-overview');
    }

    #[On('account-selected')]
    public function handleAccountChange($accountId): void
    {
        $this->selectedAccountId = $accountId;

        // Invalidate computed caches
        unset($this->chartData, $this->accountStats, $this->categoryBreakdown, $this->statsMonth);

        // Dispatch all chart data to Alpine
        $this->dispatch('account-charts-updated', [
            'incomeExpense' => $this->chartData,
            'categoryBreakdown' => $this->categoryBreakdown,
        ]);
    }

    /**
     * Wire-callable method for Alpine to fetch fresh chart data.
     */
    public function getChartData(string $chartName): array
    {
        return match ($chartName) {
            'incomeExpense' => $this->chartData,
            'categoryBreakdown' => $this->categoryBreakdown,
            default => [],
        };
    }

    /**
     * Income vs Expense bar chart data — 12 months.
     * 2 batch queries (payments + transactions).
     */
    #[Computed]
    public function chartData(): array
    {
        if (! $this->selectedAccountId) {
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
            ->keyBy(fn ($row) => $row->y.'-'.$row->m);

        // Batch query 2: Transaction income/expense by month
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
            ->keyBy(fn ($row) => $row->y.'-'.$row->m);

        $months = [];
        $currentDate = $globalStart->copy();

        for ($i = 0; $i < 12; $i++) {
            $month = $currentDate->copy()->addMonths($i);
            $key = $month->year.'-'.$month->month;

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

    /**
     * Determine the effective stats month: current month if it has data,
     * otherwise fallback to the latest month with transaction/payment data.
     *
     * @return array{start: \Carbon\Carbon, end: \Carbon\Carbon, label: string}
     */
    #[Computed]
    public function statsMonth(): array
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        if (! $this->selectedAccountId) {
            return ['start' => $start, 'end' => $end, 'label' => $start->translatedFormat('F Y')];
        }

        // Check if current month has any data
        $hasCurrentData = BankTransaction::where('bank_account_id', $this->selectedAccountId)
            ->whereBetween('transaction_date', [$start, $end])
            ->exists();

        if (! $hasCurrentData) {
            $hasCurrentData = Payment::where('bank_account_id', $this->selectedAccountId)
                ->whereBetween('payment_date', [$start, $end])
                ->exists();
        }

        if ($hasCurrentData) {
            return ['start' => $start, 'end' => $end, 'label' => $start->translatedFormat('F Y')];
        }

        // Fallback: find the latest month with data
        $latestTrx = BankTransaction::where('bank_account_id', $this->selectedAccountId)
            ->orderByDesc('transaction_date')
            ->value('transaction_date');

        $latestPayment = Payment::where('bank_account_id', $this->selectedAccountId)
            ->orderByDesc('payment_date')
            ->value('payment_date');

        $latest = collect([$latestTrx, $latestPayment])->filter()->max();

        if ($latest) {
            $latestDate = \Carbon\Carbon::parse($latest);
            $start = $latestDate->copy()->startOfMonth();
            $end = $latestDate->copy()->endOfMonth();

            return ['start' => $start, 'end' => $end, 'label' => $start->translatedFormat('F Y')];
        }

        // No data at all
        return ['start' => now()->startOfMonth(), 'end' => now()->endOfMonth(), 'label' => now()->translatedFormat('F Y')];
    }

    /**
     * Top expense categories — donut chart.
     * Uses smart period (current month or latest month with data).
     * 1 query with JOIN + GROUP BY.
     */
    #[Computed]
    public function categoryBreakdown(): array
    {
        if (! $this->selectedAccountId) {
            return [];
        }

        $period = $this->statsMonth;

        return BankTransaction::where('bank_transactions.bank_account_id', $this->selectedAccountId)
            ->where('bank_transactions.transaction_type', 'debit')
            ->whereBetween('bank_transactions.transaction_date', [$period['start'], $period['end']])
            ->whereNotNull('bank_transactions.category_id')
            ->join('transaction_categories', 'bank_transactions.category_id', '=', 'transaction_categories.id')
            ->selectRaw('transaction_categories.label as name, SUM(bank_transactions.amount) as total')
            ->groupBy('transaction_categories.id', 'transaction_categories.label')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->toArray();
    }

    /**
     * Account stats — mini stat cards.
     * Uses smart period (current month or latest month with data).
     * 2 queries (transactions CASE WHEN + payments SUM).
     */
    #[Computed]
    public function accountStats(): array
    {
        if (! $this->selectedAccountId) {
            return [
                'total_income' => 0,
                'total_expense' => 0,
                'net_cashflow' => 0,
                'transaction_count' => 0,
            ];
        }

        $period = $this->statsMonth;

        $trxStats = BankTransaction::where('bank_account_id', $this->selectedAccountId)
            ->whereBetween('transaction_date', [$period['start'], $period['end']])
            ->selectRaw("
                SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as credit_total,
                SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as debit_total,
                COUNT(*) as trx_count
            ")
            ->first();

        $paymentsIncome = (int) Payment::where('bank_account_id', $this->selectedAccountId)
            ->whereBetween('payment_date', [$period['start'], $period['end']])
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

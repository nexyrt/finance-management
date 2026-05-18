<?php

namespace App\Livewire\Accounts;

use App\Models\BankTransaction;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy]
class QuickActionsOverview extends Component
{
    public $selectedAccountId;

    public string $selectedMonth = '';

    public function placeholder(): View
    {
        return view('livewire.placeholders.quick-actions-skeleton');
    }

    public function updatedSelectedMonth(): void
    {
        unset($this->accountStats, $this->categoryBreakdown, $this->statsMonth);

        $this->dispatch('account-charts-updated', [
            'incomeExpense' => $this->chartData,
            'categoryBreakdown' => $this->categoryBreakdown,
        ]);
    }

    public function render()
    {
        return view('livewire.accounts.quick-actions-overview');
    }

    #[On('account-selected')]
    public function handleAccountChange($accountId): void
    {
        $this->selectedAccountId = $accountId;
        $this->selectedMonth = '';

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
     * Resolve the stats period.
     * - Month selected  → filter to that month
     * - No filter       → all time (null start/end)
     *
     * @return array{start: Carbon|null, end: Carbon|null, label: string, is_all_time: bool}
     */
    #[Computed]
    public function statsMonth(): array
    {
        // Read directly from Livewire's data bag to avoid PropertyNotFoundException
        // when hydrating from old snapshots that predate this property.
        $selectedMonth = $this->all()['selectedMonth'] ?? '';

        if ($selectedMonth !== '') {
            $date = Carbon::createFromFormat('Y-m', $selectedMonth);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            return ['start' => $start, 'end' => $end, 'label' => $start->translatedFormat('F Y'), 'is_all_time' => false];
        }

        return ['start' => null, 'end' => null, 'label' => __('pages.all_time'), 'is_all_time' => true];
    }

    /**
     * Top expense categories — donut chart.
     * Scoped to selected month, or all time when no filter is set.
     */
    #[Computed]
    public function categoryBreakdown(): array
    {
        if (! $this->selectedAccountId) {
            return [];
        }

        $period = $this->statsMonth;

        $query = BankTransaction::where('bank_transactions.bank_account_id', $this->selectedAccountId)
            ->where('bank_transactions.transaction_type', 'debit')
            ->whereNotNull('bank_transactions.category_id')
            ->join('transaction_categories', 'bank_transactions.category_id', '=', 'transaction_categories.id')
            ->selectRaw('transaction_categories.label as name, SUM(bank_transactions.amount) as total')
            ->groupBy('transaction_categories.id', 'transaction_categories.label')
            ->orderByDesc('total')
            ->limit(6);

        if (! $period['is_all_time']) {
            $query->whereBetween('bank_transactions.transaction_date', [$period['start'], $period['end']]);
        }

        return $query->get()->toArray();
    }

    /**
     * Account stats — mini stat cards.
     * Scoped to selected month, or all time when no filter is set.
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

        $trxQuery = BankTransaction::where('bank_account_id', $this->selectedAccountId);
        $payQuery = Payment::where('bank_account_id', $this->selectedAccountId);

        if (! $period['is_all_time']) {
            $trxQuery->whereBetween('transaction_date', [$period['start'], $period['end']]);
            $payQuery->whereBetween('payment_date', [$period['start'], $period['end']]);
        }

        $trxStats = $trxQuery->selectRaw("
                SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as credit_total,
                SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as debit_total,
                COUNT(*) as trx_count
            ")->first();

        $paymentsIncome = (int) $payQuery->sum('amount');
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

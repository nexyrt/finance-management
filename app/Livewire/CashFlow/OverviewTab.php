<?php

namespace App\Livewire\CashFlow;

use App\Models\BankTransaction;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class OverviewTab extends Component
{
    public ?string $period = 'last_year';

    // Dispatch event when period changes
    public function updatedPeriod(): void
    {
        $this->dispatch('charts-updated');
    }

    #[Computed]
    public function stats(): array
    {
        $startDate = $this->getStartDate();
        $endDate = now();

        // Bank transactions aggregated in a single query using JOIN
        $bankStats = BankTransaction::query()
            ->join('transaction_categories', 'bank_transactions.category_id', '=', 'transaction_categories.id')
            ->whereBetween('bank_transactions.transaction_date', [$startDate, $endDate])
            ->selectRaw("
                SUM(CASE WHEN bank_transactions.transaction_type = 'credit' AND transaction_categories.type = 'income' THEN bank_transactions.amount ELSE 0 END) as bank_income,
                SUM(CASE WHEN bank_transactions.transaction_type = 'debit' AND transaction_categories.type = 'expense' THEN bank_transactions.amount ELSE 0 END) as total_expenses,
                SUM(CASE WHEN bank_transactions.transaction_type = 'debit' AND transaction_categories.type = 'transfer' THEN bank_transactions.amount ELSE 0 END) as total_transfers
            ")
            ->first();

        // Invoice profit in a single query
        $invoiceStats = Invoice::whereBetween('issue_date', [$startDate, $endDate])
            ->selectRaw('COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();

        $itemStats = InvoiceItem::query()
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->whereBetween('invoices.issue_date', [$startDate, $endDate])
            ->selectRaw("
                COALESCE(SUM(CASE WHEN invoice_items.is_tax_deposit = 0 THEN invoice_items.cogs_amount ELSE 0 END), 0) as total_cogs,
                COALESCE(SUM(CASE WHEN invoice_items.is_tax_deposit = 1 THEN invoice_items.amount ELSE 0 END), 0) as total_tax_deposits
            ")
            ->first();

        $invoiceProfit = $invoiceStats->total_revenue - $itemStats->total_cogs - $itemStats->total_tax_deposits;
        $totalIncome = $bankStats->bank_income + $invoiceProfit;

        return [
            'total_income' => $totalIncome,
            'total_expenses' => (int) $bankStats->total_expenses,
            'net_cash_flow' => $totalIncome - $bankStats->total_expenses,
            'total_transfers' => (int) $bankStats->total_transfers,
        ];
    }

    #[Computed]
    public function monthlyTrendData(): array
    {
        $periods = match ($this->period) {
            'this_month' => $this->getWeekPeriods(),
            'last_3_months' => $this->getThreeMonthPeriods(),
            'last_year' => $this->getYearlyPeriods(),
            default => $this->getYearlyPeriods(),
        };

        if (empty($periods)) {
            return [];
        }

        $globalStart = $periods[0]['start'];
        $globalEnd = end($periods)['end'];

        // Batch query 1: Bank income by period
        $bankIncomeRows = DB::table('bank_transactions')
            ->join('transaction_categories', 'bank_transactions.category_id', '=', 'transaction_categories.id')
            ->where('bank_transactions.transaction_type', 'credit')
            ->where('transaction_categories.type', 'income')
            ->whereBetween('bank_transactions.transaction_date', [$globalStart, $globalEnd])
            ->select('bank_transactions.transaction_date', 'bank_transactions.amount')
            ->get();

        // Batch query 2: Expenses by period
        $expenseRows = DB::table('bank_transactions')
            ->join('transaction_categories', 'bank_transactions.category_id', '=', 'transaction_categories.id')
            ->where('bank_transactions.transaction_type', 'debit')
            ->where('transaction_categories.type', 'expense')
            ->whereBetween('bank_transactions.transaction_date', [$globalStart, $globalEnd])
            ->select('bank_transactions.transaction_date', 'bank_transactions.amount')
            ->get();

        // Batch query 3: Invoice revenue by period
        $invoiceRows = DB::table('invoices')
            ->whereBetween('issue_date', [$globalStart, $globalEnd])
            ->select('id', 'issue_date', 'total_amount')
            ->get();

        $invoiceIds = $invoiceRows->pluck('id');

        // Batch query 4: Invoice items (COGS + tax deposits)
        $itemRows = $invoiceIds->isNotEmpty()
            ? DB::table('invoice_items')
                ->whereIn('invoice_id', $invoiceIds)
                ->select('invoice_id', 'is_tax_deposit', 'cogs_amount', 'amount')
                ->get()
            : collect();

        // Index items by invoice_id for fast lookup
        $itemsByInvoice = $itemRows->groupBy('invoice_id');

        // Assign data to periods
        return array_map(function ($period) use ($bankIncomeRows, $expenseRows, $invoiceRows, $itemsByInvoice) {
            $start = $period['start'];
            $end = $period['end'];

            $bankIncome = $bankIncomeRows
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');

            $expenses = $expenseRows
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');

            $periodInvoices = $invoiceRows->whereBetween('issue_date', [$start, $end]);
            $revenue = $periodInvoices->sum('total_amount');
            $cogs = 0;
            $taxDeposits = 0;

            foreach ($periodInvoices as $inv) {
                $items = $itemsByInvoice->get($inv->id, collect());
                foreach ($items as $item) {
                    if ($item->is_tax_deposit) {
                        $taxDeposits += $item->amount;
                    } else {
                        $cogs += $item->cogs_amount;
                    }
                }
            }

            $income = $bankIncome + ($revenue - $cogs - $taxDeposits);

            return [
                'month' => $period['label'],
                'income' => (int) $income,
                'expenses' => (int) $expenses,
            ];
        }, $periods);
    }

    private function getWeekPeriods(): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $weeks = [];
        $currentWeek = $startOfMonth->copy();
        $weekNumber = 1;

        while ($currentWeek <= $endOfMonth) {
            $weekStart = $currentWeek->copy();
            $weekEnd = $currentWeek->copy()->endOfWeek()->min($endOfMonth);

            $weeks[] = [
                'label' => 'Week ' . $weekNumber,
                'start' => $weekStart->format('Y-m-d'),
                'end' => $weekEnd->format('Y-m-d'),
            ];

            $currentWeek->addWeek()->startOfWeek();
            $weekNumber++;
            if ($weekNumber > 5) break;
        }

        return $weeks;
    }

    private function getThreeMonthPeriods(): array
    {
        $months = [];
        for ($i = 2; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = [
                'label' => $month->translatedFormat('M Y'),
                'start' => $month->copy()->startOfMonth()->format('Y-m-d'),
                'end' => $month->copy()->endOfMonth()->format('Y-m-d'),
            ];
        }
        return $months;
    }

    private function getYearlyPeriods(): array
    {
        $startDate = now()->subMonths(11)->startOfMonth();
        $endDate = now();
        return $this->generateMonthLabels($startDate, $endDate);
    }

    #[Computed]
    public function expenseByCategoryData(): array
    {
        $startDate = $this->getStartDate();
        $endDate = now();

        return DB::table('bank_transactions')
            ->join('transaction_categories as cat', 'bank_transactions.category_id', '=', 'cat.id')
            ->leftJoin('transaction_categories as parent', 'cat.parent_code', '=', 'parent.code')
            ->where('bank_transactions.transaction_type', 'debit')
            ->where('cat.type', 'expense')
            ->whereBetween('bank_transactions.transaction_date', [$startDate, $endDate])
            ->selectRaw('COALESCE(parent.label, cat.label) as category, SUM(bank_transactions.amount) as total')
            ->groupByRaw('COALESCE(parent.label, cat.label)')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => ['category' => $row->category, 'total' => (int) $row->total])
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
        return BankTransaction::query()
            ->join('transaction_categories', 'bank_transactions.category_id', '=', 'transaction_categories.id')
            ->whereIn('transaction_categories.type', ['income', 'expense'])
            ->select('bank_transactions.*')
            ->with(['bankAccount', 'category'])
            ->latest('bank_transactions.transaction_date')
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
                'label' => $current->translatedFormat('M Y'),
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
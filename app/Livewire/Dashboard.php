<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\TransactionCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Dashboard extends Component
{
    // ============================================
    // FILTER PROPERTIES
    // ============================================

    public $period = 'this_month'; // this_month, last_month, this_quarter, last_quarter, this_year, last_year, custom
    public $startDate = null;
    public $endDate = null;

    public $chartPeriod = '6_months'; // this_month, 6_months, 12_months

    // Reset computed properties when period changes
    private function resetComputedProperties()
    {
        unset($this->incomeThisMonth);
        unset($this->expensesThisMonth);
        unset($this->expensesByCategoryChart);
        $this->dispatch('charts-refresh');
    }

    public function updatedPeriod()
    {
        $this->resetComputedProperties();
    }

    public function updatedStartDate()
    {
        $this->resetComputedProperties();
    }

    public function updatedEndDate()
    {
        $this->resetComputedProperties();
    }

    public function updatedChartPeriod()
    {
        unset($this->cashFlowChart);
        unset($this->revenueVsExpensesChart);
        $this->dispatch('charts-refresh');
    }

    // ============================================
    // HELPER METHODS FOR DATE RANGE
    // ============================================

    private function getDateRange()
    {
        return match ($this->period) {
            'this_month' => [
                'start' => Carbon::now()->startOfMonth(),
                'end' => Carbon::now()->endOfMonth(),
            ],
            'last_month' => [
                'start' => Carbon::now()->subMonth()->startOfMonth(),
                'end' => Carbon::now()->subMonth()->endOfMonth(),
            ],
            'this_quarter' => [
                'start' => Carbon::now()->startOfQuarter(),
                'end' => Carbon::now()->endOfQuarter(),
            ],
            'last_quarter' => [
                'start' => Carbon::now()->subQuarter()->startOfQuarter(),
                'end' => Carbon::now()->subQuarter()->endOfQuarter(),
            ],
            'this_year' => [
                'start' => Carbon::now()->startOfYear(),
                'end' => Carbon::now()->endOfYear(),
            ],
            'last_year' => [
                'start' => Carbon::now()->subYear()->startOfYear(),
                'end' => Carbon::now()->subYear()->endOfYear(),
            ],
            'custom' => [
                'start' => $this->startDate ? Carbon::parse($this->startDate) : Carbon::now()->startOfMonth(),
                'end' => $this->endDate ? Carbon::parse($this->endDate) : Carbon::now()->endOfMonth(),
            ],
            default => [
                'start' => Carbon::now()->startOfMonth(),
                'end' => Carbon::now()->endOfMonth(),
            ],
        };
    }

    // ============================================
    // QUICK STATS (4 Cards)
    // ============================================

    #[Computed]
    public function totalBankBalance()
    {
        return BankAccount::all()->sum(fn ($account) => $account->balance);
    }

    #[Computed]
    public function incomeThisMonth()
    {
        $dateRange = $this->getDateRange();
        return BankTransaction::where('transaction_type', 'credit')
            ->whereBetween('transaction_date', [$dateRange['start'], $dateRange['end']])
            ->sum('amount');
    }

    #[Computed]
    public function expensesThisMonth()
    {
        $dateRange = $this->getDateRange();
        return BankTransaction::where('transaction_type', 'debit')
            ->whereBetween('transaction_date', [$dateRange['start'], $dateRange['end']])
            ->sum('amount');
    }

    #[Computed]
    public function pendingInvoicesCount()
    {
        return Invoice::whereIn('status', ['sent', 'partially_paid', 'overdue'])
            ->where('due_date', '>=', Carbon::today())
            ->count();
    }

    #[Computed]
    public function pendingInvoicesAmount()
    {
        $totalOutstanding = Invoice::whereIn('status', ['sent', 'partially_paid', 'overdue'])
            ->where('due_date', '>=', Carbon::today())
            ->sum('total_amount');

        $totalPaid = Payment::whereHas('invoice', function ($query) {
            $query->whereIn('status', ['sent', 'partially_paid', 'overdue'])
                ->where('due_date', '>=', Carbon::today());
        })->sum('amount');

        return $totalOutstanding - $totalPaid;
    }

    // ============================================
    // CASH FLOW CHART
    // ============================================

    #[Computed]
    public function cashFlowChart()
    {
        if ($this->chartPeriod === 'this_month') {
            return $this->getWeeklyData();
        }

        $months = $this->chartPeriod === '12_months' ? 11 : 5;
        $data = [];
        for ($i = $months; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);

            $income = BankTransaction::where('transaction_type', 'credit')
                ->whereMonth('transaction_date', $month->month)
                ->whereYear('transaction_date', $month->year)
                ->sum('amount');

            $expenses = BankTransaction::where('transaction_type', 'debit')
                ->whereMonth('transaction_date', $month->month)
                ->whereYear('transaction_date', $month->year)
                ->sum('amount');

            $data[] = [
                'label' => $this->chartPeriod === '12_months'
                    ? $month->translatedFormat('M y')
                    : $month->translatedFormat('M'),
                'income' => $income,
                'expenses' => $expenses,
            ];
        }
        return $data;
    }

    private function getWeeklyData()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $data = [];
        $week = 1;
        $weekStart = $startOfMonth->copy();

        while ($weekStart->lte($endOfMonth)) {
            $weekEnd = $weekStart->copy()->addDays(6);
            if ($weekEnd->gt($endOfMonth)) {
                $weekEnd = $endOfMonth->copy();
            }

            $income = BankTransaction::where('transaction_type', 'credit')
                ->whereBetween('transaction_date', [$weekStart, $weekEnd])
                ->sum('amount');

            $expenses = BankTransaction::where('transaction_type', 'debit')
                ->whereBetween('transaction_date', [$weekStart, $weekEnd])
                ->sum('amount');

            $data[] = [
                'label' => 'W' . $week . ' (' . $weekStart->format('d') . '-' . $weekEnd->format('d') . ')',
                'income' => $income,
                'expenses' => $expenses,
            ];

            $week++;
            $weekStart = $weekEnd->copy()->addDay();
        }

        return $data;
    }

    // ============================================
    // EXPENSE BY CATEGORY CHART (Pie Chart)
    // ============================================

    #[Computed]
    public function expensesByCategoryChart()
    {
        $dateRange = $this->getDateRange();

        // Define consistent colors matching Chart.js
        $colors = [
            'rgb(37, 99, 235)',      // Blue
            'rgb(22, 163, 74)',      // Green
            'rgb(245, 158, 11)',     // Amber
            'rgb(168, 85, 247)',     // Purple
            'rgb(14, 165, 233)',     // Sky
            'rgb(156, 163, 175)',    // Gray
        ];

        // Get expenses for selected period grouped by category
        $transactions = BankTransaction::where('transaction_type', 'debit')
            ->whereBetween('transaction_date', [$dateRange['start'], $dateRange['end']])
            ->whereNotNull('category_id')
            ->with('category')
            ->get();

        $grouped = $transactions->groupBy('category_id')
            ->map(function ($group) {
                return [
                    'name' => $group->first()->category->label ?? 'Lainnya',
                    'value' => $group->sum('amount'),
                ];
            })
            ->sortByDesc('value')
            ->take(5)
            ->values()
            ->toArray();

        // Calculate uncategorized
        $uncategorized = BankTransaction::where('transaction_type', 'debit')
            ->whereBetween('transaction_date', [$dateRange['start'], $dateRange['end']])
            ->whereNull('category_id')
            ->sum('amount');

        if ($uncategorized > 0) {
            $grouped[] = [
                'name' => 'Tanpa Kategori',
                'value' => $uncategorized,
            ];
        }

        // Add color to each item
        foreach ($grouped as $index => $item) {
            $grouped[$index]['color'] = $colors[$index % count($colors)];
        }

        return $grouped;
    }

    // ============================================
    // REVENUE VS EXPENSES CHART
    // ============================================

    #[Computed]
    public function revenueVsExpensesChart()
    {
        if ($this->chartPeriod === 'this_month') {
            return $this->getWeeklyRevenueExpenseData();
        }

        $months = $this->chartPeriod === '12_months' ? 11 : 5;
        $data = [];
        for ($i = $months; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);

            $revenue = BankTransaction::where('transaction_type', 'credit')
                ->whereMonth('transaction_date', $month->month)
                ->whereYear('transaction_date', $month->year)
                ->sum('amount');

            $expenses = BankTransaction::where('transaction_type', 'debit')
                ->whereMonth('transaction_date', $month->month)
                ->whereYear('transaction_date', $month->year)
                ->sum('amount');

            $data[] = [
                'label' => $this->chartPeriod === '12_months'
                    ? $month->translatedFormat('M y')
                    : $month->translatedFormat('M'),
                'revenue' => $revenue,
                'expenses' => $expenses,
            ];
        }
        return $data;
    }

    private function getWeeklyRevenueExpenseData()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $data = [];
        $week = 1;
        $weekStart = $startOfMonth->copy();

        while ($weekStart->lte($endOfMonth)) {
            $weekEnd = $weekStart->copy()->addDays(6);
            if ($weekEnd->gt($endOfMonth)) {
                $weekEnd = $endOfMonth->copy();
            }

            $revenue = BankTransaction::where('transaction_type', 'credit')
                ->whereBetween('transaction_date', [$weekStart, $weekEnd])
                ->sum('amount');

            $expenses = BankTransaction::where('transaction_type', 'debit')
                ->whereBetween('transaction_date', [$weekStart, $weekEnd])
                ->sum('amount');

            $data[] = [
                'label' => 'W' . $week . ' (' . $weekStart->format('d') . '-' . $weekEnd->format('d') . ')',
                'revenue' => $revenue,
                'expenses' => $expenses,
            ];

            $week++;
            $weekStart = $weekEnd->copy()->addDay();
        }

        return $data;
    }

    // ============================================
    // BANK ACCOUNTS LIST
    // ============================================

    #[Computed]
    public function bankAccounts()
    {
        return BankAccount::all()->map(function ($account) {
            return [
                'id' => $account->id,
                'name' => $account->account_name,
                'bank' => $account->bank_name,
                'account_number' => $account->account_number,
                'balance' => $account->balance,
            ];
        })->sortByDesc('balance')->values();
    }

    // ============================================
    // PENDING INVOICES LIST
    // ============================================

    #[Computed]
    public function pendingInvoicesList()
    {
        return Invoice::with('client')
            ->whereIn('status', ['sent', 'partially_paid', 'overdue'])
            ->where('due_date', '>=', Carbon::today())
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get()
            ->map(function ($invoice) {
                $totalPaid = Payment::where('invoice_id', $invoice->id)->sum('amount');
                $remaining = $invoice->total_amount - $totalPaid;

                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'client' => $invoice->client->name,
                    'amount' => $remaining,
                    'due_date' => $invoice->due_date,
                    'status' => $invoice->status,
                    'days_until_due' => Carbon::today()->diffInDays($invoice->due_date, false),
                ];
            });
    }

    // ============================================
    // RECENT TRANSACTIONS LIST
    // ============================================

    #[Computed]
    public function recentTransactions()
    {
        // Combine BankTransactions and Payments
        $bankTransactions = BankTransaction::with(['category', 'bankAccount'])
            ->orderBy('transaction_date', 'desc')
            ->take(5)
            ->get()
            ->map(function ($transaction) {
                return [
                    'date' => $transaction->transaction_date,
                    'description' => $transaction->description ?: ($transaction->category->label ?? 'Transaksi Bank'),
                    'type' => $transaction->transaction_type === 'credit' ? 'income' : 'expense',
                    'amount' => $transaction->amount,
                    'account' => $transaction->bankAccount->account_name ?? '-',
                    'category' => $transaction->category->label ?? 'Tanpa Kategori',
                ];
            });

        $payments = Payment::with(['invoice.client', 'bankAccount'])
            ->orderBy('payment_date', 'desc')
            ->take(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'date' => $payment->payment_date,
                    'description' => 'Pembayaran ' . $payment->invoice->invoice_number . ' - ' . $payment->invoice->client->name,
                    'type' => 'income',
                    'amount' => $payment->amount,
                    'account' => $payment->bankAccount->account_name ?? '-',
                    'category' => 'Pembayaran Invoice',
                ];
            });

        return collect($bankTransactions)
            ->concat($payments)
            ->sortByDesc('date')
            ->take(5)
            ->values();
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    public function getChartData(string $chartName)
    {
        return $this->$chartName;
    }

    public function getChartPeriodLabel()
    {
        return match ($this->chartPeriod) {
            'this_month' => 'Bulan ini (per minggu)',
            '6_months' => '6 bulan terakhir',
            '12_months' => '12 bulan terakhir',
            default => '6 bulan terakhir',
        };
    }

    public function getPeriodLabel()
    {
        return match ($this->period) {
            'this_month' => 'Bulan ini',
            'last_month' => 'Bulan lalu',
            'this_quarter' => 'Kuartal ini',
            'last_quarter' => 'Kuartal lalu',
            'this_year' => 'Tahun ini',
            'last_year' => 'Tahun lalu',
            'custom' => 'Periode kustom',
            default => 'Bulan ini',
        };
    }

    public function getPeriodDateRange()
    {
        $range = $this->getDateRange();

        return $range['start']->translatedFormat('d M Y') . ' - ' . $range['end']->translatedFormat('d M Y');
    }

    public function formatCurrency($amount)
    {
        if ($amount >= 1000000000) {
            return 'Rp ' . number_format($amount / 1000000000, 1, ',', '.') . 'M';
        } elseif ($amount >= 1000000) {
            return 'Rp ' . number_format($amount / 1000000, 1, ',', '.') . 'jt';
        } else {
            return 'Rp ' . number_format($amount, 0, ',', '.');
        }
    }

    public function formatNumber($number)
    {
        if ($number >= 1000000) {
            return number_format($number / 1000000, 1, ',', '.') . 'jt';
        } elseif ($number >= 1000) {
            return number_format($number / 1000, 1, ',', '.') . 'rb';
        }
        return number_format($number, 0, ',', '.');
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('components.layouts.new-layout');
    }
}

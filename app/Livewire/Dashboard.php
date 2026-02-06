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

    // Reset computed properties when period changes
    public function updatedPeriod()
    {
        unset($this->incomeThisMonth);
        unset($this->expensesThisMonth);
        unset($this->expensesByCategoryChart);
    }

    public function updatedStartDate()
    {
        unset($this->incomeThisMonth);
        unset($this->expensesThisMonth);
        unset($this->expensesByCategoryChart);
    }

    public function updatedEndDate()
    {
        unset($this->incomeThisMonth);
        unset($this->expensesThisMonth);
        unset($this->expensesByCategoryChart);
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
        return BankAccount::all()->sum(function ($account) {
            $credits = BankTransaction::where('bank_account_id', $account->id)
                ->where('transaction_type', 'credit')
                ->sum('amount');

            $debits = BankTransaction::where('bank_account_id', $account->id)
                ->where('transaction_type', 'debit')
                ->sum('amount');

            return $account->initial_balance + $credits - $debits;
        });
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
    // CASH FLOW CHART (6 Months)
    // ============================================

    #[Computed]
    public function cashFlowChart()
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
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
                'month' => $month->translatedFormat('M'),
                'income' => $income,
                'expenses' => $expenses,
                'net' => $income - $expenses,
            ];
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
    // REVENUE VS EXPENSES CHART (6 Months Bar Chart)
    // ============================================

    #[Computed]
    public function revenueVsExpensesChart()
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
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
                'month' => $month->translatedFormat('M'),
                'revenue' => $revenue,
                'expenses' => $expenses,
            ];
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
            $credits = BankTransaction::where('bank_account_id', $account->id)
                ->where('transaction_type', 'credit')
                ->sum('amount');

            $debits = BankTransaction::where('bank_account_id', $account->id)
                ->where('transaction_type', 'debit')
                ->sum('amount');

            $balance = $account->initial_balance + $credits - $debits;

            return [
                'id' => $account->id,
                'name' => $account->account_name,
                'bank' => $account->bank_name,
                'account_number' => $account->account_number,
                'balance' => $balance,
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

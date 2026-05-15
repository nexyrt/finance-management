<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class BankAccountController extends Controller
{
    public function index(): Response
    {
        $accounts = BankAccount::query()
            ->with(['transactions', 'payments'])
            ->get()
            ->map(function (BankAccount $account) {
                $now = now();

                $monthlyIncome = $account->transactions()
                    ->where('transaction_type', 'credit')
                    ->whereMonth('transaction_date', $now->month)
                    ->whereYear('transaction_date', $now->year)
                    ->sum('amount')
                    + $account->payments()
                        ->whereMonth('payment_date', $now->month)
                        ->whereYear('payment_date', $now->year)
                        ->sum('amount');

                $monthlyExpense = $account->transactions()
                    ->where('transaction_type', 'debit')
                    ->whereMonth('transaction_date', $now->month)
                    ->whereYear('transaction_date', $now->year)
                    ->sum('amount');

                $thirtyDaysAgo = now()->subDays(30);
                $recentCredit = $account->transactions()
                    ->where('transaction_type', 'credit')
                    ->where('transaction_date', '>=', $thirtyDaysAgo)
                    ->sum('amount');
                $recentDebit = $account->transactions()
                    ->where('transaction_type', 'debit')
                    ->where('transaction_date', '>=', $thirtyDaysAgo)
                    ->sum('amount');
                $recentPayments = $account->payments()
                    ->where('payment_date', '>=', $thirtyDaysAgo)
                    ->sum('amount');

                return [
                    'id' => $account->id,
                    'account_name' => $account->account_name,
                    'account_number' => $account->account_number,
                    'bank_name' => $account->bank_name,
                    'branch' => $account->branch,
                    'initial_balance' => $account->initial_balance,
                    'balance' => $account->balance,
                    'monthly_income' => $monthlyIncome,
                    'monthly_expense' => $monthlyExpense,
                    'trend' => $recentCredit + $recentPayments - $recentDebit,
                    'transaction_count' => $account->transactions->count(),
                    'payment_count' => $account->payments->count(),
                ];
            });

        return Inertia::render('bank-accounts/index', [
            'accounts' => $accounts,
            'stats' => [
                'total_balance' => $accounts->sum('balance'),
                'total_income' => $accounts->sum('monthly_income'),
                'total_expense' => $accounts->sum('monthly_expense'),
                'account_count' => $accounts->count(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|unique:bank_accounts,account_number',
            'bank_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'initial_balance' => 'required|integer|min:0',
        ]);

        $account = BankAccount::create($validated);

        return response()->json([
            'message' => 'Rekening berhasil ditambahkan.',
            'account' => [
                'id' => $account->id,
                'account_name' => $account->account_name,
                'account_number' => $account->account_number,
                'bank_name' => $account->bank_name,
                'branch' => $account->branch,
                'initial_balance' => $account->initial_balance,
                'balance' => $account->balance,
                'monthly_income' => 0,
                'monthly_expense' => 0,
                'trend' => 0,
                'transaction_count' => 0,
                'payment_count' => 0,
            ],
        ]);
    }

    public function update(Request $request, BankAccount $bankAccount): JsonResponse
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|unique:bank_accounts,account_number,'.$bankAccount->id,
            'bank_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'initial_balance' => 'required|integer|min:0',
        ]);

        $bankAccount->update($validated);

        $now = now();
        $monthlyIncome = $bankAccount->transactions()
            ->where('transaction_type', 'credit')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount')
            + $bankAccount->payments()
                ->whereMonth('payment_date', $now->month)
                ->whereYear('payment_date', $now->year)
                ->sum('amount');

        $monthlyExpense = $bankAccount->transactions()
            ->where('transaction_type', 'debit')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount');

        $bankAccount->load(['transactions', 'payments']);

        return response()->json([
            'message' => 'Rekening berhasil diperbarui.',
            'account' => [
                'id' => $bankAccount->id,
                'account_name' => $bankAccount->account_name,
                'account_number' => $bankAccount->account_number,
                'bank_name' => $bankAccount->bank_name,
                'branch' => $bankAccount->branch,
                'initial_balance' => $bankAccount->initial_balance,
                'balance' => $bankAccount->balance,
                'monthly_income' => $monthlyIncome,
                'monthly_expense' => $monthlyExpense,
                'trend' => 0,
                'transaction_count' => $bankAccount->transactions->count(),
                'payment_count' => $bankAccount->payments->count(),
            ],
        ]);
    }

    public function destroy(Request $request, BankAccount $bankAccount): JsonResponse
    {
        $txCount = $bankAccount->transactions()->count();
        $payCount = $bankAccount->payments()->count();

        $bankAccount->transactions()->delete();
        $bankAccount->delete();

        return response()->json([
            'message' => 'Rekening berhasil dihapus'.($txCount + $payCount > 0
                ? " beserta {$txCount} transaksi dan {$payCount} pembayaran."
                : '.'),
        ]);
    }

    public function chartData(BankAccount $bankAccount): JsonResponse
    {
        $months = collect(range(11, 0))->map(function ($i) use ($bankAccount) {
            $date = now()->subMonths($i);

            $income = $bankAccount->transactions()
                ->where('transaction_type', 'credit')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount')
                + $bankAccount->payments()
                    ->whereMonth('payment_date', $date->month)
                    ->whereYear('payment_date', $date->year)
                    ->sum('amount');

            $expense = $bankAccount->transactions()
                ->where('transaction_type', 'debit')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount');

            return [
                'label' => $date->format('M'),
                'income' => (int) $income,
                'expense' => (int) $expense,
            ];
        });

        $categories = BankTransaction::query()
            ->where('bank_account_id', $bankAccount->id)
            ->where('transaction_type', 'debit')
            ->whereNotNull('category_id')
            ->join('transaction_categories', 'bank_transactions.category_id', '=', 'transaction_categories.id')
            ->select('transaction_categories.name', DB::raw('SUM(bank_transactions.amount) as total'))
            ->groupBy('transaction_categories.id', 'transaction_categories.name')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->map(fn ($r) => ['label' => $r->name, 'total' => (int) $r->total]);

        return response()->json([
            'months' => $months,
            'categories' => $categories,
        ]);
    }
}

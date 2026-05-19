<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Carbon\Carbon;
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

                $trend = (int) ($recentCredit + $recentPayments - $recentDebit);
                $trendPct = $account->initial_balance > 0
                    ? round(($trend / $account->initial_balance) * 100, 1)
                    : 0.0;

                return [
                    'id' => $account->id,
                    'account_name' => $account->account_name,
                    'account_number' => $account->account_number,
                    'last4_account_number' => substr((string) $account->account_number, -4),
                    'bank_name' => $account->bank_name,
                    'branch' => $account->branch,
                    'initial_balance' => $account->initial_balance,
                    'balance' => $account->balance,
                    'monthly_income' => (int) $monthlyIncome,
                    'monthly_expense' => (int) $monthlyExpense,
                    'trend' => $trend,
                    'trend_percentage' => $trendPct,
                    'sparkline_30d' => $this->buildSparkline30Days($account),
                    'smart_insight' => $this->buildSmartInsight($trend, $trendPct, (int) $monthlyIncome, (int) $monthlyExpense),
                    'transaction_count' => $account->transactions->count(),
                    'payment_count' => $account->payments->count(),
                ];
            });

        $trend30dTotal = (int) $accounts->sum('trend');
        $totalInitial = (int) $accounts->sum('initial_balance');

        return Inertia::render('bank-accounts/index', [
            'accounts' => $accounts,
            'stats' => [
                'total_balance' => $accounts->sum('balance'),
                'total_income' => $accounts->sum('monthly_income'),
                'total_expense' => $accounts->sum('monthly_expense'),
                'account_count' => $accounts->count(),
                'trend_30d_total' => $trend30dTotal,
                'trend_percentage_total' => $totalInitial > 0
                    ? round(($trend30dTotal / $totalInitial) * 100, 1)
                    : 0.0,
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
                'last4_account_number' => substr((string) $account->account_number, -4),
                'bank_name' => $account->bank_name,
                'branch' => $account->branch,
                'initial_balance' => $account->initial_balance,
                'balance' => $account->balance,
                'monthly_income' => 0,
                'monthly_expense' => 0,
                'trend' => 0,
                'trend_percentage' => 0.0,
                'sparkline_30d' => array_fill(0, 30, 0),
                'smart_insight' => 'Rekening baru — belum ada aktivitas.',
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
        $monthlyIncome = (int) ($bankAccount->transactions()
            ->where('transaction_type', 'credit')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount')
            + $bankAccount->payments()
                ->whereMonth('payment_date', $now->month)
                ->whereYear('payment_date', $now->year)
                ->sum('amount'));

        $monthlyExpense = (int) $bankAccount->transactions()
            ->where('transaction_type', 'debit')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount');

        $thirtyDaysAgo = now()->subDays(30);
        $recentCredit = $bankAccount->transactions()
            ->where('transaction_type', 'credit')
            ->where('transaction_date', '>=', $thirtyDaysAgo)
            ->sum('amount');
        $recentDebit = $bankAccount->transactions()
            ->where('transaction_type', 'debit')
            ->where('transaction_date', '>=', $thirtyDaysAgo)
            ->sum('amount');
        $recentPayments = $bankAccount->payments()
            ->where('payment_date', '>=', $thirtyDaysAgo)
            ->sum('amount');

        $trend = (int) ($recentCredit + $recentPayments - $recentDebit);
        $trendPct = $bankAccount->initial_balance > 0
            ? round(($trend / $bankAccount->initial_balance) * 100, 1)
            : 0.0;

        $bankAccount->load(['transactions', 'payments']);

        return response()->json([
            'message' => 'Rekening berhasil diperbarui.',
            'account' => [
                'id' => $bankAccount->id,
                'account_name' => $bankAccount->account_name,
                'account_number' => $bankAccount->account_number,
                'last4_account_number' => substr((string) $bankAccount->account_number, -4),
                'bank_name' => $bankAccount->bank_name,
                'branch' => $bankAccount->branch,
                'initial_balance' => $bankAccount->initial_balance,
                'balance' => $bankAccount->balance,
                'monthly_income' => $monthlyIncome,
                'monthly_expense' => $monthlyExpense,
                'trend' => $trend,
                'trend_percentage' => $trendPct,
                'sparkline_30d' => $this->buildSparkline30Days($bankAccount),
                'smart_insight' => $this->buildSmartInsight($trend, $trendPct, $monthlyIncome, $monthlyExpense),
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
            ->select('transaction_categories.label', DB::raw('SUM(bank_transactions.amount) as total'))
            ->groupBy('transaction_categories.id', 'transaction_categories.label')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->map(fn ($r) => ['label' => $r->label, 'total' => (int) $r->total]);

        return response()->json([
            'months' => $months,
            'categories' => $categories,
            'daily_30d' => $this->buildSparkline30Days($bankAccount),
        ]);
    }

    public function transactions(BankAccount $bankAccount, Request $request): JsonResponse
    {
        $query = $bankAccount->transactions()->with('category.parent');

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->input('transaction_type'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('month')) {
            [$year, $month] = explode('-', $request->input('month'));
            $query->whereYear('transaction_date', $year)->whereMonth('transaction_date', $month);
        }

        if ($request->filled('search')) {
            $search = '%'.$request->input('search').'%';
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', $search)
                    ->orWhere('reference_number', 'like', $search);
            });
        }

        $sortBy = in_array($request->input('sort_by'), ['description', 'transaction_date', 'amount'])
            ? $request->input('sort_by')
            : 'transaction_date';
        $sortDir = $request->input('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortBy, $sortDir);

        $perPage = min((int) $request->input('per_page', 15), 100);
        $paginated = $query->paginate($perPage);

        return response()->json([
            'data' => $paginated->getCollection()->map(fn ($t) => [
                'id' => $t->id,
                'transaction_type' => $t->transaction_type,
                'amount' => $t->amount,
                'transaction_date' => $t->transaction_date?->format('Y-m-d'),
                'description' => $t->description,
                'reference_number' => $t->reference_number,
                'category_id' => $t->category_id,
                'category' => $t->category ? [
                    'id' => $t->category->id,
                    'label' => $t->category->label,
                    'parent' => $t->category->parent ? ['id' => $t->category->parent->id, 'label' => $t->category->parent->label] : null,
                ] : null,
                'attachment_url' => $t->attachment_path ? \Storage::url($t->attachment_path) : null,
                'attachment_name' => $t->attachment_name,
            ]),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    public function payments(BankAccount $bankAccount, Request $request): JsonResponse
    {
        $query = $bankAccount->payments()->with('invoice.client');

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->filled('invoice_status')) {
            $query->whereHas('invoice', fn ($q) => $q->where('status', $request->input('invoice_status')));
        }

        if ($request->filled('month')) {
            [$year, $month] = explode('-', $request->input('month'));
            $query->whereYear('payment_date', $year)->whereMonth('payment_date', $month);
        }

        if ($request->filled('search')) {
            $search = '%'.$request->input('search').'%';
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', $search)
                    ->orWhereHas('invoice', fn ($iq) => $iq->where('invoice_number', 'like', $search))
                    ->orWhereHas('invoice.client', fn ($cq) => $cq->where('name', 'like', $search));
            });
        }

        $sortBy = $request->input('sort_by', 'payment_date');
        $sortDir = $request->input('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';

        if (in_array($sortBy, ['payment_date', 'amount', 'payment_method'])) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('payment_date', $sortDir);
        }

        $perPage = min((int) $request->input('per_page', 15), 100);
        $paginated = $query->paginate($perPage);

        return response()->json([
            'data' => $paginated->getCollection()->map(fn ($p) => [
                'id' => $p->id,
                'amount' => $p->amount,
                'payment_date' => $p->payment_date?->format('Y-m-d'),
                'payment_method' => $p->payment_method,
                'reference_number' => $p->reference_number,
                'invoice_number' => $p->invoice?->invoice_number,
                'invoice_status' => $p->invoice?->status,
                'client_name' => $p->invoice?->client?->name,
                'client_type' => $p->invoice?->client?->type,
                'attachment_url' => $p->attachment_path ? \Storage::url($p->attachment_path) : null,
                'attachment_name' => $p->attachment_name,
            ]),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    public function monthlyStats(BankAccount $bankAccount, Request $request): JsonResponse
    {
        // Custom period override
        if ($request->filled('from') && $request->filled('to')) {
            $start = Carbon::parse($request->input('from'))->startOfDay();
            $end = Carbon::parse($request->input('to'))->endOfDay();
            $periodLabel = $start->translatedFormat('d M').' – '.$end->translatedFormat('d M Y');
        } else {
            // Smart period: current month if it has data, else latest month with data
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
            $periodLabel = null;
        }

        // Smart period fallback only when no custom period
        if (! $request->filled('from')) {
            $hasData = $bankAccount->transactions()->whereBetween('transaction_date', [$start, $end])->exists()
                || $bankAccount->payments()->whereBetween('payment_date', [$start, $end])->exists();

            if (! $hasData) {
                $latestTrx = $bankAccount->transactions()->orderByDesc('transaction_date')->value('transaction_date');
                $latestPay = $bankAccount->payments()->orderByDesc('payment_date')->value('payment_date');
                $latest = collect([$latestTrx, $latestPay])->filter()->max();

                if ($latest) {
                    $d = Carbon::parse($latest);
                    $start = $d->copy()->startOfMonth();
                    $end = $d->copy()->endOfMonth();
                }
            }
        }

        $trxStats = \DB::table('bank_transactions')
            ->where('bank_account_id', $bankAccount->id)
            ->whereBetween('transaction_date', [$start, $end])
            ->selectRaw("
                SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as credit_total,
                SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as debit_total
            ")
            ->first();

        $paymentsIncome = (int) $bankAccount->payments()
            ->whereBetween('payment_date', [$start, $end])
            ->sum('amount');

        $totalIncome = $paymentsIncome + (int) ($trxStats->credit_total ?? 0);
        $totalExpense = (int) ($trxStats->debit_total ?? 0);

        // 12-month bar chart data (batched)
        $globalStart = now()->startOfMonth()->subMonths(11);
        $globalEnd = now()->endOfMonth();

        $paymentsByMonth = \DB::table('payments')
            ->where('bank_account_id', $bankAccount->id)
            ->whereBetween('payment_date', [$globalStart, $globalEnd])
            ->selectRaw('YEAR(payment_date) as y, MONTH(payment_date) as m, SUM(amount) as total')
            ->groupByRaw('YEAR(payment_date), MONTH(payment_date)')
            ->get()
            ->keyBy(fn ($row) => $row->y.'-'.$row->m);

        $trxByMonth = \DB::table('bank_transactions')
            ->where('bank_account_id', $bankAccount->id)
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
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->startOfMonth()->subMonths($i);
            $key = $month->year.'-'.$month->month;
            $months[] = [
                'label' => $month->format('M'),
                'income' => (int) ($paymentsByMonth[$key]->total ?? 0) + (int) ($trxByMonth[$key]->credit_total ?? 0),
                'expense' => (int) ($trxByMonth[$key]->debit_total ?? 0),
            ];
        }

        // Category breakdown for the smart period
        $categories = \DB::table('bank_transactions')
            ->join('transaction_categories', 'bank_transactions.category_id', '=', 'transaction_categories.id')
            ->where('bank_transactions.bank_account_id', $bankAccount->id)
            ->where('bank_transactions.transaction_type', 'debit')
            ->whereBetween('bank_transactions.transaction_date', [$start, $end])
            ->whereNotNull('bank_transactions.category_id')
            ->selectRaw('transaction_categories.label, SUM(bank_transactions.amount) as total')
            ->groupBy('transaction_categories.id', 'transaction_categories.label')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->map(fn ($r) => ['label' => $r->label, 'total' => (int) $r->total])
            ->values()
            ->toArray();

        return response()->json([
            'period_label' => $periodLabel ?? $start->translatedFormat('F Y'),
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_cashflow' => $totalIncome - $totalExpense,
            'months' => $months,
            'categories' => $categories,
        ]);
    }

    public function activity(BankAccount $bankAccount): JsonResponse
    {
        $payments = $bankAccount->payments()
            ->latest('payment_date')
            ->limit(8)
            ->with('invoice.client')
            ->get()
            ->map(fn ($p) => [
                'id' => "payment-{$p->id}",
                'date' => $p->payment_date instanceof Carbon
                    ? $p->payment_date->format('Y-m-d')
                    : (string) $p->payment_date,
                'type' => 'in',
                'amount' => (int) $p->amount,
                'label' => 'Pemb. '.($p->invoice?->invoice_number ?? '#'),
            ]);

        $transactions = $bankAccount->transactions()
            ->latest('transaction_date')
            ->limit(8)
            ->with('category')
            ->get()
            ->map(fn ($t) => [
                'id' => "tx-{$t->id}",
                'date' => $t->transaction_date instanceof Carbon
                    ? $t->transaction_date->format('Y-m-d')
                    : (string) $t->transaction_date,
                'type' => $t->transaction_type === 'credit' ? 'in' : 'out',
                'amount' => (int) $t->amount,
                'label' => $t->description ?? $t->category?->label ?? 'Transaksi',
            ]);

        $combined = $payments->concat($transactions)
            ->sortByDesc('date')
            ->take(8)
            ->values();

        return response()->json(['activity' => $combined]);
    }

    private function buildSparkline30Days(BankAccount $account): array
    {
        $days = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();

            $credit = (int) $account->transactions()
                ->where('transaction_type', 'credit')
                ->whereDate('transaction_date', $date)
                ->sum('amount');

            $debit = (int) $account->transactions()
                ->where('transaction_type', 'debit')
                ->whereDate('transaction_date', $date)
                ->sum('amount');

            $payments = (int) $account->payments()
                ->whereDate('payment_date', $date)
                ->sum('amount');

            $days[] = $credit + $payments - $debit;
        }

        return $days;
    }

    private function buildSmartInsight(int $trend, float $trendPct, int $monthlyIncome, int $monthlyExpense): string
    {
        if ($monthlyIncome === 0 && $monthlyExpense === 0) {
            return 'Belum ada aktivitas bulan ini.';
        }

        if ($trendPct >= 10) {
            return 'Cashflow positif kuat 30 hari terakhir.';
        }

        if ($trendPct >= 3) {
            return 'Cashflow positif bulan ini.';
        }

        if ($trend > 0) {
            return 'Net positif tipis 30 hari terakhir.';
        }

        if ($trend === 0) {
            return 'Cashflow seimbang bulan ini.';
        }

        if ($trendPct <= -10) {
            return 'Pengeluaran tinggi — perlu perhatian.';
        }

        return 'Net negatif 30 hari terakhir.';
    }
}

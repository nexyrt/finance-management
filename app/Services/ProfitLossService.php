<?php

namespace App\Services;

use App\Models\BankTransaction;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Generates the Profit & Loss (Laporan Laba Rugi) report.
 *
 * Policy (final, see .claude/context/laba-rugi.md):
 *  - Cash basis: revenue recognized when payment received.
 *  - Cost-recovery HPP timing: payments first cover invoice COGS until it is
 *    fully recovered, then the remainder is profit. HPP recognized to date
 *    for an invoice = MIN(paid_to_date, total_cogs).
 *  - HPP also accepts MANUAL transactions (pl_group=cogs) for non-invoice
 *    sales. Anti-double-count rule is a data-entry discipline.
 *  - Categories drive expense/non-invoice income classification via pl_group.
 *  - financing/transfer category types are excluded entirely.
 *  - Unclassified income/expense surface in a separate bucket — they are NOT
 *    rolled into totals, so the user is forced to classify rather than
 *    silently mis-categorize.
 */
class ProfitLossService
{
    /**
     * Build the full P&L report for the period [start, end] (inclusive).
     *
     * @return array<string, mixed>
     */
    public function generate(Carbon $start, Carbon $end): array
    {
        $invoiceRevenue = $this->revenueFromInvoices($start, $end);
        $nonInvoiceRevenue = $this->transactionsByGroup('revenue', 'credit', $start, $end);
        $revenueTotal = $invoiceRevenue + $nonInvoiceRevenue['total'];

        $invoiceCogs = $this->cogsFromInvoices($start, $end);
        $manualCogs = $this->transactionsByGroup('cogs', 'debit', $start, $end);
        $cogsTotal = $invoiceCogs + $manualCogs['total'];

        $grossProfit = $revenueTotal - $cogsTotal;

        $opex = $this->transactionsByGroup('opex', 'debit', $start, $end);
        $operatingProfit = $grossProfit - $opex['total'];

        $otherIncome = $this->transactionsByGroup('other_income', 'credit', $start, $end);
        $otherExpense = $this->transactionsByGroup('other_expense', 'debit', $start, $end);
        $preTaxProfit = $operatingProfit + $otherIncome['total'] - $otherExpense['total'];

        $tax = $this->transactionsByGroup('tax', 'debit', $start, $end);
        $netProfit = $preTaxProfit - $tax['total'];

        return [
            'period' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'revenue' => [
                'invoice' => $invoiceRevenue,
                'non_invoice' => $nonInvoiceRevenue['total'],
                'non_invoice_by_category' => $nonInvoiceRevenue['by_category'],
                'total' => $revenueTotal,
            ],
            'cogs' => [
                'invoice' => $invoiceCogs,
                'manual' => $manualCogs['total'],
                'manual_by_category' => $manualCogs['by_category'],
                'total' => $cogsTotal,
            ],
            'gross_profit' => $grossProfit,
            'opex' => $opex,
            'operating_profit' => $operatingProfit,
            'other_income' => $otherIncome,
            'other_expense' => $otherExpense,
            'pre_tax_profit' => $preTaxProfit,
            'tax' => $tax,
            'net_profit' => $netProfit,
            'unclassified' => [
                'income' => $this->unclassified('credit', $start, $end),
                'expense' => $this->unclassified('debit', $start, $end),
            ],
        ];
    }

    /**
     * Cash revenue from invoices: sum of Payment.amount within the period.
     */
    private function revenueFromInvoices(Carbon $start, Carbon $end): int
    {
        return (int) Payment::whereBetween('payment_date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');
    }

    /**
     * Invoice COGS recognized in the period using the cost-recovery method:
     * For each invoice, contribution = MIN(paid_through_end, total_cogs) − MIN(paid_through_start_prev, total_cogs).
     *
     * Only invoices that received at least one payment in [start, end] can
     * contribute; invoices fully paid before the period have already had
     * all their HPP recognized in earlier periods (delta = 0).
     */
    private function cogsFromInvoices(Carbon $start, Carbon $end): int
    {
        $invoiceIds = Payment::query()
            ->whereBetween('payment_date', [$start->toDateString(), $end->toDateString()])
            ->distinct()
            ->pluck('invoice_id');

        if ($invoiceIds->isEmpty()) {
            return 0;
        }

        $invoices = Invoice::query()->whereIn('id', $invoiceIds)->with('items')->get();

        $total = 0;
        foreach ($invoices as $invoice) {
            $totalCogs = (int) $invoice->total_cogs;
            if ($totalCogs <= 0) {
                continue;
            }

            $paidThroughEnd = (int) Payment::where('invoice_id', $invoice->id)
                ->where('payment_date', '<=', $end->toDateString())
                ->sum('amount');

            $paidThroughStartPrev = (int) Payment::where('invoice_id', $invoice->id)
                ->where('payment_date', '<', $start->toDateString())
                ->sum('amount');

            $total += min($paidThroughEnd, $totalCogs) - min($paidThroughStartPrev, $totalCogs);
        }

        return $total;
    }

    /**
     * Aggregate BankTransaction rows filtered by category pl_group + direction.
     *
     * @return array{by_category: list<array{category_id:int, category_label:string, amount:int}>, total: int}
     */
    private function transactionsByGroup(string $plGroup, string $direction, Carbon $start, Carbon $end): array
    {
        $rows = BankTransaction::query()
            ->join('transaction_categories', 'bank_transactions.category_id', '=', 'transaction_categories.id')
            ->where('transaction_categories.pl_group', $plGroup)
            ->where('bank_transactions.transaction_type', $direction)
            ->whereBetween('bank_transactions.transaction_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('transaction_categories.id', 'transaction_categories.label')
            ->orderBy('transaction_categories.label')
            ->select(
                'transaction_categories.id as category_id',
                'transaction_categories.label as category_label',
                DB::raw('SUM(bank_transactions.amount) as total')
            )
            ->get();

        return [
            'by_category' => $rows->map(fn ($r) => [
                'category_id' => (int) $r->category_id,
                'category_label' => (string) $r->category_label,
                'amount' => (int) $r->total,
            ])->all(),
            'total' => (int) $rows->sum('total'),
        ];
    }

    /**
     * Transactions that cannot be placed in the P&L yet — surfaced separately
     * so the user can fix the classification. Includes:
     *   - transactions with no category at all
     *   - transactions whose category is income/expense type but has no pl_group
     * Financing/transfer-typed categories are excluded entirely (intentional).
     *
     * @return array{by_category: list<array{category_id:?int, category_label:string, amount:int}>, total: int}
     */
    private function unclassified(string $direction, Carbon $start, Carbon $end): array
    {
        $matchingType = $direction === 'credit' ? 'income' : 'expense';

        $rows = BankTransaction::query()
            ->leftJoin('transaction_categories', 'bank_transactions.category_id', '=', 'transaction_categories.id')
            ->where('bank_transactions.transaction_type', $direction)
            ->whereBetween('bank_transactions.transaction_date', [$start->toDateString(), $end->toDateString()])
            ->where(function ($q) use ($matchingType) {
                $q->whereNull('transaction_categories.id')
                    ->orWhere(function ($q2) use ($matchingType) {
                        $q2->where('transaction_categories.type', $matchingType)
                            ->whereNull('transaction_categories.pl_group');
                    });
            })
            ->groupBy('transaction_categories.id', 'transaction_categories.label')
            ->orderByRaw('transaction_categories.label IS NULL DESC')
            ->orderBy('transaction_categories.label')
            ->select(
                'transaction_categories.id as category_id',
                DB::raw("COALESCE(transaction_categories.label, '(Tanpa Kategori)') as category_label"),
                DB::raw('SUM(bank_transactions.amount) as total')
            )
            ->get();

        return [
            'by_category' => $rows->map(fn ($r) => [
                'category_id' => $r->category_id !== null ? (int) $r->category_id : null,
                'category_label' => (string) $r->category_label,
                'amount' => (int) $r->total,
            ])->all(),
            'total' => (int) $rows->sum('total'),
        ];
    }
}

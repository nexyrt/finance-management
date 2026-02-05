<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Payment;
use App\Models\CompanyProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CashFlowExportService
{
    /**
     * Generate Cash Flow PDF Report
     */
    public function generatePdf(
        ?int $bankAccountId = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $month = null,
        ?string $year = null
    ) {
        // If startDate and endDate are provided, use them
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $periodText = $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
        } else {
            // Otherwise use month/year
            $month = $month ?? now()->format('m');
            $year = $year ?? now()->format('Y');
            $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $end = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            $periodText = $this->getIndonesianMonth((int)$month) . ' ' . $year;
        }

        // Get company profile
        $company = CompanyProfile::current();

        // Get bank account if specified
        $bankAccount = $bankAccountId ? BankAccount::find($bankAccountId) : null;

        // Get transactions for the period
        $transactions = $this->getTransactionsForDateRange($bankAccountId, $start, $end);

        // Calculate opening balance (balance before start date)
        $openingBalance = $this->getBalanceBeforeDate($bankAccountId, $start);

        // Prepare data for PDF
        $data = [
            'company' => $company,
            'bankAccount' => $bankAccount,
            'periodText' => $periodText,
            'startDate' => $start,
            'endDate' => $end,
            'openingBalance' => $openingBalance,
            'transactions' => $transactions,
            'closingBalance' => $this->calculateClosingBalance($openingBalance, $transactions),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.cash-flow-report', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    /**
     * Get all transactions for a date range
     */
    private function getTransactionsForDateRange(?int $bankAccountId, Carbon $startDate, Carbon $endDate)
    {

        // Get bank transactions
        $bankTransactionsQuery = BankTransaction::with(['category', 'bankAccount'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date')
            ->orderBy('id');

        if ($bankAccountId) {
            $bankTransactionsQuery->where('bank_account_id', $bankAccountId);
        }

        $bankTransactions = $bankTransactionsQuery->get()->map(function ($transaction) {
            return [
                'date' => $transaction->transaction_date,
                'description' => $transaction->description,
                'category' => $transaction->category?->full_path ?? '-',
                'credit' => $transaction->transaction_type === 'credit' ? $transaction->amount : 0,
                'debit' => $transaction->transaction_type === 'debit' ? $transaction->amount : 0,
                'type' => 'transaction',
            ];
        });

        // Get payments (invoice payments)
        $paymentsQuery = Payment::with(['invoice.client', 'bankAccount'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date')
            ->orderBy('id');

        if ($bankAccountId) {
            $paymentsQuery->where('bank_account_id', $bankAccountId);
        }

        $payments = $paymentsQuery->get()->map(function ($payment) {
            return [
                'date' => $payment->payment_date,
                'description' => 'PENGAJUAN DANA NO. ' . $payment->invoice->invoice_number,
                'category' => 'Pembayaran Invoice - ' . ($payment->invoice->client->name ?? ''),
                'credit' => $payment->amount,
                'debit' => 0,
                'type' => 'payment',
            ];
        });

        // Merge and sort by date
        return $bankTransactions->concat($payments)->sortBy('date')->values();
    }

    /**
     * Calculate opening balance (balance before start date)
     * Formula from BankAccount model: initial_balance + payments + credits - debits
     */
    private function getBalanceBeforeDate(?int $bankAccountId, Carbon $beforeDate): int
    {
        if ($bankAccountId) {
            $account = BankAccount::find($bankAccountId);
            $initialBalance = $account->initial_balance ?? 0;

            // Sum all transactions before start date
            $creditSum = BankTransaction::where('bank_account_id', $bankAccountId)
                ->where('transaction_type', 'credit')
                ->where('transaction_date', '<', $beforeDate)
                ->sum('amount');

            $debitSum = BankTransaction::where('bank_account_id', $bankAccountId)
                ->where('transaction_type', 'debit')
                ->where('transaction_date', '<', $beforeDate)
                ->sum('amount');

            $paymentsSum = Payment::where('bank_account_id', $bankAccountId)
                ->where('payment_date', '<', $beforeDate)
                ->sum('amount');

            // Formula: initial_balance + payments + credits - debits
            return $initialBalance + $paymentsSum + $creditSum - $debitSum;
        }

        // If no specific account, sum all accounts
        $totalInitial = BankAccount::sum('initial_balance');

        $totalCredit = BankTransaction::where('transaction_type', 'credit')
            ->where('transaction_date', '<', $beforeDate)
            ->sum('amount');

        $totalDebit = BankTransaction::where('transaction_type', 'debit')
            ->where('transaction_date', '<', $beforeDate)
            ->sum('amount');

        $totalPayments = Payment::where('payment_date', '<', $beforeDate)
            ->sum('amount');

        return $totalInitial + $totalPayments + $totalCredit - $totalDebit;
    }

    /**
     * Calculate closing balance
     */
    private function calculateClosingBalance(int $openingBalance, $transactions): int
    {
        $balance = $openingBalance;

        foreach ($transactions as $transaction) {
            $balance += $transaction['credit'];
            $balance -= $transaction['debit'];
        }

        return $balance;
    }

    /**
     * Get Indonesian month name
     */
    private function getIndonesianMonth(int $month): string
    {
        $months = [
            1 => 'JANUARI',
            2 => 'FEBRUARI',
            3 => 'MARET',
            4 => 'APRIL',
            5 => 'MEI',
            6 => 'JUNI',
            7 => 'JULI',
            8 => 'AGUSTUS',
            9 => 'SEPTEMBER',
            10 => 'OKTOBER',
            11 => 'NOVEMBER',
            12 => 'DESEMBER',
        ];

        return $months[$month] ?? '';
    }
}

<?php

namespace App\Services;

use App\Models\Payment;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentExportService
{
    public function exportExcel(array $filters = [])
    {
        $payments = $this->getFilteredPayments($filters);

        return Excel::download(
            new \App\Exports\PaymentsExport($payments),
            'payments-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf(array $filters = [])
    {
        $payments = $this->getFilteredPayments($filters);
        $stats = $this->calculateStats($payments);

        return Pdf::loadView('exports.payments-pdf', [
            'payments' => $payments,
            'stats' => $stats,
            'filters' => $filters,
            'exportDate' => now()
        ])->setPaper('A4', 'landscape');
    }

    private function getFilteredPayments(array $filters = [])
    {
        $query = Payment::query()
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->join('bank_accounts', 'payments.bank_account_id', '=', 'bank_accounts.id')
            ->select([
                'payments.*',
                'invoices.invoice_number',
                'invoices.status as invoice_status',
                'clients.name as client_name',
                'clients.type as client_type',
                'bank_accounts.bank_name',
                'bank_accounts.account_name',
            ]);

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('invoices.invoice_number', 'like', "%{$filters['search']}%")
                    ->orWhere('clients.name', 'like', "%{$filters['search']}%")
                    ->orWhere('payments.reference_number', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['paymentMethodFilter'])) {
            $query->where('payments.payment_method', $filters['paymentMethodFilter']);
        }

        if (!empty($filters['bankAccountFilter'])) {
            $query->where('payments.bank_account_id', $filters['bankAccountFilter']);
        }

        if (!empty($filters['invoiceStatusFilter'])) {
            $query->where('invoices.status', $filters['invoiceStatusFilter']);
        }

        if (!empty($filters['dateRange']) && count($filters['dateRange']) >= 2) {
            $query->whereDate('payments.payment_date', '>=', $filters['dateRange'][0])
                ->whereDate('payments.payment_date', '<=', $filters['dateRange'][1]);
        }

        return $query->orderBy('payments.payment_date', 'desc')->get();
    }

    private function calculateStats($payments)
    {
        return [
            'total_count' => $payments->count(),
            'total_amount' => $payments->sum('amount'),
            'by_method' => [
                'bank_transfer' => $payments->where('payment_method', 'bank_transfer')->sum('amount'),
                'cash' => $payments->where('payment_method', 'cash')->sum('amount'),
            ],
            'by_status' => [
                'paid' => $payments->where('invoice_status', 'paid')->sum('amount'),
                'partially_paid' => $payments->where('invoice_status', 'partially_paid')->sum('amount'),
            ]
        ];
    }
}
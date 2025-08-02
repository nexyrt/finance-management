<?php

namespace App\Services;

use App\Models\Invoice;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceExportService
{
    public function exportExcel(array $filters = [])
    {
        $invoices = $this->getFilteredInvoices($filters);

        return Excel::download(
            new \App\Exports\InvoicesExport($invoices),
            'invoices-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf(array $filters = [])
    {
        $invoices = $this->getFilteredInvoices($filters);

        return Pdf::loadView('exports.invoices-pdf', [
            'invoices' => $invoices,
            'filters' => $filters,
            'exportDate' => now()
        ])->setPaper('A4', 'landscape');
    }

    private function getFilteredInvoices(array $filters = [])
    {
        $query = Invoice::query()
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->leftJoin('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->select([
                'invoices.*',
                'clients.name as client_name',
                'clients.type as client_type',
                \DB::raw('COALESCE(SUM(payments.amount), 0) as amount_paid')
            ])
            ->groupBy([
                'invoices.id',
                'invoices.invoice_number',
                'invoices.billed_to_id',
                'invoices.total_amount',
                'invoices.issue_date',
                'invoices.due_date',
                'invoices.status',
                'invoices.created_at',
                'invoices.updated_at',
                'invoices.subtotal',
                'invoices.discount_amount',
                'invoices.discount_type',
                'invoices.discount_value',
                'invoices.discount_reason',
                'clients.name',
                'clients.type'
            ]);

        // Apply same filters as table
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('invoices.invoice_number', 'like', "%{$filters['search']}%")
                    ->orWhere('clients.name', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['statusFilter'])) {
            $query->where('invoices.status', $filters['statusFilter']);
        }

        if (!empty($filters['clientFilter'])) {
            $query->where('invoices.billed_to_id', $filters['clientFilter']);
        }

        if (!empty($filters['dateRange']) && count($filters['dateRange']) >= 2) {
            $query->whereDate('invoices.issue_date', '>=', $filters['dateRange'][0])
                ->whereDate('invoices.issue_date', '<=', $filters['dateRange'][1]);
        }

        return $query->orderBy('invoices.invoice_number', 'desc')->get();
    }
}
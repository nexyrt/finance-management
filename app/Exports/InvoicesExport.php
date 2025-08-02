<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $invoices;

    public function __construct(Collection $invoices)
    {
        $this->invoices = $invoices;
    }

    public function collection()
    {
        return $this->invoices;
    }

    public function headings(): array
    {
        return [
            'No. Invoice',
            'Klien',
            'Tipe Klien',
            'Tanggal Invoice',
            'Tanggal Jatuh Tempo',
            'Status',
            'Subtotal',
            'Diskon',
            'Total',
            'Terbayar',
            'Sisa',
            'Dibuat'
        ];
    }

    public function map($invoice): array
    {
        $remainingAmount = $invoice->total_amount - $invoice->amount_paid;
        
        return [
            $invoice->invoice_number,
            $invoice->client_name,
            ucfirst($invoice->client_type),
            $invoice->issue_date->format('d/m/Y'),
            $invoice->due_date->format('d/m/Y'),
            ucfirst($invoice->status),
            'Rp ' . number_format($invoice->subtotal, 0, ',', '.'),
            'Rp ' . number_format($invoice->discount_amount, 0, ',', '.'),
            'Rp ' . number_format($invoice->total_amount, 0, ',', '.'),
            'Rp ' . number_format($invoice->amount_paid, 0, ',', '.'),
            'Rp ' . number_format($remainingAmount, 0, ',', '.'),
            $invoice->created_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // No. Invoice
            'B' => 25, // Klien
            'C' => 12, // Tipe Klien
            'D' => 15, // Tanggal Invoice
            'E' => 15, // Tanggal Jatuh Tempo
            'F' => 12, // Status
            'G' => 15, // Subtotal
            'H' => 15, // Diskon
            'I' => 15, // Total
            'J' => 15, // Terbayar
            'K' => 15, // Sisa
            'L' => 18, // Dibuat
        ];
    }
}
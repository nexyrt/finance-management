<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $payments;

    public function __construct($payments)
    {
        $this->payments = $payments;
    }

    public function collection()
    {
        return $this->payments;
    }

    public function headings(): array
    {
        return [
            'Tanggal Pembayaran',
            'No. Invoice',
            'Klien',
            'Tipe Klien',
            'Jumlah (Rp)',
            'Metode Pembayaran',
            'Bank',
            'Rekening',
            'Status Invoice',
            'No. Referensi',
            'Dibuat Pada'
        ];
    }

    public function map($payment): array
    {
        return [
            \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y'),
            $payment->invoice_number,
            $payment->client_name,
            $payment->client_type === 'individual' ? 'Individu' : 'Perusahaan',
            $payment->amount,
            $payment->payment_method === 'bank_transfer' ? 'Transfer Bank' : 'Tunai',
            $payment->bank_name,
            $payment->account_name,
            match($payment->invoice_status) {
                'paid' => 'Lunas',
                'partially_paid' => 'Sebagian Dibayar',
                'sent' => 'Terkirim',
                'overdue' => 'Terlambat',
                default => ucfirst($payment->invoice_status)
            },
            $payment->reference_number ?: '-',
            $payment->created_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A1:K1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3b82f6']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF']]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Tanggal
            'B' => 15, // Invoice
            'C' => 25, // Klien
            'D' => 12, // Tipe
            'E' => 15, // Jumlah
            'F' => 15, // Metode
            'G' => 15, // Bank
            'H' => 20, // Rekening
            'I' => 15, // Status
            'J' => 15, // Referensi
            'K' => 15  // Created
        ];
    }
}
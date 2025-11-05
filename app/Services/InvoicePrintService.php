<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePrintService
{
    public function generateSingleInvoicePdf(Invoice $invoice, ?int $dpAmount = null)
    {
        $invoice->load(['client', 'items.client', 'payments.bankAccount']);

        // DP Logic
        $isDownPayment = !is_null($dpAmount) && $dpAmount > 0;
        $displayAmount = $isDownPayment ? $dpAmount : $invoice->total_amount;

        $netRevenue = $invoice->items->where('is_tax_deposit', false)->sum('amount');
        $totalCogs = $invoice->items->where('is_tax_deposit', false)->sum('cogs_amount');
        $grossProfit = $netRevenue - $totalCogs - ($invoice->discount_amount ?? 0);

        $regularItems = $invoice->items->where('is_tax_deposit', false);
        $taxDepositItems = $invoice->items->where('is_tax_deposit', true);

        $data = [
            'invoice' => $invoice,
            'client' => $invoice->client,
            'items' => $invoice->items,
            'regular_items' => $regularItems,
            'tax_deposit_items' => $taxDepositItems,
            'payments' => $invoice->payments,
            'company' => $this->getCompanyInfo(),
            'terbilang' => $this->numberToWords($displayAmount), // Changed
            'is_down_payment' => $isDownPayment,                // Added
            'dp_amount' => $dpAmount,                           // Added
            'display_amount' => $displayAmount,                 // Added
            'financial_summary' => [
                'net_revenue' => $netRevenue,
                'total_cogs' => $totalCogs,
                'gross_profit' => $grossProfit,
                'tax_deposits_total' => $taxDepositItems->sum('amount'),
                'has_tax_deposits' => $taxDepositItems->isNotEmpty(),
                'profit_margin' => $netRevenue > 0 ? ($grossProfit / $netRevenue) * 100 : 0
            ]
        ];

        $pdf = Pdf::loadView('pdf.kisantra-invoice', $data)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf;
    }

    public function downloadSingleInvoice(Invoice $invoice)
    {
        $pdf = $this->generateSingleInvoicePdf($invoice);

        $filename = 'Invoice-' . str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $invoice->invoice_number) . '.pdf';

        return $pdf->download($filename);
    }

    private function getCompanyInfo(): array
    {
        return [
            'name' => 'PT. KINARA SADAYATRA NUSANTARA',
            'address' => 'Jl. A. Wahab Syahranie Perum Pondok Alam Indah, Nomor 3D, Kel. Sempaja Barat, Kota Samarinda - Kalimantan Timur',
            'email' => 'kisantra.official@gmail.com',
            'phone' => '0852-8888-2600',
            'logo_base64' => $this->getLogoBase64(),
            'signature_base64' => $this->getSignatureBase64(),
            'stamp_base64' => $this->getStampBase64(),
            'bank_accounts' => [
                [
                    'bank' => 'MANDIRI',
                    'account_number' => '1480045452425',
                    'account_name' => 'PT. KINARA SADAYATRA NUSANTARA'
                ]
            ],
            'signature' => [
                'name' => 'Mohammad Denny Jodysetiawan',
                'position' => 'Manajer Keuangan'
            ]
        ];
    }

    private function getLogoBase64(): string
    {
        $logoPath = public_path('images/letter-head.png');
        if (file_exists($logoPath)) {
            return 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }
        return '';
    }

    private function getSignatureBase64(): string
    {
        $signaturePath = public_path('images/pdf-signature.png');
        if (file_exists($signaturePath)) {
            return 'data:image/png;base64,' . base64_encode(file_get_contents($signaturePath));
        }
        return '';
    }

    private function getStampBase64(): string
    {
        $stampPath = public_path('images/kisantra-stamp.png');
        if (file_exists($stampPath)) {
            return 'data:image/png;base64,' . base64_encode(file_get_contents($stampPath));
        }
        return '';
    }

    private function numberToWords($number): string
    {
        $words = [
            '',
            'Satu',
            'Dua',
            'Tiga',
            'Empat',
            'Lima',
            'Enam',
            'Tujuh',
            'Delapan',
            'Sembilan',
            'Sepuluh',
            'Sebelas',
            'Dua Belas',
            'Tiga Belas',
            'Empat Belas',
            'Lima Belas',
            'Enam Belas',
            'Tujuh Belas',
            'Delapan Belas',
            'Sembilan Belas'
        ];

        $tens = ['', '', 'Dua Puluh', 'Tiga Puluh', 'Empat Puluh', 'Lima Puluh', 'Enam Puluh', 'Tujuh Puluh', 'Delapan Puluh', 'Sembilan Puluh'];

        if ($number < 20) {
            return $words[$number];
        } elseif ($number < 100) {
            return $tens[intval($number / 10)] . ' ' . $words[$number % 10];
        } elseif ($number < 1000) {
            return $words[intval($number / 100)] . ' Ratus ' . $this->numberToWords($number % 100);
        } elseif ($number < 1000000) {
            return $this->numberToWords(intval($number / 1000)) . ' Ribu ' . $this->numberToWords($number % 1000);
        } elseif ($number < 1000000000) {
            return $this->numberToWords(intval($number / 1000000)) . ' Juta ' . $this->numberToWords($number % 1000000);
        }

        return 'Angka terlalu besar';
    }
}
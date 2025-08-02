<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePrintService
{
    public function generateSingleInvoicePdf(Invoice $invoice)
    {
        $invoice->load(['client', 'items', 'payments.bankAccount']);
        
        $data = [
            'invoice' => $invoice,
            'client' => $invoice->client,
            'items' => $invoice->items,
            'payments' => $invoice->payments,
            'company' => $this->getCompanyInfo(),
            'terbilang' => $this->numberToWords($invoice->total_amount),
        ];
        
        $pdf = Pdf::loadView('pdf.single-invoice', $data)
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
            'name' => 'PT. JASA KONSULTAN BORNEO',
            'address' => 'Jl. Delima Dalam, RT 53 Blok E, Kel. Sidodadi Kec. Samarinda Ulu, Kota Samarinda - Kalimantan Timur',
            'email' => 'jasakonsultanborneo01@gmail.com',
            'website' => 'www.konsultanborneo.com',
            'phone' => '0852-8888-2600',
            'logo_base64' => $this->getLogoBase64(),
            'bank_accounts' => [
                [
                    'bank' => 'Mandiri',
                    'account_number' => '1480022212743',
                    'account_name' => 'PT. JASA KONSULTAN BORNEO'
                ],
                [
                    'bank' => 'BNI',
                    'account_number' => '1911640858',
                    'account_name' => 'PT. JASA KONSULTAN BORNEO'
                ]
            ],
            'signature' => [
                'name' => 'Mohammad Denny Jodysetyawan, S.M.',
                'position' => 'Manajer Keuangan'
            ]
        ];
    }

    private function getLogoBase64(): string
    {
        $logoPath = public_path('images/logo-header.png');
        if (file_exists($logoPath)) {
            return 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }
        return '';
    }

    private function numberToWords($number): string
    {
        $words = [
            '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan',
            'Sepuluh', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas',
            'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'
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
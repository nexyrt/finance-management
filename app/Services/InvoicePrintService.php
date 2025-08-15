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

        $pdf = Pdf::loadView('pdf.jitsugen-invoice', $data) // Ganti menyesuaikan dengan nama template PDF yang digunakan
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
            'name' => 'CV. JITSUGEN ARTHA HARMONI',
            'address' => 'Alamat CV. JITSUGEN ARTHA HARMONI', // Update sesuai alamat
            'email' => 'jitsugen@gmail.com', // Update email
            'phone' => '0852-8888-2600',
            'logo_base64' => $this->getLogoBase64(),
            'signature_base64' => $this->getSignatureBase64(),
            'stamp_base64' => $this->getStampBase64(),
            'bank_accounts' => [
                [
                    'bank' => 'Mandiri',
                    'account_number' => '1480025066799',
                    'account_name' => 'CV. JITSUGEN ARTHA HARMONI'
                ],
                // [
                //     'bank' => 'BNI',
                //     'account_number' => '1911640858',
                //     'account_name' => 'PT. JASA KONSULTAN BORNEO'
                // ]
            ],
            'signature' => [
                'name' => 'Lutfiannur Fahrizal Arjun',
                'position' => 'Direktur'
            ]
        ];
    }

    private function getLogoBase64(): string
    {
        $logoPath = public_path('images/jitsugen.png'); // Update path sesuai logo yang digunakan
        if (file_exists($logoPath)) {
            return 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }
        return '';
    }

    private function getSignatureBase64(): string
    {
        $signaturePath = public_path('images/signature-jitsugen.png');
        if (file_exists($signaturePath)) {
            return 'data:image/png;base64,' . base64_encode(file_get_contents($signaturePath));
        }
        return '';
    }

    private function getStampBase64(): string
    {
        $stampPath = public_path('images/jitsugen-stamp.png');
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
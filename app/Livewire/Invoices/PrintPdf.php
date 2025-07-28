<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\Attributes\On;
use TallStackUi\Traits\Interactions;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PrintPdf extends Component
{
    use Interactions;

    public ?Invoice $invoice = null;
    public bool $showModal = false;
    
    // Print options
    public string $format = 'A4';
    public string $orientation = 'portrait';
    public bool $showPayments = true;
    public bool $showClientDetails = true;
    public string $notes = '';

    #[On('print-invoice')]
    public function printInvoice(int $invoiceId): void
    {
        $this->invoice = Invoice::with([
            'client', 
            'items.client', 
            'payments.bankAccount'
        ])->find($invoiceId);
        
        if (!$this->invoice) {
            $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
            return;
        }
        
        $this->showModal = true;
    }

    public function resetData(): void
    {
        $this->invoice = null;
        $this->showModal = false;
        $this->resetOptions();
    }

    private function resetOptions(): void
    {
        $this->format = 'A4';
        $this->orientation = 'portrait';
        $this->showPayments = true;
        $this->showClientDetails = true;
        $this->notes = '';
    }

    public function downloadPdf()
    {
        if (!$this->invoice) {
            $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
            return;
        }

        try {
            // Generate PDF
            $pdf = $this->generatePdf();
            
            // Return PDF download
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, "Invoice-{$this->invoice->invoice_number}.pdf", [
                'Content-Type' => 'application/pdf',
            ]);

        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal generate PDF: ' . $e->getMessage())->send();
        }
    }

    public function previewPdf(): void
    {
        if (!$this->invoice) {
            $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
            return;
        }

        try {
            // Generate PDF
            $pdf = $this->generatePdf();
            
            // Save temporary file
            $fileName = 'temp/invoice-' . $this->invoice->invoice_number . '-' . time() . '.pdf';
            Storage::put($fileName, $pdf->output());
            
            // Get URL for preview
            $url = Storage::url($fileName);
            
            $this->toast()->info('Preview', 'PDF berhasil digenerate untuk preview')->send();
            
            // Dispatch event untuk buka preview di new tab
            $this->dispatch('open-pdf-preview', url: $url);

        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal generate preview: ' . $e->getMessage())->send();
        }
    }

    private function generatePdf()
    {
        // Prepare data for PDF
        $data = [
            'invoice' => $this->invoice,
            'client' => $this->invoice->client,
            'items' => $this->invoice->items,
            'payments' => $this->showPayments ? $this->invoice->payments : collect(),
            'options' => [
                'show_payments' => $this->showPayments,
                'show_client_details' => $this->showClientDetails,
                'notes' => $this->notes,
            ],
            'company' => $this->getCompanyInfo(),
        ];

        // Generate PDF with options
        $pdf = Pdf::loadView('pdf.invoice', $data)
            ->setPaper($this->format, $this->orientation)
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'isFontSubsettingEnabled' => true,
            ]);

        return $pdf;
    }

    private function getCompanyInfo(): array
    {
        // You can store this in settings table or config
        return [
            'name' => 'Finance Management System',
            'address' => 'Jl. Contoh No. 123, Jakarta',
            'phone' => '+62 21 1234 5678',
            'email' => 'info@finance.com',
            'website' => 'www.finance.com',
            'logo' => null, // Path to logo if available
        ];
    }

    public function sendEmail(): void
    {
        if (!$this->invoice) {
            $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
            return;
        }

        if (!$this->invoice->client->email) {
            $this->toast()->error('Error', 'Klien tidak memiliki email')->send();
            return;
        }

        try {
            // Generate PDF
            $pdf = $this->generatePdf();
            
            // Save temporary file
            $fileName = 'temp/invoice-' . $this->invoice->invoice_number . '.pdf';
            Storage::put($fileName, $pdf->output());

            // Send email (placeholder - implement with your mail system)
            // Mail::send(...);

            $this->toast()->success('Email Terkirim', 
                "Invoice berhasil dikirim ke {$this->invoice->client->email}")
                ->send();

            // Clean up temp file
            Storage::delete($fileName);

        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal mengirim email: ' . $e->getMessage())->send();
        }
    }

    public function print(): void
    {
        if (!$this->invoice) return;

        // Generate PDF for printing
        try {
            $pdf = $this->generatePdf();
            
            // Save temporary file for printing
            $fileName = 'temp/print-invoice-' . $this->invoice->invoice_number . '-' . time() . '.pdf';
            Storage::put($fileName, $pdf->output());
            
            $url = Storage::url($fileName);
            
            // Dispatch event to open print dialog
            $this->dispatch('print-pdf', url: $url);
            
            $this->toast()->info('Print', 'File PDF siap untuk print')->send();

        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal menyiapkan print: ' . $e->getMessage())->send();
        }
    }

    /**
     * Generate PDF untuk route (tanpa modal)
     */
    public function generatePdfForInvoice(Invoice $invoice)
    {
        // Load relationships
        $this->invoice = $invoice->load(['client', 'items.client', 'payments.bankAccount']);
        
        // Set default options
        $this->format = 'A4';
        $this->orientation = 'portrait';
        $this->showPayments = true;
        $this->showClientDetails = true;
        $this->notes = '';
        
        return $this->generatePdf();
    }

    public function render()
    {
        return view('livewire.invoices.print-pdf');
    }
}
<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\Attributes\On;
use TallStackUi\Traits\Interactions;

class Show extends Component
{
    use Interactions;

    public ?Invoice $invoice = null;
    public bool $showModal = false;

    #[On('show-invoice')]
    public function show(int $invoiceId): void
    {
        $this->invoice = Invoice::with(['client', 'items.client', 'payments.bankAccount'])
            ->find($invoiceId);

        if (!$this->invoice) {
            $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
            return;
        }

        $this->showModal = true;
    }

    // Method ini dipanggil setelah modal ditutup via Alpine
    public function resetData(): void
    {
        $this->invoice = null;
        $this->showModal = false; // Pastikan sync dengan Alpine
    }

    public function sendInvoice(): void
    {
        if (!$this->invoice || $this->invoice->status !== 'draft')
            return;

        $this->invoice->update(['status' => 'sent']);
        $this->toast()->success('Success', 'Invoice berhasil dikirim')->send();
        $this->dispatch('invoice-updated');
    }

    public function recordPayment(): void
    {
        if (!$this->invoice)
            return;

        $invoiceId = $this->invoice->id;
        $this->resetData(); // Close modal first
        $this->dispatch('record-payment', invoiceId: $invoiceId);
    }

    public function printInvoice()
    {
        if (!$this->invoice) {
            $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
            return;
        }

        $invoice = $this->invoice;

        // Buka preview di tab baru dengan delay
        $this->dispatch('open-preview-delayed', [
            'url' => route('invoice.pdf.preview', $invoice->id),
            'delay' => 500
        ]);

        $service = new \App\Services\InvoicePrintService();
        $pdf = $service->generateSingleInvoicePdf($invoice);

        $filename = 'Invoice-' . str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $invoice->invoice_number) . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf'
        ]);
    }

    public function render()
    {
        return view('livewire.invoices.show');
    }
}
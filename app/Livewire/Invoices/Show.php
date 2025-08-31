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
    public bool $modal = false;

    #[On('show-invoice')]
    public function show(int $invoiceId): void
    {
        $this->invoice = Invoice::with(['client', 'items.client', 'payments.bankAccount'])
            ->find($invoiceId);

        if (!$this->invoice) {
            $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
            return;
        }

        $this->modal = true;
    }

    public function resetData(): void
    {
        $this->invoice = null;
        $this->modal = false;
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
        $this->dispatch('record-payment', invoiceId: $invoiceId);
        // TallStackUI akan handle modal switching otomatis
    }

    public function editInvoice(): void
    {
        if (!$this->invoice)
            return;

        $invoiceId = $this->invoice->id;
        $this->resetData(); // Reset hanya untuk navigation
        $this->redirect(route('invoices.edit', $invoiceId), navigate: true);
    }

    public function printInvoice(): void
    {
        if (!$this->invoice) {
            $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
            return;
        }

        // Dispatch JavaScript untuk print (preview + download)
        $this->dispatch('print-invoice', [
            'previewUrl' => route('invoice.pdf.preview', $this->invoice->id),
            'downloadUrl' => route('invoice.pdf.download', $this->invoice->id),
            'filename' => 'Invoice-' . $this->invoice->invoice_number . '.pdf'
        ]);

        $this->toast()->success('Print', 'PDF sedang diproses')->send();
    }

    public function render()
    {
        return view('livewire.invoices.show');
    }
}
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

    public function downloadPdf(): void
    {
        if (!$this->invoice) return;
        
        $this->toast()->info('Download', 'Generating PDF...')->send();
        // TODO: Implement PDF generation
    }

    public function sendInvoice(): void
    {
        if (!$this->invoice || $this->invoice->status !== 'draft') return;
        
        $this->invoice->update(['status' => 'sent']);
        $this->toast()->success('Success', 'Invoice berhasil dikirim')->send();
        $this->dispatch('invoice-updated');
    }

    public function duplicateInvoice(): void
    {
        if (!$this->invoice) return;
        
        $invoiceId = $this->invoice->id;
        $this->resetData(); // Close modal first
        $this->dispatch('duplicate-invoice', invoiceId: $invoiceId);
    }

    public function editInvoice(): void
    {
        if (!$this->invoice) return;
        
        $invoiceId = $this->invoice->id;
        $this->resetData(); // Close modal first
        $this->dispatch('edit-invoice', invoiceId: $invoiceId);
    }

    public function recordPayment(): void
    {
        if (!$this->invoice) return;
        
        $invoiceId = $this->invoice->id;
        $this->resetData(); // Close modal first
        $this->dispatch('record-payment', invoiceId: $invoiceId);
    }

    public function render()
    {
        return view('livewire.invoices.show');
    }
}
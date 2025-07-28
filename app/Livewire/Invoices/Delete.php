<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\Attributes\On;
use TallStackUi\Traits\Interactions;

class Delete extends Component
{
    use Interactions;

    public ?Invoice $invoice = null;
    public bool $showModal = false;

    #[On('delete-invoice')]
    public function delete(int $invoiceId): void
    {
        $this->invoice = Invoice::with([
            'client', 
            'items', 
            'payments'
        ])->find($invoiceId);
        
        if ($this->invoice) {
            $this->showModal = true;
        }
    }

    public function resetData(): void
    {
        $this->invoice = null;
        $this->showModal = false;
    }

    public function confirm(): void
    {
        if (!$this->invoice) {
            $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
            return;
        }

        try {
            $invoiceNumber = $this->invoice->invoice_number;
            $hasPayments = $this->invoice->payments->count() > 0;
            $status = $this->invoice->status;
            
            // Delete all related payments first if they exist
            if ($hasPayments) {
                $this->invoice->payments()->delete();
            }
            
            // Delete the invoice
            $this->invoice->delete();
            
            $this->resetData(); 
            $this->dispatch('invoice-updated');
            
            $message = "Invoice {$invoiceNumber} berhasil dihapus.";
            if ($hasPayments) {
                $message .= " (Termasuk semua pembayaran yang terkait)";
            }
            
            $this->dialog()
                ->success('Invoice Dihapus', $message)
                ->send();
                
        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal menghapus invoice: ' . $e->getMessage())->send();
        }
    }

    public function render()
    {
        return view('livewire.invoices.delete');
    }
}
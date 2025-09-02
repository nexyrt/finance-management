<?php

namespace App\Livewire;

use TallStackUi\Traits\Interactions;
use App\Models\Invoice;
use Livewire\Component;

class TestingPage extends Component
{
    // use Interactions;
    
    // public ?Invoice $invoice = null;
    // public int $invoiceId;
    
    // public function mount($id): void
    // {
    //     $this->invoiceId = $id;
    //     $this->loadInvoice();
    // }
    
    // public function loadInvoice(): void
    // {
    //     $this->invoice = Invoice::with('items', 'client')->find($this->invoiceId);
        
    //     if (!$this->invoice) {
    //         abort(404, 'Invoice not found');
    //     }
        
    //     // Transform items untuk Alpine.js
    //     $items = $this->invoice->items->map(fn($item) => [
    //         'client_id' => $item->client_id,
    //         'service_name' => $item->service_name,
    //         'quantity' => $item->quantity,
    //         'price' => $item->unit_price,
    //         'cogs' => $item->cogs_amount ?? 0
    //     ])->toArray();
        
    //     // Dispatch ke Alpine.js setelah component ready
    //     $this->dispatch('populate-items', $items);
        
    //     $this->toast()->success('Loaded', "Invoice #{$this->invoice->invoice_number}")->send();
    // }
    
    public function render()
    {
        return view('livewire.testing-page');
    }
}
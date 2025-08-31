<?php

namespace App\Livewire;

use TallStackUi\Traits\Interactions;
use App\Models\Invoice;
use Livewire\Component;

class TestingPage extends Component
{
    use Interactions;
    
    public array $services = [];
    public ?int $selectedInvoiceId = null;
    public ?Invoice $invoice = null;
    
    public function updatedSelectedInvoiceId(): void
    {
        if (!$this->selectedInvoiceId) {
            $this->invoice = null;
            $this->dispatch('populate-repeater', []);
            return;
        }
        
        $this->invoice = Invoice::with('items')->find($this->selectedInvoiceId);
        
        if ($this->invoice) {
            // Transform invoice items to Alpine.js format
            $invoiceItems = $this->invoice->items->map(function($item) {
                return [
                    'name' => $item->service_name,
                    'price' => $item->unit_price,
                    'quantity' => $item->quantity
                ];
            })->toArray();
            
            // Send to Alpine.js
            $this->dispatch('populate-repeater', $invoiceItems);
            
            $this->toast()
                ->success('Invoice Loaded', "Invoice #{$this->invoice->invoice_number} loaded")
                ->send();
        }
    }
    
    public function saveServices($alpineData): void
    {
        $this->services = $alpineData;
        
        if (!$this->invoice) {
            $this->toast()->error('Error', 'No invoice selected')->send();
            return;
        }
        
        // Delete existing items
        $this->invoice->items()->delete();
        
        // Create new items from Alpine data
        foreach ($alpineData as $item) {
            $this->invoice->items()->create([
                'client_id' => $this->invoice->billed_to_id,
                'service_name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'amount' => $item['quantity'] * $item['price'],
                'cogs_amount' => 0 // Default value
            ]);
        }
        
        // Update invoice totals
        $subtotal = array_sum(array_map(fn($item) => $item['quantity'] * $item['price'], $alpineData));
        $this->invoice->update([
            'subtotal' => $subtotal,
            'total_amount' => $subtotal - $this->invoice->discount_amount
        ]);
        
        // Reset all data
        $this->reset(['services', 'selectedInvoiceId', 'invoice']);
        $this->dispatch('reset-repeater');
        
        $this->toast()
            ->success('Success', "Invoice updated successfully. Data has been reset.")
            ->send();
    }
    
    public function render()
    {
        return view('livewire.testing-page', [
            'invoiceOptions' => Invoice::with('client')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn($invoice) => [
                    'label' => "#{$invoice->invoice_number} - {$invoice->client->name}",
                    'value' => $invoice->id
                ])
                ->toArray()
        ]);
    }
}
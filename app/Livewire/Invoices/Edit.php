<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Service;
use TallStackUi\Traits\Interactions;

class Edit extends Component
{
    use Interactions;

    public $invoiceId;
    public $invoice;
    public $clients = [];
    public $services = [];
    
    // Invoice fields
    public $invoice_number = '';
    public $billed_to_id = '';
    public $issue_date = '';
    public $due_date = '';
    public $status = 'draft';
    
    // Discount fields
    public $discount_type = 'fixed';
    public $discount_value = 0;
    public $discount_reason = '';
    
    // Items - will be managed by Alpine.js
    public $items = [];

    public function mount($invoice = null)
    {
        if ($invoice) {
            $this->invoiceId = $invoice;
            $this->loadInvoice($invoice);
        }
        $this->loadOptions();
    }
    
    public function hydrate()
    {
        // Trigger Alpine.js populate after component is hydrated
        if ($this->invoice && $this->invoice->items->isNotEmpty()) {
            $invoiceItems = $this->invoice->items->map(function ($item) {
                return [
                    'client_id' => $item->client_id,
                    'service_id' => '',
                    'service_name' => $item->service_name,
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price,
                    'cogs_amount' => $item->cogs_amount ?? 0,
                ];
            })->toArray();
            
            $this->dispatch('populate-invoice-items', $invoiceItems);
        }
    }

    public function loadInvoice($invoiceId = null)
    {
        $id = $invoiceId ?? $this->invoiceId;
        $this->invoice = Invoice::with('items', 'client')->find($id);
        
        if (!$this->invoice) {
            abort(404, 'Invoice not found');
        }
        
        $this->invoiceId = $this->invoice->id;
        $this->invoice_number = $this->invoice->invoice_number;
        $this->billed_to_id = $this->invoice->billed_to_id;
        $this->issue_date = $this->invoice->issue_date->format('Y-m-d');
        $this->due_date = $this->invoice->due_date->format('Y-m-d');
        $this->status = $this->invoice->status;
        
        $this->discount_type = $this->invoice->discount_type;
        $this->discount_value = $this->invoice->discount_value;
        $this->discount_reason = $this->invoice->discount_reason ?? '';
        
        // Transform items for Alpine.js
        if ($this->invoice->items->isNotEmpty()) {
            $invoiceItems = $this->invoice->items->map(function ($item) {
                return [
                    'client_id' => $item->client_id,
                    'service_id' => '',
                    'service_name' => $item->service_name,
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price,
                    'cogs_amount' => $item->cogs_amount ?? 0,
                ];
            })->toArray();
            
            // Send to Alpine.js
            $this->dispatch('populate-invoice-items', $invoiceItems);
        } else {
            // Send empty array to reset Alpine
            $this->dispatch('populate-invoice-items', []);
        }
    }

    public function loadOptions()
    {
        $this->clients = Client::where('status', 'Active')
            ->get()
            ->map(fn($client) => [
                'label' => $client->name,
                'value' => $client->id
            ])
            ->toArray();
            
        $this->services = Service::all()
            ->map(fn($service) => [
                'label' => $service->name,
                'value' => $service->id,
                'description' => 'Rp ' . number_format($service->price, 0, ',', '.'),
                'price' => $service->price
            ])
            ->toArray();
    }

    public function saveInvoice($alpineItems, $alpineSubtotal, $alpineTotal)
    {
        $this->validate([
            'invoice_number' => 'required|string',
            'billed_to_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
        ]);

        // Validate Alpine items
        foreach ($alpineItems as $index => $item) {
            if (empty($item['client_id']) || empty($item['service_name']) || 
                $item['quantity'] <= 0 || $item['price'] < 0) {
                $this->toast()->error('Error', "Item " . ($index + 1) . " has invalid data")->send();
                return;
            }
        }

        try {
            \DB::transaction(function () use ($alpineItems, $alpineSubtotal, $alpineTotal) {
                $this->updateInvoice($alpineItems, $alpineSubtotal, $alpineTotal);
            });

            $this->toast()->success('Success', 'Invoice updated successfully!')->send();
            return redirect()->route('invoices.index');
            
        } catch (\Exception $e) {
            $this->toast()->error('Error', $e->getMessage())->send();
        }
    }

    public function updateInvoice($alpineItems, $alpineSubtotal, $alpineTotal)
    {
        $oldStatus = $this->invoice->status;
        
        // Calculate discount amount
        $discountAmount = 0;
        if ($this->discount_type === 'percentage') {
            $discountAmount = (int) (($alpineSubtotal * $this->discount_value) / 10000);
        } else {
            $discountAmount = (int) $this->discount_value;
        }

        $this->invoice->update([
            'invoice_number' => $this->invoice_number,
            'billed_to_id' => $this->billed_to_id,
            'subtotal' => $alpineSubtotal,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'discount_amount' => $discountAmount,
            'discount_reason' => $this->discount_reason,
            'total_amount' => max(0, $alpineSubtotal - $discountAmount),
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date,
        ]);

        $this->updateInvoiceItems($alpineItems);

        $newStatus = $this->evaluateStatus();
        $this->invoice->update(['status' => $newStatus]);

        if ($oldStatus !== $newStatus) {
            $this->logStatusChange($oldStatus, $newStatus);
        }
    }

    public function updateInvoiceItems($alpineItems)
    {
        $this->invoice->items()->delete();
        
        foreach ($alpineItems as $item) {
            $this->invoice->items()->create([
                'client_id' => $item['client_id'],
                'service_name' => $item['service_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'amount' => $item['quantity'] * $item['price'],
                'cogs_amount' => $item['cogs_amount'] ?? 0,
            ]);
        }
    }

    public function evaluateStatus()
    {
        $this->invoice->refresh();
        $totalPaid = $this->invoice->payments()->sum('amount');
        $totalAmount = $this->invoice->total_amount;
        $dueDate = \Carbon\Carbon::parse($this->invoice->due_date);

        if ($totalPaid >= $totalAmount && $totalPaid > 0) {
            return 'paid';
        }

        if ($totalPaid > 0 && $totalPaid < $totalAmount) {
            return 'partially_paid';
        }

        if ($totalPaid == 0) {
            return $dueDate->isPast() ? 'overdue' : 'sent';
        }

        return 'draft';
    }

    public function logStatusChange($oldStatus, $newStatus)
    {
        \Log::info("Invoice {$this->invoice_number} status changed from {$oldStatus} to {$newStatus}");
    }

    public function render()
    {
        return view('livewire.invoices.edit');
    }
}
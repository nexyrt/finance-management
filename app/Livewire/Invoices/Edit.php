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
    
    // Items array
    public $items = [];

    protected $listeners = [
        'edit-invoice' => 'loadInvoice'
    ];

    public function mount($invoice = null)
    {
        if ($invoice) {
            $this->invoiceId = $invoice;
            $this->loadInvoice($invoice);
        }
        $this->loadOptions();
        
        // Add initial item if no items loaded
        if (empty($this->items)) {
            $this->addItem();
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
        
        // Load discount data
        $this->discount_type = $this->invoice->discount_type;
        $this->discount_value = $this->invoice->discount_value;
        $this->discount_reason = $this->invoice->discount_reason ?? '';
        
        if ($this->invoice->items->isNotEmpty()) {
            $this->items = $this->invoice->items->map(function ($item) {
                return [
                    'client_id' => $item->client_id,
                    'service_id' => '',
                    'service_name' => $item->service_name,
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price,
                    'total' => $item->amount
                ];
            })->toArray();
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

    public function addItem()
    {
        $this->items[] = [
            'client_id' => '',
            'service_id' => '',
            'service_name' => '',
            'quantity' => 1,
            'price' => 0,
            'total' => 0
        ];
    }

    public function removeItem($index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function updated($propertyName)
    {
        if (str_contains($propertyName, 'items.')) {
            $parts = explode('.', $propertyName);
            $index = $parts[1];
            
            if (in_array($parts[2], ['quantity', 'price'])) {
                $this->calculateTotal($index);
            }
            
            if ($parts[2] === 'service_id') {
                $this->setServiceDetails($index);
            }
        }
        
        // Recalculate discount when discount fields change
        if (in_array($propertyName, ['discount_type', 'discount_value'])) {
            // Auto-update will trigger getGrandTotalProperty recalculation
        }
    }

    public function calculateTotal($index)
    {
        $qty = (int) $this->items[$index]['quantity'];
        $price = (int) $this->items[$index]['price'];
        $this->items[$index]['total'] = $qty * $price;
    }

    public function setServiceDetails($index)
    {
        $serviceId = $this->items[$index]['service_id'];
        if ($serviceId) {
            $service = collect($this->services)->firstWhere('value', $serviceId);
            if ($service) {
                $this->items[$index]['service_name'] = $service['label'];
                $this->items[$index]['price'] = $service['price'];
                $this->calculateTotal($index);
            }
        }
    }

    public function getSubtotalProperty()
    {
        return collect($this->items)->sum('total');
    }

    public function getDiscountAmountProperty()
    {
        if ($this->discount_type === 'percentage') {
            // discount_value stored as percentage * 100 (e.g., 1500 = 15%)
            return (int) (($this->subtotal * $this->discount_value) / 10000);
        } else {
            // Fixed amount discount
            return (int) $this->discount_value;
        }
    }

    public function getGrandTotalProperty()
    {
        return max(0, $this->subtotal - $this->discountAmount);
    }

    public function save()
    {
        $this->validate([
            'invoice_number' => 'required|string',
            'billed_to_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'items.*.client_id' => 'required|exists:clients,id',
            'items.*.service_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|integer|min:0',
        ]);

        try {
            \DB::transaction(function () {
                $this->updateInvoice();
            });

            $this->toast()->success('Success', 'Invoice updated successfully!')->flash()->send();
            return redirect()->route('invoices.index');
            
        } catch (\Exception $e) {
            $this->toast()->error('Error', $e->getMessage())->send();
        }
    }

    public function updateInvoice()
    {
        $oldStatus = $this->invoice->status;

        $this->invoice->update([
            'invoice_number' => $this->invoice_number,
            'billed_to_id' => $this->billed_to_id,
            'subtotal' => $this->subtotal,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'discount_amount' => $this->discountAmount,
            'discount_reason' => $this->discount_reason,
            'total_amount' => $this->grandTotal,
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date,
        ]);

        $this->updateInvoiceItems();

        $newStatus = $this->evaluateStatus();
        $this->invoice->update(['status' => $newStatus]);

        if ($oldStatus !== $newStatus) {
            $this->logStatusChange($oldStatus, $newStatus);
        }
    }

    public function updateInvoiceItems()
    {
        $this->invoice->items()->delete();
        
        foreach ($this->items as $item) {
            $this->invoice->items()->create([
                'client_id' => $item['client_id'],
                'service_name' => $item['service_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'amount' => $item['total']
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

    public function getPreviewStatusProperty()
    {
        if (!$this->invoice || !$this->invoiceId) return 'draft';
        
        // Get fresh payments data
        $invoice = Invoice::find($this->invoiceId);
        if (!$invoice) return 'draft';
        
        $totalPaid = $invoice->payments()->sum('amount');
        $newTotalAmount = $this->grandTotal;
        $newDueDate = \Carbon\Carbon::parse($this->due_date);

        // Apply same logic as evaluateStatus but with new values
        if ($totalPaid >= $newTotalAmount && $totalPaid > 0) {
            return 'paid';
        }

        if ($totalPaid > 0 && $totalPaid < $newTotalAmount) {
            return 'partially_paid';
        }

        if ($totalPaid == 0) {
            return $newDueDate->isPast() ? 'overdue' : 'sent';
        }

        return 'draft';
    }

    public function evaluateStatusForInvoice($invoice)
    {
        $totalPaid = $invoice->payments()->sum('amount');
        $totalAmount = $invoice->total_amount;
        $dueDate = \Carbon\Carbon::parse($invoice->due_date);

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

    public function render()
    {
        return view('livewire.invoices.edit');
    }
}
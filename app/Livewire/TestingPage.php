<?php

namespace App\Livewire;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class TestingPage extends Component
{
    use Interactions;

    public $items = [];

    public $invoiceId = null;
    public $clients = [];
    public $services = [];
    
    // Invoice fields
    public $invoice_number = '';
    public $billed_to_id = '';
    public $issue_date = '';
    public $due_date = '';
    public $status = 'draft';

    public function mount($invoiceId = null)
    {
        $this->invoiceId = $invoiceId;
        $this->loadOptions();
        
        if ($invoiceId) {
            $this->loadInvoice($invoiceId);
        } else {
            $this->initializeNewInvoice();
        }
    }

    public function initializeNewInvoice()
    {
        $this->invoice_number = 'INV-' . now()->format('Ymd') . '-' . str_pad(\App\Models\Invoice::count() + 1, 4, '0', STR_PAD_LEFT);
        $this->issue_date = now()->format('Y-m-d');
        $this->due_date = now()->addDays(30)->format('Y-m-d');
        $this->addItem();
    }

    public function loadInvoice($invoiceId)
    {
        $invoice = \App\Models\Invoice::with('items')->find($invoiceId);
        
        if ($invoice) {
            $this->invoice_number = $invoice->invoice_number;
            $this->billed_to_id = $invoice->billed_to_id;
            $this->issue_date = $invoice->issue_date->format('Y-m-d');
            $this->due_date = $invoice->due_date->format('Y-m-d');
            $this->status = $invoice->status;
            
            if ($invoice->items->count() > 0) {
                $this->items = $invoice->items->map(function ($item) {
                    return [
                        'client_id' => $item->client_id,
                        'service_id' => '',
                        'service_name' => $item->service_name,
                        'quantity' => $item->quantity,
                        'price' => $item->unit_price,
                        'total' => $item->amount
                    ];
                })->toArray();
            } else {
                $this->addItem();
            }
        } else {
            $this->initializeNewInvoice();
        }
    }

    public function loadOptions()
    {
        $this->clients = \App\Models\Client::where('status', 'Active')
            ->get()
            ->map(fn($client) => [
                'label' => $client->name,
                'value' => $client->id
            ])
            ->toArray();
            
        $this->services = \App\Models\Service::all()
            ->map(fn($service) => [
                'label' => $service->name,
                'value' => $service->id,
                'description' => 'Rp ' . number_format($service->price, 0, ',', '.'),
                'price' => $service->price
            ])
            ->toArray();
    }

    public function loadInvoiceItems($invoiceId)
    {
        // This method is no longer needed - merged into loadInvoice
    }

    public function resetAll()
    {
        $this->items = [];
        $this->addItem();
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

    public function calculateTotal($index)
    {
        $qty = (int) $this->items[$index]['quantity'];
        $price = (int) $this->items[$index]['price'];
        $this->items[$index]['total'] = $qty * $price;
    }

    public function getGrandTotalProperty()
    {
        return collect($this->items)->sum('total');
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
                if ($this->invoiceId) {
                    $this->updateInvoice();
                } else {
                    $this->createInvoice();
                }
            });

            $action = $this->invoiceId ? 'updated' : 'created';
            $this->dialog()->success('Success', "Invoice {$action} successfully!")->send();
            
        } catch (\Exception $e) {
            $this->dialog()->error('Error', $e->getMessage())->send();
        }
    }

    public function createInvoice()
    {
        $invoice = \App\Models\Invoice::create([
            'invoice_number' => $this->invoice_number,
            'billed_to_id' => $this->billed_to_id,
            'subtotal' => $this->grandTotal,
            'total_amount' => $this->grandTotal,
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date,
            'status' => 'draft'
        ]);

        $this->createInvoiceItems($invoice);
        
        // Auto-evaluate status after creation
        $newStatus = $this->evaluateStatus($invoice);
        $invoice->update(['status' => $newStatus]);
        
        $this->invoiceId = $invoice->id;
    }

    public function updateInvoice()
    {
        $invoice = \App\Models\Invoice::find($this->invoiceId);
        $oldStatus = $invoice->status;

        // Update invoice data
        $invoice->update([
            'invoice_number' => $this->invoice_number,
            'billed_to_id' => $this->billed_to_id,
            'subtotal' => $this->grandTotal,
            'total_amount' => $this->grandTotal,
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date,
        ]);

        // Update items
        $this->updateInvoiceItems($invoice);

        // Auto-evaluate status
        $newStatus = $this->evaluateStatus($invoice);
        $invoice->update(['status' => $newStatus]);

        // Log status change if different
        if ($oldStatus !== $newStatus) {
            $this->logStatusChange($oldStatus, $newStatus);
        }
    }

    public function createInvoiceItems($invoice)
    {
        foreach ($this->items as $item) {
            \App\Models\InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'client_id' => $item['client_id'],
                'service_name' => $item['service_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'amount' => $item['total']
            ]);
        }
    }

    public function updateInvoiceItems($invoice)
    {
        // Delete existing items
        $invoice->items()->delete();
        
        // Create new items
        $this->createInvoiceItems($invoice);
    }

    public function evaluateStatus($invoice)
    {
        $invoice->refresh(); // Get latest data
        $totalPaid = $invoice->payments()->sum('amount');
        $totalAmount = $invoice->total_amount;
        $dueDate = \Carbon\Carbon::parse($invoice->due_date);

        // Paid (including overpaid)
        if ($totalPaid >= $totalAmount && $totalPaid > 0) {
            return 'paid';
        }

        // Partially paid
        if ($totalPaid > 0 && $totalPaid < $totalAmount) {
            return 'partially_paid';
        }

        // No payment yet
        if ($totalPaid == 0) {
            return $dueDate->isPast() ? 'overdue' : 'sent';
        }

        return 'draft'; // Fallback
    }

    public function logStatusChange($oldStatus, $newStatus)
    {
        \Log::info("Invoice {$this->invoice_number} status changed from {$oldStatus} to {$newStatus}");
        
        // Optional: Store in activity log table
        // ActivityLog::create([...]);
    }

    public function getPreviewStatusProperty()
    {
        if (!$this->invoiceId) return 'draft';
        
        $invoice = \App\Models\Invoice::find($this->invoiceId);
        if (!$invoice) return 'draft';
        
        // Simulate the new total for preview
        $tempInvoice = $invoice->replicate();
        $tempInvoice->total_amount = $this->grandTotal;
        $tempInvoice->due_date = $this->due_date;
        
        return $this->evaluateStatus($tempInvoice);
    }

    public function render()
    {
        return view('livewire.testing-page');
    }
}
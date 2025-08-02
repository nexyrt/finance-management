<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Service;
use TallStackUi\Traits\Interactions;

class Create extends Component
{
    use Interactions;

    public $showModal = false;
    public $clients = [];
    public $services = [];

    // Invoice fields
    public $invoice_number = '';
    public $billed_to_id = '';
    public $issue_date = '';
    public $due_date = '';

    // Discount fields
    public $discount_type = 'fixed';
    public $discount_value = 0;
    public $discount_reason = '';

    // Items array
    public $items = [];

    protected $listeners = ['create-invoice' => 'openModal'];

    public function openModal()
    {
        $this->resetForm();
        $this->loadOptions();
        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->generateInvoiceNumber();
        $this->billed_to_id = '';
        $this->issue_date = now()->format('Y-m-d');
        $this->due_date = now()->addDays(30)->format('Y-m-d');
        $this->discount_type = 'fixed';
        $this->discount_value = 0;
        $this->discount_reason = '';
        $this->items = [];
        $this->addItem();
        $this->resetValidation();
    }

    public function generateInvoiceNumber($issueDate = null)
    {
        $date = $issueDate ? \Carbon\Carbon::parse($issueDate) : now();
        $currentMonth = $date->format('m');
        $currentYear = $date->format('y');

        // Find highest sequence number in selected month/year
        $invoices = Invoice::whereYear('issue_date', $date->year)
            ->whereMonth('issue_date', $date->month)
            ->pluck('invoice_number');

        $maxSequence = 0;
        foreach ($invoices as $invoiceNumber) {
            // Extract sequence from format INV/XX/JKB/MM.YY
            if (preg_match('/INV\/(\d+)\/JKB\/\d{2}\.\d{2}/', $invoiceNumber, $matches)) {
                $sequence = (int) $matches[1];
                $maxSequence = max($maxSequence, $sequence);
            }
        }

        $nextSequence = $maxSequence + 1;

        $this->invoice_number = sprintf(
            'INV/%02d/JKB/%02d.%s',
            $nextSequence,
            (int) $currentMonth,
            $currentYear
        );
    }

    public function updatedIssueDate()
    {
        $this->generateInvoiceNumber($this->issue_date);
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
            'client_id' => $this->billed_to_id,
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

        // Update all items client_id when main client changes
        if ($propertyName === 'billed_to_id') {
            foreach ($this->items as $index => $item) {
                $this->items[$index]['client_id'] = $this->billed_to_id;
            }
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
            return (int) (($this->subtotal * $this->discount_value) / 100);
        } else {
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
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
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
                $invoice = Invoice::create([
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
                    'status' => 'draft'
                ]);

                foreach ($this->items as $item) {
                    $invoice->items()->create([
                        'client_id' => $item['client_id'],
                        'service_name' => $item['service_name'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'amount' => $item['total']
                    ]);
                }
            });

            $this->showModal = false;
            $this->toast()->success('Success', 'Invoice created successfully!')->flash()->send();
            $this->dispatch('invoice-created');
            return redirect()->route('invoices.index');

        } catch (\Exception $e) {
            $this->toast()->error('Error', $e->getMessage())->send();
        }
    }

    public function render()
    {
        return view('livewire.invoices.create');
    }
}
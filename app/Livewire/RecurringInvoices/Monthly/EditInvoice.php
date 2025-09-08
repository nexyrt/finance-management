<?php

namespace App\Livewire\RecurringInvoices\Monthly;

use TallStackUi\Traits\Interactions;
use App\Models\RecurringInvoice;
use App\Models\Client;
use App\Models\Service;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class EditInvoice extends Component
{
    use Interactions;

    public ?RecurringInvoice $invoice = null;
    public bool $modal = false;

    public array $invoiceData = [
        'scheduled_date' => '',
    ];

    public array $items = [];
    public array $selectedItems = [];
    public int $itemsToAdd = 1;
    public bool $hasDiscount = false;
    public array $discount = ['type' => 'fixed', 'value' => 0, 'reason' => ''];

    public function mount(): void
    {
        $this->items = [];
        $this->selectedItems = [];
    }

    #[On('edit-invoice')]
    public function load($invoiceId): void
    {
        $this->invoice = RecurringInvoice::with(['client', 'template'])->find($invoiceId);

        if (!$this->invoice || $this->invoice->status === 'published') {
            $this->toast()->error('Error', 'Invoice tidak dapat diedit')->send();
            return;
        }

        $this->invoiceData = [
            'scheduled_date' => $this->invoice->scheduled_date->format('Y-m-d'),
        ];

        // Load items from invoice data
        $this->items = $this->invoice->invoice_data['items'] ?? [];

        if (empty($this->items)) {
            $this->items = [
                [
                    'client_id' => $this->invoice->client_id,
                    'service_id' => '',
                    'service_name' => '',
                    'quantity' => 1,
                    'unit_price' => '',
                    'amount' => 0,
                    'cogs_amount' => ''
                ]
            ];
        }

        // Load discount
        if (($this->invoice->invoice_data['discount_amount'] ?? 0) > 0) {
            $this->hasDiscount = true;
            $this->discount = [
                'type' => $this->invoice->invoice_data['discount_type'] ?? 'fixed',
                'value' => $this->invoice->invoice_data['discount_value'] ?? 0,
                'reason' => $this->invoice->invoice_data['discount_reason'] ?? ''
            ];
        } else {
            $this->hasDiscount = false;
            $this->discount = ['type' => 'fixed', 'value' => 0, 'reason' => ''];
        }

        $this->selectedItems = [];
        $this->modal = true;
    }

    #[Computed]
    public function clientOptions()
    {
        return Client::orderBy('name')
            ->get()
            ->map(fn($client) => [
                'label' => $client->name,
                'value' => $client->id,
                'description' => ucfirst($client->type)
            ])
            ->toArray();
    }

    #[Computed]
    public function serviceOptions()
    {
        return Service::orderBy('name')
            ->get()
            ->map(fn($service) => [
                'label' => $service->name . ' - ' . $service->formatted_price,
                'value' => $service->id,
                'description' => $service->type
            ])
            ->toArray();
    }

    #[Computed]
    public function subtotal()
    {
        return collect($this->items)->sum('amount');
    }

    #[Computed]
    public function discountAmount()
    {
        if (!$this->hasDiscount)
            return 0;

        $subtotal = $this->subtotal;
        return $this->discount['type'] === 'percentage'
            ? ($subtotal * $this->discount['value'] / 100)
            : $this->discount['value'];
    }

    #[Computed]
    public function totalAmount()
    {
        return $this->subtotal - $this->discountAmount;
    }

    public function addItem()
    {
        $this->items[] = [
            'client_id' => $this->invoice->client_id,
            'service_id' => '',
            'service_name' => '',
            'quantity' => 1,
            'unit_price' => '',
            'amount' => 0,
            'cogs_amount' => ''
        ];
    }

    public function addMultipleItems()
    {
        $count = max(1, $this->itemsToAdd);
        for ($i = 0; $i < $count; $i++) {
            $this->addItem();
        }
        $this->itemsToAdd = 1;
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->selectedItems = array_values(
            array_filter($this->selectedItems, fn($idx) => $idx < $index)
        );
    }

    public function bulkDeleteItems()
    {
        if (empty($this->selectedItems)) {
            $this->toast()->warning('Warning', 'Pilih item yang ingin dihapus')->send();
            return;
        }

        $indices = collect($this->selectedItems)->sort()->reverse();
        foreach ($indices as $index) {
            unset($this->items[$index]);
        }

        $this->items = array_values($this->items);
        $this->selectedItems = [];
        $this->toast()->success('Berhasil', 'Item berhasil dihapus')->send();
    }

    public function fillServiceData($itemIndex)
    {
        $serviceId = $this->items[$itemIndex]['service_id'] ?? null;
        if ($serviceId) {
            $service = Service::find($serviceId);
            if ($service) {
                $this->items[$itemIndex]['service_name'] = $service->name;
                $this->items[$itemIndex]['unit_price'] = number_format($service->price, 0, ',', '.');
                $this->calculateAmount($itemIndex);
            }
        }
    }

    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 2 && in_array($parts[1], ['quantity', 'unit_price', 'cogs_amount'])) {
            $this->calculateAmount($parts[0]);
        }
    }

    private function calculateAmount($index)
    {
        $item = &$this->items[$index];
        $quantity = $item['quantity'] ?? 1;
        $unitPrice = $this->parseAmount($item['unit_price'] ?? '0');
        $item['amount'] = $quantity * $unitPrice;
    }

    private function parseAmount($amount)
    {
        return (int) preg_replace('/[^0-9]/', '', $amount);
    }

    public function save()
    {
        if ($this->invoice->status === 'published') {
            $this->toast()->error('Error', 'Invoice yang sudah dipublish tidak dapat diedit')->send();
            return;
        }

        // Parse currency fields
        foreach ($this->items as $index => $item) {
            $this->items[$index]['unit_price'] = $this->parseAmount($item['unit_price'] ?? '0');
            $this->items[$index]['cogs_amount'] = $this->parseAmount($item['cogs_amount'] ?? '0');
        }

        $this->validate([
            'invoiceData.scheduled_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.client_id' => 'required|exists:clients,id',
            'items.*.service_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|integer|min:0',
            'items.*.cogs_amount' => 'required|integer|min:0',
        ]);

        $this->invoice->update([
            'scheduled_date' => $this->invoiceData['scheduled_date'],
            'invoice_data' => [
                'items' => $this->items,
                'subtotal' => $this->subtotal,
                'discount_amount' => $this->discountAmount,
                'discount_type' => $this->discount['type'],
                'discount_value' => $this->discount['value'],
                'discount_reason' => $this->discount['reason'],
                'total_amount' => $this->totalAmount,
            ]
        ]);

        $this->dispatch('invoice-updated');
        $this->resetExcept('invoice');
        $this->modal = false;

        $this->toast()->success('Berhasil', 'Invoice berhasil diperbarui')->send();
    }

    public function render()
    {
        return view('livewire.recurring-invoices.monthly.edit-invoice');
    }
}
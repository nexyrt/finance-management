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

        // Load items dengan struktur yang benar
        $this->items = [];
        $invoiceItems = $this->invoice->invoice_data['items'] ?? [];

        if (!empty($invoiceItems)) {
            foreach ($invoiceItems as $item) {
                $this->items[] = [
                    'client_id' => $item['client_id'] ?? $this->invoice->client_id,
                    'service_id' => '', // Reset service_id
                    'service_name' => $item['service_name'] ?? '',
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_price' => $item['unit_price'] ?? 0,
                    'amount' => $item['amount'] ?? 0,
                    'cogs_amount' => $item['cogs_amount'] ?? 0
                ];
            }
        } else {
            $this->addItem();
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
    public function clientOptions(): array
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
    public function serviceOptions(): array
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
    public function subtotal(): int
    {
        return collect($this->items)->sum('amount');
    }

    #[Computed]
    public function discountAmount(): int
    {
        if (!$this->hasDiscount)
            return 0;

        $subtotal = $this->subtotal;
        return $this->discount['type'] === 'percentage'
            ? (int) ($subtotal * $this->discount['value'] / 100)
            : (int) $this->discount['value'];
    }

    #[Computed]
    public function totalAmount(): int
    {
        return $this->subtotal - $this->discountAmount;
    }

    public function addItem(): void
    {
        $this->items[] = [
            'client_id' => $this->invoice->client_id ?? '',
            'service_id' => '',
            'service_name' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'amount' => 0,
            'cogs_amount' => 0
        ];
    }

    public function addMultipleItems(): void
    {
        $count = max(1, $this->itemsToAdd);
        for ($i = 0; $i < $count; $i++) {
            $this->addItem();
        }
        $this->itemsToAdd = 1;
    }

    public function removeItem($index): void
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
            $this->selectedItems = array_values(
                array_filter($this->selectedItems, fn($idx) => $idx < count($this->items))
            );
        }
    }

    public function bulkDeleteItems(): void
    {
        if (empty($this->selectedItems)) {
            $this->toast()->warning('Warning', 'Pilih item yang ingin dihapus')->send();
            return;
        }

        $indices = collect($this->selectedItems)->sort()->reverse();
        foreach ($indices as $index) {
            if (count($this->items) > 1) {
                unset($this->items[$index]);
            }
        }

        $this->items = array_values($this->items);
        $this->selectedItems = [];

        if (empty($this->items)) {
            $this->addItem();
        }

        $this->toast()->success('Berhasil', 'Item berhasil dihapus')->send();
    }

    // Auto-fill service data
    public function fillServiceData($itemIndex): void
    {
        if (!isset($this->items[$itemIndex]['service_id']))
            return;

        $serviceId = $this->items[$itemIndex]['service_id'];
        if (!$serviceId)
            return;

        $service = Service::find($serviceId);
        if (!$service)
            return;

        $this->items[$itemIndex]['service_name'] = $service->name;
        $this->items[$itemIndex]['unit_price'] = $service->price;
        $this->calculateAmount($itemIndex);
    }

    // Handle manual field changes
    public function updatedItems($value, $key): void
    {
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = (int) $parts[0];
            $field = $parts[1];

            if (in_array($field, ['quantity', 'unit_price'])) {
                $this->calculateAmount($index);
            }
        }
    }

    private function calculateAmount($index): void
    {
        if (!isset($this->items[$index]))
            return;

        $quantity = (int) ($this->items[$index]['quantity'] ?? 1);
        $unitPrice = $this->parseAmount($this->items[$index]['unit_price'] ?? 0);

        $this->items[$index]['amount'] = $quantity * $unitPrice;
    }

    private function parseAmount($amount): int
    {
        if (is_numeric($amount))
            return (int) $amount;
        return (int) preg_replace('/[^0-9]/', '', $amount);
    }

    public function save(): void
    {
        if ($this->invoice->status === 'published') {
            $this->toast()->error('Error', 'Invoice yang sudah dipublish tidak dapat diedit')->send();
            return;
        }

        // Parse currency fields
        foreach ($this->items as $index => $item) {
            $this->items[$index]['unit_price'] = $this->parseAmount($item['unit_price'] ?? 0);
            $this->items[$index]['cogs_amount'] = $this->parseAmount($item['cogs_amount'] ?? 0);
            $this->items[$index]['amount'] = $this->parseAmount($item['amount'] ?? 0);
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

        try {
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
            $this->modal = false;
            $this->toast()->success('Berhasil', 'Invoice berhasil diperbarui')->send();

        } catch (\Exception $e) {
            $this->toast()->error('Error', $e->getMessage())->send();
        }
    }

    public function render()
    {
        return view('livewire.recurring-invoices.monthly.edit-invoice');
    }
}
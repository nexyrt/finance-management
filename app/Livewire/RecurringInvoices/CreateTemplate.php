<?php

namespace App\Livewire\RecurringInvoices;

use TallStackUi\Traits\Interactions;
use App\Models\RecurringTemplate;
use App\Models\Client;
use App\Models\Service;
use Livewire\Component;
use Livewire\Attributes\Computed;

class CreateTemplate extends Component
{
    use Interactions;

    public bool $modal = false;
    public array $template = [
        'template_name' => '',
        'client_id' => '',
        'start_date' => '',
        'end_date' => '',
        'frequency' => 'monthly'
    ];

    public array $items = [];
    public array $selectedItems = [];
    public int $itemsToAdd = 1;
    public bool $hasDiscount = false;
    public array $discount = ['type' => 'fixed', 'value' => 0, 'reason' => ''];

    public function mount()
    {
        $this->template['start_date'] = now()->format('Y-m-d');
        $this->template['end_date'] = now()->addYear()->format('Y-m-d');
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

    #[Computed]
    public function estimatedInvoices()
    {
        $start = \Carbon\Carbon::parse($this->template['start_date']);
        $end = \Carbon\Carbon::parse($this->template['end_date']);

        return match ($this->template['frequency']) {
            'monthly' => $start->diffInMonths($end) + 1,
            'quarterly' => intval(($start->diffInMonths($end) + 1) / 3),
            'semi_annual' => intval(($start->diffInMonths($end) + 1) / 6),
            'annual' => $start->diffInYears($end) + 1,
            default => 1
        };
    }

    public function addItem()
    {
        $this->items[] = [
            'client_id' => $this->template['client_id'] ?? '',
            'service_id' => '',
            'service_name' => '',
            'quantity' => 1,
            'unit_price' => '',
            'amount' => 0,
            'cogs_amount' => ''
        ];
    }

    public function updatedTemplateClientId()
    {
        // Update existing items with new client selection
        foreach ($this->items as $index => $item) {
            $this->items[$index]['client_id'] = $this->template['client_id'];
        }
    }

    public function addMultipleItems()
    {
        $count = max(1, $this->itemsToAdd);

        for ($i = 0; $i < $count; $i++) {
            $this->addItem();
        }

        $this->itemsToAdd = 1; // Reset to default
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);

        // Update selected items indices
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

        // Sort indices in descending order to avoid index shifting
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
        // Parse currency fields before validation
        foreach ($this->items as $index => $item) {
            $this->items[$index]['unit_price'] = $this->parseAmount($item['unit_price'] ?? '0');
            $this->items[$index]['cogs_amount'] = $this->parseAmount($item['cogs_amount'] ?? '0');
        }

        $this->validate([
            'template.template_name' => 'required|string|max:255',
            'template.client_id' => 'required|exists:clients,id',
            'template.start_date' => 'required|date',
            'template.end_date' => 'required|date|after:template.start_date',
            'template.frequency' => 'required|in:monthly,quarterly,semi_annual,annual',
            'items' => 'required|array|min:1',
            'items.*.client_id' => 'required|exists:clients,id',
            'items.*.service_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|integer|min:0',
            'items.*.cogs_amount' => 'required|integer|min:0',
        ]);

        $template = RecurringTemplate::create([
            'client_id' => $this->template['client_id'],
            'template_name' => $this->template['template_name'],
            'start_date' => $this->template['start_date'],
            'end_date' => $this->template['end_date'],
            'frequency' => $this->template['frequency'],
            'next_generation_date' => $this->template['start_date'],
            'status' => 'active',
            'invoice_template' => [
                'items' => $this->items,
                'subtotal' => $this->subtotal,
                'discount_amount' => $this->discountAmount,
                'discount_type' => $this->discount['type'],
                'discount_value' => $this->discount['value'],
                'discount_reason' => $this->discount['reason'],
                'total_amount' => $this->totalAmount,
            ]
        ]);

        $this->dispatch('template-created');
        $this->reset(['template', 'items', 'selectedItems', 'hasDiscount', 'discount']);
        $this->itemsToAdd = 1;
        $this->modal = false;

        $this->toast()->success('Berhasil', 'Template recurring invoice berhasil dibuat')->send();
    }

    public function render()
    {
        return view('livewire.recurring-invoices.create-template');
    }
}
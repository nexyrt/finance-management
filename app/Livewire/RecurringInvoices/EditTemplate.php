<?php

namespace App\Livewire\RecurringInvoices;

use TallStackUi\Traits\Interactions;
use App\Models\RecurringTemplate;
use App\Models\Client;
use App\Models\Service;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class EditTemplate extends Component
{
    use Interactions;

    public ?RecurringTemplate $template = null;
    public bool $modal = false;

    public array $templateData = [
        'template_name' => '',
        'client_id' => '',
        'start_date' => '',
        'end_date' => '',
        'frequency' => 'monthly',
        'status' => 'active'
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

    #[On('edit-template')]
    public function load($templateId): void
    {
        $this->template = RecurringTemplate::with('client')->find($templateId);

        if (!$this->template) {
            $this->toast()->error('Error', 'Template tidak ditemukan')->send();
            return;
        }

        $this->loadTemplateData();
        $this->loadTemplateItems();
        $this->loadDiscountData();

        $this->selectedItems = [];
        $this->modal = true;
    }

    private function loadTemplateData(): void
    {
        $this->templateData = [
            'template_name' => $this->template->template_name,
            'client_id' => $this->template->client_id,
            'start_date' => $this->template->start_date->format('Y-m-d'),
            'end_date' => $this->template->end_date->format('Y-m-d'),
            'frequency' => $this->template->frequency,
            'status' => $this->template->status
        ];
    }

    private function loadTemplateItems(): void
    {
        $this->items = [];
        $invoiceTemplate = $this->template->invoice_template;

        if (!empty($invoiceTemplate['items'])) {
            foreach ($invoiceTemplate['items'] as $item) {
                $this->items[] = [
                    'client_id' => $item['client_id'] ?? $this->templateData['client_id'],
                    'service_id' => '',
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
    }

    private function loadDiscountData(): void
    {
        $invoiceTemplate = $this->template->invoice_template;

        if (($invoiceTemplate['discount_amount'] ?? 0) > 0) {
            $this->hasDiscount = true;
            $this->discount = [
                'type' => $invoiceTemplate['discount_type'] ?? 'fixed',
                'value' => $invoiceTemplate['discount_value'] ?? 0,
                'reason' => $invoiceTemplate['discount_reason'] ?? ''
            ];
        } else {
            $this->hasDiscount = false;
            $this->discount = ['type' => 'fixed', 'value' => 0, 'reason' => ''];
        }
    }

    // Auto-fill saat service dipilih - ini method yang akan dipanggil dari blade
    public function fillServiceData($itemIndex)
    {
        if (!isset($this->items[$itemIndex]['service_id']))
            return;

        $serviceId = $this->items[$itemIndex]['service_id'];
        if (!$serviceId)
            return;

        $service = Service::find($serviceId);
        if (!$service)
            return;

        // Update item dengan data service
        $this->items[$itemIndex]['service_name'] = $service->name;
        $this->items[$itemIndex]['unit_price'] = $service->price;

        // Hitung ulang amount
        $this->calculateAmount($itemIndex);

        // Trigger UI update
        $this->dispatch('$refresh');
    }

    // Method untuk handle perubahan manual pada field
    public function updatedItems($value, $key)
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

    #[Computed]
    public function estimatedInvoices(): int
    {
        if (empty($this->templateData['start_date']) || empty($this->templateData['end_date'])) {
            return 0;
        }

        $start = \Carbon\Carbon::parse($this->templateData['start_date']);
        $end = \Carbon\Carbon::parse($this->templateData['end_date']);

        return match ($this->templateData['frequency']) {
            'monthly' => $start->diffInMonths($end) + 1,
            'quarterly' => intval(($start->diffInMonths($end) + 1) / 3),
            'semi_annual' => intval(($start->diffInMonths($end) + 1) / 6),
            'annual' => $start->diffInYears($end) + 1,
            default => 1
        };
    }

    public function addItem(): void
    {
        $this->items[] = [
            'client_id' => $this->templateData['client_id'] ?? '',
            'service_id' => '',
            'service_name' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'amount' => 0,
            'cogs_amount' => 0
        ];
    }

    public function updatedTemplateDataClientId(): void
    {
        foreach ($this->items as $index => $item) {
            $this->items[$index]['client_id'] = $this->templateData['client_id'];
        }
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

    public function save(): void
    {
        foreach ($this->items as $index => $item) {
            $this->items[$index]['unit_price'] = $this->parseAmount($item['unit_price'] ?? 0);
            $this->items[$index]['cogs_amount'] = $this->parseAmount($item['cogs_amount'] ?? 0);
            $this->items[$index]['amount'] = $this->parseAmount($item['amount'] ?? 0);
        }

        $this->validate([
            'templateData.template_name' => 'required|string|max:255',
            'templateData.client_id' => 'required|exists:clients,id',
            'templateData.start_date' => 'required|date',
            'templateData.end_date' => 'required|date|after:templateData.start_date',
            'templateData.frequency' => 'required|in:monthly,quarterly,semi_annual,annual',
            'templateData.status' => 'required|in:active,inactive',
            'items' => 'required|array|min:1',
            'items.*.client_id' => 'required|exists:clients,id',
            'items.*.service_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|integer|min:0',
            'items.*.cogs_amount' => 'required|integer|min:0',
        ]);

        try {
            \DB::transaction(function () {
                $this->updateTemplate();
            });

            $this->dispatch('template-updated');
            $this->modal = false;
            $this->toast()->success('Berhasil', 'Template berhasil diperbarui')->send();

        } catch (\Exception $e) {
            $this->toast()->error('Error', $e->getMessage())->send();
        }
    }

    private function updateTemplate(): void
    {
        $nextDate = \Carbon\Carbon::parse($this->templateData['start_date']);

        $this->template->update([
            'client_id' => $this->templateData['client_id'],
            'template_name' => $this->templateData['template_name'],
            'start_date' => $this->templateData['start_date'],
            'end_date' => $this->templateData['end_date'],
            'frequency' => $this->templateData['frequency'],
            'status' => $this->templateData['status'],
            'next_generation_date' => $nextDate,
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
    }

    public function render()
    {
        return view('livewire.recurring-invoices.edit-template');
    }
}
<?php

namespace App\Livewire\RecurringInvoices\Monthly;

use App\Models\Client;
use App\Models\Service;
use App\Models\RecurringInvoice;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class EditInvoice extends Component
{
    public RecurringInvoice $invoice;

    // Invoice data - synced from Alpine
    public $invoiceData = [
        'scheduled_date' => null,
    ];

    // Items - synced from Alpine
    public $items = [];

    // Discount - synced from Alpine
    public $discount = [
        'type' => 'fixed',
        'value' => 0,
        'reason' => '',
    ];

    public function mount(RecurringInvoice $invoice)
    {
        if ($invoice->status === 'published') {
            session()->flash('error', 'Published invoice cannot be edited.');
            return $this->redirect(route('recurring-invoices.index'), navigate: true);
        }

        $this->invoice = $invoice->load('client');
    }

    public function update()
    {
        if ($this->invoice->status === 'published') {
            session()->flash('error', 'Published invoice cannot be edited.');
            return;
        }

        $this->validate([
            'invoiceData.scheduled_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.client_id' => 'required|exists:clients,id',
            'items.*.service_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required',
            'discount.type' => 'in:fixed,percentage',
            'discount.value' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Parse items
            $parsedItems = [];
            $subtotal = 0;

            foreach ($this->items as $item) {
                $unitPrice = $this->parseAmount($item['unit_price']);
                $quantity = $item['quantity'];
                $amount = $unitPrice * $quantity;
                $cogsAmount = $this->parseAmount($item['cogs_amount'] ?? '0');
                $isTaxDeposit = $item['is_tax_deposit'] ?? false;

                $parsedItems[] = [
                    'client_id' => $item['client_id'],
                    'service_name' => $item['service_name'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'amount' => $amount,
                    'cogs_amount' => $cogsAmount,
                    'is_tax_deposit' => $isTaxDeposit,
                ];

                $subtotal += $amount;
            }

            // Calculate discount
            $discountAmount = 0;
            $discountValue = 0;
            if ($this->discount['type'] === 'fixed') {
                $discountValue = $this->discount['value'];
                $discountAmount = $this->discount['value'];
            } else {
                $discountValue = $this->discount['value'];
                $discountAmount = ($subtotal * $this->discount['value']) / 100;
            }

            $totalAmount = max(0, $subtotal - $discountAmount);

            // Update invoice
            $this->invoice->update([
                'scheduled_date' => $this->invoiceData['scheduled_date'],
                'invoice_data' => [
                    'items' => $parsedItems,
                    'subtotal' => $subtotal,
                    'discount_type' => $this->discount['type'] ?? 'fixed',
                    'discount_value' => $discountValue,
                    'discount_amount' => $discountAmount,
                    'discount_reason' => $this->discount['reason'],
                    'total_amount' => $totalAmount,
                ],
            ]);

            DB::commit();

            session()->flash('success', "Invoice for {$this->invoice->scheduled_date->format('F Y')} updated successfully!");

            return $this->redirect(route('recurring-invoices.index'), navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update recurring invoice: ' . $e->getMessage());
            session()->flash('error', 'Failed to update invoice. Please try again.');
        }
    }

    private function parseAmount($value): int
    {
        if (empty($value))
            return 0;
        return (int) preg_replace('/[^0-9]/', '', $value);
    }

    #[Computed]
    public function existingInvoiceData()
    {
        return [
            'scheduled_date' => $this->invoice->scheduled_date->format('Y-m-d'),
        ];
    }

    #[Computed]
    public function existingItems()
    {
        $invoiceData = $this->invoice->invoice_data;
        return collect($invoiceData['items'] ?? [])->map(function ($item, $index) {
            $client = Client::find($item['client_id']);
            return [
                'id' => $index + 1,
                'client_id' => $item['client_id'],
                'client_name' => $client->name ?? '',
                'service_name' => $item['service_name'],
                'quantity' => $item['quantity'],
                'unit_price' => number_format($item['unit_price'], 0, ',', '.'),
                'amount' => $item['amount'],
                'cogs_amount' => number_format($item['cogs_amount'] ?? 0, 0, ',', '.'),
                'profit' => $item['amount'] - ($item['cogs_amount'] ?? 0),
                'is_tax_deposit' => $item['is_tax_deposit'] ?? false,
            ];
        })->toArray();
    }

    #[Computed]
    public function existingDiscount()
    {
        $invoiceData = $this->invoice->invoice_data;
        return [
            'type' => $invoiceData['discount_type'] ?? 'fixed',
            'value' => ($invoiceData['discount_type'] ?? 'fixed') === 'percentage'
                ? ($invoiceData['discount_value'] ?? 0)
                : number_format($invoiceData['discount_value'] ?? 0, 0, ',', '.'),
            'reason' => $invoiceData['discount_reason'] ?? '',
        ];
    }

    #[Computed]
    public function clients()
    {
        return Client::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'logo'])
            ->toArray();
    }

    #[Computed]
    public function services()
    {
        return Service::orderBy('name')
            ->get(['id', 'name', 'price', 'type'])
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $service->price,
                    'type' => $service->type,
                    'formatted_price' => 'Rp ' . number_format($service->price, 0, ',', '.')
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.recurring-invoices.monthly.edit-invoice');
    }
}
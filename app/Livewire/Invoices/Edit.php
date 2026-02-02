<?php

namespace App\Livewire\Invoices;

use App\Models\Client;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public Invoice $invoice;

    // Invoice data - will be synced from Alpine
    public $invoiceData = [
        'invoice_number' => '',
        'client_id' => null,
        'issue_date' => null,
        'due_date' => null,
    ];

    public $faktur;
    public $fakturName;

    // Items array - will be synced from Alpine
    public $items = [];

    // Discount - will be synced from Alpine
    public $discount = [
        'type' => 'fixed',
        'value' => 0,
        'reason' => '',
    ];

    public function mount(Invoice $invoice)
    {
        $this->invoice = $invoice->load(['items.client']);
    }

    public function update()
    {
        $this->validate([
            'invoiceData.client_id' => 'required|exists:clients,id',
            'invoiceData.issue_date' => 'required|date',
            'invoiceData.due_date' => 'required|date|after_or_equal:invoiceData.issue_date',
            'items' => 'required|array|min:1',
            'items.*.client_id' => 'required|exists:clients,id',
            'items.*.service_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required',
            'discount.type' => 'in:fixed,percentage',
            'discount.value' => 'nullable|numeric|min:0',
            'faktur' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'fakturName' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

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

            $updateData = [
                'billed_to_id' => $this->invoiceData['client_id'],
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'discount_type' => $this->discount['type'] ?? 'fixed',
                'discount_value' => $discountValue,
                'discount_reason' => $this->discount['reason'],
                'total_amount' => $totalAmount,
                'issue_date' => $this->invoiceData['issue_date'],
                'due_date' => $this->invoiceData['due_date'],
            ];

            if ($this->faktur) {
                if ($this->invoice->faktur) {
                    Storage::disk('public')->delete($this->invoice->faktur);
                }
                $customName = $this->fakturName ? $this->fakturName : $this->faktur->getClientOriginalName();
                $extension = $this->faktur->getClientOriginalExtension();
                $fileName = pathinfo($customName, PATHINFO_FILENAME) . '.' . $extension;
                $updateData['faktur'] = $this->faktur->storeAs('invoices/fakturs', $fileName, 'public');
            }

            // Update invoice
            $this->invoice->update($updateData);

            // Delete old items and create new ones
            $this->invoice->items()->delete();

            foreach ($parsedItems as $itemData) {
                InvoiceItem::create([
                    'invoice_id' => $this->invoice->id,
                    'client_id' => $itemData['client_id'],
                    'service_name' => $itemData['service_name'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'amount' => $itemData['amount'],
                    'cogs_amount' => $itemData['cogs_amount'],
                    'is_tax_deposit' => $itemData['is_tax_deposit'],
                ]);
            }

            // Evaluate status based on payments
            $this->evaluateAndUpdateStatus();

            DB::commit();

            session()->flash('success', "Invoice {$this->invoice->invoice_number} updated successfully!");

            return $this->redirect(route('invoices.index'), navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update invoice: ' . $e->getMessage());
            session()->flash('error', 'Failed to update invoice. Please try again.');
        }
    }

    private function evaluateAndUpdateStatus()
    {
        $this->invoice->refresh();
        $totalPaid = $this->invoice->payments()->sum('amount');
        $totalAmount = $this->invoice->total_amount;
        $dueDate = \Carbon\Carbon::parse($this->invoice->due_date);

        // Fully paid
        if ($totalPaid >= $totalAmount && $totalPaid > 0) {
            $this->invoice->update(['status' => 'paid']);
            return;
        }

        // Partial payment
        if ($totalPaid > 0 && $totalPaid < $totalAmount) {
            $this->invoice->update(['status' => 'partially_paid']);
            return;
        }

        // No payment
        if ($totalPaid == 0) {
            if ($this->invoice->status === 'draft') {
                $this->invoice->update(['status' => $dueDate->isPast() ? 'overdue' : 'draft']);
            } else {
                $this->invoice->update(['status' => $dueDate->isPast() ? 'overdue' : $this->invoice->status]);
            }
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
        // Load client jika belum di-load
        if (!$this->invoice->relationLoaded('client')) {
            $this->invoice->load('client');
        }

        return [
            'invoice_number' => $this->invoice->invoice_number,
            'client_id' => $this->invoice->billed_to_id,
            'client_name' => $this->invoice->client->name ?? '',
            'issue_date' => $this->invoice->issue_date->format('Y-m-d'),
            'due_date' => $this->invoice->due_date->format('Y-m-d'),
        ];
    }

    #[Computed]
    public function existingItems()
    {
        return $this->invoice->items->map(function ($item, $index) {
            return [
                'id' => $index + 1,
                'client_id' => $item->client_id,
                'client_name' => $item->client->name,
                'service_name' => $item->service_name,
                'quantity' => $item->quantity,
                'unit_price' => number_format($item->unit_price, 0, ',', '.'),
                'amount' => $item->amount,
                'cogs_amount' => number_format($item->cogs_amount, 0, ',', '.'),
                'profit' => $item->amount - $item->cogs_amount,
                'is_tax_deposit' => $item->is_tax_deposit,
            ];
        })->toArray();
    }

    #[Computed]
    public function existingDiscount()
    {
        return [
            'type' => $this->invoice->discount_type,
            'value' => $this->invoice->discount_type === 'percentage'
                ? $this->invoice->discount_value
                : number_format($this->invoice->discount_value, 0, ',', '.'),
            'reason' => $this->invoice->discount_reason ?? '',
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
        return view('livewire.invoices.edit');
    }
}